<?php
/**
 * WP_Inci_Admin
 *
 * @category Plugin
 * @package  Wpinci
 * @author   xlthlx <wp-inci@piccioni.london>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPL 3
 * @link     https://wordpress.org/plugins/wp-inci/
 */

/**
 * Check if the class exists.
 */
if ( ! class_exists( 'WP_Inci_Admin', false ) ) {
	/**
	 * Class for Manage Admin (back-end)
	 *
	 * @category Plugin
	 * @package  Wpinci
	 * @author   xlthlx <wp-inci@piccioni.london>
	 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPL 3
	 * @link     https://wordpress.org/plugins/wp-inci/
	 */
	class WP_Inci_Admin extends WP_Inci {


		/**
		 * A static reference to track the single instance of this class.
		 *
		 * @var object
		 */
		private static $_instance;

		/**
		 * Url for the admin folder.
		 *
		 * @var string
		 */
		public $admin_url = '';

		/**
		 * Constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->init();
			$this->admin_url = plugins_url( '', __FILE__ );
		}

		/**
		 * Standard init.
		 *
		 * @return void
		 */
		public function init() {
			/**
			 * Load localizations if available.
			 */
			load_plugin_textdomain( 'wp-inci', false, 'wp-inci/languages' );

			/**
			 * Add hooks and queue.
			 */
			add_action( 'admin_init', array( $this, 'registerPluginSettings' ) );
			add_action( 'admin_init', array( $this, 'pluginInit' ) );
			add_action( 'admin_menu', array( $this, 'pluginSetup' ) );
		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return WP_Inci_Admin|null
		 */
		public static function get_instanceAdmin() {

			if ( null === self::$_instance ) {
				self::$_instance = new WP_Inci_Admin();
			}

			return self::$_instance;
		}

		/**
		 * Register activation and deactivation.
		 *
		 * @return void
		 */
		public function pluginInit() {

			register_activation_hook( __FILE__, 'pluginActivation' );
			register_deactivation_hook( __FILE__, 'pluginDeactivation' );
			register_uninstall_hook( __FILE__, 'pluginUninstall' );
		}

		/**
		 * Plugin activation hook.
		 *
		 * @return void
		 */
		public function pluginActivation() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			// @codingStandardsIgnoreStart
			flush_rewrite_rules();
			// @codingStandardsIgnoreEnd
		}

		/**
		 * Plugin deactivation hook.
		 *
		 * @return void
		 */
		public function pluginDeactivation() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			// @codingStandardsIgnoreStart
			flush_rewrite_rules();
			// @codingStandardsIgnoreEnd
		}

		/**
		 * Plugin uninstall hook.
		 *
		 * @return void
		 */
		public function pluginUninstall() {
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
		 * Attach styles and scripts.
		 *
		 * @return void
		 */
		public function pluginSetup() {

			$this->pluginAdminStyles();
			$this->pluginAdminScripts();

		}

		/**
		 * Add queue for CSS.
		 *
		 * @return void
		 */
		public function pluginAdminStyles() {
			wp_register_style(
				'wp-inci-admin-css',
				$this->admin_url . '/css/wp-inci-admin.min.css',
				array(),
				get_bloginfo( 'version' )
			);
			wp_enqueue_style( 'wp-inci-admin-css' );
		}

		/**
		 * Add queue for JS.
		 *
		 * @return void
		 */
		public function pluginAdminScripts() {
			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			wp_register_script(
				'wp-inci-admin-js',
				$this->admin_url . '/js/wp-inci-admin.min.js',
				array( 'jquery' ),
				get_bloginfo( 'version' ),
				array( 'in_footer' => true )
			);
			wp_enqueue_script( 'wp-inci-admin-js' );
		}

		/**
		 * Attach settings in WordPress Plugins list.
		 *
		 * @return void
		 */
		public function registerPluginSettings() {
			add_action(
				'plugin_action_links',
				array( $this, 'addPluginSettings' ),
				10,
				2
			);
		}

		/**
		 * Add settings link to plugin actions.
		 *
		 * @param array  $plugin_actions The plugin actions.
		 * @param string $plugin_file    The plugin file.
		 *
		 * @return array
		 * @since  1.0
		 */
		public function addPluginSettings( $plugin_actions, $plugin_file ) {
			$new_actions = array();

			if ( plugin_basename( 'wp-inci/wp-inci.php' ) === $plugin_file ) {
				$new_actions['wi_settings'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=wi_settings' ) ) . '">' . __(
					'Settings',
					'wp-inci'
				) . '</a>';
			}

			return array_merge( $new_actions, $plugin_actions );
		}
	}

	add_action( 'plugins_loaded', array( 'WP_Inci_Admin', 'get_instanceAdmin' ) );
}
