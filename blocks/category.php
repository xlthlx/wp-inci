<?php
/**
 * Register Block Category.
 *
 * @category Plugin
 * @package  Wpinci
 * @author   xlthlx <wp-inci@piccioni.london>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPL 3
 * @link     https://wordpress.org/plugins/wp-inci/
 */

/**
 * Add block category WP INCI.
 *
 * @param array $categories Block categories array
 *
 * @return array
 */
function Wi_Blocks_category( $categories )
{
    $block_category = [ 'title' => 'WP INCI', 'slug' => 'wp-inci' ];
    $category_slugs = array_column($categories, 'slug');

    if (! in_array($block_category['slug'], $category_slugs, true) ) {
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

add_filter('block_categories_all', 'Wi_Blocks_category', 10, 1);
