<?php
/**
 * WP INCI
 *
 * @category  Plugin
 * @package   Wpinci
 * @author    xlthlx <wp-inci@piccioni.london>
 * @copyright 2022 xlthlx (email: wp-inci at piccioni.london)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL 3
 * @link      https://wordpress.org/plugins/wp-inci/
 *
 * @wordpress-plugin
 * Plugin Name:       WP INCI
 * Plugin URI:        https://wordpress.org/plugins/wp-inci/
 * Description:       A WordPress plugin to manage INCI (International Nomenclature of Cosmetic Ingredients).
 * Version:           1.6.6
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            xlthlx
 * Author URI:        https://profiles.wordpress.org/xlthlx/
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

require_once WPINCI_BASE_PATH . 'vendor/autoload.php';
if ( ! defined( 'CMB2_VERSION' ) ) {
	require_once WPINCI_BASE_PATH . 'vendor/cmb2/cmb2/init.php';
}
require_once WPINCI_BASE_PATH . 'class-wp-inci.php';
require_once WPINCI_BASE_PATH . 'class-wp-inci-fields.php';

if ( is_admin() ) {
	include_once WPINCI_BASE_PATH . 'admin/class-wp-inci-admin.php';
	include_once WPINCI_BASE_PATH . 'admin/class-wp-inci-meta.php';
} else {
	include_once WPINCI_BASE_PATH . 'public/class-wp-inci-frontend.php';
}
