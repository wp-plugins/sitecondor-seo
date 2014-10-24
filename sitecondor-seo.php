<?php
/**
 *
 * @package   SiteCondor_SEO
 * @author    Sebastián Brocher <seb@sitecondor.com> and Judd Lyon <judd@sitecondor.com>
 * @license   GPL-2.0+
 * @link      https://www.sitecondor.com/wordpress-plugin
 * @copyright 2014 Noctual, LLC
 *
 * @wordpress-plugin
 * Plugin Name:       SiteCondor SEO
 * Plugin URI:        https://www.sitecondor.com/wordpress-plugin
 * Description:       SiteCondor SEO plugin
 * Version:           1.1.0
 * Author:            Sebastián Brocher and Judd Lyon
 * Author URI:        http://blog.sitecondor.com
 * Text Domain:       sitecondor-seo
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-sitecondor-seo.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'SiteCondor_SEO', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SiteCondor_SEO', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'SiteCondor_SEO', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/admin.php' );

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-sitecondor-seo-admin.php' );

	add_action( 'plugins_loaded', array( 'SiteCondor_SEO_Admin', 'get_instance' ) );

}
