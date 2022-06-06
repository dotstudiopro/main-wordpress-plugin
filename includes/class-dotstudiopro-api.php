<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 *
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Dotstudiopro_Api
 * @subpackage Dotstudiopro_Api/includes
 */
class Dotstudiopro_Api {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Dotstudiopro_Api_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $Dotstudiopro_Api    The string used to uniquely identify this plugin.
     */
    protected $Dotstudiopro_Api;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->Dotstudiopro_Api = 'dotstudiopro-api';
        $this->version = '1.2.7';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Dotstudiopro_Api_Loader. Orchestrates the hooks of the plugin.
     * - Dotstudiopro_Api_i18n. Defines internationalization functionality.
     * - Dotstudiopro_Api_Admin. Defines all hooks for the dashboard.
     * - Dotstudiopro_Api_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-dotstudiopro-api-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-dotstudiopro-api-i18n.php';

        /**
         * The class responsible for DSP API Helper functions
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api-helper.php';

        /**
         * The class responsible for external API Request
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-dotstudiopro-external-api-requests.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-dotstudiopro-api-admin.php';

        /**
         * The class responsible for custom post type category and channel in the Dashboard.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-dotstudiopro-api-posttypes.php';

        /**
         * The class responsible for REST API
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'REST/class-rest-api-handler.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'REST/class-manage-categories.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'REST/class-manage-channels.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'REST/class-manage-videos.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'REST/class-manage-users.php';
        include_once './wp-admin/includes/user.php';

        $this->loader = new Dotstudiopro_Api_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Dotstudiopro_Api_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Dotstudiopro_Api_i18n();
        $plugin_i18n->set_domain($this->get_Dotstudiopro_Api());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Dotstudiopro_Api_Admin($this->get_Dotstudiopro_Api(), $this->get_version());
        $rest_api = new Dsp_REST_Api_Handler($this->get_Dotstudiopro_Api(), $this->get_version());
        $posttype = new Dsp_Custom_Posttypes();

        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('admin_notices', $plugin_admin, 'show_admin_notice');
        $this->loader->add_action('admin_init', $plugin_admin, 'settings_api_init');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_post_validate_dotstudiopro_api', $plugin_admin, 'validate_dotstudiopro_api');
        $this->loader->add_action('admin_post_nopriv_validate_dotstudiopro_api', $plugin_admin, 'validate_dotstudiopro_api');
        $this->loader->add_action('wp_ajax_reset_token', $plugin_admin, 'reset_token');
        $this->loader->add_action('wp_ajax_validate_dotstudiopro_api', $plugin_admin, 'validate_dotstudiopro_api');
        $this->loader->add_action('init', $posttype, 'create_dotstudiopro_post_types');
        $this->loader->add_action('add_meta_boxes', $posttype, 'create_custom_metabox');
        //$this->loader->add_action('save_post', $posttype, 'category_metabox_save'); // Right now this action is not in use.
        $this->loader->add_action('admin_head-edit.php', $posttype, 'add_button_to_custom_posttypes');
        $this->loader->add_filter('manage_category_posts_columns', $posttype, 'dsp_category_table_head');
        $this->loader->add_action('manage_category_posts_custom_column', $posttype, 'dsp_category_table_content', 10, 2);
        $this->loader->add_action('wp_ajax_import_category_post_data', $posttype, 'import_category_post_data');
        $this->loader->add_action('wp_ajax_import_channel_post_data', $posttype, 'import_channel_post_data');
        $this->loader->add_action('admin_menu', $posttype, 'remove_submenus');
        $this->loader->add_filter('manage_channel_posts_columns', $posttype, 'dsp_channel_table_head');
        $this->loader->add_action('manage_channel_posts_custom_column', $posttype, 'dsp_channel_table_content', 10, 2);
        $this->loader->add_action('rest_api_init', $rest_api, 'dsp_webhook_routes');
        // Add settings link in Plugins page
        $this->loader->add_filter('plugin_action_links', $plugin_admin, 'add_settings_link', 10, 2);
        // Import configs for company analytics on the player and pages if we don't already have it
        add_action('init', function() {
            $external_api = new Dsp_External_Api_Request();
            $analytics_options = get_option('dsp_analytics_parameters');
            if ($analytics_options) return;
            $configs = $external_api->get_analytics_config();
            if ($configs) {
                // Convert configs to an object, store in options table
                update_option('dsp_analytics_parameters', json_decode(json_encode($configs)));
            }
        });
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_Dotstudiopro_Api() {
        return $this->Dotstudiopro_Api;
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_Dotstudiopro_Video_Table() {
        global $wpdb;
        return $wpdb->prefix . 'videos';
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Dotstudiopro_Api_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
