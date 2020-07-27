CMB2 custom field "input_search_ajax"
==================

Custom field for [CMB2](https://github.com/WebDevStudios/CMB2) to attach posts to each other. Based on [CMB2 custom field "post_search_ajax"](https://github.com/alexis-magina/cmb2-field-post-search-ajax).

Same approach than [CMB2 Attached Posts Field](https://github.com/WebDevStudios/cmb2-attached-posts/) with Ajax request, multiple/single option, and different UI.

## Installation

You can install this field type as a WordPress plugin:

- Download the plugin
- Place the plugin folder in your /wp-content/plugins/ directory
- Activate the plugin in the Plugin dashboard

With Composer:

```
composer require xlthlx/cmb2-field-input-search-ajax
```

## Usage
### Backend

Follow the example in [`example-field-setup.php`](https://github.com/xlthlx/cmb2-field-input-search-ajax/blob/master/example-field-setup.php).

Options: 
- limit (int, default = 1 : single selection) : limit the number of posts that can be selected
- sortable (bool, default = false) : Allow selected posts to be sort
- query_args (array) : setup the ajax search query : pass a wp_query args array.

Filter:
Ajax results can be filtered to customize returned text and posts values.
Use filter "cmb_input_search_ajax_result", for example:
```
function example_callback( $arr ) {
	// $arr['data'] : contains post_id
	// $arr['guid'] : contains admin edit post url
	// $arr['value'] : contains post title
	$arr['value'] = 'Custom string ' . $arr['value'];

	return $arr;
}

add_filter( 'cmb_input_search_ajax_result', 'example_callback' );
```

### Frontend

You can retrieve the metadata using get_post_meta( get_the_ID(), 'your_field_id', true ); 

If field limit > 1, this will return an array of attached post IDs.
If field limit == 1, this will return only the single attached post ID.

## Screenshot

![example](https://github.com/xlthlx/cmb2-field-input-search-ajax/blob/master/screenshot.gif)

## Changelog

### 1.0.0
* Initial commit
