<?php

/**
 * WP_Inci_Admin
 * Class for Manage Admin (back-end)
 *
 * @package         wp-inci
 * @author          xlthlx <github@piccioni.london>
 *
 */
if ( ! class_exists( 'WP_Inci_Meta', false ) ) {
	class WP_Inci_Meta extends WP_Inci {

		/**
		 * A static reference to track the single instance of this class.
		 */
		private static $instance;

		public function __construct() {
			( WP_Inci::get_instance() )->__construct();
			$this->init();
			$this->url = plugins_url( "", __FILE__ );
		}

		/**
		 * Standard init
		 */
		public function init() {

			/**
			 * Include and setup custom meta boxes and fields.
			 */
			add_action( 'cmb2_admin_init', array( $this, 'wp_inci_register_source_url' ) );
			add_action( 'cmb2_admin_init', array( $this, 'wp_inci_register_ingredients_repeater' ) );
			add_action( 'cmb2_admin_init', array( $this, 'wp_inci_register_safety_select' ) );
			add_action( 'cmb2_admin_init', array( $this, 'wp_inci_register_page_settings' ) );
			add_action( 'admin_init', array( $this, 'wp_inci_remove_menu_page' ) );
			add_filter( 'parent_file', array( $this, 'wp_inci_select_other_menu' ) );
		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return WP_Inci_Meta|null
		 */
		public static function get_instance_meta() {

			if ( null === self::$instance ) {
				self::$instance = new WP_Inci_Meta();
			}

			return self::$instance;
		}

		/**
		 * Create new custom meta 'source_url' for Source taxonomy.
		 */
		public function wp_inci_register_source_url() {

			$cmb_term = new_cmb2_box( array(
				'id'               => 'wp_inci_source_url',
				'title'            => __( 'Url', 'wp-inci' ),
				'object_types'     => array( 'term' ),
				'taxonomies'       => array( 'source' ),
				'new_term_section' => true,
			) );

			$cmb_term->add_field( array(
				'name'      => __( 'Url', 'wp-inci' ),
				'id'        => 'source_url',
				'type'      => 'text_url',
				'protocols' => array( 'http', 'https' ),
			) );

		}

		/**
		 * Create new custom meta 'ingredients' and 'may_contain' for Product post type.
		 */
		public function wp_inci_register_ingredients_repeater() {

			$ingredients = new_cmb2_box( array(
				'id'           => 'ingredients_search_ajax',
				'title'        => __( 'Ingredients', 'wp-inci' ),
				'object_types' => array( 'product' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => false,
			) );

			$ingredients->add_field( array(
				'name'       => __( 'Ingredient', 'wp-inci' ),
				'id'         => 'ingredients',
				'type'       => 'input_search_ajax',
				'desc'       => __( 'Start typing ingredient name. No results found?',
						'wp-inci' ) . ' <a target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=ingredient' ) ) . '" class="button desc">' . __( 'Add New Ingredient',
						'wp-inci' ) . '</a>',
				'sortable'   => true,
				'limit'      => 10,
				'query_args' => array(
					'post_type'      => 'ingredient',
					'posts_per_page' => - 1,
					'post_status'    => 'publish',
					'order'          => 'ASC',
					'orderby'        => 'title',
				)
			) );

			$may_contain = new_cmb2_box( array(
				'id'           => 'may_contain_search_ajax',
				'title'        => __( 'May Contain', 'wp-inci' ),
				'object_types' => array( 'product' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => false,
			) );

			$may_contain->add_field( array(
				'name'       => __( 'May Contain', 'wp-inci' ),
				'id'         => 'may_contain',
				'type'       => 'input_search_ajax',
				'desc'       => __( 'Start typing ingredient name. No results found?',
						'wp-inci' ) . ' <a target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=ingredient' ) ) . '" class="button desc">' . __( 'Add New Ingredient',
						'wp-inci' ) . '</a>',
				'sortable'   => true,
				'limit'      => 10,
				'query_args' => array(
					'post_type'      => 'ingredient',
					'posts_per_page' => - 1,
					'post_status'    => 'publish',
					'order'          => 'ASC',
					'orderby'        => 'title',
				)
			) );

		}


		/**
		 * Create the Safety select.
		 */
		public function wp_inci_register_safety_select() {

			$safety = new_cmb2_box( array(
				'id'           => 'inci',
				'title'        => __( 'INCI', 'wp-inci' ),
				'object_types' => array( 'ingredient' ), // Post type
				'context'      => 'side',
				'priority'     => 'high',
				'show_names'   => true,
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			) );

			$safety->add_field( array(
				'name'             => '',
				'id'               => 'safety',
				'type'             => 'select',
				'show_option_none' => true,
				'before_field'     => array( $this, 'wp_inci_before_safety' ),
				'options'          => array(
					'1' => __( 'Double green', 'wp-inci' ),
					'2' => __( 'Green', 'wp-inci' ),
					'3' => __( 'Yellow', 'wp-inci' ),
					'4' => __( 'Red', 'wp-inci' ),
					'5' => __( 'Double red', 'wp-inci' ),
				),
			) );

			/**
			 * Create the CAS Number field.
			 */
			$safety->add_field( array(
				'name' => __( 'CAS #', 'wp-inci' ),
				'id'   => 'cas_number',
				'type' => 'text_small',
			) );

			/**
			 * Create the EC Number field.
			 */
			$safety->add_field( array(
				'name' => __( 'EC #', 'wp-inci' ),
				'id'   => 'ec_number',
				'type' => 'text_small',
			) );

			/**
			 * Create the Cosmetic Restriction field.
			 */
			$safety->add_field( array(
				'name' => __( 'Cosmetic Restriction', 'wp-inci' ),
				'id'   => 'cosmetic_restriction',
				'type' => 'text_small',
			) );
		}


		/**
		 * Returns the safety custom meta with HTML before the Safety select.
		 *
		 * @param $field_args
		 * @param $field
		 */
		public function wp_inci_before_safety( $field_args, $field ) {
			echo ( WP_Inci::get_instance() )->wp_inci_get_safety_html( $field->object_id );
		}

		/**
		 * Sets CSS for default style.
		 */
		public function wp_inci_default_style(): string {
			return "
table.wp-inci {
	max-width: 100%;
	margin-bottom: 1em
}

table.wp-inci th {
	text-align: left;
	padding: 5px
}

table.wp-inci td {
	padding: 5px 5px 0;
	vertical-align: top;
	min-width: 55px
}

table.wp-inci h5 {
	margin-bottom: 5px
}

table.wp-inci h6 {
	margin-bottom: 5px;
	font-size: small
}

table.wp-inci .disclaimer {
	font-size: small
}

table.wp-inci .first {
	margin-right: 1px;
	margin-bottom: 5px
}

table.wp-inci div.g {
	background-color: #32cd32;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	border: 0;
	color: #32cd32;
	height: 20px;
	width: 20px;
	display: inline-table
}

table.wp-inci div.r {
	background-color: #dc143c;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	border: 0;
	color: #dc143c;
	height: 20px;
	width: 20px;
	display: inline-table
}

table.wp-inci div.y {
	background-color: #ffd700;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	border: 0;
	color: #ffd700;
	height: 20px;
	width: 20px;
	display: inline-table
}

table.wp-inci div.w {
	background-color: #eee;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	border: 0;
	color: #eee;
	height: 20px;
	width: 20px;
	display: inline-table
}

.disclaimer {
	font-style: italic;
	font-size: 80%;
	font-weight: 400;
	line-height: normal;
	color: #6c757d !important;
}";
		}


		/**
		 * Returns the button to copy the WP INCI style.
		 */
		public function wp_inci_copy_button() {
			echo "<script>var wi_style=`" . $this->wp_inci_default_style() . "`;";
			echo "var wi_msg='". __( 'Style copied to clipboard.', 'wp-inci' ) ."';</script>";
			echo '<button id="copy_style" class="button copy">' . __( 'Copy style', 'wp-inci' ) . '</button><span id="msg"></span>';
		}

		/**
		 * Create WP INCI Settings page.
		 */
		public function wp_inci_register_page_settings() {

			$args = array(
				'id'           => 'wp_inci_settings',
				'title'        => __( 'WP INCI Settings', 'wp-inci' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'wp_inci_settings',
				'tab_group'    => 'wp_inci_settings',
				'tab_title'    => __( 'Settings', 'wp-inci' ),
				'parent_slug'  => 'options-general.php',
			);

			$main_options = new_cmb2_box( $args );

			$desc = __( 'You can disable the WP INCI style and add your own to your theme.<br/>'
			            . 'Just copy the standard WP INCI style above into your style.css and customize it.', 'wp-inci' );

			/**
			 * Create style settings.
			 */
			$main_options->add_field( array(
				'name'    => __( 'Disable WP INCI style', 'wp-inci' ),
				'id'      => 'disable_style',
				'desc'    => '',
				'type'    => 'switch',
				'default' => 'off',
			) );


			$main_options->add_field( array(
				'name'        => __( 'WP INCI Default Style', 'wp-inci' ),
				'desc'        => $desc,
				'id'          => 'textarea_style',
				'type'        => 'textarea_code',
				'default_cb'  => array( $this, 'wp_inci_default_style' ),
				'save_field'  => false,
				'attributes'  => array(
					'readonly'        => 'readonly',
					'data-codeeditor' => json_encode( array(
						'codemirror' => array(
							'mode'     => 'css',
							'readOnly' => 'nocursor',
						),
					) ),
				),
				'after_field' => array( $this, 'wp_inci_copy_button' ),
			) );


			/**
			 * Create disclaimer settings.
			 */
			$args = array(
				'id'           => 'wp_inci_disclaimer',
				'title'        => __( 'WP INCI Disclaimer', 'wp-inci' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'wp_inci_disclaimer',
				'parent_slug'  => 'options-general.php',
				'tab_group'    => 'wp_inci_settings',
				'tab_title'    => __( 'Disclaimer', 'wp-inci' ),
			);

			$secondary_options = new_cmb2_box( $args );

			$secondary_options->add_field( array(
				'name'       => __( 'Disclaimer', 'wp-inci' ),
				'desc'       => __( 'Add a disclaimer after WP INCI table of ingredients.', 'wp-inci' ),
				'id'         => 'textarea_disclaimer',
				'type'       => 'textarea_code',
				'default_cb' => array( $this, 'wp_inci_default_disclaimer' ),
			) );

		}

		/**
		 * Remove the disclaimer menu.
		 */
		public function wp_inci_remove_menu_page() {
			remove_submenu_page( 'options-general.php', 'wp_inci_disclaimer' );
		}


		/**
		 * Highlight the setting menu.
		 *
		 * @param string $parent_file The parent file.
		 *
		 * @return string
		 */
		public function wp_inci_select_other_menu( $parent_file ) {
			global $plugin_page;

			if ( 'wp_inci_disclaimer' === $plugin_page ) {
				$plugin_page = 'wp_inci_settings';
			}

			return $parent_file;
		}

	}

	add_action( 'plugins_loaded', array( 'WP_Inci_Meta', 'get_instance_meta' ) );
}
