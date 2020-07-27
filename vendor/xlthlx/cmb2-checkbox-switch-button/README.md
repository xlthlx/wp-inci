# CMB2 Checkbox Switch Button Field Type
Custom Checkbox Switch Button field type for CMB2 Metabox for WordPress.

## Installation
You can install it as a plugin, or include the main file into your theme or plugin folder.

## Usage:

```php
add_action( 'cmb2_admin_init', 'create_your_metabox' );
if(!function_exists('create_your_metabox')){
  function create_your_metabox(){
    $prefix = '_slug_';

    $cmb2_metabox = new_cmb2_box( array(
        'id'            => $prefix . 'test_metabox',
        'title'         => esc_html__( 'Test Metabox', 'tmv' ),
        'object_types'  => array( 'page'), // Post type
        'priority'   => 'high',
        'context'    => 'normal',
    ) );

    $cmb2_metabox->add_field( array(
        'name'             => esc_html__( 'Dynamically Load', 'text-domain' ),
        'id'               => $prefix . 'metabox_id',
        'desc'             => esc_html__('','text-domain'),
        'type'	           => 'switch',
        'default'          => 'on' //If it's checked by default 
    ) );
  }
}
```

* The usage in the template as same as CMB2 checkbox field type:

```php
$test_meta = get_post_meta($post->ID, '_slug_metabox_id', true);

if($test_meta){
  //Do something when it's checked;
}
```


## Screenshot:

<img src="https://github.com/xlthlx/cmb2-checkbox-switch-button/blob/master/screenshot.gif" width="250" />
