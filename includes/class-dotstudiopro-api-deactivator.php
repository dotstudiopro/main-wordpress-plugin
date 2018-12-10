<?php

/**
 * Fired during plugin deactivation
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Dotstudiopro_Api
 * @subpackage Dotstudiopro_Api/includes
 */
class Dotstudiopro_Api_Deactivator {

    /**
     * The function which handles deactivation of our plugin
     *
     * @since    1.0.0
     */
    public static function deactivate() {

        // we need to deactivate the dotstudioPRO Subsctiption plugin first if exits in the site.
        if (class_exists('Dotstudiopro_Subscription')) {
            wp_die('Sorry, but this plugin requires the "dotstudioPRO Subsctiption" plugin to be Deactive first. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
        }
        //flush rewrite rules. Don't want no lingering stuff!
        flush_rewrite_rules();
        //would want to use flush_rewrite_rules only but that does not work for some reason??
        delete_option('rewrite_rules');
    }

}
