<?php
/**
 * Wp_Inci_Frontend
 *
 * Frontend Class
 *
 * @package         wp-inci
 * @author          xlthlx <wp-inci@piccioni.london>
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
			load_plugin_textdomain( 'wp-inci', false,
				dirname( plugin_basename( $this->plugin_file ) ) . '/languages/' );

			/**
			 * Add CSS into queue, add content filter for ingredients table and product shortcode.
			 */
			add_action( 'wp_enqueue_scripts',
				array( $this, 'wi_enqueue_style' ) );
			add_filter( 'the_content', array( $this, 'wi_content_ingredients' ),
				10, 1 );
			add_action( 'init', array( $this, 'wi_add_product_shortcode' ) );
		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return Wp_Inci_Frontend|null
		 */
		public static function get_instance_frontend(): ?Wp_Inci_Frontend {

			if ( null === self::$instance ) {
				self::$instance = new Wp_Inci_Frontend();
			}

			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function wi_enqueue_style(): void {

			$disable_style = cmb2_get_option( 'wi_settings',
				'wi_disable_style' );

			wp_enqueue_style( 'wp-inci',
				esc_url( plugins_url( 'css/wp-inci.min.css', __FILE__ ) ) );

			if ( $disable_style == 'on' ) {
				wp_dequeue_style( 'wp-inci' );
			}


		}

		/**
		 * Gets the HTML for a single ingredient.
		 *
		 * @param int $ingredient
		 * @param string $safety
		 *
		 * @return bool|string
		 */
		public function get_ingredient(
			int $ingredient,
			string $safety = 'true'
		) {
			$output = false;
			$post   = get_post( $ingredient );

			if ( null !== $post ) {
				$functions      = '';
				$functions_list = get_the_terms( $post->ID, 'functions' );
				if ( $functions_list && ! is_wp_error( $functions_list ) ) {
					$functions = ' (' . implode( ' / ',
							wp_list_pluck( $functions_list, 'name' ) ) . ')';
				}

				$output = '<tr>';
				if ( 'true' === $safety ) {
					$output .= '<td>';
					$output .= ( WP_Inci::get_instance() )->get_safety_html( $post->ID );
					$output .= '</td>';
					$output .= '<td>';
				} else {
					$output .= '<td class="nofirst">';
				}
				$output .= $post->post_title . $functions;
				$output .= '	</td>
					</tr>';
			}

			return $output;
		}

		/**
		 * Gets the HTML for all ingredients.
		 *
		 * @param int $post_id
		 * @param string $safety
		 *
		 * @return string
		 */
		public function get_ingredients_table(
			int $post_id,
			string $safety = 'true'
		): string {
			$output      = '';
			$ingredients = get_post_meta( $post_id, 'ingredients', true );
			if ( ! empty( $ingredients ) ) {
				$output .= '
				<table class="wp-inci">
						<tbody>';
				foreach ( $ingredients as $ingredient ) {
					$output .= $this->get_ingredient( $ingredient, $safety );
				}

				$output .= '</tbody>
					</table>';
			}

			$may_contain = get_post_meta( $post_id, 'may_contain', true );
			if ( ! empty( $may_contain ) ) {
				$output .= '<h4>' . __( 'MAY CONTAIN', 'wp-inci' ) . '</h4>';
				$output .= '
				<table class="wp-inci">
						<tbody>';
				foreach ( $may_contain as $may ) {
					$output .= $this->get_ingredient( $may, $safety );
				}

				$output .= '</tbody>
					</table>';
			}

			$output .= '<div class="disclaimer">' . cmb2_get_option( 'wi_disclaimer',
					'textarea_disclaimer',
					$this->wi_default_disclaimer() ) . '</div>';

			return $output;
		}

		/**
		 * Show the ingredients table into product content.
		 *
		 * @param string $content
		 *
		 * @return string $content
		 */
		public function wi_content_ingredients( string $content ): string {
			global $post;
			$output = '';

			if ( is_singular() && in_the_loop() && is_main_query() ) {
				if ( $post->post_type == 'product' ) {
					$output = '<div class="wp-inci">' . $this->get_ingredients_table( $post->ID ) . '</div>';
				}

				return $content . $output;
			}

			return $content;
		}

		/**
		 * Add the product shortcode.
		 */
		public function wi_add_product_shortcode(): void {
			if ( ! shortcode_exists( 'wp_inci_product' ) ) {
				add_shortcode( 'wp_inci_product',
					array( $this, 'wi_product_shortcode' ) );
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
		public function wi_product_shortcode(
			array $atts,
			string $content,
			string $shortcode
		): string {

			// Example: [wp_inci_product id="33591" title="My custom title" link="true" list="false" safety="false"]
			// Basic use: [wp_inci_product id="33591"]

			// Normalize attribute keys, lowercase.
			$atts = array_change_key_case( (array) $atts, CASE_LOWER );

			// Sets shortcode attributes with defaults.
			$atts = shortcode_atts(
				array(
					'id'     => 0,
					'title'  => '',
					'link'   => 'false',
					'list'   => 'true',
					'safety' => 'true',
				),
				$atts,
				$shortcode
			);

			$output = '';

			if ( 0 !== $atts['id'] ) {

				$output .= '<div class="wp-inci">';

				$start = '<h3>';
				$end   = '</h3>';
				$title = esc_html( get_the_title( $atts['id'] ) );

				if ( '' !== $atts['title'] ) {
					$title = esc_html( $atts['title'] );
				}

				if ( 'true' === $atts['link'] ) {
					$start = '<h3><a title="' . $title . '" href="' . get_permalink( $atts['id'] ) . '">';
					$end   = '</a></h3>';
				}

				$output .= $start . $title . $end;

				if ( 'true' === $atts['list'] ) {
					$output .= $this->get_ingredients_table( $atts['id'],
						$atts['safety'] );
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

	add_action( 'plugins_loaded',
		array( 'Wp_Inci_Frontend', 'get_instance_frontend' ) );
}
