<?php
/**
 * Register Block Category.
 *
 * @package         wp-inci
 * @author          xlthlx <wp-inci@piccioni.london>
 *
 */

function wi_blocks_category( $categories ) {
	$block_category = [ 'title' => 'WP INCI', 'slug' => 'wp-inci' ];
	$category_slugs = array_column( $categories, 'slug' );

	if ( ! in_array( $block_category['slug'], $category_slugs, true ) ) {
		$categories = array_merge(
			[
				[
					'title' => $block_category['title'],
					'slug'  => $block_category['slug'],
				],
			],
			$categories
		);
	}

	return $categories;
}

add_filter( 'block_categories_all', 'wi_blocks_category', 10, 1 );
