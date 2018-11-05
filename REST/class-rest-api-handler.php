<?php

/**
 * The file that defines the Custom REST API functions
 *
 * A class to provide a webhook connectivity for third party applications to post data to wordpress
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/REST
 */
class Dsp_REST_Api_Handler {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($name, $version) {
        $this->plugin_name = $name;
        $this->version = $version;
        $this->namespace = $this->plugin_name . '/v' . intval($this->version);
        $this->manageCategories = new Dsp_Manage_categories();
        $this->manageChannels = new Dsp_Manage_channels();
    }

    /**
     * Add the endpoints to the API
     * 
     * @since 1.0.0
     */
    public function dsp_webhook_routes() {

        // Category endpoints 

        register_rest_route($this->namespace, '/category/delete', [
            'methods' => WP_REST_Server::DELETABLE,
            'permission_callback' => $this->dsp_check_auth(),
            'callback' => array($this->manageCategories, 'delete_category'),
            'args' => $this->dsp_get_category_args('delete')
        ]);
        register_rest_route($this->namespace, '/category/update', [
            'methods' => WP_REST_Server::EDITABLE,
            'permission_callback' => $this->dsp_check_auth(),
            'callback' => array($this->manageCategories, 'manage_category'),
            'args' => $this->dsp_get_category_args('update')
        ]);
        register_rest_route($this->namespace, '/category/add', [
            'methods' => WP_REST_Server::CREATABLE,
            'permission_callback' => $this->dsp_check_auth(),
            'callback' => array($this->manageCategories, 'manage_category'),
            'args' => $this->dsp_get_category_args('add')
        ]);
        register_rest_route($this->namespace, '/category/order/update', [
            'methods' => WP_REST_Server::CREATABLE,
            'permission_callback' => $this->dsp_check_auth(),
            'callback' => array($this->manageCategories, 'order_category'),
            'args' => $this->dsp_get_category_args('order')
        ]);

        // Channel endpoints 

        register_rest_route($this->namespace, '/channel/delete', [
            'methods' => WP_REST_Server::DELETABLE,
            'permission_callback' => $this->dsp_check_auth(),
            'callback' => array($this->manageChannels, 'delete_channel'),
            'args' => $this->dsp_get_channel_args('delete')
        ]);
        register_rest_route($this->namespace, '/channel/update', [
            'methods' => WP_REST_Server::EDITABLE,
            'permission_callback' => $this->dsp_check_auth(),
            'callback' => array($this->manageChannels, 'manage_channel'),
            'args' => $this->dsp_get_channel_args('update')
        ]);
        register_rest_route($this->namespace, '/channel/add', [
            'methods' => WP_REST_Server::CREATABLE,
            'permission_callback' => $this->dsp_check_auth(),
            'callback' => array($this->manageChannels, 'manage_channel'),
            'args' => $this->dsp_get_channel_args('add')
        ]);
        register_rest_route($this->namespace, '/channel/order/update', [
            'methods' => WP_REST_Server::CREATABLE,
            'permission_callback' => $this->dsp_check_auth(),
            'callback' => array($this->manageChannels, 'order_channel'),
            'args' => $this->dsp_get_channel_args('order')
        ]);
    }

    /**
     * This function is used as a middleware for all the API 
     * (Right now this function is not in use because we don't need to add authentication on our API)
     * 
     * @since 1.0.0
     */
    private function dsp_check_auth() {

        return;

        /* $token = 'asdfghjkl';
          if($_SERVER['PHP_AUTH_USER'] != $token){
          $send_response = array('message' => 'unauthorized!!');
          wp_send_json_error($send_response, 401);
          } */
    }

    /**
     * This function is used to check the category api's arguments
     * 
     * @since 1.0.0
     * @param type $event
     * @return string
     */
    private function dsp_get_category_args($event = null) {

        $args = [];

        switch ($event):
            case 'add':
            case 'update':
                $args['_id'] = [
                    'required' => true,
                    'description' => esc_html__('New category ID.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['name'] = [
                    'required' => true,
                    'description' => esc_html__('New category Name.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['slug'] = [
                    'required' => true,
                    'description' => esc_html__('New category slug.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['description'] = [
                    'required' => false,
                    'description' => esc_html__('New category description.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['wallpaper'] = [
                    'required' => false,
                    'description' => esc_html__('New category wallpaper.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['poster'] = [
                    'required' => false,
                    'description' => esc_html__('New category poster.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['menu'] = [
                    'required' => false,
                    'description' => esc_html__('New category in menu (true/false).', 'dotstudiopro-api'),
                    'type' => 'boolean',
                ];
                $args['homepage'] = [
                    'required' => false,
                    'description' => esc_html__('New category on homepage (true/false).', 'dotstudiopro-api'),
                    'type' => 'boolean',
                ];
                break;
            case 'delete':
                $args['_id'] = [
                    'required' => true,
                    'description' => esc_html__('Pass the category ID which you would like to delete.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                break;
            case 'order':
                $args['categories'] = [
                    'required' => true,
                    'description' => esc_html__('Pass the categories object which you need to update ', 'dotstudiopro-api'),
                ];
                break;
        endswitch;

        return $args;
    }

    /**
     * This function is used to check the channel api's arguments
     * 
     * @since 1.0.0
     * @param type $event
     * @return string
     */
    function dsp_get_channel_args($event = null) {

        $args = [];

        switch ($event):
            case 'add':
            case 'update':
                $args['_id'] = [
                    'required' => true,
                    'description' => esc_html__('New channel ID.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['dspro_id'] = [
                    'required' => true,
                    'description' => esc_html__('Dotstudiopro Channel ID.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['title'] = [
                    'required' => true,
                    'description' => esc_html__('New channel Name.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['slug'] = [
                    'required' => true,
                    'description' => esc_html__('New channel slug.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['description'] = [
                    'required' => false,
                    'description' => esc_html__('New channel description.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['company_id'] = [
                    'required' => true,
                    'description' => esc_html__('New channel company_id.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['company_logo'] = [
                    'required' => true,
                    'description' => esc_html__('New channel Company logo.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['poster'] = [
                    'required' => false,
                    'description' => esc_html__('New channel poster.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['spotlight_poster'] = [
                    'required' => false,
                    'description' => esc_html__('New channel Spotlight poster.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['writers'] = [
                    'required' => false,
                    'description' => esc_html__('New channel writers.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['genres'] = [
                    'required' => false,
                    'description' => esc_html__('New channel genres.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['directors'] = [
                    'required' => false,
                    'description' => esc_html__('New channel directors.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['actors'] = [
                    'required' => false,
                    'description' => esc_html__('New channel actors.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['childchannels'] = [
                    'required' => false,
                    'description' => esc_html__('New channel childchannels.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['categories'] = [
                    'required' => false,
                    'description' => esc_html__('New channel categories.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['playlist'] = [
                    'required' => false,
                    'description' => esc_html__('New channel Playlist.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['video'] = [
                    'required' => false,
                    'description' => esc_html__('New channel video.', 'dotstudiopro-api'),
                    'type' => 'object',
                ];
                break;
            case 'delete':
                $args['_id'] = [
                    'required' => true,
                    'description' => esc_html__('Pass the channel ID which you would like to delete.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                break;
            case 'order':
                $args['channels'] = [
                    'required' => true,
                    'description' => esc_html__('Pass the channels object which you need to update ', 'dotstudiopro-api'),
                ];
                break;
        endswitch;
        return $args;
    }

}
