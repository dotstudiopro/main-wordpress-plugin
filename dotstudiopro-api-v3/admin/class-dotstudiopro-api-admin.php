<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/includes
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

    /**
     * Creating custom posttypes
     * 
     * @since 1.0.0 
     */
    
    public function create_dotstudiopro_post_types(){
        $labels = array(
            'name'                  => _x( 'Channels', 'Post Type General Name', 'dotstudiopro-api' ),
            'singular_name'         => _x( 'Channel', 'Post Type Singular Name', 'dotstudiopro-api' ),
            'menu_name'             => __( 'Channels', 'dotstudiopro-api' ),
            'name_admin_bar'        => __( 'Channels', 'dotstudiopro-api' ),
        );
        $args = array(
            'hierarchical'          => true,     
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true, 
            'show_in_menu'          => true, 
            'query_var'             => true,
            'rewrite'               => true,
            'capability_type'       => 'page',
            'has_archive'           => false, 
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-format-video',
            'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields' ),
        ); 
        register_post_type('channel', $args);
        
        $labels = array(
            'name'                  => _x( 'Categories', 'Post Type General Name', 'dotstudiopro-api' ),
            'singular_name'         => _x( 'Category', 'Post Type Singular Name', 'dotstudiopro-api' ),
            'menu_name'             => __( 'Categories', 'dotstudiopro-api' ),
            'name_admin_bar'        => __( 'Categories', 'dotstudiopro-api' ),
        );
        $args = array(
            'hierarchical'          => true,     
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true, 
            'show_in_menu'          => true, 
            'query_var'             => true,
            'rewrite'               => true,
            'capability_type'       => 'page',
            'has_archive'           => false, 
            'menu_position'         => 26,
            'menu_icon'             => 'dashicons-playlist-video',
            'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields' )
        ); 
        register_post_type('category', $args);
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        global $pagenow;
        $page = '';
        if (isset($_GET['page']))
            $page = $_GET['page'];
        if ($pagenow == 'admin.php' && $page == 'dsp-api-settings') {
            wp_enqueue_style($this->name, plugin_dir_url(__FILE__) . 'css/dsp-global.css', array(), $this->version, 'all');
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('custom-script-handle', plugin_dir_url(__FILE__) . 'js/custom-script.js', array('wp-color-picker'), false, true);
        }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function validate_dotstudiopro_api() {

        if (dsp_verify_nonce('activate_dotstudiopro_api_key')) {
            $this->activate_key();
        } elseif (dsp_verify_nonce('deactivate_dotstudiopro_api_key')) {
            $this->deactivate_key();
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

        $response = $this->dspExternalApiClass->get_token('token', $post);

        // ensure response is expected JSON array (not string)
        if (is_string($response)) {
            $response = new WP_Error('server_error', esc_html($response));
        }
        // error
        if (is_wp_error($response)) {
            $this->show_error($response);
            wp_safe_redirect(wp_get_referer());
        }
        // success
        if ($response['success'] == 1) {
            $response['message'] = 'The API key Activate successfully.';
            $this->add_admin_notice($response['message']);
            update_option('dotstudiopro_api_key', $_POST['dotstudiopro_api_key']);
            update_option('dotstudiopro_api_token', $response['token']);
            wp_safe_redirect(wp_get_referer());
        } else {
            $response['message'] = 'Something Went wrong.';
            $this->show_error($response['message']);
            wp_safe_redirect(wp_get_referer());
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
            return false;
        delete_option('dotstudiopro_api_key');
        delete_option('dotstudiopro_api_token');
        $message = 'The API key Deactivate successfully.';
        $this->add_admin_notice($message);
        wp_safe_redirect(wp_get_referer());
    }

    /**
     *  show_error
     *
     *  This function will show an error notice (only once)
     *  
     *  @since    1.0.0
     */

    function show_error($error = '') {

        // error object
        if (is_wp_error($error)) {
            $error = __('<b>Error</b>. Could not connect to update server', 'dotstudiopro-api') . ' <span class="description">(' . esc_html($error->get_error_message()) . ')</span>';
        }

        $this->add_admin_notice($error, 'error');
    }

}
