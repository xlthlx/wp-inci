<?php

/**
 * Wp_Inci_Api
 *
 * CosIng API Class
 *
 * @package         wp-inci
 * @author          xlthlx <wp-inci@piccioni.london>
 *
 */
if ( ! class_exists( 'Wp_Inci_Api', false ) ) {
	class Wp_Inci_Api extends WP_Inci {

		/**
		 * A static reference to track the single instance of this class.
		 */
		private static $instance;

		/**
		 * Constructor.
		 */
		public function __construct() {
			( WP_Inci::get_instance() )->__construct();
			$this->init();
		}

		/**
		 * Standard init.
		 */
		public function init() {
			/**
			 * Insert the API term as a source.
			 */
			add_action( 'activated_plugin', array( $this, 'add_source_term' ) );
			add_action( 'init', array( $this, 'update' ) );

		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return Wp_Inci_Api|null
		 */
		public static function get_instance_api() {

			if ( null === self::$instance ) {
				self::$instance = new Wp_Inci_Api();
			}

			return self::$instance;
		}

		/**
		 * Add the API source to ingredients.
		 */
		public function add_source_term() {
			$term = term_exists( 'cosing-api', 'source' );
			if ( 0 === $term || null === $term ) {

				$term_id = wp_insert_term(
					'CosIng API',
					'source',
					array(
						'description' => __( 'Inventory of Cosmetic Ingredients as amended by Decision 2006/257/EC establishing a common nomenclature of ingredients employed for labelling cosmetic products throughout the EU.', 'wp-inci' ),
						'slug'        => 'cosing-api',
					)
				);

				if ( ! is_wp_error( $term_id ) ) {
					add_term_meta( $term_id['term_id'], 'source_url', 'https://public.opendatasoft.com/explore/dataset/cosmetic-ingredient-database-ingredients-and-fragrance-inventory/api/', true );
				}

			}

		}

		/**
		 * Get a specific ingredient from the API.
		 *
		 * @param $name
		 * @param string $type
		 *
		 * @return array|false
		 */
		public function get_from_api( $name, $type = '' ) {

			$return  = false;
			$api_url = 'https://public.opendatasoft.com/api/records/1.0/search/?dataset=cosmetic-ingredient-database-ingredients-and-fragrance-inventory&q=&lang=en&rows=1&refine.inci_name=' . $name;

			if ( 'q' === $type ) {
				$api_url = 'https://public.opendatasoft.com/api/records/1.0/search/?dataset=cosmetic-ingredient-database-ingredients-and-fragrance-inventory&q=' . $name . '&lang=en&rows=1';
			}

			$options = array(
				'timeout'     => 120,
				'httpversion' => '1.1',
			);

			$request = wp_remote_get( $api_url, $options );

			try {
				$response = json_decode( $request['body'] );
			} catch ( Exception $ex ) {
				error_log( $ex );
				$response = false;
			}

			if ( ( $response ) && ( isset( $response->records ) ) ) {
				$i = 0;
				foreach ( $response->records as $field ) {
					$return[ $i ]['inci_name']   = $field->fields->inci_name;
					$return[ $i ]['functions']   = $field->fields->function;
					$return[ $i ]['last_update'] = $field->fields->update_date;
					$return[ $i ]['cosing_id']   = $field->fields->cosing_ref_no;
					$return[ $i ]['content']     = $field->fields->chem_iupac_name_description;
					if ( isset( $field->fields->cas_no ) ) {
						$return[ $i ]['cas_no'] = $field->fields->cas_no;
					} else {
						$return[ $i ]['cas_no'] = '';
					}
					if ( isset( $field->fields->ec_no ) ) {
						$return[ $i ]['ec_no'] = $field->fields->ec_no;
					} else {
						$return[ $i ]['ec_no'] = '';
					}
					if ( isset( $field->fields->restriction ) ) {
						$return[ $i ]['restriction'] = $field->fields->restriction;
					}

					$i ++;
				}
			}

			if ( ( $response ) && ( isset ( $response->error ) ) ) {
				error_log( $response );
			}

			return $return;

		}

		/**
		 * Insert a new ingredient with data from the API.
		 *
		 * @param $fields
		 *
		 * @return false|int|WP_Error
		 */
		public function insert_ingredient( $fields ) {

			$restriction = '';

			if ( isset( $fields[0]['restriction'] ) ) {
				$restriction = $fields[0]['restriction'];
			}

			$term_api = get_term_by( 'slug', 'cosing-api', 'source' );
			$source   = array( $term_api->term_id );

			$args = array(
				'post_type'    => 'ingredient',
				'post_title'   => $fields[0]['inci_name'],
				'post_content' => $fields[0]['content'],
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
				'tax_input'    => array(
					'functions' => strtolower( $fields[0]['functions'] ),
					'source'    => $source,
				),
				'meta_input'   => array(
					'cosing_id'   => $fields[0]['cosing_id'],
					'last_update' => $fields[0]['last_update'],
					'cas_number'  => $fields[0]['cas_no'],
					'ec_number'   => $fields[0]['ec_no'],
					'restriction' => $restriction,
				),
			);

			$post_id = wp_insert_post( $args );

			if ( ! is_wp_error( $post_id ) ) {
				$return = $post_id;
			} else {
				error_log( $post_id->get_error_message() );
				$return = false;
			}

			return $return;
		}

		/**
		 * Update an ingredient with data from the API.
		 *
		 * @param $fields
		 *
		 * @param $post_id
		 *
		 * @return false|int|WP_Error
		 */
		public function update_ingredient( $fields, $post_id ) {

			$restriction = '';

			if ( isset( $fields[0]['restriction'] ) ) {
				$restriction = $fields[0]['restriction'];
			}

			$content = get_post_field( 'post_content', $post_id ) . '<p>' . $fields[0]['content'] . '</p>';

			$term_api         = get_term_by( 'slug', 'cosing-api', 'source' );
			$term_source_list = get_the_terms( $post_id, 'source' );
			if ( $term_source_list && ! is_wp_error( $term_source_list ) ) {
				foreach ( $term_source_list as $list ) {
					$all[] = $list->term_id;
				}
			}
			$all[]  = $term_api->term_id;
			$source = array_unique( $all );

			$term_functions_list = get_the_terms( $post_id, 'functions' );


			if ( $term_functions_list && ! is_wp_error( $term_functions_list ) ) {
				foreach ( $term_functions_list as $list ) {
					$all[] = $list->name;
				}
			}

			$funcs = explode( ', ', $fields[0]['functions'] );
			foreach ( $funcs as $func ) {
				$all[] = $func;
			}
			$functions = array_unique( $all );

			$args = array(
				'ID'           => $post_id,
				'post_content' => apply_filters( 'the_content', $content ),
				'tax_input'    => array(
					'functions' => $functions,
					'source'    => $source,
				),
				'meta_input'   => array(
					'cosing_id'   => $fields[0]['cosing_id'],
					'last_update' => $fields[0]['last_update'],
					'cas_number'  => $fields[0]['cas_no'],
					'ec_number'   => $fields[0]['ec_no'],
					'restriction' => $restriction,
				),
			);

			$update = wp_update_post( $args );

			if ( ! is_wp_error( $update ) ) {
				$return = $update;
			} else {
				error_log( $update->get_error_message() );
				$return = false;
			}

			return $return;
		}

		public function update() {
			//Example
			//$this->update_ingredient( $this->get_from_api( 'DISODIUM EDTA' ), 71 );
		}

	}

	add_action( 'plugins_loaded', array( 'Wp_Inci_Api', 'get_instance_api' ) );
}