<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * @package           Dotstudiopro_Api
 *
 * @wordpress-plugin
 * Plugin Name:       dotstudioPRO API
 * Plugin URI:        https://www.dotstudiopro.com
 * Description:       This plugin provides a connector class to the dotstudioPRO API for use in plugin/theme development.
 * Version:           1.1.14
 * Author:            dotstudioPRO
 * Author URI:        http://www.dotstudiopro.com
 * License:           GPLv3
 * Text Domain:       dotstudiopro-api
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * A script/plugin that communicates with our WP Updater service to determine plugin updates
 */
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://updates.wordpress.dotstudiopro.com/wp-update-server/?action=get_metadata&slug=dspdev-main-plugin',
    __FILE__,
    'dspdev-main-plugin'
);

/**
 * Defining global variable for plugin basefile to use anywhere throughout the site
 */
if (!defined('DOTSTUDIOPRO_API_BASENAME')) {
    define('DOTSTUDIOPRO_API_BASENAME', plugin_basename(__FILE__));
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-dotstudiopro-api-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-dotstudiopro-api-deactivator.php';

/** This action is documented in includes/class-dotstudiopro-api-activator.php */
register_activation_hook(__FILE__, array('Dotstudiopro_Api_Activator', 'activate'));

/** This action is documented in includes/class-dotstudiopro-api-deactivator.php */
register_deactivation_hook(__FILE__, array('Dotstudiopro_Api_Deactivator', 'deactivate'));


/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-dotstudiopro-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Dotstudiopro_Api() {

    $plugin = new Dotstudiopro_Api();
    $plugin->run();
}

run_Dotstudiopro_Api();
function dsp_plugin_welcome_redirect() {
	$redirect = get_transient( 'dsp_plugin_welcome_redirect' );
	delete_transient( 'dsp_plugin_welcome_redirect' );
	$redirect && wp_redirect( admin_url( 'admin.php?page=dsp-api-settings' ) );
}

// Enables redirect on activation.
add_action( 'admin_init', 'dsp_plugin_welcome_redirect' );
