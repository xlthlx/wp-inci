<?php
/**
 * WP_Inci_Meta
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
if ( ! class_exists( 'WP_Inci_Meta', false ) ) {
	/**
	 * Class for Manage Meta Box (back-end)
	 *
	 * @category Plugin
	 * @package  Wpinci
	 * @author   xlthlx <wp-inci@piccioni.london>
	 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPL 3
	 * @link     https://wordpress.org/plugins/wp-inci/
	 */
	class WP_Inci_Meta extends WP_Inci {


		/**
		 * A static reference to track the single instance of this class.
		 *
		 * @var object
		 */
		private static $_instance;

		/**
		 * Constructor.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			parent::__construct();
			$this->init();
			$this->url = plugins_url( '', __FILE__ );
		}

		/**
		 * Standard init.
		 *
		 * @return void
		 */
		public function init() {
			/**
			 * Include and setup custom meta boxes and fields.
			 */
			add_action( 'cmb2_admin_init', array( $this, 'registerSourceUrl' ) );
			add_action( 'cmb2_admin_init', array( $this, 'registerIngredientsRepeater' ) );
			add_action( 'cmb2_admin_init', array( $this, 'registerSafetySelect' ) );
			add_action( 'cmb2_admin_init', array( $this, 'registerPageSettings' ) );
			add_action( 'cmb2_admin_init', array( $this, 'registerBrandMetabox' ) );
			add_action( 'admin_init', array( $this, 'removeMenuPage' ) );
			add_filter( 'parent_file', array( $this, 'selectOtherMenu' ) );
		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return WP_Inci_Meta|null
		 */
		public static function get_instanceMeta() {

			if ( null === self::$_instance ) {
				self::$_instance = new WP_Inci_Meta();
			}

			return self::$_instance;
		}

		/**
		 * Create new custom meta 'source_url' for Source taxonomy.
		 *
		 * @return void
		 */
		public function registerSourceUrl() {

			$cmb_term = new_cmb2_box(
				array(
					'id'               => 'source_url_add',
					'title'            => __( 'Url', 'wp-inci' ),
					'object_types'     => array( 'term' ),
					'taxonomies'       => array( 'source' ),
					'new_term_section' => true,
				)
			);

			$cmb_term->add_field(
				array(
					'name'         => __( 'Url', 'wp-inci' ),
					'id'           => 'source_url',
					'type'         => 'text_url',
					'protocols'    => array( 'http', 'https' ),
					'show_in_rest' => WP_REST_Server::ALLMETHODS,
				)
			);

		}

		/**
		 * Create new custom meta 'ingredients' and 'may_contain' for Product post type.
		 *
		 * @return void
		 */
		public function registerIngredientsRepeater() {

			$ingredients = new_cmb2_box(
				array(
					'id'           => 'ingredients_search_ajax',
					'title'        => __( 'Ingredients', 'wp-inci' ),
					'object_types' => array( 'product' ),
					'context'      => 'normal',
					'priority'     => 'high',
					'show_names'   => false,
				)
			);

			$ingredients->add_field(
				array(
					'name'         => __( 'Ingredient', 'wp-inci' ),
					'id'           => 'ingredients',
					'type'         => 'search_ajax',
					'desc'         => __( 'Start typing ingredient name, then select one from the list. No results found?', 'wp-inci' ),
					'sortable'     => true,
					'limit'        => 10,
					'query_args'   => array(
						'post_type'      => 'ingredient',
						'posts_per_page' => - 1,
						'post_status'    => 'publish',
						'order'          => 'ASC',
						'orderby'        => 'title',
					),
					'show_in_rest' => WP_REST_Server::ALLMETHODS,
				)
			);

			$may_contain = new_cmb2_box(
				array(
					'id'           => 'may_contain_search_ajax',
					'title'        => __( 'May Contain', 'wp-inci' ),
					'object_types' => array( 'product' ),
					'context'      => 'normal',
					'priority'     => 'default',
					'show_names'   => false,
				)
			);

			$may_contain->add_field(
				array(
					'name'         => __( 'May Contain', 'wp-inci' ),
					'id'           => 'may_contain',
					'type'         => 'search_ajax',
					'desc'         => __( 'Start typing ingredient name, then select one from the list. No results found?', 'wp-inci' ),
					'sortable'     => true,
					'limit'        => 10,
					'query_args'   => array(
						'post_type'      => 'ingredient',
						'posts_per_page' => - 1,
						'post_status'    => 'publish',
						'order'          => 'ASC',
						'orderby'        => 'title',
					),
					'show_in_rest' => WP_REST_Server::ALLMETHODS,
				)
			);

		}

		/**
		 * Create the Safety select.
		 *
		 * @return void
		 */
		public function registerSafetySelect() {

			$safety = new_cmb2_box(
				array(
					'id'           => 'inci',
					'title'        => __( 'INCI', 'wp-inci' ),
					'object_types' => array( 'ingredient' ),
					'context'      => 'side',
					'priority'     => 'default',
					'show_names'   => true,
				)
			);

			$safety->add_field(
				array(
					'name'             => '',
					'id'               => 'safety',
					'type'             => 'select',
					'show_option_none' => true,
					'before_field'     => array( $this, 'beforeSafety' ),
					'options'          => array(
						'1' => __( 'Double green', 'wp-inci' ),
						'2' => __( 'Green', 'wp-inci' ),
						'3' => __( 'Yellow', 'wp-inci' ),
						'4' => __( 'Red', 'wp-inci' ),
						'5' => __( 'Double red', 'wp-inci' ),
					),
					'show_in_rest'     => WP_REST_Server::ALLMETHODS,
				)
			);

			$safety->add_field(
				array(
					'id'   => 'cosing_id',
					'type' => 'hidden',
				)
			);

			$safety->add_field(
				array(
					'id'   => 'last_update',
					'type' => 'hidden',
				)
			);

			/**
			 * Create the CAS Number field.
			 */
			$safety->add_field(
				array(
					'name'         => __( 'CAS #', 'wp-inci' ),
					'id'           => 'cas_number',
					'type'         => 'text_small',
					'show_in_rest' => WP_REST_Server::ALLMETHODS,
				)
			);

			/**
			 * Create the EC Number field.
			 */
			$safety->add_field(
				array(
					'name'         => __( 'EC #', 'wp-inci' ),
					'id'           => 'ec_number',
					'type'         => 'text_small',
					'show_in_rest' => WP_REST_Server::ALLMETHODS,
				)
			);

			/**
			 * Create the Restriction field.
			 */
			$safety->add_field(
				array(
					'name'         => __( 'Restrictions', 'wp-inci' ),
					'id'           => 'restriction',
					'type'         => 'text_small',
					'show_in_rest' => WP_REST_Server::ALLMETHODS,
				)
			);
		}

		/**
		 * Returns the safety custom meta with HTML before the Safety select.
		 *
		 * @param array  $field_args The fields args.
		 * @param object $field      The field object.
		 *
		 * @return void
		 */
		public function beforeSafety( $field_args, $field ) {
			// @codingStandardsIgnoreStart
			echo ( WP_Inci::get_instance() )->get_safety_html( esc_attr( $field->object_id ) );
			// @codingStandardsIgnoreEnd
		}

		/**
		 * Check filesystem credentials.
		 *
		 * @param string $url     The url.
		 * @param string $method  The method.
		 * @param string $context The context.
		 * @param array  $fields  The fields.
		 *
		 * @return bool
		 */
		public function connect( $url, $method, $context, $fields = null ) {
			$credentials = request_filesystem_credentials( $url, $method, false, $context, $fields );

			if ( false === $credentials ) {
				return false;
			}

			if ( ! WP_Filesystem( $credentials ) ) {
				request_filesystem_credentials( $url, $method, true, $context );

				return false;
			}

			return true;
		}

		/**
		 * Sets CSS for default style reading the content of the CSS.
		 *
		 * @return string
		 */
		public function defaultStyle() {
			global $wp_filesystem;

			$url = wp_nonce_url( 'options-general.php?page=settings' );

			if ( $this->connect( $url, '', WP_PLUGIN_DIR . '/wp-inci/public/css' ) ) {
				$dir  = $wp_filesystem->find_folder( WP_PLUGIN_DIR . '/wp-inci/public/css' );
				$file = trailingslashit( $dir ) . 'wp-inci.css';

				if ( $wp_filesystem->exists( $file ) ) {
					$text = $wp_filesystem->get_contents( $file );
					if ( ! $text ) {
						return '';
					}

					return $text;
				}

				return "File doesn't exist";
			}

			return 'Cannot initialize filesystem';
		}

		/**
		 * Returns the button to copy the WP INCI style.
		 *
		 * @return void
		 */
		public function copyButton() {
			// @codingStandardsIgnoreStart
			echo '<script>const wi_style=`' . $this->defaultStyle() . '`;';
			// @codingStandardsIgnoreEnd
			echo "const wi_msg='" . esc_attr( __( 'Style copied to clipboard.', 'wp-inci' ) ) . "';</script>";
			echo '<button id="copy_style" type="button" class="button copy">' . esc_attr( __( 'Copy style', 'wp-inci' ) ) . '</button><span id="msg"></span>';
		}

		/**
		 * Create WP INCI Settings page.
		 *
		 * @return void
		 */
		public function registerPageSettings() {

			$args = array(
				'id'           => 'wi_settings',
				'title'        => __( 'WP INCI', 'wp-inci' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'wi_settings',
				'tab_group'    => 'wi_settings',
				'tab_title'    => __( 'Settings', 'wp-inci' ),
				'parent_slug'  => 'options-general.php',
				'message_cb'   => array( $this, 'optionsPageMessageCallback' ),
			);

			$main_options = new_cmb2_box( $args );

			$desc = __(
				'You can disable the WP INCI style and add your own to your theme.<br/>Just copy the standard WP INCI style above into your style.css and customize it.',
				'wp-inci'
			);

			/**
			 * Create style settings.
			 *
			 * @return void
			 */
			$main_options->add_field(
				array(
					'name'         => __( 'WP INCI Default Style', 'wp-inci' ),
					'desc'         => '',
					'id'           => 'textarea_style',
					'type'         => 'textarea_code',
					'default_cb'   => array( $this, 'defaultStyle' ),
					'save_field'   => false,
					'attributes'   => array(
						'readonly'        => 'readonly',
						'disabled'        => 'disabled',
						'data-codeeditor' => wp_json_encode(
							array(
								'codemirror' => array(
									'mode'     => 'css',
									'readOnly' => 'nocursor',
								),
							),
						),
					),
					'after_field'  => array( $this, 'copyButton' ),
					'show_in_rest' => WP_REST_Server::ALLMETHODS,
				)
			);

			$main_options->add_field(
				array(
					'name'           => __( 'Disable WP INCI style', 'wp-inci' ),
					'id'             => 'wi_disable_style',
					'desc'           => $desc,
					'type'           => 'switch',
					'default'        => 'off',
					'active_value'   => 'on',
					'inactive_value' => 'off',
					'show_in_rest'   => WP_REST_Server::ALLMETHODS,
				)
			);

			/**
			 * Create disclaimer settings.
			 *
			 * @return void
			 */
			$args = array(
				'id'           => 'wi_disclaimer',
				'title'        => __( 'WP INCI', 'wp-inci' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'wi_disclaimer',
				'parent_slug'  => 'options-general.php',
				'tab_group'    => 'wi_settings',
				'tab_title'    => __( 'Disclaimer', 'wp-inci' ),
				'message_cb'   => array( $this, 'optionsPageMessageCallback' ),
			);

			$secondary_options = new_cmb2_box( $args );

			$secondary_options->add_field(
				array(
					'name'         => __( 'Disclaimer', 'wp-inci' ),
					'desc'         => __( 'Add a disclaimer after WP INCI table of ingredients.', 'wp-inci' ),
					'id'           => 'textarea_disclaimer',
					'type'         => 'textarea_code',
					'default_cb'   => array( $this, 'get_default_disclaimer' ),
					'show_in_rest' => WP_REST_Server::ALLMETHODS,
				)
			);

		}

		/**
		 * Remove the disclaimer menu.
		 *
		 * @return void
		 */
		public function removeMenuPage() {
			remove_submenu_page( 'options-general.php', 'wi_disclaimer' );
		}

		/**
		 * Highlight the setting menu.
		 *
		 * @param string $parent_file The parent file.
		 *
		 * @return string
		 */
		public function selectOtherMenu( $parent_file ) {
			global $plugin_page;

			if ( 'wi_disclaimer' === $plugin_page ) {
				// @codingStandardsIgnoreStart
				$plugin_page = 'wi_settings';
				// @codingStandardsIgnoreEnd
			}

			return $parent_file;
		}


		/**
		 * Modify the updated message.
		 *
		 * @param string $cmb  The cmb.
		 * @param array  $args The args.
		 *
		 * @return void
		 */
		public function optionsPageMessageCallback( $cmb, $args ) {
			if ( ! empty( $args['should_notify'] ) ) {

				if ( ( 'updated' === $args['type'] ) || ( 'notice-warning' === $args['type'] ) ) {
					$args['message'] = __( 'Settings saved.', 'wp-inci' );
				}

				add_settings_error( $args['setting'], $args['code'], $args['message'], 'success' );

			}
		}

		/**
		 * Custom metabox for brand taxonomy.
		 *
		 * @return void
		 */
		public function registerBrandMetabox() {

			$brand = new_cmb2_box(
				array(
					'id'           => 'brand_box',
					'title'        => __( 'Brand', 'wp-inci' ),
					'object_types' => array( 'product' ),
					'context'      => 'side',
					'priority'     => 'default',
					'show_names'   => false,
				)
			);

			$brand->add_field(
				array(
					'name'         => __( 'Brand', 'wp-inci' ),
					'desc'         => '',
					'id'           => 'taxonomy_brand',
					'taxonomy'     => 'brand',
					'type'         => 'taxonomy_select',
					'after_field'  => '<br/><a style="" target="_blank" href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=brand' ) ) . '" class="button brand">' . __( 'Add new brand', 'wp-inci' ) . '</a>',
					'show_in_rest' => WP_REST_Server::ALLMETHODS,
				)
			);
		}

	}

	add_action( 'plugins_loaded', array( 'WP_Inci_Meta', 'get_instanceMeta' ) );
}
