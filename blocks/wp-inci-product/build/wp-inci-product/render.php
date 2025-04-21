<div <?php echo get_block_wrapper_attributes(); ?>>
    <div class="wp-inci">
		<?php
		global $post;

		$product_id         = get_post_meta( $post->ID, 'wi-product-id', true );
		$custom_title       = get_post_meta( $post->ID, 'wi-custom-title', true );
		$product_link       = get_post_meta( $post->ID, 'wi-product-link', true );
		$product_content    = get_post_meta( $post->ID, 'wi-product-content', true );
		$product_list       = get_post_meta( $post->ID, 'wi-ingredients-list', true );
		$product_safety     = get_post_meta( $post->ID, 'wi-ingredients-safety', true );
		$product_disclaimer = get_post_meta( $post->ID, 'wi-disclaimer', true );

		$product_id = '' === $product_id ? 0 : (int) $product_id;

		$product_title_render   = __( 'Select a product', 'wp-inci' );
		$product_content_render = '';
		$list                   = '';

		if ( 0 !== $product_id ) {
			require_once WPINCI_BASE_PATH . 'public/class-wp-inci-frontend.php';

			$start = '';
			$end   = '';
			$title = esc_html( get_the_title( $product_id ) );

			if ( '' !== $custom_title ) {
				$title = esc_html( $custom_title );
			}

			if ( 'Yes' === $product_link ) {
				$start = '<a title="' . $title . '" href="' . get_permalink( $product_id ) . '">';
				$end   = '</a>';
			}

			$product_title_render = $start . $title . $end;
			if ( 'Yes' === $product_content ) {
				$product_content_render = get_the_content( '', true, $product_id );
			}

			if ( '' === $product_list ) {
				$list = ( Wp_Inci_Frontend::get_instanceFrontend() )->getIngredientsTable(
					$product_id,
					'' === $product_safety ? 'true' : 'false',
					'' === $product_disclaimer ? 'false' : 'true'
				);
			}
		}
		?>
        <h3><?php echo wp_kses_post( $product_title_render ); ?></h3>
		<?php echo wp_kses_post( $product_content_render ); ?>
		<?php echo wp_kses_post( $list ); ?>
    </div>
</div>
