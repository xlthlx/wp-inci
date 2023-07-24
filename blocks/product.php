<?php
/**
 * Gutenberg Block Product
 *
 * @category Plugin
 * @package  Wpinci
 * @author   xlthlx <wp-inci@piccioni.london>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPL 3
 * @link     https://wordpress.org/plugins/wp-inci/
 */

// @codingStandardsIgnoreStart
use Carbon_Fields\Block;
use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Field;
// @codingStandardsIgnoreEnd

require_once WPINCI_BASE_PATH . 'public/class-wp-inci-frontend.php';

/**
 * Load Carbon Fields.
 *
 * @return void
 */
function wi_load_cb() {
	Carbon_Fields::boot();
}

add_action( 'after_setup_theme', 'wi_load_cb' );

/**
 * Get list of products.
 *
 * @param bool $select If the callback is for a select.
 *
 * @return array
 */
function wi_get_products( $select = false ) {

	$results = array();

	$args = array(
		'post_type'   => array( 'product' ),
		'post_status' => 'publish',
		'order'       => 'ASC',
		'ordeby'      => 'title',
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) {

		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$results[ get_the_ID() ] = get_the_title();
		}
	}

	wp_reset_postdata();

	if ( $select ) {
		$results[0] = __( 'Select a product', 'wp-inci' );
		ksort( $results );
	}

	return $results;
}

/**
 * Populate the block select.
 *
 * @return array
 */
function wi_populate_select() {
	return wi_get_products( true );
}

/**
 * Register Product block.
 *
 * @return void
 */
function wi_product_block() {
	Block::make( __( 'Product', 'wp-inci' ) )
		->add_fields(
			array(
				Field::make( 'text', 'title', __( 'Custom title', 'wp-inci' ) )->set_help_text( 'Leave blank to show the product title' ),
				Field::make( 'select', 'product', __( 'Select product', 'wp-inci' ) )->add_options( 'wi_populate_select' ),
				Field::make( 'checkbox', 'linked', __( 'Show link', 'wp-inci' ) )->set_option_value( 'yes' ),
				Field::make( 'checkbox', 'list', __( 'Hide ingredient list', 'wp-inci' ) )->set_option_value( 'yes' ),
				Field::make( 'checkbox', 'safety', __( 'Hide safety', 'wp-inci' ) )->set_option_value( 'yes' )->set_conditional_logic(
					array(
						array(
							'field' => 'list',
							'value' => false,
						),
					)
				),
			)
		)
		 ->set_description( __( 'A block to insert a Product into content.', 'wp-inci' ) )
		 ->set_category( 'wp-inci' )
		 ->set_keywords( array( __( 'product', 'wp-inci' ), __( 'inci', 'wp-inci' ), __( 'ingredients', 'wp-inci' ) ) )
		 ->set_icon( 'wp-inci' )
		// @codingStandardsIgnoreStart
		 ->set_render_callback(
			function ( $fields, $attributes, $inner_blocks ) {
				$output = '<div class="block">
                 <div class="block__product">
                    <div class="wp-inci">';

				$start = '<h3>';
				$end   = '</h3>';
				$title = esc_html( get_the_title( $fields['product'] ) );

				if ( isset( $fields['title'] ) && '' !== $fields['title'] ) {
					$title = esc_html( $fields['title'] );
				}

				if ( $fields['linked'] ) {
					$start = '<h3><a title="' . $title . '" href="' . get_permalink( $fields['product'] ) . '">';
					$end   = '</a></h3>';
				}

				$output .= $start . $title . $end;

				$safety = 'true';

				if ( isset( $fields['safety'] ) && true === $fields['safety'] ) {
					$safety = 'false';
				}

				if ( ! $fields['list'] ) {
					$output .= ( Wp_Inci_Frontend::get_instanceFrontend() )->getIngredientsTable(
						$fields['product'],
						$safety
					);
				}
				$output .= '</div>
		        </div>
            </div>';

				//echo '<pre>' . print_r($fields, true) . '</pre>';
				echo $output;
			}
		// @codingStandardsIgnoreEnd
		 );
}

add_action( 'carbon_fields_register_fields', 'wi_product_block' );
