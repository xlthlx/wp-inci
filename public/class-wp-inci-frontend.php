<?php
/**
 * Wp_Inci_Frontend
 *
 * Frontend Class
 *
 * @package         wp-inci
 * @subpackage      wp-inci_frontend.php
 * @author          xlthlx <github@piccioni.london>Wp_Inci_Frontend
 *
 */
if ( ! class_exists( 'Wp_Inci_Frontend', false ) ) {
	class Wp_Inci_Frontend extends WP_Inci {

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
		 * Standard init
		 */
		public function init() {

			/**
			 * Load the plugin text domain for frontend translation.
			 */
			load_plugin_textdomain( 'wp-inci', false, dirname( plugin_basename( $this->plugin_file ) ) . '/languages/' );

			/**
			 * Add CSS into queue, add content filter for ingredients table and product shortcode.
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_inci_enqueue_style' ) );
			add_filter( 'the_content', array( $this, 'wp_inci_show_ingredients_table' ), 10, 1 );
			add_action( 'init', array( $this, 'wp_inci_add_product_shortcode' ) );
		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return Wp_Inci_Frontend|null
		 */
		public static function get_instance_frontend() {

			if ( null === self::$instance ) {
				self::$instance = new Wp_Inci_Frontend();
			}

			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function wp_inci_enqueue_style() {

			wp_enqueue_style( 'wp-inci', esc_url( plugins_url( 'css/wp-inci.min.css', __FILE__ ) ) );

		}

		/**
		 * Gets the HTML for a single ingredient.
		 *
		 * @param $ingredient
		 *
		 * @return bool|string
		 */
		public function wp_inci_get_ingredient( $ingredient ) {
			$output = false;
			$post   = get_post( $ingredient );

			if ( null !== $post ) {
				$functions      = '';
				$functions_list = get_the_terms( $post->ID, 'functions' );
				if ( $functions_list && ! is_wp_error( $functions_list ) ) {
					$functions = ' (' . implode( ' / ', wp_list_pluck( $functions_list, 'name' ) ) . ')';
				}

				$output = '
					<tr>
							<td>';
				$output .= ( WP_Inci::get_instance() )->wp_inci_get_safety_html( $post->ID );
				$output .= '	</td>
							<td>';
				$output .= $post->post_title . $functions;
				$output .= '	</td>
					</tr>';
			}

			return $output;
		}

		/**
		 * Gets the HTML for all ingredients.
		 * @param $post_id
		 *
		 * @return string
		 */
		public function wp_inci_get_ingredients_table( $post_id ): string {
			$output      = '';
			$ingredients = get_post_meta( $post_id, 'ingredients', true );
			if ( ! empty( $ingredients ) ) {
				$output .= '
				<table class="table-sm table-borderless table-responsive wp-inci">
						<tbody>';
				foreach ( $ingredients as $ingredient ) {
					$output .= $this->wp_inci_get_ingredient( $ingredient );
				}

				$output .= '</tbody>
					</table>';
			}

			$may_contain = get_post_meta( $post_id, 'may_contain', true );
			if ( ! empty( $may_contain ) ) {
				$output .= '<h5>' . __( 'MAY CONTAIN', 'wp-inci' ) . '</h5>';
				$output .= '
				<table class="table-sm table-borderless table-responsive wp-inci">
						<tbody>';
				foreach ( $may_contain as $may ) {
					$output .= $this->wp_inci_get_ingredient( $may );
				}

				$output .= '</tbody>
					</table>';
			}

			return $output;
		}

		/**
		 * Show the ingredients table into product content.
		 *
		 * @param string $content
		 *
		 * @return string $content
		 */
		public function wp_inci_show_ingredients_table( $content ): string {
			global $post;

			if ( ( $post->post_type === 'product' ) && is_singular() && in_the_loop() && is_main_query() ) {
				$output = $this->wp_inci_get_ingredients_table( $post->ID );

				return $content . $output;
			}

			return $content;
		}

		/**
		 * Add the product shortcode.
		 */
		public function wp_inci_add_product_shortcode() {
			if ( ! shortcode_exists( 'wp_inci_product' ) ) {
				add_shortcode( 'wp_inci_product', array( $this, 'wp_inci_product_shortcode' ) );
			}
		}

		/**
		 * Set up the shortcode to show the product.
		 *
		 * @param array $atts
		 * @param string $content
		 * @param string $shortcode
		 *
		 * @return string
		 */
		public function wp_inci_product_shortcode( $atts, $content, $shortcode ): string {
			// Normalize attribute keys, lowercase.
			$atts = array_change_key_case( (array) $atts, CASE_LOWER );

			// Sets shortcode attributes with defaults.
			$atts = shortcode_atts(
				array(
					'id'    => 0,
					'title' => '',
					'list'  => true,
					'link'  => false,
				),
				$atts,
				$shortcode
			);

			$output = '';

			if ( 0 !== $atts['id'] ) {

				$output .= '<div class="wp-inci">';

				if ( '' !== $atts['title'] ) {
					$output .= '<h4>' . esc_html( $atts['title'] ) . '</h4>';
				}

				if ( $atts['list'] ) {
					$output .= $this->wp_inci_get_ingredients_table( $atts['id'] );
				}

				if ( '' !== $content ) {
					// Secure output by executing the_content filter hook on $content.
					$output .= apply_filters( 'the_content', $content );

					// Run shortcode parser recursively.
					$output .= do_shortcode( $content );
				}

				$output .= '</div>';
			}

			// Remove paragraphs around shortcode before output.
			return shortcode_unautop( $output );
		}

	}

	add_action( 'plugins_loaded', array( 'Wp_Inci_Frontend', 'get_instance_frontend' ) );
}
