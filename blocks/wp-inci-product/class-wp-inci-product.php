<?php
/**
 * Wp_Inci_Product.
 * Product block for WP INCI.
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
if ( ! class_exists( 'Wp_Inci_Product', false ) ) {
	/**
	 * Product Class.
	 *
	 * @category Plugin
	 * @package  Wpinci
	 * @author   xlthlx <wp-inci@piccioni.london>
	 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPL 3
	 * @link     https://wordpress.org/plugins/wp-inci/
	 */
	class Wp_Inci_Product extends WP_Inci {
		/**
		 * A static reference to track the single instance of this class.
		 *
		 * @var object|null
		 */
		private static ?object $_instance = null;

		/**
		 * Constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->init();
		}

		/**
		 * Standard init
		 *
		 * @return void
		 */
		public function init(): void {
			add_action( 'init', array( $this, 'create_block_wp_inci_product_init' ) );
			add_action( 'rest_api_init', array( $this, 'register_endpoint_ingredients_table' ) );
		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return Wp_Inci_Product|null
		 */
		public static function get_instance_product(): Wp_Inci_Product|null {

			if ( null === self::$_instance ) {
				self::$_instance = new Wp_Inci_Product();
			}

			return self::$_instance;
		}

		/**
		 * Registers the Product block using a `blocks-manifest.php` file.
		 *
		 * @return void
		 */
		public function create_block_wp_inci_product_init(): void {

			if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
				wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );

				return;
			}

			if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
				wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
			}

			$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
			foreach ( array_keys( $manifest_data ) as $block_type ) {
				register_block_type( __DIR__ . "/build/{$block_type}" );
			}
		}

		/**
		 * Register the ingredients table endpoint.
		 *
		 * @return void
		 */
		public function register_endpoint_ingredients_table(): void {

			register_rest_route(
				'wp-inci/v1',
				'/get-table',
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_ingredients_table' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => $this->get_arguments(),
				)
			);
		}

		/**
		 * Gets the ingredients table.
		 *
		 * @param array $request The request array.
		 *
		 * @return WP_Error|WP_HTTP_Response|WP_REST_Response Response from the function.
		 */
		public function get_ingredients_table( $request ): WP_Error|WP_HTTP_Response|WP_REST_Response {
			if ( isset( $request['product_id'] ) && ( 0 !== $request['product_id'] ) ) {

				$safety     = $request['safety'] ?? 'true';
				$disclaimer = $request['disclaimer'] ?? 'true';

				require_once WPINCI_BASE_PATH . 'public/class-wp-inci-frontend.php';

				$response = ( Wp_Inci_Frontend::get_instanceFrontend() )->getIngredientsTable(
					(int) $request['product_id'],
					$safety,
					$disclaimer
				);

				return rest_ensure_response( $response );
			}

			return new WP_Error( 'rest_invalid', esc_html__( 'The product_id parameter is required.', 'wp-inci' ), array( 'status' => 400 ) );
		}

		/**
		 * Check permissions.
		 *
		 * @return bool
		 */
		public function permissions_check(): bool {
			return current_user_can( 'edit_posts' );
		}

		/**
		 * Set up the REST API arguments.
		 *
		 * @return array
		 */
		public function get_arguments(): array {
			$args               = array();
			$args['product_id'] = array(
				'description'       => esc_html__( 'The Product ID parameter is used to get the Ingredients', 'wp-inci' ),
				'type'              => 'string',
				'required'          => true,
				'validate_callback' => array( $this, 'arg_validate_string_callback' ),
				'sanitize_callback' => array( $this, 'arg_sanitize_string_callback' ),
			);
			$args['safety']     = array(
				'description'       => esc_html__( 'The safety parameter is used to show/hide the safety from the Ingredients list', 'wp-inci' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this, 'arg_validate_string_callback' ),
				'sanitize_callback' => array( $this, 'arg_sanitize_string_callback' ),
			);
			$args['disclaimer'] = array(
				'description'       => esc_html__( 'The disclaimer parameter is used to show/hide the Disclaimer from the Ingredients list', 'wp-inci' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this, 'arg_validate_string_callback' ),
				'sanitize_callback' => array( $this, 'arg_sanitize_string_callback' ),
			);

			return $args;
		}

		/**
		 * Validate a request string argument.
		 *
		 * @param mixed $value Value of the 'filter' argument.
		 *
		 * @return string|WP_Error
		 */
		public function arg_validate_string_callback( $value ): WP_Error|string {

			if ( ! is_string( $value ) ) {
				return new WP_Error( 'rest_invalid_param', esc_html__( 'The filter argument must be a string.', 'wp-inci' ), array( 'status' => 400 ) );
			}

			return $value;
		}

		/**
		 * Sanitize a request string argument.
		 *
		 * @param mixed $value Value of the 'filter' argument.
		 *
		 * @return string
		 */
		public function arg_sanitize_string_callback( $value ): string {
			return sanitize_text_field( $value );
		}

	}

	add_action( 'plugins_loaded', array( 'Wp_Inci_Product', 'get_instance_product' ) );
}
