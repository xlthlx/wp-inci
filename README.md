# ![WP INCI](https://github.com/xlthlx/wp-inci/blob/master/img/banner.png "WordPress Plugin")

**WP INCI** - A WordPress plugin to manage INCI (International Nomenclature of Cosmetic Ingredients).

[![Version](https://img.shields.io/badge/version-1.1.2-blueviolet)](https://plugintests.com/plugins/wporg/wp-inci/latest) [![License](https://img.shields.io/badge/license-GPL_v3%2B-blueviolet)](https://github.com/xlthlx/wp-inci/blob/master/LICENSE)
![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/wp-inci?color=blueviolet) ![WordPress Plugin: Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/wp-inci?color=blueviolet) ![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/xlthlx/wp-inci/master?color=blueviolet) 
[![WP compatibility](https://plugintests.com/plugins/wporg/wp-inci/wp-badge.svg)](https://plugintests.com/plugins/wporg/wp-inci/latest) [![PHP compatibility](https://plugintests.com/plugins/wporg/wp-inci/php-badge.svg)](https://plugintests.com/plugins/wporg/wp-inci/latest)

You can set up your database of ingredients and products and easily insert a product table into posts and pages using a shortcode.
There is an example product with ingredients into the [`data`](https://github.com/xlthlx/wp-inci/tree/master/data) directory that can be imported using the standard WordPress Importer.

## Plugin Features

* Custom Post Type Ingredient: it comes with a function list, a source list and a visual safety field.
* Custom Post Type Product: it comes with a brand taxonomy.
* Single and multiple search for ingredients: check the ingredient against the local database.
* Options: possibility to exclude the default CSS, copy it into your style.css and customize it; change the disclaimer content.
* Shortcode: in the product list, there is a column where you can copy the 'basic' shortcode relative to a specific product.
If you need a different way to display it, you can:

    * specify a different title
    Example: `[wp_inci_product id="33591" title="My custom title"]`
    * automatically insert the product permalink
    Example: `[wp_inci_product id="33591" link="true"]`
    * remove the ingredients listing
    Example: `[wp_inci_product id="33591" link="true" list="false"]`

* Languages: English, Italian.

## Screenshots

1. Ingredients list and single ingredient

![Ingredients list and single ingredient](https://github.com/xlthlx/wp-inci/blob/master/img/screenshot-1.gif)

2. Product list and single product

![Product list and single product](https://github.com/xlthlx/wp-inci/blob/master/img/screenshot-2.gif)

3. How to manage options

![How to manage options](https://github.com/xlthlx/wp-inci/blob/master/img/screenshot-3.gif)

4. How to use the product shortcode

![How to use the product shortcode](https://github.com/xlthlx/wp-inci/blob/master/img/screenshot-4.gif)

5. Post example

![Post example](https://github.com/xlthlx/wp-inci/blob/master/img/screenshot-5.gif)

### Changelog

#### 1.1.2
* Bugfix

#### 1.1.1
* Changed admin icon
* Support for blocks

#### 1.1.0
* New: ingredients multiple search
* New: gets the ingredient from the CosIng API if not found on search

#### 1.0.2
* Fixed CSS
* Added safety to ingredients list

#### 1.0.1
* Tested up to 5.5
* Bugfix

#### 1.0
* First release

## Installation

1. Upload `wp-inci` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
4. Enjoy

## Credits
* [CMB2](https://en-gb.wordpress.org/plugins/cmb2/) by [CMB2 team](https://cmb2.io/)
* [Extended CPTs](https://github.com/johnbillion/extended-cpts) by [John Blackbourn](https://johnblackbourn.com/)

## Frequently Asked Questions

### Can I translate the plugin interface?
Yes, just edit .POT file in the [`languages`](https://github.com/xlthlx/wp-inci/tree/master/languages) folder.
