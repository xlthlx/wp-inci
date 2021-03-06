<?php

/**
 * WP_Inci_Admin
 *
 * Class for Manage Admin (back-end)
 *
 * @package         wp-inci
 * @author          xlthlx <wp-inci@piccioni.london>
 *
 */
if ( ! class_exists( 'WP_Inci_Admin', false ) ) {
	class WP_Inci_Admin extends WP_Inci {

		/**
		 * A static reference to track the single instance of this class.
		 */
		private static $instance;

		/**
		 * Url for the admin folder.
		 */
		public $admin_url = "";

		/**
		 * Constructor.
		 */
		public function __construct() {
			( WP_Inci::get_instance() )->__construct();
			$this->init();
			$this->admin_url = plugins_url( "", __FILE__ );
		}

		/**
		 * Standard init
		 */
		public function init() {
			/**
			 * Load localizations if available.
			 */
			load_plugin_textdomain( 'wp-inci', false, 'wp-inci/languages' );

			/**
			 * Add hooks and queue.
			 */
			add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );
			add_action( 'admin_init', array( $this, 'plugin_init' ) );
			add_action( 'admin_menu', array( $this, 'plugin_setup' ) );
		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return WP_Inci_Admin|null
		 */
		public static function get_instance_admin() {

			if ( null === self::$instance ) {
				self::$instance = new WP_Inci_Admin();
			}

			return self::$instance;
		}

		/**
		 * Register activation and deactivation.
		 */
		public function plugin_init() {

			register_activation_hook( __FILE__, 'plugin_activation' );
			register_deactivation_hook( __FILE__, 'plugin_deactivation' );
			register_uninstall_hook( __FILE__, 'plugin_uninstall' );
		}

		/**
		 * Plugin activation hook
		 */
		public function plugin_activation() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			flush_rewrite_rules();
		}

		/**
		 * Plugin deactivation hook
		 */
		public function plugin_deactivation() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			flush_rewrite_rules();
		}

		/**
		 * Plugin uninstall hook
		 */
		public function plugin_uninstall() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			if ( __FILE__ !== WP_UNINSTALL_PLUGIN ) {
				return;
			}

			delete_option( 'wi_disclaimer' );
			delete_option( 'wi_disable_style' );

		}

		/**
		 * Attach styles and scripts
		 */
		public function plugin_setup() {

			$this->plugin_admin_styles();
			$this->plugin_admin_scripts();

		}

		/**
		 * Add queue for CSS.
		 */
		public function plugin_admin_styles() {
			wp_register_style( 'wp-inci-admin-css', $this->admin_url . '/css/wp-inci-admin.min.css', array(), $this->version );
			wp_enqueue_style( 'wp-inci-admin-css' );
		}

		/**
		 * Add queue for JS.
		 */
		public function plugin_admin_scripts() {
			if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			wp_register_script( 'wp-inci-admin-js', $this->admin_url . '/js/wp-inci-admin.min.js', array( 'jquery' ), $this->version );
			wp_enqueue_script( 'wp-inci-admin-js' );
		}

		/**
		 * Attach settings in WordPress Plugins list.
		 */
		public function register_plugin_settings() {
			add_action( 'plugin_action_links', array( $this, 'add_plugin_settings' ), 10, 2 );
		}

		/**
		 * Add settings link to plugin actions.
		 *
		 * @param array $plugin_actions
		 * @param string $plugin_file
		 *
		 * @return array
		 * @since  1.0
		 */
		public function add_plugin_settings( array $plugin_actions, string $plugin_file ) {
			$new_actions = array();

			if ( $plugin_file === plugin_basename( $this->plugin_file ) ) {
				$new_actions['wi_settings'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=wi_settings' ) ) . '">' . __( 'Settings' ) . '</a>';
			}

			return array_merge( $new_actions, $plugin_actions );
		}
	}

	add_action( 'plugins_loaded', array( 'WP_Inci_Admin', 'get_instance_admin' ) );
}
