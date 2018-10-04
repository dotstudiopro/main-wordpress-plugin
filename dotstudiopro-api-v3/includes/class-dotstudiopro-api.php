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
        $this->version = '1.0.0';

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
         * DSP API Helper functions
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api-helper.php';

        /**
         *  External API Request
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-dotstudiopro-external-api-requests.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-dotstudiopro-api-admin.php';

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
        $plugin_api = new Dsp_External_Api_Request();

        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('admin_notices', $plugin_admin, 'show_admin_notice');
        $this->loader->add_action('admin_init', $plugin_admin, 'settings_api_init');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_post_validate_dotstudiopro_api', $plugin_admin, 'validate_dotstudiopro_api');
        $this->loader->add_action('admin_post_nopriv_validate_dotstudiopro_api', $plugin_admin, 'validate_dotstudiopro_api');
        $this->loader->add_action('init', $plugin_admin, 'create_dotstudiopro_post_types');
        $this->loader->add_action('wp_ajax_reset_token', $plugin_admin, 'reset_token');
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
