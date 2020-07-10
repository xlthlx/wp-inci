<?php

/**
 * WP_Inci
 *
 * Main class for subclassing backend and frontend class.
 *
 * @package         wp-inci
 * @author          xlthlx <github@piccioni.london>
 *
 */
if ( ! class_exists( 'WP_Inci', false ) ) {
	class WP_Inci {

		/**
		 * A static reference to track the single instance of this class.
		 */
		private static $instance;

		/**
		 * Plugin version.
		 *
		 * @since 0.2
		 * @var string
		 */
		public $version = "0.2.0";

		/**
		 * release.minor.revision
		 * See split below.
		 *
		 * @since 0.2
		 * @var integer
		 */
		public $release = 0;
		public $minor = 2;
		public $revision = 0;

		/**
		 * Plugin name
		 *
		 * @since 0.2
		 * @var string
		 */
		public $plugin_name = "WP INCI";

		/**
		 * Added for Fix Administrator Permission Warning.
		 *
		 * @since 0.2
		 * @var string
		 */
		public $plugin_slug = "wp-inci";

		/**
		 * Setting from main file to __FILE__.
		 *
		 * @since 0.2
		 * @var string
		 */
		public $plugin_file = __DIR__ . '/wp-inci.php';

		/**
		 * Options array containing all options for this plugin.
		 *
		 * @since 0.2
		 * @var array
		 */
		public $options = array();

		/**
		 * This plugin url.
		 *
		 * @since 0.2
		 * @var string
		 */
		public $url = "";

		/**
		 * Constructor.
		 *
		 * @since 0.2
		 */
		public function __construct() {
			$this->init();
			/**
			 * Split version for more detail.
			 */
			$split_version  = explode( ".", $this->version );
			$this->release  = $split_version[0];
			$this->minor    = $split_version[1];
			$this->revision = $split_version[2];

			/**
			 * Sets url for the plugin.
			 */
			$this->url = plugins_url( "", __DIR__ . '/wp-inci.php' );

		}

		public function init() {
			/**
			 * Add Custom Post Types.
			 */
			add_action( 'init', array( $this, 'post_type_init' ) );

		}

		/**
		 * Method used to provide a single instance of this class.
		 *
		 * @return WP_Inci|null
		 */
		public static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new WP_Inci();
			}

			return self::$instance;
		}

		/**
		 * Add Custom Post Types and Taxonomies.
		 */
		public function post_type_init() {
			$this->wp_inci_ingredients_post_type();
			$this->wp_inci_products_post_type();

		}

		/**
		 * Register Custom Post Type Ingredient and Functions and Source Taxonomy.
		 */
		public function wp_inci_ingredients_post_type() {

			$ingredients_labels = [
				'name'                     => _x( 'Ingredients', 'post type general name', 'wp-inci' ),
				'singular_name'            => _x( 'Ingredient', 'post type singular name', 'wp-inci' ),
				'add_new'                  => __( 'Add new', 'wp-inci' ),
				'add_new_item'             => __( 'Add new Ingredient', 'wp-inci' ),
				'edit_item'                => __( 'Edit Ingredient', 'wp-inci' ),
				'new_item'                 => __( 'New Ingredient', 'wp-inci' ),
				'view_item'                => __( 'View Ingredient', 'wp-inci' ),
				'view_items'               => __( 'View ingredients', 'wp-inci' ),
				'search_items'             => __( 'Search Ingredient', 'wp-inci' ),
				'not_found'                => __( 'No ingredients found', 'wp-inci' ),
				'not_found_in_trash'       => __( 'No ingredients found in trash', 'wp-inci' ),
				'all_items'                => __( 'All ingredients', 'wp-inci' ),
				'archives'                 => __( 'Ingredients archives', 'wp-inci' ),
				'attributes'               => __( 'Ingredient attributes', 'wp-inci' ),
				'insert_into_item'         => __( 'Insert into ingredient', 'wp-inci' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this ingredient', 'wp-inci' ),
				'featured_image'           => _x( 'Featured image', 'ingredient', 'wp-inci' ),
				'set_featured_image'       => _x( 'Set featured image', 'ingredient', 'wp-inci' ),
				'remove_featured_image'    => _x( 'Remove featured image', 'ingredient', 'wp-inci' ),
				'use_featured_image'       => _x( 'Use as featured image', 'ingredient', 'wp-inci' ),
				'filter_items_list'        => __( 'Filter ingredients list', 'wp-inci' ),
				'items_list_navigation'    => __( 'Ingredients list navigation', 'wp-inci' ),
				'items_list'               => __( 'Ingredients list', 'wp-inci' ),
				'item_published'           => __( 'Ingredient published.', 'wp-inci' ),
				'item_published_privately' => __( 'Ingredient published privately.', 'wp-inci' ),
				'item_reverted_to_draft'   => __( 'Ingredient reverted to draft.', 'wp-inci' ),
				'item_scheduled'           => __( 'Ingredient scheduled.', 'wp-inci' ),
				'item_updated'             => __( 'Ingredient updated.', 'wp-inci' ),
			];


			register_extended_post_type( 'ingredient', array(
				'publicly_queryable' => false,
				'menu_icon'          => esc_url( plugins_url( 'admin/images/menu.png', __FILE__ ) ),
				'rewrite'            => false,
				'labels'             => $ingredients_labels,
				'capability_type'    => 'page',
				'has_archive'        => false,
				'hierarchical'       => false,
				'supports'           => array(
					'title',
					'editor',
					'author',
					'revisions',
				),
				'admin_cols'         => array(
					'title'     => array(
						'title'   => __( 'Ingredient', 'wp-inci' ),
						'default' => 'ASC',
					),
					'safety'    => [
						'title'    => __( 'Safety', 'wp-inci' ),
						'function' => function () {
							global $post;
							echo $this->wp_inci_get_safety_html( $post->ID );
						},
					],
					'functions' => array(
						'taxonomy' => 'functions'
					),
					'source'    => array(
						'taxonomy' => 'source'
					),
					'author'    => array(
						'title'      => __( 'Author' ),
						'post_field' => 'post_author',
					),
					'date'      => array(
						'title' => __( 'Date' ),
					),
				),
				'admin_filters'      => array(
					'functions' => array(
						'title'    => __( 'All Functions', 'wp-inci' ),
						'taxonomy' => 'functions',
					),
					'source'    => array(
						'title'    => __( 'All Sources', 'wp-inci' ),
						'taxonomy' => 'source',
					),
					'author'    => array(
						'title'       => __( 'All Authors', 'wp-inci' ),
						'post_author' => true,
					),
				),
			), array(

				'singular' => __( 'Ingredient', 'wp-inci' ),
				'plural'   => __( 'Ingredients', 'wp-inci' ),
				'slug'     => __( 'ingredient', 'wp-inci' )

			) );

			$functions_labels = array(
				'name'                       => _x( 'Functions', 'taxonomy general name', 'wp-inci' ),
				'singular_name'              => _x( 'Function', 'taxonomy singular name', 'wp-inci' ),
				'search_items'               => __( 'Search functions', 'wp-inci' ),
				'popular_items'              => __( 'Popular functions', 'wp-inci' ),
				'all_items'                  => __( 'All functions', 'wp-inci' ),
				'parent_item'                => __( 'Parent function', 'wp-inci' ),
				'parent_item_colon'          => __( 'Parent function:', 'wp-inci' ),
				'edit_item'                  => __( 'Edit function', 'wp-inci' ),
				'view_item'                  => __( 'View function', 'wp-inci' ),
				'update_item'                => __( 'Update function', 'wp-inci' ),
				'add_new_item'               => __( 'Add new function', 'wp-inci' ),
				'new_item_name'              => __( 'New function name', 'wp-inci' ),
				'separate_items_with_commas' => __( 'Separate functions with commas', 'wp-inci' ),
				'add_or_remove_items'        => __( 'Add or remove functions', 'wp-inci' ),
				'choose_from_most_used'      => __( 'Choose a function from the most used', 'wp-inci' ),
				'not_found'                  => __( 'No functions found.', 'wp-inci' ),
				'no_terms'                   => __( 'No functions.', 'wp-inci' ),
				'items_list_navigation'      => __( 'Functions list navigation', 'wp-inci' ),
				'items_list'                 => __( 'Functions list', 'wp-inci' ),
				'most_used'                  => _x( 'Most used', 'functions', 'wp-inci' ),
				'back_to_items'              => __( '&larr; Back to functions', 'wp-inci' ),
			);

			register_extended_taxonomy( 'functions', 'ingredient', array(
				'hierarchical' => false,
				'labels'       => $functions_labels,
				'public'       => false,
				'rewrite'      => false,

			), array(

				'singular' => __( 'Function', 'wp-inci' ),
				'plural'   => __( 'Functions', 'wp-inci' ),
				'slug'     => __( 'functions', 'wp-inci' )

			) );

			$source_labels = array(
				'name'                       => _x( 'Sources', 'taxonomy general name', 'wp-inci' ),
				'singular_name'              => _x( 'Source', 'taxonomy singular name', 'wp-inci' ),
				'search_items'               => __( 'Search sources', 'wp-inci' ),
				'popular_items'              => __( 'Popular sources', 'wp-inci' ),
				'all_items'                  => __( 'All sources', 'wp-inci' ),
				'parent_item'                => __( 'Parent source', 'wp-inci' ),
				'parent_item_colon'          => __( 'Parent source:', 'wp-inci' ),
				'edit_item'                  => __( 'Edit source', 'wp-inci' ),
				'view_item'                  => __( 'View source', 'wp-inci' ),
				'update_item'                => __( 'Update source', 'wp-inci' ),
				'add_new_item'               => __( 'Add new source', 'wp-inci' ),
				'new_item_name'              => __( 'New source name', 'wp-inci' ),
				'separate_items_with_commas' => __( 'Separate sources with commas', 'wp-inci' ),
				'add_or_remove_items'        => __( 'Add or remove sources', 'wp-inci' ),
				'choose_from_most_used'      => __( 'Choose a source from the most used', 'wp-inci' ),
				'not_found'                  => __( 'No sources found.', 'wp-inci' ),
				'no_terms'                   => __( 'No sources.', 'wp-inci' ),
				'items_list_navigation'      => __( 'Sources list navigation', 'wp-inci' ),
				'items_list'                 => __( 'Sources list', 'wp-inci' ),
				'most_used'                  => _x( 'Most used', 'source', 'wp-inci' ),
				'back_to_items'              => __( '&larr; Back to sources', 'wp-inci' ),
			);

			register_extended_taxonomy( 'source', 'ingredient', array(
				'public'       => false,
				'labels'       => $source_labels,
				'rewrite'      => false,
				'hierarchical' => true,
				'admin_cols'   => array(
					'url' => array(
						'title'    => __( 'Url', 'wp-inci' ),
						'meta_key' => 'source_url',
					),
				),

			), array(
				'singular' => __( 'Source', 'wp-inci' ),
				'plural'   => __( 'Sources', 'wp-inci' ),
				'slug'     => __( 'source', 'wp-inci' )

			) );

		}

		/**
		 * Returns the safety custom meta with HTML.
		 *
		 * @param int $post_id
		 *
		 * @return string
		 */
		public function wp_inci_get_safety_html( $post_id ): string {

			$safety = $this->wp_inci_get_safety_value( $post_id );
			$array  = str_split( $safety, 1 );

			return '<div class="' . $array[0] . ' first">' . strtoupper( $array[0] ) . '</div>
               <div class="' . $array[1] . ' second">' . strtoupper( $array[1] ) . '</div>';
		}

		/**
		 * Gets the value of safety custom meta and fills the gaps.
		 *
		 * @param $post_id
		 *
		 * @return mixed|string
		 */
		public function wp_inci_get_safety_value( $post_id ): string {

			$safety = get_post_meta( $post_id, 'safety', true );
			if ( '' === $safety ) {
				$safety = 'ww';
			}

			if ( 1 === ( strlen( $safety ) ) ) {
				$safety .= 'w';
			}

			return $safety;

		}

		/**
		 * Register Custom Post Type Product and Brand Taxonomy.
		 */
		public function wp_inci_products_post_type() {

			$product_labels = [
				'name'                     => _x( 'Products', 'post type general name', 'wp-inci' ),
				'singular_name'            => _x( 'Product', 'post type singular name', 'wp-inci' ),
				'add_new'                  => __( 'Add new', 'wp-inci' ),
				'add_new_item'             => __( 'Add new product', 'wp-inci' ),
				'edit_item'                => __( 'Edit product', 'wp-inci' ),
				'new_item'                 => __( 'New product', 'wp-inci' ),
				'view_item'                => __( 'View product', 'wp-inci' ),
				'view_items'               => __( 'View products', 'wp-inci' ),
				'search_items'             => __( 'Search product', 'wp-inci' ),
				'not_found'                => __( 'No products found', 'wp-inci' ),
				'not_found_in_trash'       => __( 'No products found in trash', 'wp-inci' ),
				'all_items'                => __( 'All products', 'wp-inci' ),
				'archives'                 => __( 'Products archives', 'wp-inci' ),
				'attributes'               => __( 'Product attributes', 'wp-inci' ),
				'insert_into_item'         => __( 'Insert into product', 'wp-inci' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this product', 'wp-inci' ),
				'featured_image'           => _x( 'Featured image', 'product', 'wp-inci' ),
				'set_featured_image'       => _x( 'Set featured image', 'product', 'wp-inci' ),
				'remove_featured_image'    => _x( 'Remove featured image', 'product', 'wp-inci' ),
				'use_featured_image'       => _x( 'Use as featured image', 'product', 'wp-inci' ),
				'filter_items_list'        => __( 'Filter products list', 'wp-inci' ),
				'items_list_navigation'    => __( 'Products list navigation', 'wp-inci' ),
				'items_list'               => __( 'Products list', 'wp-inci' ),
				'item_published'           => __( 'Product published.', 'wp-inci' ),
				'item_published_privately' => __( 'Product published privately.', 'wp-inci' ),
				'item_reverted_to_draft'   => __( 'Product reverted to draft.', 'wp-inci' ),
				'item_scheduled'           => __( 'Product scheduled.', 'wp-inci' ),
				'item_updated'             => __( 'Product updated.', 'wp-inci' ),
			];

			register_extended_post_type( 'product', array(
				'dashboard_activity' => true,
				'menu_icon'          => esc_url( plugins_url( 'admin/images/menu.png', __FILE__ ) ),
				'labels'             => $product_labels,
				'capability_type'    => 'page',
				'rewrite'            => true,
				'supports'           => array(
					'title',
					'editor',
					'author',
					'revisions',
				),

			), array(

				'singular' => __( 'Product', 'wp-inci' ),
				'plural'   => __( 'Products', 'wp-inci' ),
				'slug'     => __( 'product', 'wp-inci' ),

			) );

			$brand_labels = array(
				'name'                       => _x( 'Brands', 'taxonomy general name', 'wp-inci' ),
				'singular_name'              => _x( 'Brand', 'taxonomy singular name', 'wp-inci' ),
				'search_items'               => __( 'Search brands', 'wp-inci' ),
				'popular_items'              => __( 'Popular brands', 'wp-inci' ),
				'all_items'                  => __( 'All brands', 'wp-inci' ),
				'parent_item'                => __( 'Parent brand', 'wp-inci' ),
				'parent_item_colon'          => __( 'Parent brand:', 'wp-inci' ),
				'edit_item'                  => __( 'Edit brand', 'wp-inci' ),
				'view_item'                  => __( 'View brand', 'wp-inci' ),
				'update_item'                => __( 'Update brand', 'wp-inci' ),
				'add_new_item'               => __( 'Add new brand', 'wp-inci' ),
				'new_item_name'              => __( 'New brand name', 'wp-inci' ),
				'separate_items_with_commas' => __( 'Separate brands with commas', 'wp-inci' ),
				'add_or_remove_items'        => __( 'Add or remove brands', 'wp-inci' ),
				'choose_from_most_used'      => __( 'Choose a brand from the most used', 'wp-inci' ),
				'not_found'                  => __( 'No brands found.', 'wp-inci' ),
				'no_terms'                   => __( 'No brands.', 'wp-inci' ),
				'items_list_navigation'      => __( 'Brands list navigation', 'wp-inci' ),
				'items_list'                 => __( 'Brands list', 'wp-inci' ),
				'most_used'                  => _x( 'Most used', 'brand', 'wp-inci' ),
				'back_to_items'              => __( '&larr; Back to brands', 'wp-inci' ),
			);

			register_extended_taxonomy( 'brand', 'product', array(
				'public'       => true,
				'rewrite'      => true,
				'hierarchical' => false,
				'exclusive'    => true,
				'labels'       => $brand_labels,
				'meta_box'     => 'dropdown',
			), array(
				'singular' => __( 'Brand', 'wp-inci' ),
				'plural'   => __( 'Brands', 'wp-inci' ),
				'slug'     => __( 'brand', 'wp-inci' )

			) );

			$colour_labels = array(
				'name'                       => _x( 'Colours', 'taxonomy general name', 'wp-inci' ),
				'singular_name'              => _x( 'Colour', 'taxonomy singular name', 'wp-inci' ),
				'search_items'               => __( 'Search colours', 'wp-inci' ),
				'popular_items'              => __( 'Popular colours', 'wp-inci' ),
				'all_items'                  => __( 'All colours', 'wp-inci' ),
				'parent_item'                => __( 'Parent colour', 'wp-inci' ),
				'parent_item_colon'          => __( 'Parent colour:', 'wp-inci' ),
				'edit_item'                  => __( 'Edit colour', 'wp-inci' ),
				'view_item'                  => __( 'View colour', 'wp-inci' ),
				'update_item'                => __( 'Update colour', 'wp-inci' ),
				'add_new_item'               => __( 'Add new colour', 'wp-inci' ),
				'new_item_name'              => __( 'New colour name', 'wp-inci' ),
				'separate_items_with_commas' => __( 'Separate colours with commas', 'wp-inci' ),
				'add_or_remove_items'        => __( 'Add or remove colours', 'wp-inci' ),
				'choose_from_most_used'      => __( 'Choose a colour from the most used', 'wp-inci' ),
				'not_found'                  => __( 'No colours found.', 'wp-inci' ),
				'no_terms'                   => __( 'No colours.', 'wp-inci' ),
				'items_list_navigation'      => __( 'Colours list navigation', 'wp-inci' ),
				'items_list'                 => __( 'Colours list', 'wp-inci' ),
				'most_used'                  => _x( 'Most used', 'colour', 'wp-inci' ),
				'back_to_items'              => __( '&larr; Back to colours', 'wp-inci' ),
			);

			register_extended_taxonomy( 'colour', 'product', array(
				'public'       => true,
				'rewrite'      => true,
				'hierarchical' => false,
				'labels'       => $colour_labels,
			), array(
				'singular' => __( 'Colour', 'wp-inci' ),
				'plural'   => __( 'Colours', 'wp-inci' ),
				'slug'     => __( 'colour', 'wp-inci' )

			) );

		}

	}

	add_action( 'plugins_loaded', array( 'WP_Inci', 'get_instance' ) );
}
