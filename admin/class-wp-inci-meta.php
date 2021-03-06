<?php

/**
 * WP_Inci_Admin
 * Class for Manage Admin (back-end)
 *
 * @package         wp-inci
 * @author          xlthlx <wp-inci@piccioni.london>
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
		 * Standard init.
		 */
		public function init() {
			/**
			 * Include and setup custom meta boxes and fields.
			 */
			add_action( 'cmb2_admin_init', array( $this, 'register_source_url' ) );
			add_action( 'cmb2_admin_init', array( $this, 'register_ingredients_repeater' ) );
			add_action( 'cmb2_admin_init', array( $this, 'register_safety_select' ) );
			add_action( 'cmb2_admin_init', array( $this, 'register_page_settings' ) );
			add_action( 'cmb2_admin_init', array( $this, 'register_custom_brand_metabox' ) );
			add_action( 'admin_init', array( $this, 'remove_menu_page' ) );
			add_filter( 'parent_file', array( $this, 'select_other_menu' ) );
			add_action( 'admin_head', array( $this, 'remove_gutenberg_tips' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'disable_editor_fullscreen' ) );
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
		public function register_source_url() {

			$cmb_term = new_cmb2_box( array(
				'id'               => 'source_url_add',
				'title'            => __( 'Url', 'wp-inci' ),
				'object_types'     => array( 'term' ),
				'taxonomies'       => array( 'source' ),
				'new_term_section' => true,
			) );

			$cmb_term->add_field( array(
				'name'         => __( 'Url', 'wp-inci' ),
				'id'           => 'source_url',
				'type'         => 'text_url',
				'protocols'    => array( 'http', 'https' ),
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			) );

		}

		/**
		 * Create new custom meta 'ingredients' and 'may_contain' for Product post type.
		 */
		public function register_ingredients_repeater() {

			$ingredients = new_cmb2_box( array(
				'id'           => 'ingredients_search_ajax',
				'title'        => __( 'Ingredients', 'wp-inci' ),
				'object_types' => array( 'product' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => false,
			) );


			$ingredients->add_field( array(
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
			) );

			$may_contain = new_cmb2_box( array(
				'id'           => 'may_contain_search_ajax',
				'title'        => __( 'May Contain', 'wp-inci' ),
				'object_types' => array( 'product' ),
				'context'      => 'normal',
				'priority'     => 'default',
				'show_names'   => false,
			) );

			$may_contain->add_field( array(
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
			) );

		}

		/**
		 * Create the Safety select.
		 */
		public function register_safety_select() {

			$safety = new_cmb2_box( array(
				'id'           => 'inci',
				'title'        => __( 'INCI', 'wp-inci' ),
				'object_types' => array( 'ingredient' ), // Post type
				'context'      => 'side',
				'priority'     => 'default',
				'show_names'   => true,
			) );

			$safety->add_field( array(
				'name'             => '',
				'id'               => 'safety',
				'type'             => 'select',
				'show_option_none' => true,
				'before_field'     => array( $this, 'before_safety' ),
				'options'          => array(
					'1' => __( 'Double green', 'wp-inci' ),
					'2' => __( 'Green', 'wp-inci' ),
					'3' => __( 'Yellow', 'wp-inci' ),
					'4' => __( 'Red', 'wp-inci' ),
					'5' => __( 'Double red', 'wp-inci' ),
				),
				'show_in_rest'     => WP_REST_Server::ALLMETHODS,
			) );

			$safety->add_field( array(
				'id'   => 'cosing_id',
				'type' => 'hidden',
			) );

			$safety->add_field( array(
				'id'   => 'last_update',
				'type' => 'hidden',
			) );

			/**
			 * Create the CAS Number field.
			 */
			$safety->add_field( array(
				'name'         => __( 'CAS #', 'wp-inci' ),
				'id'           => 'cas_number',
				'type'         => 'text_small',
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			) );

			/**
			 * Create the EC Number field.
			 */
			$safety->add_field( array(
				'name'         => __( 'EC #', 'wp-inci' ),
				'id'           => 'ec_number',
				'type'         => 'text_small',
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			) );

			/**
			 * Create the Restriction field.
			 */
			$safety->add_field( array(
				'name'         => __( 'Restrictions', 'wp-inci' ),
				'id'           => 'restriction',
				'type'         => 'text_small',
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			) );
		}

		/**
		 * Returns the safety custom meta with HTML before the Safety select.
		 *
		 * @param $field_args
		 * @param $field
		 */
		public function before_safety( $field_args, $field ) {
			echo ( WP_Inci::get_instance() )->get_safety_html( $field->object_id );
		}

		/**
		 * Check filesystem credentials.
		 *
		 * @param $url
		 * @param $method
		 * @param $context
		 * @param $fields
		 *
		 * @return bool
		 */
		public function connect( $url, $method, $context, $fields = null ) {

			if ( false === ( $credentials = request_filesystem_credentials( $url, $method, false, $context, $fields ) ) ) {
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
		 */
		public function default_style() {
			global $wp_filesystem;

			$url = wp_nonce_url( "options-general.php?page=settings" );

			if ( $this->connect( $url, "", WP_PLUGIN_DIR . "/wp-inci/public/css" ) ) {
				$dir  = $wp_filesystem->find_folder( WP_PLUGIN_DIR . "/wp-inci/public/css" );
				$file = trailingslashit( $dir ) . "wp-inci.css";

				if ( $wp_filesystem->exists( $file ) ) {
					$text = $wp_filesystem->get_contents( $file );
					if ( ! $text ) {
						return "";
					}

					return $text;
				}

				return "File doesn't exist";
			}

			return "Cannot initialize filesystem";
		}

		/**
		 * Returns the button to copy the WP INCI style.
		 */
		public function copy_button() {
			echo "<script>var wi_style=`" . $this->default_style() . "`;";
			echo "var wi_msg='" . __( 'Style copied to clipboard.', 'wp-inci' ) . "';</script>";
			echo '<button id="copy_style" type="button" class="button copy">' . __( 'Copy style', 'wp-inci' ) . '</button><span id="msg"></span>';
		}

		/**
		 * Create WP INCI Settings page.
		 */
		public function register_page_settings() {

			$args = array(
				'id'           => 'wi_settings',
				'title'        => __( 'WP INCI', 'wp-inci' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'wi_settings',
				'tab_group'    => 'wi_settings',
				'tab_title'    => __( 'Settings', 'wp-inci' ),
				'parent_slug'  => 'options-general.php',
				'message_cb'   => array( $this, 'options_page_message_callback' ),
			);

			$main_options = new_cmb2_box( $args );

			$desc = __( 'You can disable the WP INCI style and add your own to your theme.<br/>'
			            . 'Just copy the standard WP INCI style above into your style.css and customize it.', 'wp-inci' );

			/**
			 * Create style settings.
			 */
			$main_options->add_field( array(
				'name'         => __( 'WP INCI Default Style', 'wp-inci' ),
				'desc'         => '',
				'id'           => 'textarea_style',
				'type'         => 'textarea_code',
				'default_cb'   => array( $this, 'default_style' ),
				'save_field'   => false,
				'attributes'   => array(
					'readonly'        => 'readonly',
					'disabled'        => 'disabled',
					'data-codeeditor' => json_encode( array(
						'codemirror' => array(
							'mode'     => 'css',
							'readOnly' => 'nocursor',
						),
					) ),
				),
				'after_field'  => array( $this, 'copy_button' ),
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			) );

			$main_options->add_field( array(
				'name'           => __( 'Disable WP INCI style', 'wp-inci' ),
				'id'             => 'wi_disable_style',
				'desc'           => $desc,
				'type'           => 'switch',
				'default'        => 'off',
				'active_value'   => 'on',
				'inactive_value' => 'off',
				'show_in_rest'   => WP_REST_Server::ALLMETHODS,
			) );

			/**
			 * Create disclaimer settings.
			 */
			$args = array(
				'id'           => 'wi_disclaimer',
				'title'        => __( 'WP INCI', 'wp-inci' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'wi_disclaimer',
				'parent_slug'  => 'options-general.php',
				'tab_group'    => 'wi_settings',
				'tab_title'    => __( 'Disclaimer', 'wp-inci' ),
				'message_cb'   => array( $this, 'options_page_message_callback' ),
			);

			$secondary_options = new_cmb2_box( $args );

			$secondary_options->add_field( array(
				'name'         => __( 'Disclaimer', 'wp-inci' ),
				'desc'         => __( 'Add a disclaimer after WP INCI table of ingredients.', 'wp-inci' ),
				'id'           => 'textarea_disclaimer',
				'type'         => 'textarea_code',
				'default_cb'   => array( $this, 'wi_default_disclaimer' ),
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			) );

		}

		/**
		 * Remove the disclaimer menu.
		 */
		public function remove_menu_page() {
			remove_submenu_page( 'options-general.php', 'wi_disclaimer' );
		}

		/**
		 * Highlight the setting menu.
		 *
		 * @param string $parent_file The parent file.
		 *
		 * @return string
		 */
		public function select_other_menu( $parent_file ) {
			global $plugin_page;

			if ( 'wi_disclaimer' === $plugin_page ) {
				$plugin_page = 'wi_settings';
			}

			return $parent_file;
		}


		/**
		 * Modify the updated message.
		 *
		 * @param $cmb
		 * @param $args
		 */
		public function options_page_message_callback( $cmb, $args ) {
			if ( ! empty( $args['should_notify'] ) ) {

				if ( ( 'updated' == $args['type'] ) || ( 'notice-warning' == $args['type'] ) ) {
					$args['message'] = __( 'Settings saved.' );
				}

				add_settings_error( $args['setting'], $args['code'], $args['message'], 'success' );

			}
		}

		/**
		 * Custom metabox for brand taxonomy.
		 */
		public function register_custom_brand_metabox() {

			$brand = new_cmb2_box( array(
				'id'           => 'brand_box',
				'title'        => __( 'Brand', 'wp-inci' ),
				'object_types' => array( 'product' ),
				'context'      => 'side',
				'priority'     => 'default',
				'show_names'   => false,
			) );

			$brand->add_field( array(
				'name'         => __( 'Brand', 'wp-inci' ),
				'desc'         => '',
				'id'           => 'taxonomy_brand',
				'taxonomy'     => 'brand',
				'type'         => 'taxonomy_select',
				'after_field'  => '<br/><a style="" target="_blank" href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=brand' ) ) . '" class="button brand">' . __( 'Add new brand', 'wp-inci' ) . '</a>',
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			) );
		}

		/**
		 * Hides the very annoying Welcome Tips popup for Gutenberg.
		 */
		public function remove_gutenberg_tips() {
			global $pagenow;

			if ( 'post.php' == $pagenow && isset( $_GET['post'] ) ) {

				$post_type = get_post_type( $_GET['post'] );

				if ( ( 'ingredient' == $post_type ) || ( 'product' == $post_type ) ) {
					?>
                    <style>
                        .components-modal__frame.components-guide {
                            display: none !important;
                        }

                        .components-modal__screen-overlay {
                            display: none !important;
                        }
                    </style>
					<?php
				}
			}
		}

		/**
		 * Disable the very annoying fullscreen mode for Gutenberg.
		 */
		public function disable_editor_fullscreen() {
			global $pagenow;

			if ( 'post.php' == $pagenow && isset( $_GET['post'] ) ) {

				$post_type = get_post_type( $_GET['post'] );
				if ( ( 'ingredient' == $post_type ) || ( 'product' == $post_type ) ) {

					$script = "window.onload = function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } }";

					wp_add_inline_script( 'wp-blocks', $script );
				}
			}
		}

	}

	add_action( 'plugins_loaded', array( 'WP_Inci_Meta', 'get_instance_meta' ) );
}
