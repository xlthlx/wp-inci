<?php
/**
 * Wp_Inci_Frontend
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
if ( ! class_exists( 'Wp_Inci_Frontend', false ) ) {
	/**
	 * Frontend Class.
	 *
	 * @category Plugin
	 * @package  Wpinci
	 * @author   xlthlx <wp-inci@piccioni.london>
	 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPL 3
	 * @link     https://wordpress.org/plugins/wp-inci/
	 */
	class Wp_Inci_Frontend extends WP_Inci {
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

			/**
			 * Load the plugin text domain for frontend translation.
			 */
			load_plugin_textdomain(
				'wp-inci',
				false,
				WPINCI_BASE_PATH . 'languages/'
			);

			/**
			 * Add CSS into queue, add content filter for ingredients table and product shortcode.
			 */
			add_action(
				'wp_enqueue_scripts',
				array( $this, 'wiEnqueueStyle' )
			);
			add_filter(
				'the_content',
				array( $this, 'wiContentIngredients' )
			);
			add_action( 'init', array( $this, 'wiAddProductShortcode' ) );
		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return Wp_Inci_Frontend|null
		 */
		public static function get_instanceFrontend(): Wp_Inci_Frontend|null {

			if ( null === self::$_instance ) {
				self::$_instance = new Wp_Inci_Frontend();
			}

			return self::$_instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @return void
		 */
		public function wiEnqueueStyle(): void {

			$disable_style = cmb2_get_option( 'wi_settings', 'wi_disable_style' );
			$disable_style = is_array( $disable_style ) ? $disable_style[0] : $disable_style;

			wp_enqueue_style( 'wp-inci', esc_url( plugins_url( 'css/wp-inci.min.css', __FILE__ ) ), array(), get_bloginfo( 'version' ) );

			if ( 'on' === $disable_style ) {
				wp_dequeue_style( 'wp-inci' );
			}

		}

		/**
		 * Gets the HTML for a single ingredient.
		 *
		 * @param int    $ingredient Ingredient ID.
		 * @param string $safety Show safety.
		 *
		 * @return false|string
		 */
		public function getIngredient( int $ingredient, string $safety = 'true' ): false|string {
			$output = false;
			$post   = get_post( $ingredient );

			if ( null !== $post ) {
				$functions      = '';
				$functions_list = get_the_terms( $post->ID, 'functions' );
				if ( $functions_list && ! is_wp_error( $functions_list ) ) {
					$functions = ' (' . implode(
						' / ',
						wp_list_pluck( $functions_list, 'name' )
					) . ')';
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
		 * @param int    $post_id Post ID.
		 * @param string $safety (Optional) Show safety.
		 * @param string $disclaimer (Optional) Show disclaimer.
		 *
		 * @return string
		 * @noinspection PhpArrayToStringConversionInspection
		 */
		public function getIngredientsTable( int $post_id, string $safety = 'true', string $disclaimer = 'true' ): string {
			$output = '';

			if ( 0 !== $post_id ) {
				$ingredients = get_post_meta( $post_id, 'ingredients', true );
				if ( ! empty( $ingredients ) ) {
					$output .= '
				<table class="wp-inci">
						<tbody>';
					foreach ( $ingredients as $ingredient ) {
						$output .= $this->getIngredient( $ingredient, $safety );
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
						$output .= $this->getIngredient( $may, $safety );
					}

					$output .= '</tbody>
					</table>';
				}

				if ( 'true' !== $disclaimer ) {
					$output .= '<div class="disclaimer">' . cmb2_get_option(
						'wi_disclaimer',
						'textarea_disclaimer',
						$this->get_default_disclaimer()
					) . '</div>';
				}
			}

			return $output;
		}

		/**
		 * Show the ingredients table into product content.
		 *
		 * @param string $content Post content.
		 *
		 * @return string
		 */
		public function wiContentIngredients( string $content ): string {
			global $post;
			$output = '';

			if ( is_singular() && is_main_query() ) {
				if ( 'product' === $post->post_type ) {
					$output = '<div class="wp-inci">' . $this->getIngredientsTable( $post->ID ) . '</div>';
				}

				return $content . $output;
			}

			return $content;
		}

		/**
		 * Add the product shortcode.
		 *
		 * @return void
		 */
		public function wiAddProductShortcode(): void {
			if ( ! shortcode_exists( 'wp_inci_product' ) ) {
				// @codingStandardsIgnoreStart
				add_shortcode(
					'wp_inci_product',
					array( $this, 'wiProductShortcode' )
				);
				// @codingStandardsIgnoreEnd
			}
		}

		/**
		 * Set up the shortcode to show the product.
		 *
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Post content.
		 * @param string $shortcode Shortcode name.
		 *
		 * @return string
		 */
		public function wiProductShortcode( array $atts, string $content, string $shortcode ): string {

			// Example: [wp_inci_product id="31" title="Neo Nude Glow Foundation" link="true" list="false" safety="false" content="true" disclaimer="true"].
			// Basic use: [wp_inci_product id="31"].

			// Normalize attribute keys, lowercase.
			$atts = array_change_key_case( $atts );

			// Sets shortcode attributes with defaults.
			$atts = shortcode_atts(
				array(
					'id'         => 0,
					'content'    => 'false',
					'disclaimer' => 'true',
					'link'       => 'false',
					'list'       => 'true',
					'safety'     => 'true',
					'title'      => '',
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

				$product_content = get_the_content( '', '', $atts['id'] );

				if ( ( 'true' === $atts['content'] ) && '' !== $product_content ) {
					$output .= $product_content;
				}

				if ( 'true' === $atts['list'] ) {
					$output .= $this->getIngredientsTable(
						$atts['id'],
						$atts['safety'],
						$atts['disclaimer']
					);
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

	add_action( 'plugins_loaded', array( 'Wp_Inci_Frontend', 'get_instanceFrontend' ) );
}
