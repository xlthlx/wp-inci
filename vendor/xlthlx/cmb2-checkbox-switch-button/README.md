# CMB2 Checkbox Switch Button Field Type
Custom Checkbox Switch Button field type for CMB2 Metabox for WordPress. Based on [CMB2 Switch Button Field Type](https://github.com/themevan/CMB2-Switch-Button).

## Installation
You can install this field type as a WordPress plugin:

- Download the plugin
- Place the plugin folder in your /wp-content/plugins/ directory
- Activate the plugin in the Plugin dashboard

With Composer:

```
composer require xlthlx/cmb2-checkbox-switch-button
```

## Usage:

```php
add_action( 'cmb2_admin_init', 'create_your_metabox' );

if ( ! function_exists( 'create_your_metabox' ) ) {
	function create_your_metabox() {
		$prefix = 'your_slug_';
		$text_domain = 'your-text-domain';
		
		$cmb2_metabox = new_cmb2_box( array(
			'id'           => $prefix . 'test_metabox',
			'title'        => esc_html__( 'Test Metabox', $text_domain ),
			'object_types' => array( 'page' ), // Post type
			'priority'     => 'high',
			'context'      => 'normal',
		) );

		$cmb2_metabox->add_field( array(
			'name'    => esc_html__( 'Dynamically Load', $text_domain ),
			'id'      => $prefix . 'metabox_id',
			'desc'    => esc_html__( '', $text_domain ),
			'type'    => 'switch',
			'default' => 'on' //If it's checked by default 
		) );
	}
}
```

* The usage in the template as same as CMB2 checkbox field type:

```php
$test_meta = get_post_meta( $post->ID, '_slug_metabox_id', true );

if ( $test_meta ) {
	// The field is checked.
}
```


## Screenshot:

<img src="https://github.com/xlthlx/cmb2-checkbox-switch-button/blob/master/screenshot.gif" width="250" />
