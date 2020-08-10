<?php

/**
 * WP_Inci
 *
 * Main class for subclassing backend and frontend class.
 *
 * @package         wp-inci
 * @author          xlthlx <wp-inci@piccioni.london>
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
		 * @since 1.0
		 * @var string
		 */
		public $version = "1.1.0";

		/**
		 * release.minor.revision
		 * See split below.
		 *
		 * @since 1.0
		 * @var integer
		 */
		public $release = 1;
		public $minor = 1;
		public $revision = 0;

		/**
		 * Plugin name
		 *
		 * @since 1.0
		 * @var string
		 */
		public $plugin_name = "WP INCI";

		/**
		 * Main plugin slug.
		 *
		 * @since 1.0
		 * @var string
		 */
		public $plugin_slug = "wp-inci";

		/**
		 * Setting from main file to __FILE__.
		 *
		 * @since 1.0
		 * @var string
		 */
		public $plugin_file = __DIR__ . '/wp-inci.php';

		/**
		 * Options array containing all options for this plugin.
		 *
		 * @since 1.0
		 * @var array
		 */
		public $options = array();

		/**
		 * This plugin url.
		 *
		 * @since 1.0
		 * @var string
		 */
		public $url = "";

		/**
		 * Constructor.
		 *
		 * @since 1.0
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

		/**
		 * Standard init.
		 */
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
			$this->ingredients_post_type();
			$this->products_post_type();

		}

		/**
		 * Register Custom Post Type Ingredient and Functions and Source Taxonomy.
		 */
		public function ingredients_post_type() {

			$ingredients_labels = array(
				'name'                     => __( 'Ingredients', 'wp-inci' ),
				'singular_name'            => __( 'Ingredient', 'wp-inci' ),
				'add_new'                  => __( 'Add new', 'wp-inci' ),
				'add_new_item'             => __( 'Add new ingredient', 'wp-inci' ),
				'edit_item'                => __( 'Edit ingredient', 'wp-inci' ),
				'new_item'                 => __( 'New ingredient', 'wp-inci' ),
				'view_item'                => __( 'View ingredient', 'wp-inci' ),
				'view_items'               => __( 'View ingredients', 'wp-inci' ),
				'search_items'             => __( 'Search ingredient', 'wp-inci' ),
				'not_found'                => __( 'No ingredients found', 'wp-inci' ),
				'not_found_in_trash'       => __( 'No ingredients found in trash', 'wp-inci' ),
				'all_items'                => __( 'All ingredients', 'wp-inci' ),
				'archives'                 => __( 'Ingredients archives', 'wp-inci' ),
				'attributes'               => __( 'Ingredient attributes', 'wp-inci' ),
				'insert_into_item'         => __( 'Insert into ingredient', 'wp-inci' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this ingredient', 'wp-inci' ),
				'featured_image'           => __( 'Featured image', 'wp-inci' ),
				'set_featured_image'       => __( 'Set featured image', 'wp-inci' ),
				'remove_featured_image'    => __( 'Remove featured image', 'wp-inci' ),
				'use_featured_image'       => __( 'Use as featured image', 'wp-inci' ),
				'filter_items_list'        => __( 'Filter ingredients list', 'wp-inci' ),
				'items_list_navigation'    => __( 'Ingredients list navigation', 'wp-inci' ),
				'items_list'               => __( 'Ingredients list', 'wp-inci' ),
				'item_published'           => __( 'Ingredient published.', 'wp-inci' ),
				'item_published_privately' => __( 'Ingredient published privately.', 'wp-inci' ),
				'item_reverted_to_draft'   => __( 'Ingredient reverted to draft.', 'wp-inci' ),
				'item_scheduled'           => __( 'Ingredient scheduled.', 'wp-inci' ),
				'item_updated'             => __( 'Ingredient updated.', 'wp-inci' ),
			);


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
					'safety'    => array(
						'title'    => __( 'Safety', 'wp-inci' ),
						'function' => function () {
							global $post;
							echo $this->get_safety_html( $post->ID );
						},
					),
					'functions' => array(
						'title'    => __( 'Functions', 'wp-inci' ),
						'taxonomy' => 'functions'
					),
					'source'    => array(
						'title'    => __( 'Source', 'wp-inci' ),
						'taxonomy' => 'source'
					),
					'author'    => array(
						'title'      => __( 'Author', 'wp-inci' ),
						'post_field' => 'post_author',
					),
					'date'      => array(
						'title' => __( 'Date', 'wp-inci' ),
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
				'name'                       => __( 'Functions', 'wp-inci' ),
				'singular_name'              => __( 'Function', 'wp-inci' ),
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
				'most_used'                  => __( 'Most used', 'wp-inci' ),
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
				'name'                       => __( 'Sources', 'wp-inci' ),
				'singular_name'              => __( 'Source', 'wp-inci' ),
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
				'most_used'                  => __( 'Most used', 'wp-inci' ),
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
		public function get_safety_html( $post_id ): string {

			$safety = $this->get_safety_value( $post_id );

			return '<div class="' . $safety[0] . ' first">' . strtoupper( $safety[0] ) . '</div><div class="' . $safety[1] . ' second">' . strtoupper( $safety[1] ) . '</div>';
		}

		/**
		 * Gets the value of safety custom meta and fills the gaps.
		 *
		 * @param $post_id
		 *
		 * @return array $safety
		 */
		public function get_safety_value( $post_id ): array {

			$safety = get_post_meta( $post_id, 'safety', true );

			switch ( $safety ) {
				case '1' :
					$safety = array( 'g', 'g' );
					break;
				case '2' :
					$safety = array( 'g', 'w' );
					break;
				case '3' :
					$safety = array( 'y', 'w' );
					break;
				case '4' :
					$safety = array( 'r', 'w' );
					break;
				case '5' :
					$safety = array( 'r', 'r' );
					break;
				case '' :
					$safety = array( 'w', 'w' );
					break;
				default:
					$safety = array( 'w', 'w' );
			}

			return $safety;

		}

		/**
		 * Register Custom Post Type Product and Brand Taxonomy.
		 */
		public function products_post_type() {

			$product_labels = array(
				'name'                     => __( 'Products', 'wp-inci' ),
				'singular_name'            => __( 'Product', 'wp-inci' ),
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
				'featured_image'           => __( 'Featured image', 'wp-inci' ),
				'set_featured_image'       => __( 'Set featured image', 'wp-inci' ),
				'remove_featured_image'    => __( 'Remove featured image', 'wp-inci' ),
				'use_featured_image'       => __( 'Use as featured image', 'wp-inci' ),
				'filter_items_list'        => __( 'Filter products list', 'wp-inci' ),
				'items_list_navigation'    => __( 'Products list navigation', 'wp-inci' ),
				'items_list'               => __( 'Products list', 'wp-inci' ),
				'item_published'           => __( 'Product published.', 'wp-inci' ),
				'item_published_privately' => __( 'Product published privately.', 'wp-inci' ),
				'item_reverted_to_draft'   => __( 'Product reverted to draft.', 'wp-inci' ),
				'item_scheduled'           => __( 'Product scheduled.', 'wp-inci' ),
				'item_updated'             => __( 'Product updated.', 'wp-inci' ),
			);

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
				'admin_cols'         => array(
					'title'     => array(
						'title'   => __( 'Product', 'wp-inci' ),
						'default' => 'ASC',
					),
					'brand'     => array(
						'title'    => __( 'Brand', 'wp-inci' ),
						'taxonomy' => 'brand'
					),
					'shortcode' => array(
						'title'    => __( 'Shortcode', 'wp-inci' ),
						'function' => function () {
							global $post;
							echo '<input readonly="readonly" type="text" onclick="copyShort(this)" value="[wp_inci_product id=' . $post->ID . ']">';
						},
					),
					'author'    => array(
						'title'      => __( 'Author', 'wp-inci' ),
						'post_field' => 'post_author',
					),
					'date'      => array(
						'title' => __( 'Date', 'wp-inci' ),
					),
				),
				'admin_filters'      => array(
					'brand'  => array(
						'title'    => __( 'All Brands', 'wp-inci' ),
						'taxonomy' => 'brand',
					),
					'author' => array(
						'title'       => __( 'All Authors', 'wp-inci' ),
						'post_author' => true,
					),
				),

			), array(

				'singular' => __( 'Product', 'wp-inci' ),
				'plural'   => __( 'Products', 'wp-inci' ),
				'slug'     => __( 'product', 'wp-inci' ),

			) );

			$brand_labels = array(
				'name'                       => __( 'Brands', 'wp-inci' ),
				'singular_name'              => __( 'Brand', 'wp-inci' ),
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
				'most_used'                  => __( 'Most used', 'wp-inci' ),
				'back_to_items'              => __( '&larr; Back to brands', 'wp-inci' ),
			);

			register_extended_taxonomy( 'brand', 'product', array(
				'public'       => true,
				'rewrite'      => true,
				'hierarchical' => false,
				'labels'       => $brand_labels,
				'meta_box_cb'  => false,
			), array(
				'singular' => __( 'Brand', 'wp-inci' ),
				'plural'   => __( 'Brands', 'wp-inci' ),
				'slug'     => __( 'brand', 'wp-inci' )

			) );

		}

		/**
		 * Sets text for default disclaimer.
		 */
		public function wi_default_disclaimer() {
			return __( "The evaluation of these ingredients reflects the opinion of the author, who is not a specialist in this field. This evaluation is based on some online databases (e.g. <a title=\"CosIng - Cosmetic ingredients database\" href=\"https://ec.europa.eu/growth/sectors/cosmetics/cosing/\" target=\"_blank\">CosIng</a>).", 'wp-inci' );
		}

	}

	add_action( 'plugins_loaded', array( 'WP_Inci', 'get_instance' ) );
}
