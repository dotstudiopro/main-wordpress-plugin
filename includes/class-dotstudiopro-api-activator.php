<?php

/**
 * Fired during plugin activation
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Dotstudiopro_Api
 * @subpackage Dotstudiopro_Api/includes
 */
class Dotstudiopro_Api_Activator {

    /**
     * Flush rewrite rules
     * @since    1.0.0
     */
    public static function activate() {
        //flush rewrite rules. Just to make sure our rewrite rules from an earlier activation are applied again!
        flush_rewrite_rules();
        //would want to use flush_rewrite_rules only but that does not work for some reason??
        delete_option('rewrite_rules');

        $dsp = new Dotstudiopro_Api();
        $dsp_admin = new Dotstudiopro_Api_Admin($dsp->get_Dotstudiopro_Api(), $dsp->get_version());
        //$dsp_rest = new Dsp_REST_Api_Handler($dsp->get_Dotstudiopro_Api(), $dsp->get_version());

        $message = sprintf(wp_kses(__('You need to enter your API Key in order to use its features <a href="%s">Do so here.</a>', 'dotstudiopro-api'), array('a' => array('href' => array()))), esc_url(admin_url() . 'admin.php?page=dsp-api-settings'));

        $dsp_admin->add_admin_notice($message);
    }

}
