# ![WP INCI](https://github.com/xlthlx/wp-inci/blob/main/img/banner.png "WordPress Plugin")

[![Version](https://img.shields.io/badge/version-1.5.2-blueviolet)](https://plugintests.com/plugins/wporg/wp-inci/latest) [![License](https://img.shields.io/badge/license-GPL_v3%2B-blueviolet)](https://github.com/xlthlx/wp-inci/blob/main/LICENSE)
![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/wp-inci?color=blueviolet)  ![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/xlthlx/wp-inci/main) 

**WP INCI** - A WordPress plugin to manage INCI (International Nomenclature of Cosmetic Ingredients).

You can set up your database of ingredients and products and easily insert a product table into posts and pages using a shortcode.
There is an example product with ingredients into the [`data`](https://github.com/xlthlx/wp-inci/tree/main/data) directory that can be imported using the standard WordPress Importer.

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
    * remove the safety from ingredients listing
  
      Example: `[wp_inci_product id="33591" safety="false"]`

* Languages: English, Italian.

## Screenshots

1. Ingredients list and single ingredient

![Ingredients list and single ingredient](https://github.com/xlthlx/wp-inci/blob/main/img/screenshot-1.gif)

2. Product list and single product

![Product list and single product](https://github.com/xlthlx/wp-inci/blob/main/img/screenshot-2.gif)

3. How to manage options

![How to manage options](https://github.com/xlthlx/wp-inci/blob/main/img/screenshot-3.gif)

4. How to use the product shortcode

![How to use the product shortcode](https://github.com/xlthlx/wp-inci/blob/main/img/screenshot-4.gif)

5. Post example

![Post example](https://github.com/xlthlx/wp-inci/blob/main/img/screenshot-5.gif)

## Installation

1. Upload `wp-inci` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
4. Enjoy

## Credits
* [CMB2](https://en-gb.wordpress.org/plugins/cmb2/) by [CMB2 team](https://cmb2.io/)
* [Extended CPTs](https://github.com/johnbillion/extended-cpts) by [John Blackbourn](https://johnblackbourn.com/)
* [Carbon Fields](https://github.com/htmlburger/carbon-fields) by [htmlBurger](https://htmlburger.com/)

## Frequently Asked Questions

### Can I translate the plugin interface?
Yes, just edit .POT file in the [`languages`](https://github.com/xlthlx/wp-inci/tree/main/languages) folder.
