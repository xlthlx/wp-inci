<?php
/**
 * WP INCI
 *
 * @package           wp-inci
 * @author            xlthlx
 * @copyright         wp-inci (email: wp-inci at piccioni.london)
 * @license           GPLv3+
 *
 * @wordpress-plugin
 * Plugin Name:       WP INCI
 * Plugin URI:        https://wordpress.org/plugins/wp-inci/
 * Description:       A WordPress plugin to manage INCI (International Nomenclature of Cosmetic Ingredients).
 * Version:           1.5.1
 * Requires at least: 5.2
 * Requires PHP:      7.4
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

define( 'WPINCI_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPINCI_BASE_URL', plugins_url() );

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/cmb2/cmb2/init.php';
require_once __DIR__ . '/class-wp-inci.php';
require_once __DIR__ . '/class-wp-inci-fields.php';

if ( is_admin() ) {
	require_once __DIR__ . '/admin/class-wp-inci-admin.php';
	require_once __DIR__ . '/admin/class-wp-inci-meta.php';
} else {
	require_once __DIR__ . '/public/class-wp-inci-frontend.php';
}

foreach ( glob( __DIR__ . "/blocks/*.php" ) as $filename ) {
	require_once $filename;
}