<?php
// This file is generated. Do not modify it manually.
return array(
	'wp-inci-product' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'wp-inci/product',
		'version' => '0.1.0',
		'title' => 'WP INCI Product',
		'category' => 'text',
		'description' => 'Show a Product into the content of any post type.',
		'keywords' => array(
			'product',
			'wp-inci'
		),
		'supports' => array(
			'html' => false,
			'customClassName' => false
		),
		'attributes' => array(
			'productId' => array(
				'type' => 'string',
				'default' => '0'
			),
			'productLink' => array(
				'type' => 'string'
			),
			'productContent' => array(
				'type' => 'string'
			),
			'customTitle' => array(
				'type' => 'string'
			),
			'ingredientsList' => array(
				'type' => 'string'
			),
			'ingredientsSafety' => array(
				'type' => 'string'
			),
			'disclaimer' => array(
				'type' => 'string'
			)
		),
		'textdomain' => 'wp-inci',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./../../../../public/css/wp-inci.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	)
);
