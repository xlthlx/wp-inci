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
		public $version = "1.5.1";

		/**
		 * release.minor.revision
		 * See split below.
		 *
		 * @since 1.0
		 * @var string
		 */
		public $release = 1;
		public $minor = 5;
		public $revision = 1;

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
			 * Add Custom Post Types and Taxonomies.
			 */
			add_action( 'init', [ $this, 'post_type_init' ] );
			add_filter( 'post_updated_messages', [ $this, 'ingredient_updated_messages' ] );
			add_filter( 'bulk_post_updated_messages', [ $this, 'ingredient_bulk_updated_messages' ], 10, 2 );
			add_filter( 'post_updated_messages', [ $this, 'product_updated_messages' ] );
			add_filter( 'bulk_post_updated_messages', [ $this, 'product_bulk_updated_messages' ], 10, 2 );
			add_filter( 'term_updated_messages', [ $this, 'function_updated_messages' ] );
			add_filter( 'term_updated_messages', [ $this, 'source_updated_messages' ] );
			add_filter( 'term_updated_messages', [ $this, 'brand_updated_messages' ] );
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

			$ingredients_labels = [
				'name'                  => __( 'Ingredients', 'wp-inci' ),
				'singular_name'         => __( 'Ingredient', 'wp-inci' ),
				'all_items'             => __( 'All Ingredients', 'wp-inci' ),
				'archives'              => __( 'Ingredient Archives', 'wp-inci' ),
				'attributes'            => __( 'Ingredient Attributes', 'wp-inci' ),
				'insert_into_item'      => __( 'Insert into Ingredient', 'wp-inci' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Ingredient', 'wp-inci' ),
				'featured_image'        => _x( 'Featured Image', 'ingredient', 'wp-inci' ),
				'set_featured_image'    => _x( 'Set featured image', 'ingredient', 'wp-inci' ),
				'remove_featured_image' => _x( 'Remove featured image', 'ingredient', 'wp-inci' ),
				'use_featured_image'    => _x( 'Use as featured image', 'ingredient', 'wp-inci' ),
				'filter_items_list'     => __( 'Filter Ingredients list', 'wp-inci' ),
				'items_list_navigation' => __( 'Ingredients list navigation', 'wp-inci' ),
				'items_list'            => __( 'Ingredients list', 'wp-inci' ),
				'new_item'              => __( 'New Ingredient', 'wp-inci' ),
				'add_new'               => __( 'Add New', 'wp-inci' ),
				'add_new_item'          => __( 'Add New Ingredient', 'wp-inci' ),
				'edit_item'             => __( 'Edit Ingredient', 'wp-inci' ),
				'view_item'             => __( 'View Ingredient', 'wp-inci' ),
				'view_items'            => __( 'View Ingredients', 'wp-inci' ),
				'search_items'          => __( 'Search Ingredients', 'wp-inci' ),
				'not_found'             => __( 'No Ingredients found', 'wp-inci' ),
				'not_found_in_trash'    => __( 'No Ingredients found in trash', 'wp-inci' ),
				'parent_item_colon'     => __( 'Parent Ingredient:', 'wp-inci' ),
				'menu_name'             => __( 'Ingredients', 'wp-inci' ),
			];


			register_extended_post_type( 'ingredient', array(
				'publicly_queryable' => false,
				'menu_icon'          => 'dashicons-wi-menu',
				'rewrite'            => false,
				'labels'             => $ingredients_labels,
				'capability_type'    => 'page',
				'has_archive'        => false,
				'hierarchical'       => false,
				'show_in_rest'       => true,
				'block_editor'       => true,
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

			$functions_labels = [
				'name'                       => __( 'Functions', 'wp-inci' ),
				'singular_name'              => _x( 'Function', 'taxonomy general name', 'wp-inci' ),
				'search_items'               => __( 'Search Functions', 'wp-inci' ),
				'popular_items'              => __( 'Popular Functions', 'wp-inci' ),
				'all_items'                  => __( 'All Functions', 'wp-inci' ),
				'parent_item'                => __( 'Parent Function', 'wp-inci' ),
				'parent_item_colon'          => __( 'Parent Function:', 'wp-inci' ),
				'edit_item'                  => __( 'Edit Function', 'wp-inci' ),
				'update_item'                => __( 'Update Function', 'wp-inci' ),
				'view_item'                  => __( 'View Function', 'wp-inci' ),
				'add_new_item'               => __( 'Add New Function', 'wp-inci' ),
				'new_item_name'              => __( 'New Function', 'wp-inci' ),
				'separate_items_with_commas' => __( 'Separate Functions with commas', 'wp-inci' ),
				'add_or_remove_items'        => __( 'Add or remove Functions', 'wp-inci' ),
				'choose_from_most_used'      => __( 'Choose from the most used Functions', 'wp-inci' ),
				'not_found'                  => __( 'No Functions found.', 'wp-inci' ),
				'no_terms'                   => __( 'No Functions', 'wp-inci' ),
				'menu_name'                  => __( 'Functions', 'wp-inci' ),
				'items_list_navigation'      => __( 'Functions list navigation', 'wp-inci' ),
				'items_list'                 => __( 'Functions list', 'wp-inci' ),
				'most_used'                  => _x( 'Most Used', 'function', 'wp-inci' ),
				'back_to_items'              => __( '&larr; Back to Functions', 'wp-inci' ),
			];

			register_extended_taxonomy( 'functions', 'ingredient', array(
				'hierarchical' => false,
				'labels'       => $functions_labels,
				'public'       => false,
				'show_in_rest' => true,
				'rewrite'      => false,

			), array(

				'singular' => __( 'Function', 'wp-inci' ),
				'plural'   => __( 'Functions', 'wp-inci' ),
				'slug'     => __( 'functions', 'wp-inci' )

			) );

			$source_labels = [
				'name'                       => __( 'Sources', 'wp-inci' ),
				'singular_name'              => _x( 'Source', 'taxonomy general name', 'wp-inci' ),
				'search_items'               => __( 'Search Sources', 'wp-inci' ),
				'popular_items'              => __( 'Popular Sources', 'wp-inci' ),
				'all_items'                  => __( 'All Sources', 'wp-inci' ),
				'parent_item'                => __( 'Parent Source', 'wp-inci' ),
				'parent_item_colon'          => __( 'Parent Source:', 'wp-inci' ),
				'edit_item'                  => __( 'Edit Source', 'wp-inci' ),
				'update_item'                => __( 'Update Source', 'wp-inci' ),
				'view_item'                  => __( 'View Source', 'wp-inci' ),
				'add_new_item'               => __( 'Add New Source', 'wp-inci' ),
				'new_item_name'              => __( 'New Source', 'wp-inci' ),
				'separate_items_with_commas' => __( 'Separate Sources with commas', 'wp-inci' ),
				'add_or_remove_items'        => __( 'Add or remove Sources', 'wp-inci' ),
				'choose_from_most_used'      => __( 'Choose from the most used Sources', 'wp-inci' ),
				'not_found'                  => __( 'No Sources found.', 'wp-inci' ),
				'no_terms'                   => __( 'No Sources', 'wp-inci' ),
				'menu_name'                  => __( 'Sources', 'wp-inci' ),
				'items_list_navigation'      => __( 'Sources list navigation', 'wp-inci' ),
				'items_list'                 => __( 'Sources list', 'wp-inci' ),
				'most_used'                  => _x( 'Most Used', 'source', 'wp-inci' ),
				'back_to_items'              => __( '&larr; Back to Sources', 'wp-inci' ),
			];

			register_extended_taxonomy( 'source', 'ingredient', array(
				'public'       => false,
				'labels'       => $source_labels,
				'rewrite'      => false,
				'show_in_rest' => true,
				'hierarchical' => true,
				'admin_cols'   => array(
					'url' => array(
						'title'    => __( 'Url', 'wp-inci' ),
						'function' => function ( $term_id ) {
							$term = get_term_by( 'id', $term_id, 'source' );
							$url  = get_term_meta( $term_id, 'source_url',
								true );
							echo '<a href="' . $url . '" target="_blank">' . $term->name . ' &#x2197;</a>';
						},
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
		 * @param int|false $post_id
		 *
		 * @return string
		 */
		public function get_safety_html( $post_id ) {

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
		public function get_safety_value( $post_id ) {

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

			$product_labels = [
				'name'                  => __( 'Products', 'wp-inci' ),
				'singular_name'         => __( 'Product', 'wp-inci' ),
				'all_items'             => __( 'All Products', 'wp-inci' ),
				'archives'              => __( 'Product Archives', 'wp-inci' ),
				'attributes'            => __( 'Product Attributes', 'wp-inci' ),
				'insert_into_item'      => __( 'Insert into Product', 'wp-inci' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Product', 'wp-inci' ),
				'featured_image'        => _x( 'Featured Image', 'product', 'wp-inci' ),
				'set_featured_image'    => _x( 'Set featured image', 'product', 'wp-inci' ),
				'remove_featured_image' => _x( 'Remove featured image', 'product', 'wp-inci' ),
				'use_featured_image'    => _x( 'Use as featured image', 'product', 'wp-inci' ),
				'filter_items_list'     => __( 'Filter Products list', 'wp-inci' ),
				'items_list_navigation' => __( 'Products list navigation', 'wp-inci' ),
				'items_list'            => __( 'Products list', 'wp-inci' ),
				'new_item'              => __( 'New Product', 'wp-inci' ),
				'add_new'               => __( 'Add New', 'wp-inci' ),
				'add_new_item'          => __( 'Add New Product', 'wp-inci' ),
				'edit_item'             => __( 'Edit Product', 'wp-inci' ),
				'view_item'             => __( 'View Product', 'wp-inci' ),
				'view_items'            => __( 'View Products', 'wp-inci' ),
				'search_items'          => __( 'Search Products', 'wp-inci' ),
				'not_found'             => __( 'No Products found', 'wp-inci' ),
				'not_found_in_trash'    => __( 'No Products found in trash', 'wp-inci' ),
				'parent_item_colon'     => __( 'Parent Product:', 'wp-inci' ),
				'menu_name'             => __( 'Products', 'wp-inci' ),
			];

			register_extended_post_type( 'product', array(
				'dashboard_activity' => true,
				'menu_icon'          => 'dashicons-wi-menu',
				'labels'             => $product_labels,
				'capability_type'    => 'page',
				'show_in_feed'       => true,
				'show_in_rest'       => true,
				'block_editor'       => true,
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

			$brand_labels = [
				'name'                       => __( 'Brands', 'wp-inci' ),
				'singular_name'              => _x( 'Brand', 'taxonomy general name', 'wp-inci' ),
				'search_items'               => __( 'Search Brands', 'wp-inci' ),
				'popular_items'              => __( 'Popular Brands', 'wp-inci' ),
				'all_items'                  => __( 'All Brands', 'wp-inci' ),
				'parent_item'                => __( 'Parent Brand', 'wp-inci' ),
				'parent_item_colon'          => __( 'Parent Brand:', 'wp-inci' ),
				'edit_item'                  => __( 'Edit Brand', 'wp-inci' ),
				'update_item'                => __( 'Update Brand', 'wp-inci' ),
				'view_item'                  => __( 'View Brand', 'wp-inci' ),
				'add_new_item'               => __( 'Add New Brand', 'wp-inci' ),
				'new_item_name'              => __( 'New Brand', 'wp-inci' ),
				'separate_items_with_commas' => __( 'Separate Brands with commas', 'wp-inci' ),
				'add_or_remove_items'        => __( 'Add or remove Brands', 'wp-inci' ),
				'choose_from_most_used'      => __( 'Choose from the most used Brands', 'wp-inci' ),
				'not_found'                  => __( 'No Brands found.', 'wp-inci' ),
				'no_terms'                   => __( 'No Brands', 'wp-inci' ),
				'menu_name'                  => __( 'Brands', 'wp-inci' ),
				'items_list_navigation'      => __( 'Brands list navigation', 'wp-inci' ),
				'items_list'                 => __( 'Brands list', 'wp-inci' ),
				'most_used'                  => _x( 'Most Used', 'brand', 'wp-inci' ),
				'back_to_items'              => __( '&larr; Back to Brands', 'wp-inci' ),
			];

			register_extended_taxonomy( 'brand', 'product', array(
				'public'       => true,
				'rewrite'      => true,
				'hierarchical' => false,
				'show_in_rest' => false,
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
			return __( "The evaluation of these ingredients reflects the opinion of the author, who is not a specialist in this field. This evaluation is based on some online databases (e.g. <a title=\"CosIng - Cosmetic ingredients database\" href=\"https://ec.europa.eu/growth/sectors/cosmetics/cosing/\" target=\"_blank\">CosIng</a>).",
				'wp-inci' );
		}

		/**
		 * Sets the post updated messages for the `ingredient` post type.
		 *
		 * @param  array $messages Post updated messages.
		 * @return array Messages for the `ingredient` post type.
		 */
		public function ingredient_updated_messages( $messages ) {
			global $post;

			$permalink = get_permalink( $post );

			$messages['ingredient'] = [
				0  => '', // Unused. Messages start at index 1.
				/* translators: %s: post permalink */
				1  => sprintf( __( 'Ingredient updated. <a target="_blank" href="%s">View Ingredient</a>', 'wp-inci' ), esc_url( $permalink ) ),
				2  => __( 'Custom field updated.', 'wp-inci' ),
				3  => __( 'Custom field deleted.', 'wp-inci' ),
				4  => __( 'Ingredient updated.', 'wp-inci' ),
				/* translators: %s: date and time of the revision */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Ingredient restored to revision from %s', 'wp-inci' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				/* translators: %s: post permalink */
				6  => sprintf( __( 'Ingredient published. <a href="%s">View Ingredient</a>', 'wp-inci' ), esc_url( $permalink ) ),
				7  => __( 'Ingredient saved.', 'wp-inci' ),
				/* translators: %s: post permalink */
				8  => sprintf( __( 'Ingredient submitted. <a target="_blank" href="%s">Preview Ingredient</a>', 'wp-inci' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
				/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
				9  => sprintf( __( 'Ingredient scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Ingredient</a>', 'wp-inci' ), date_i18n( __( 'M j, Y @ G:i', 'wp-inci' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
				/* translators: %s: post permalink */
				10 => sprintf( __( 'Ingredient draft updated. <a target="_blank" href="%s">Preview Ingredient</a>', 'wp-inci' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			];

			return $messages;
		}

		/**
		 * Sets the bulk post updated messages for the `ingredient` post type.
		 *
		 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
		 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
		 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
		 * @return array Bulk messages for the `ingredient` post type.
		 */
		public function ingredient_bulk_updated_messages( $bulk_messages, $bulk_counts ) {

			$bulk_messages['ingredient'] = [
				/* translators: %s: Number of Ingredients. */
				'updated'   => _n( '%s Ingredient updated.', '%s Ingredients updated.', $bulk_counts['updated'], 'wp-inci' ),
				'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Ingredient not updated, somebody is editing it.', 'wp-inci' ) :
					/* translators: %s: Number of Ingredients. */
					_n( '%s Ingredient not updated, somebody is editing it.', '%s Ingredients not updated, somebody is editing them.', $bulk_counts['locked'], 'wp-inci' ),
				/* translators: %s: Number of Ingredients. */
				'deleted'   => _n( '%s Ingredient permanently deleted.', '%s Ingredients permanently deleted.', $bulk_counts['deleted'], 'wp-inci' ),
				/* translators: %s: Number of Ingredients. */
				'trashed'   => _n( '%s Ingredient moved to the Trash.', '%s Ingredients moved to the Trash.', $bulk_counts['trashed'], 'wp-inci' ),
				/* translators: %s: Number of Ingredients. */
				'untrashed' => _n( '%s Ingredient restored from the Trash.', '%s Ingredients restored from the Trash.', $bulk_counts['untrashed'], 'wp-inci' ),
			];

			return $bulk_messages;
		}

		/**
		 * Sets the post updated messages for the `product` post type.
		 *
		 * @param  array $messages Post updated messages.
		 * @return array Messages for the `product` post type.
		 */
		public function product_updated_messages( $messages ) {
			global $post;

			$permalink = get_permalink( $post );

			$messages['product'] = [
				0  => '', // Unused. Messages start at index 1.
				/* translators: %s: post permalink */
				1  => sprintf( __( 'Product updated. <a target="_blank" href="%s">View Product</a>', 'wp-inci' ), esc_url( $permalink ) ),
				2  => __( 'Custom field updated.', 'wp-inci' ),
				3  => __( 'Custom field deleted.', 'wp-inci' ),
				4  => __( 'Product updated.', 'wp-inci' ),
				/* translators: %s: date and time of the revision */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Product restored to revision from %s', 'wp-inci' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				/* translators: %s: post permalink */
				6  => sprintf( __( 'Product published. <a href="%s">View Product</a>', 'wp-inci' ), esc_url( $permalink ) ),
				7  => __( 'Product saved.', 'wp-inci' ),
				/* translators: %s: post permalink */
				8  => sprintf( __( 'Product submitted. <a target="_blank" href="%s">Preview Product</a>', 'wp-inci' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
				/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
				9  => sprintf( __( 'Product scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Product</a>', 'wp-inci' ), date_i18n( __( 'M j, Y @ G:i', 'wp-inci' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
				/* translators: %s: post permalink */
				10 => sprintf( __( 'Product draft updated. <a target="_blank" href="%s">Preview Product</a>', 'wp-inci' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			];

			return $messages;
		}

		/**
		 * Sets the bulk post updated messages for the `product` post type.
		 *
		 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
		 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
		 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
		 * @return array Bulk messages for the `product` post type.
		 */
		public function product_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
			global $post;

			$bulk_messages['product'] = [
				/* translators: %s: Number of Products. */
				'updated'   => _n( '%s Product updated.', '%s Products updated.', $bulk_counts['updated'], 'wp-inci' ),
				'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Product not updated, somebody is editing it.', 'wp-inci' ) :
					/* translators: %s: Number of Products. */
					_n( '%s Product not updated, somebody is editing it.', '%s Products not updated, somebody is editing them.', $bulk_counts['locked'], 'wp-inci' ),
				/* translators: %s: Number of Products. */
				'deleted'   => _n( '%s Product permanently deleted.', '%s Products permanently deleted.', $bulk_counts['deleted'], 'wp-inci' ),
				/* translators: %s: Number of Products. */
				'trashed'   => _n( '%s Product moved to the Trash.', '%s Products moved to the Trash.', $bulk_counts['trashed'], 'wp-inci' ),
				/* translators: %s: Number of Products. */
				'untrashed' => _n( '%s Product restored from the Trash.', '%s Products restored from the Trash.', $bulk_counts['untrashed'], 'wp-inci' ),
			];

			return $bulk_messages;
		}

		/**
		 * Sets the post updated messages for the `function` taxonomy.
		 *
		 * @param  array $messages Post updated messages.
		 * @return array Messages for the `function` taxonomy.
		 */
		public function function_updated_messages( $messages ) {

			$messages['function'] = [
				0 => '', // Unused. Messages start at index 1.
				1 => __( 'Function added.', 'wp-inci' ),
				2 => __( 'Function deleted.', 'wp-inci' ),
				3 => __( 'Function updated.', 'wp-inci' ),
				4 => __( 'Function not added.', 'wp-inci' ),
				5 => __( 'Function not updated.', 'wp-inci' ),
				6 => __( 'Functions deleted.', 'wp-inci' ),
			];

			return $messages;
		}

		/**
		 * Sets the post updated messages for the `source` taxonomy.
		 *
		 * @param  array $messages Post updated messages.
		 * @return array Messages for the `source` taxonomy.
		 */
		public function source_updated_messages( $messages ) {

			$messages['source'] = [
				0 => '', // Unused. Messages start at index 1.
				1 => __( 'Source added.', 'wp-inci' ),
				2 => __( 'Source deleted.', 'wp-inci' ),
				3 => __( 'Source updated.', 'wp-inci' ),
				4 => __( 'Source not added.', 'wp-inci' ),
				5 => __( 'Source not updated.', 'wp-inci' ),
				6 => __( 'Sources deleted.', 'wp-inci' ),
			];

			return $messages;
		}

		/**
		 * Sets the post updated messages for the `brand` taxonomy.
		 *
		 * @param  array $messages Post updated messages.
		 * @return array Messages for the `brand` taxonomy.
		 */
		public function brand_updated_messages( $messages ) {

			$messages['brand'] = [
				0 => '', // Unused. Messages start at index 1.
				1 => __( 'Brand added.', 'wp-inci' ),
				2 => __( 'Brand deleted.', 'wp-inci' ),
				3 => __( 'Brand updated.', 'wp-inci' ),
				4 => __( 'Brand not added.', 'wp-inci' ),
				5 => __( 'Brand not updated.', 'wp-inci' ),
				6 => __( 'Brands deleted.', 'wp-inci' ),
			];

			return $messages;
		}

	}

	add_action( 'plugins_loaded', array( 'WP_Inci', 'get_instance' ) );
}
