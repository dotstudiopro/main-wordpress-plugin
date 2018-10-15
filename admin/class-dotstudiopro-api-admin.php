<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/admin
 */
class Dotstudiopro_Api_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    The ID of this plugin.
     */
    private $name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($name, $version) {

        $this->name = $name;
        $this->version = $version;
        $this->dspExternalApiClass = new Dsp_External_Api_Request();
    }

    /**
     * Add admin notices
     *
     * @since   1.0.0
     */
    public function add_admin_notice($message) {

        $notices = get_transient('dsp_notice');
        if ($notices === false) {
            $new_notices[] = $message;
            set_transient('dsp_notice', $new_notices, 120);
        } else {
            $notices[] = $message;
            set_transient('dsp_notice', $notices, 120);
        }
    }

    /**
     * Show admin notices
     * @since   1.0.0
     */
    public function show_admin_notice() {

        $notices = get_transient('dsp_notice');
        if ($notices !== false) {
            foreach ($notices as $notice) {
                echo '<div class="update-nag"><p>' . $notice . '</p></div>';
            }

            delete_transient('dsp_notice');
        }
    }

    /**
     * Register the settings page
     *
     * @since    1.0.0
     */
    public function add_admin_menu() {
        add_menu_page('Dotstudiopro API Settings', __('Dotstudiopro API Settings', 'dotstudiopro-api'), 'manage_options', 'dsp-api-settings', array($this, 'create_admin_interface'), plugin_dir_url(__FILE__) . 'images/dsp.png');
    }

    /**
     * Callback function for the admin settings page.
     *
     * @since    1.0.0
     */
    public function create_admin_interface() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/admin-display.php';
    }

    /**
     * Creates our settings sections with fields etc.
     *
     * @since    1.0.0
     */
    public function settings_api_init() {
        /**
         * API key section and its setting fields
         * 
         * @since 1.0.0
         */
        add_settings_section(
                'dotstudiopro_api_key_section', '', null, 'dsp-api-key-section'
        );
        add_settings_field(
                'dsp_api_key_field', __('API key', 'dotstudiopro-api'), array($this, 'dsp_api_key_field_callback_function'), 'dsp-api-key-section', 'dotstudiopro_api_key_section'
        );
        register_setting('dsp-api-key-section', 'dsp_api_key_field');
        /**
         * Development mode Settings section and its setting fields
         * 
         * @since 1.0.0
         */
        add_settings_section(
                'dotstudiopro_api_dev_mode_section', '', null, 'dsp-dev-mode-section'
        );

        add_settings_field(
                'dsp_is_dev_mode_field', __('Development Mode', 'dotstudiopro-api'), array($this, 'dsp_is_dev_mode_field_callback_function'), 'dsp-dev-mode-section', 'dotstudiopro_api_dev_mode_section'
        );
        register_setting('dsp-dev-mode-section', 'dsp_is_dev_mode_field');

        add_settings_field(
                'dsp_country_code_field', __('Country Code', 'dotstudiopro-api'), array($this, 'dsp_country_code_field_callback_function'), 'dsp-dev-mode-section', 'dotstudiopro_api_dev_mode_section'
        );
        register_setting('dsp-dev-mode-section', 'dsp_country_code_field');

        add_settings_field(
                'dsp_reset_token_field', __('Reset token', 'dotstudiopro-api'), array($this, 'dsp_reset_token_field_callback_function'), 'dsp-dev-mode-section', 'dotstudiopro_api_dev_mode_section'
        );
        register_setting('dsp-dev-mode-section', 'dsp_reset_token_field');

        /**
         * General Settings section and its setting fields
         * 
         * @since 1.0.0
         */
        add_settings_section(
                'dotstudiopro_api_settings_section', '', null, 'dsp-setting-section'
        );

        add_settings_field(
                'dsp_video_autoplay_field', __('Video Autoplay on load', 'dotstudiopro-api'), array($this, 'dsp_video_autoplay_field_callback_function'), 'dsp-setting-section', 'dotstudiopro_api_settings_section'
        );
        register_setting('dsp-setting-section', 'dsp_video_autoplay_field');

        add_settings_field(
                'dsp_video_muteload_field', __('Video Mute on load', 'dotstudiopro-api'), array($this, 'dsp_video_muteload_field_callback_function'), 'dsp-setting-section', 'dotstudiopro_api_settings_section'
        );
        register_setting('dsp-setting-section', 'dsp_video_muteload_field');

        add_settings_field(
                'dsp_video_color_field', __('Video player color', 'dotstudiopro-api'), array($this, 'dsp_video_color_field_callback_function'), 'dsp-setting-section', 'dotstudiopro_api_settings_section'
        );
        register_setting('dsp-setting-section', 'dsp_video_color_field');

        add_settings_field(
                'dsp_enable_search_field', __('Enable search for videos and/or channels', 'dotstudiopro-api'), array($this, 'dsp_enable_search_field_callback_function'), 'dsp-setting-section', 'dotstudiopro_api_settings_section'
        );
        register_setting('dsp-setting-section', 'dsp_enable_search_field');

        /* Right now this field not in use */

        /* add_settings_field(
          'dsp_sync_data_field', __('Sync Data', 'dotstudiopro-api'), array($this, 'dsp_sync_data_field_callback_function'), 'dsp-setting-section', 'dotstudiopro_api_settings_section'
          );
          register_setting('dsp-setting-section', 'dsp_sync_data_field'); */
    }

    /**
     * Callback functions for settings
     */
    // API key configuration
    function dsp_api_key_field_callback_function() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/api/dsp_api_key_field.php';
    }

    // Development Mode
    function dsp_is_dev_mode_field_callback_function() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/development/dsp_is_dev_mode_field.php';
    }

    function dsp_country_code_field_callback_function() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/development/dsp_country_code_field.php';
    }

    function dsp_reset_token_field_callback_function() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/development/dsp_reset_token_field.php';
    }

    // Setting section    
    function dsp_video_autoplay_field_callback_function() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/settings/dsp_video_autoplay_field.php';
    }

    function dsp_video_muteload_field_callback_function() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/settings/dsp_video_muteload_field.php';
    }

    function dsp_video_color_field_callback_function() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/settings/dsp_video_color_field.php';
    }

    function dsp_enable_search_field_callback_function() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/settings/dsp_enable_search_field.php';
    }

    function dsp_sync_data_field_callback_function() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/settings/dsp_sync_data_field.php';
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style('vex', plugin_dir_url(__FILE__) . 'css/vex.css', array(), $this->version, 'all');
        wp_enqueue_style('vex-theme-plain', plugin_dir_url(__FILE__) . 'css/vex-theme-plain.css', array(), $this->version, 'all');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('vex-combined', plugin_dir_url(__FILE__) . 'js/vex.combined.min.js', array('wp-color-picker'), false, true);
        wp_enqueue_script('custom-script-handle', plugin_dir_url(__FILE__) . 'js/custom-script.js', array(), false, true);
        wp_localize_script('custom-script-handle', 'customVars', array('basedir' => plugin_dir_url(__DIR__), 'ajaxurl' => admin_url('admin-ajax.php')));
        wp_enqueue_style($this->name, plugin_dir_url(__FILE__) . 'css/dsp-global.css', array(), $this->version, 'all');
        wp_enqueue_style('fontawesome', 'http:////netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css', '', '4.0.3', 'all');
        wp_enqueue_style('tasg-inputes-css', plugin_dir_url(__FILE__) . 'css/bootstrap-tagsinput.css', array(), $this->version, 'all');
        wp_enqueue_script('tags-inpute-js', plugin_dir_url(__FILE__) . 'js/bootstrap-tagsinput.js', array(), false, true);
    }

    /**
     * reset token
     *
     * @since    1.0.0
     */
    public function reset_token() {
        if (wp_verify_nonce($_POST['nonce'], 'dsp_reset_token')) {
            $post = array(
                'key' => $_POST['api_secret'],
            );
            $response = $this->dspExternalApiClass->check_api_key($post);
            if (is_wp_error($response)) {
                $send_response = array('message' => 'Server Error : ' . $response->get_error_message());
                wp_send_json_error($send_response, 403);
            } elseif (isset($response['success']) && $response['success'] == 1) {
                update_option('dotstudiopro_api_token', $response['token']);
                update_option('dotstudiopro_api_token_time', time());
                $send_response = array('message' => 'Token has been updated.');
                wp_send_json_success($send_response, 200);
            } else {
                $send_response = array('message' => 'Internal Server Error');
                wp_send_json_error($send_response, 500);
            }
        } else {
            $send_response = array('message' => 'Internal Server Error');
            wp_send_json_error($send_response, 500);
        }
    }

    /**
     * Activate and Deactivate DoststudioPro Api key
     *
     * @since    1.0.0
     */
    public function validate_dotstudiopro_api() {
        if (dsp_verify_nonce('activate_dotstudiopro_api_key')) {
            $this->activate_key();
        } elseif (dsp_verify_nonce('deactivate_dotstudiopro_api_key')) {

            if ($_POST['btn_value'] == 'submit-api-data')
                $this->activate_key();
            else
                $this->deactivate_key();
        }
        else {
            $send_response = array('message' => 'Internal Server Error.');
            wp_send_json_error($send_response, 500);
        }
    }

    /**
     * Activate The Key Function.
     *
     * @since    1.0.0
     */
    public function activate_key() {

        $post = array(
            'key' => $_POST['dotstudiopro_api_key'],
        );

        $response = $this->dspExternalApiClass->check_api_key($post);

        // ensure response is expected JSON array (not string)
        if (is_string($response)) {
            $send_response = array('message' => 'Server Error : ' . $response);
            wp_send_json_error($send_response, 400);
        }
        // error
        if (is_wp_error($response)) {
            $send_response = array('message' => 'Server Error : ' . $response->get_error_message());
            wp_send_json_error($send_response, 403);
        }
        // success
        if (isset($response['success']) && $response['success'] == 1) {
            $response['message'] = 'The API key Activate successfully.';
            $this->add_admin_notice($response['message']);
            update_option('dotstudiopro_api_key', $_POST['dotstudiopro_api_key']);
            update_option('dotstudiopro_api_token', $response['token']);
            update_option('dotstudiopro_api_token_time', time());
            $send_response = array('message' => 'Api Key Activated Sucessfully.');
            wp_send_json_success($send_response, 200);
        } else {
            $send_response = array('message' => 'Internal Server Error.');
            wp_send_json_error($send_response, 500);
        }
    }

    /**
     * Deactivate The Key Function.
     *
     * @since    1.0.0
     */
    public function deactivate_key() {
        $dotstudiopro_api_key = get_option('dotstudiopro_api_key');
        if (!$dotstudiopro_api_key)
            wp_send_json_error(array('message' => 'Api Key Not Found..'), 404);
        
        $channels = get_pages( array( 'post_type' => 'channel') );
        foreach ( $channels as $channel ) {
            wp_delete_post($channel->ID, true);
        }
        
        $categories = get_pages( array( 'post_type' => 'category') );
        foreach ( $categories as $category ) {
            wp_delete_post($category->ID, true);
        }
        
        delete_option('dotstudiopro_api_key');
        delete_option('dotstudiopro_api_token');
        delete_option('dotstudiopro_api_token_time');
        $send_response = array('message' => 'The API key Deactivate successfully.');
        wp_send_json_success($send_response, 200);
    }

}
