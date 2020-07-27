<?php
/**
 * WP INCI
 *
 * @package           wp-inci
 * @author            xlthlx
 * @copyright         2020 xlthlx (email: github at piccioni.london)
 * @license           GPLv3+
 *
 * @wordpress-plugin
 * Plugin Name:       WP INCI
 * Plugin URI:        https://github.com/xlthlx/wp-inci
 * Description:       A WordPress plugin to manage INCI (International Nomenclature of Cosmetic Ingredients).
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            xlthlx
 * Author URI:        https://piccioni.london
 * License:           GPLv3+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wp-inci
 * Domain Path:       /languages
 *
 * WP INCI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP INCI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP INCI. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
 */

/**
 * Extended CPTs library.
 */
if ( file_exists( __DIR__ . '/vendor/johnbillion/extended-cpts/extended-cpts.php' ) ) {
	require_once __DIR__ . '/vendor/johnbillion/extended-cpts/extended-cpts.php';
}

/**
 * CMB2 library.
 */
if ( file_exists( __DIR__ . '/vendor/cmb2/cmb2/init.php' ) ) {
	require_once __DIR__ . '/vendor/cmb2/cmb2/init.php';
}

/**
 * CMB2 custom field "input_search_ajax" library.
 */
if ( file_exists( __DIR__ . '/vendor/xlthlx/cmb2-field-input-search-ajax/cmb-field-input-search-ajax.php' ) ) {
	require_once __DIR__ . '/vendor/xlthlx/cmb2-field-input-search-ajax/cmb-field-input-search-ajax.php';
}

/**
 * CMB2 Switch Button library.
 */
if ( file_exists( __DIR__ . '/vendor/xlthlx/cmb2-checkbox-switch-button/cmb2-switch-button.php' ) ) {
	require_once __DIR__ . '/vendor/xlthlx/cmb2-checkbox-switch-button/cmb2-switch-button.php';
}

/**
 * Main plugin class.
 */
if ( file_exists( __DIR__ . '/class-wp-inci.php' ) ) {
	require_once __DIR__ . '/class-wp-inci.php';
}


if ( is_admin() ) {
	if ( file_exists( __DIR__ . '/admin/class-wp-inci-admin.php' ) ) {
		require_once __DIR__ . '/admin/class-wp-inci-admin.php';
	}
	if ( file_exists( __DIR__ . '/admin/class-wp-inci-meta.php' ) ) {
		require_once __DIR__ . '/admin/class-wp-inci-meta.php';
	}

} else if ( file_exists( __DIR__ . '/public/class-wp-inci-frontend.php' ) ) {
	require_once __DIR__ . '/public/class-wp-inci-frontend.php';
}
