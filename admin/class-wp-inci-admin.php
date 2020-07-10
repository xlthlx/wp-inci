<?php

/**
 * WP_Inci_Admin
 *
 * Class for Manage Admin (back-end)
 *
 * @package         wp-inci
 * @author          xlthlx <github@piccioni.london>
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

			/**
			 * Plugin activation hook
			 */
			function wp_inci_plugin_activation() {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				flush_rewrite_rules();
			}

			register_activation_hook( __FILE__, 'wp_inci_plugin_activation' );

			/**
			 * Plugin deactivation hook
			 */
			function wp_inci_plugin_deactivation() {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				flush_rewrite_rules();
			}

			register_deactivation_hook( __FILE__, 'wp_inci_plugin_deactivation' );

			/**
			 * Plugin uninstall hook
			 */
			function wp_inci_plugin_uninstall() {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				if ( __FILE__ !== WP_UNINSTALL_PLUGIN ) {
					return;
				}
				//TODO
			}

			register_uninstall_hook( __FILE__, 'wp_inci_plugin_uninstall' );
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
			wp_register_style( 'wp-inci-admin-css', $this->admin_url . '/css/wp-inci-admin.min.css', '', $this->version );
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
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_links' ), 10, 4 );
			add_action( 'plugin_action_links', array( $this, 'add_plugin_settings' ), 10, 4 );
		}

		/**
		 * Add links on installed plugin list.
		 *
		 * @param $plugin_links
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return array
		 */
		public function add_plugin_links( $plugin_links, $plugin_file, $plugin_data, $status ) {
			if ( $plugin_file === plugin_basename( $this->plugin_file ) ) {
				$plugin_links['wp_inci_info'] = '<span class="wi_info">' . __( 'For more info visit', 'wp-inci' ) . ' <a href="https://github.com/xlthlx/wp-inci">WP INCI</a> ' . __( 'on GitHub', 'wp-inci' ) . '</span>';
			}

			return $plugin_links;
		}

		/**
		 * Add settings link to plugin actions.
		 *
		 * @param array $plugin_actions
		 * @param string $plugin_file
		 * @param $plugin_data
		 * @param $context
		 *
		 * @return array
		 * @since  1.0
		 */
		public function add_plugin_settings( $plugin_actions, $plugin_file, $plugin_data, $context ) {
			$new_actions = array();

			if ( $plugin_file === plugin_basename( $this->plugin_file ) ) {
				$new_actions['wp_inci_settings'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=wp_inci_settings' ) ) . '">' . __( 'Settings' ) . '</a>';
			}

			return array_merge( $new_actions, $plugin_actions );
		}
	}

	add_action( 'plugins_loaded', array( 'WP_Inci_Admin', 'get_instance_admin' ) );
}
