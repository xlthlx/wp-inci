<?php
/**
 * Plugin Name: CMB2 Checkbox Switch Button
 * Description: Custom Checkbox Switch Button field type for CMB2 Metabox for WordPress. Based on https://github.com/themevan/CMB2-Switch-Button
 * Version: 1.0.0
 * Author: xlthlx
 * Author URI: https://github.com/xlthlx/
 * License: GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'CMB2_Checkbox_Switch_Button' ) ) {
	/**
	 * Class CMB2_Checkbox_Switch_Button
	 */
	class CMB2_Checkbox_Switch_Button {

		/**
		 * A static reference to track the single instance of this class.
		 */
		private static $instance;

		/**
		 * Current version number.
		 */
		protected static $version = '1.0.0';

		/**
		 * Url for this plugin.
		 */
		public $url = "";

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->init();
			$this->url = plugins_url( "", __FILE__ );
		}

		/**
		 * Standard init.
		 */
		public function init() {
			/**
			 * Add hooks and queue.
			 */
			add_action( 'cmb2_render_switch', array( $this, 'render' ), 10, 5 );
			add_action( 'admin_menu', array( $this, 'plugin_setup' ) );
			add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return CMB2_Checkbox_Switch_Button|null
		 */
		public static function get_instance_switch_button() {

			if ( null === self::$instance ) {
				self::$instance = new CMB2_Checkbox_Switch_Button();
			}

			return self::$instance;
		}


		/**
		 * Render field.
		 *
		 * @param $field
		 * @param $escaped_value
		 * @param $object_id
		 * @param $object_type
		 * @param $field_type_object
		 */
		public function render( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			$field_name   = $field->_name();
			$active_value = ! empty( $field->args( 'active_value' ) ) ? $field->args( 'active_value' ) : 'on';

			$args = array(
				'type'  => 'checkbox',
				'id'    => $field_name,
				'name'  => $field_name,
				'desc'  => '',
				'value' => $active_value,
			);
			if ( $escaped_value == $active_value ) {
				$args['checked'] = 'checked';
			}

			echo '<label class="cmb2-switch">';
			echo $field_type_object->input( $args );
			echo '<span class="cmb2-slider round"></span>';
			echo '</label>';
			$field_type_object->_desc( true, true );
		}


		/**
		 * Attach styles.
		 */
		public function plugin_setup() {
			$this->plugin_admin_styles();
		}

		/**
		 * Enqueue style.
		 */
		public function plugin_admin_styles() {

			wp_register_style( 'cmb2-switch-button', $this->url . '/css/cmb2-switch-button.min.css', '', $this->version );
			wp_enqueue_style( 'cmb2-switch-button' );

		}

		/**
		 *  Adds styles for checked and focus based on WP color scheme.
		 */
		public function admin_footer() {
			global $_wp_admin_css_colors;
			if ( ! empty( $_wp_admin_css_colors[ get_user_option( 'admin_color' ) ] ) ) {
				$scheme_colors = $_wp_admin_css_colors[ get_user_option( 'admin_color' ) ]->colors;
			}
			$toggle_color = ! empty( $scheme_colors ) ? end( $scheme_colors ) : '#2196F3';
			?>
            <style>

                input:checked + .cmb2-slider {
                    background-color: <?php echo $toggle_color ?>;
                }

                input:focus + .cmb2-slider {
                    box-shadow: 0 0 1px<?php echo $toggle_color ?>;
                }
            </style>
			<?php
		}
	}

	add_action( 'plugins_loaded', array( 'CMB2_Checkbox_Switch_Button', 'get_instance_switch_button' ) );
}
