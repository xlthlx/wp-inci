<?php
/**
 * Block render.
 *
 * @category Plugin
 * @package  Wpinci
 * @author   xlthlx <wp-inci@piccioni.london>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPL 3
 * @link     https://wordpress.org/plugins/wp-inci/
 */

?>
<div <?php echo esc_html( get_block_wrapper_attributes() ); ?>>
	<div class="wp-inci">
		<?php

		$product_id         = isset( $attributes['productId'] ) ? (int) $attributes['productId'] : 0;
		$custom_title       = $attributes['customTitle'] ?? '';
		$product_link       = $attributes['productLink'] ?? '';
		$product_content    = $attributes['productContent'] ?? '';
		$ingredients_list   = $attributes['ingredientsList'] ?? '';
		$ingredients_safety = $attributes['ingredientsSafety'] ?? '';
		$disclaimer         = $attributes['disclaimer'] ?? '';

		$product_title_render   = __( 'Select a product', 'wp-inci' );
		$product_content_render = '';
		$list                   = '';

		if ( 0 !== $product_id ) {
			require_once WPINCI_BASE_PATH . 'public/class-wp-inci-frontend.php';

			$start         = '';
			$end           = '';
			$product_title = esc_html( get_the_title( $product_id ) );

			if ( '' !== $custom_title ) {
				$product_title = esc_html( $custom_title );
			}

			if ( 'Yes' === $product_link ) {
				$start = '<a title="' . $product_title . '" href="' . get_permalink( $product_id ) . '">';
				$end   = '</a>';
			}

			$product_title_render = $start . $product_title . $end;
			if ( 'Yes' === $product_content ) {
				$product_content_render = get_the_content( '', true, $product_id );
			}

			if ( '' === $ingredients_list ) {
				$list = ( Wp_Inci_Frontend::get_instanceFrontend() )->getIngredientsTable(
					$product_id,
					'' === $ingredients_safety ? 'true' : 'false',
					'' === $disclaimer ? 'false' : 'true'
				);
			}
		}
		?>
		<h3><?php echo wp_kses_post( $product_title_render ); ?></h3>
		<?php echo wp_kses_post( $product_content_render ); ?>
		<?php echo wp_kses_post( $list ); ?>
	</div>
</div>
