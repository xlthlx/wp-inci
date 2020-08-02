<?php
/**
 * Plugin Name: CMB2 Field Type Input Search Ajax
 * Plugin URI: https://github.com/alexis-magina/cmb2-field-post-search-ajax
 * Description: CMB2 field type to attach posts to each other. Based on https://github.com/alexis-magina/cmb2-field-post-search-ajax
 * Version: 2.0.0
 * Author: xlthlx
 * Author URI: https://github.com/xlthlx/
 * License: GPLv2+
 */

/**
 * Class CMB2_Field_Input_Search_Ajax
 */
if ( ! class_exists( 'CMB2_Field_Input_Search_Ajax' ) ) {

	class CMB2_Field_Input_Search_Ajax {

		/**
		 * Current version number
		 */
		protected static $version = '1.0.1';

		/**
		 * The url which is used to load local resources
		 */
		protected static $url = '';

		/**
		 * Initialize the plugin by hooking into CMB2
		 */
		public function __construct() {
			add_action( 'cmb2_render_input_search_ajax', array( $this, 'render' ), 10, 5 );
			add_action( 'cmb2_sanitize_input_search_ajax', array( $this, 'sanitize' ), 10, 4 );
			add_action( 'wp_ajax_cmb_input_search_ajax_get_results', array(
				$this,
				'cmb_input_search_ajax_get_results'
			) );
		}

		/**
		 * Render field
		 *
		 * @param $field
		 * @param $value
		 * @param $object_id
		 * @param $object_type
		 * @param $field_type
		 */
		public function render( $field, $value, $object_id, $object_type, $field_type ) {
			$this->setup_admin_scripts();
			$field_name = $field->_name();

			if ( 1 !== $field->args( 'limit' ) ) {
				echo '<ul class="cmb-post-search-ajax-results" id="' . $field_name . '_results">';
				if ( isset( $value ) && ! empty( $value ) ) {
					if ( ! is_array( $value ) ) {
						$value = array( $value );
					}
					foreach ( $value as $val ) {
						$handle = ( $field->args( 'sortable' ) ) ? '<span class="handle"></span>' : '';
						if ( $field->args( 'object_type' ) === 'user' ) {
							$guid  = get_edit_user_link( $val );
							$user  = get_userdata( $val );
							$title = $user->display_name;
						} else {
							$guid  = get_edit_post_link( $val );
							$title = get_the_title( $val );
						}
						echo '<li>' . $handle . '<input type="hidden" name="' . $field_name . '_results[]" value="' . $val . '"><a href="' . $guid . '" target="_blank" class="edit-link">' . $title . '</a><a class="remover"><span class="dashicons dashicons-no"></span><span class="dashicons dashicons-dismiss"></span></a></li>';
					}
				}
				echo '</ul>';
				$field_value = '';
			} else {
				if ( is_array( $value ) ) {
					$value = $value[0];
				}
				if ( $field->args( 'object_type' ) === 'user' ) {
					$field_value = ( $value ? get_userdata( $value )->display_name : '' );
				} else {
					$field_value = ( $value ? get_the_title( $value ) : '' );
				}
				echo $field_type->input( array(
					'type'  => 'hidden',
					'name'  => $field_name . '_results',
					'value' => $value,
					'desc'  => false
				) );
			}

			echo $field_type->input( array(
				'type'           => 'text',
				'name'           => $field_name,
				'id'             => $field_name,
				'class'          => 'cmb-post-search-ajax',
				'value'          => $field_value,
				'desc'           => false,
				'data-limit'     => $field->args( 'limit' ) ?: '1',
				'data-sortable'  => $field->args( 'sortable' ) ?: '0',
				'data-object'    => $field->args( 'object_type' ) ?: 'post',
				'data-queryargs' => $field->args( 'query_args' ) ? htmlspecialchars( json_encode( $field->args( 'query_args' ) ), ENT_QUOTES, 'UTF-8' ) : ''
			) );

			echo '<img src="' . admin_url( 'images/spinner.gif' ) . '" class="cmb-post-search-ajax-spinner" />';

			$field_type->_desc( true, true );

		}

		/**
		 * Optionally save the latitude/longitude values into two custom fields.
		 *
		 * @param $override_value
		 * @param $value
		 * @param $object_id
		 * @param $field_args
		 *
		 * @return bool|array
		 */
		public function sanitize( $override_value, $value, $object_id, $field_args ) {
			$fid = $field_args['id'];
			if ( ! empty( $field_args['render_row_cb'][0]->data_to_save[ $fid . '_results' ] ) ) {
				$value = $field_args['render_row_cb'][0]->data_to_save[ $fid . '_results' ];
			} elseif ( ! defined( 'DOING_AJAX' )) {
				$value = false;
			}

			return $value;
		}

		/**
		 * Defines the url which is used to load local resources. Based on, and uses,
		 * the CMB2_Utils class from the CMB2 library
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public static function url( $path = '' ): string {
			if ( self::$url ) {
				return self::$url . $path;
			}

			/**
			 * Set the variable cmb2_fpsa_dir
			 */
			$cmb2_fpsa_dir = trailingslashit( __DIR__ );

			/**
			 * Use CMB2_Utils to gather the url from cmb2_fpsa_dir
			 */
			$cmb2_fpsa_url = CMB2_Utils::get_url_from_dir( $cmb2_fpsa_dir );

			/**
			 * Filter the CMB2 FPSA location url
			 */
			self::$url = trailingslashit( apply_filters( 'cmb2_fpsa_url', $cmb2_fpsa_url, self::$version ) );

			return self::$url . $path;
		}

		/**
		 * Enqueue scripts and styles
		 */
		public function setup_admin_scripts() {

			wp_register_script( 'jquery-autocomplete', self::url( 'js/jquery.autocomplete.min.js' ), array( 'jquery' ), self::$version );
			wp_register_script( 'post-search-ajax', self::url( 'js/post-search-ajax.js' ), array(
				'jquery',
				'jquery-autocomplete',
				'jquery-ui-sortable'
			), self::$version );
			wp_localize_script( 'post-search-ajax', 'psa', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'cmb_input_search_ajax_get_results' ),
				'notice'  => __( 'No results found.' ), // Added line.
			) );
			wp_enqueue_script( 'post-search-ajax' );
			wp_enqueue_style( 'post-search-ajax', self::url( 'css/post-search-ajax.min.css' ), array(), self::$version );

		}

		/**
		 * Ajax request: get results
		 */
		public function cmb_input_search_ajax_get_results() {
			$nonce = $_POST['psacheck'];
			if ( ! wp_verify_nonce( $nonce, 'cmb_input_search_ajax_get_results' ) ) {
				die( json_encode( array( 'error' => __( 'Error : Unauthorized action' ) ) ) );
			}

			$args      = json_decode( stripslashes( htmlspecialchars_decode( $_POST['query_args'] ) ), true );
			$args['s'] = $_POST['query'];
			$data      = array();
			if ( $_POST['object'] === 'user' ) {
				$args['search'] = '*' . esc_attr( $_POST['query'] ) . '*';
				$users          = new WP_User_Query( $args );
				$results        = $users->get_results();
				if ( ! empty( $results ) ) {
					foreach ( $results as $result ) {
						$user_info = get_userdata( $result->ID );
						// Define filter "cmb_input_search_ajax_result" to allow customize ajax results.
						$data[] = apply_filters( 'cmb_input_search_ajax_result', array(
							'value' => $user_info->display_name,
							'data'  => $result->ID,
							'guid'  => get_edit_user_link( $result->ID )
						) );
					}
				}
			} else {
				$results = new WP_Query( $args );
				if ( $results->have_posts() ) :
					while ( $results->have_posts() ) : $results->the_post();
						// Define filter "cmb_input_search_ajax_result" to allow customize ajax results.
						$data[] = apply_filters( 'cmb_input_search_ajax_result', array(
							'value' => get_the_title(),
							'data'  => get_the_ID(),
							'guid'  => get_edit_post_link()
						) );
					endwhile;
				endif;
			}
			wp_reset_postdata();
			die( json_encode( $data ) );
		}
	}

}
$CMB2_Field_Input_Search_Ajax = new CMB2_Field_Input_Search_Ajax();
