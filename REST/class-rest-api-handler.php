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
        $this->namespace = $this->plugin_name . '/v1';
        $this->manageCategories = new Dsp_Manage_categories();
        $this->manageChannels = new Dsp_Manage_channels();
        $this->manageVideos = new Dsp_Manage_videos();
        $this->manageUsers = new Dsp_Manage_users();
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
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageCategories, 'delete_category'),
            'args' => $this->dsp_get_category_args('delete')
        ]);
        register_rest_route($this->namespace, '/category/update', [
            'methods' => WP_REST_Server::EDITABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageCategories, 'manage_category'),
            'args' => $this->dsp_get_category_args('update')
        ]);
        register_rest_route($this->namespace, '/category/add', [
            'methods' => WP_REST_Server::CREATABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageCategories, 'manage_category'),
            'args' => $this->dsp_get_category_args('add')
        ]);
        register_rest_route($this->namespace, '/category/order/update', [
            'methods' => WP_REST_Server::CREATABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageCategories, 'order_category'),
            'args' => $this->dsp_get_category_args('order')
        ]);

        // Channel endpoints

        register_rest_route($this->namespace, '/channel/delete', [
            'methods' => WP_REST_Server::DELETABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageChannels, 'delete_channel'),
            'args' => $this->dsp_get_channel_args('delete')
        ]);
        register_rest_route($this->namespace, '/channel/update', [
            'methods' => WP_REST_Server::EDITABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageChannels, 'manage_channel'),
            'args' => $this->dsp_get_channel_args('update')
        ]);
        register_rest_route($this->namespace, '/channel/add', [
            'methods' => WP_REST_Server::CREATABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageChannels, 'manage_channel'),
            'args' => $this->dsp_get_channel_args('add')
        ]);
        register_rest_route($this->namespace, '/channel/order/update', [
            'methods' => WP_REST_Server::CREATABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageChannels, 'order_channel'),
            'args' => $this->dsp_get_channel_args('order')
        ]);
        register_rest_route($this->namespace, '/all/channel/update', [
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageChannels, 'update_all_channel'),
        ]);

        // Video endpoints

        register_rest_route($this->namespace, '/video/update', [
            'methods' => WP_REST_Server::EDITABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageVideos, 'manage_videos'),
            'args' => $this->dsp_get_video_args('update')
        ]);

        register_rest_route($this->namespace, '/video/add', [
            'methods' => WP_REST_Server::CREATABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageVideos, 'manage_videos'),
            'args' => $this->dsp_get_video_args('add')
        ]);

        // User endpoints

        register_rest_route($this->namespace, '/user/delete', [
            'methods' => WP_REST_Server::DELETABLE,
            'permission_callback' => array($this, 'dsp_check_auth'),
            'callback' => array($this->manageUsers, 'delete_user'),
            'args' => $this->dsp_get_user_args('delete')
        ]);

    }

    /**
     * This function is used as a middleware for all the API
     * (Right now this function is not in use because we don't need to add authentication on our API)
     *
     * @since 1.0.0
     */
    public function dsp_check_auth(WP_REST_Request $request) {

        if (empty($_SERVER['HTTP_X_AUTH_CHECK'])) return false; // Nothing to check, request is bad

        $public_key = "-----BEGIN PUBLIC KEY-----\nMIICIDANBgkqhkiG9w0BAQEFAAOCAg0AMIICCAKCAgEAzEBUD7nRTsD/Hx34GkXt\niIOhTsOLhct+iSWrVY21HZF+uwN+WNNwTP5Y0wgxiJciovOSgykqah1E5/1qQYrk\nwFRcy89IiXQ3nF/1c5dI4hBVFH628l0yZG6sqmwLK+jv7mIBorSAQh9J0I4uj9oe\nswr22AEbOJCJpbs4Evx902R60n3kIKAgf/24UnV0o9lwDRCApS1DpC4Q02fx2ZFr\nQMjBsX2/WQ0ECkk8x9K17GKRGtZGWZ5Zr0uZ/j4vS+zr09DlABL4fjCW7UIEaTn6\nj/Tn+k4aurxm5YTzoy6sZzoep/b3Goqpmtau7wNks7P7r0xX2uZkFN/ZSNLaj/l+\noy5Aw+kZGcTpHGHEL3x8nxz05bscoD6+diV+T9/K4KkMhaEZxWr7cRPMoL4NCu9n\nY0ijhg8l58lOE3CgfSFYXiEpGr2PUnW2UANhsAscauGn4oyq85/hjD1AUgdKbKTO\nLzVR6ERI2GmjV6RL+WS1sLtOqWCVuyFLVp3FwtaF4y8ywxc7IiDhmYADB5WwDaDC\nGdecPcOKMHBvKFeyaazOnmtyn8K11AQlWS5OZGhNgIh72R40XNKdyUpr3eixlYjO\nmEdmXKe6xQ/See8uJpFqI8L/Gi6aULWBJkvgg6Ud9qiNkxeOhXRbJl4IVA+spc8G\nVK7TNfoGhOrz/7TWtE993hECAQM=\n-----END PUBLIC KEY-----";

        $signature = base64_decode($_SERVER['HTTP_X_AUTH_CHECK']);

        $data =  $request->get_body();

        $ok = openssl_verify($data, $signature, $public_key, OPENSSL_ALGO_SHA256);
        return $ok == 1;
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
                $args['category']['_id'] = [
                    'required' => true,
                    'description' => esc_html__('New category ID.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['category']['name'] = [
                    'required' => true,
                    'description' => esc_html__('New category Name.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['category']['slug'] = [
                    'required' => true,
                    'description' => esc_html__('New category slug.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['category']['description'] = [
                    'required' => false,
                    'description' => esc_html__('New category description.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['category']['wallpaper'] = [
                    'required' => false,
                    'description' => esc_html__('New category wallpaper.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['category']['poster'] = [
                    'required' => false,
                    'description' => esc_html__('New category poster.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['category']['menu'] = [
                    'required' => false,
                    'description' => esc_html__('New category in menu (true/false).', 'dotstudiopro-api'),
                    'type' => 'boolean',
                ];
                $args['category']['homepage'] = [
                    'required' => false,
                    'description' => esc_html__('New category on homepage (true/false).', 'dotstudiopro-api'),
                    'type' => 'boolean',
                ];
                break;
            case 'delete':
                $args['category']['_id'] = [
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
    private function dsp_get_channel_args($event = null) {

        $args = [];

        switch ($event):
            case 'add':
                $args['channel']['_id'] = [
                    'required' => true,
                    'description' => esc_html__('New channel ID.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['dspro_id'] = [
                    'required' => true,
                    'description' => esc_html__('Dotstudiopro Channel ID.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['title'] = [
                    'required' => true,
                    'description' => esc_html__('New channel Name.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['slug'] = [
                    'required' => true,
                    'description' => esc_html__('New channel slug.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['description'] = [
                    'required' => false,
                    'description' => esc_html__('New channel description.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['company_id'] = [
                    'required' => true,
                    'description' => esc_html__('New channel company_id.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['company_logo'] = [
                    'required' => true,
                    'description' => esc_html__('New channel Company logo.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['poster'] = [
                    'required' => false,
                    'description' => esc_html__('New channel poster.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['spotlight_poster'] = [
                    'required' => false,
                    'description' => esc_html__('New channel Spotlight poster.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['wallpaper'] = [
                    'required' => false,
                    'description' => esc_html__('New channel wallpaper.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                $args['channel']['writers'] = [
                    'required' => false,
                    'description' => esc_html__('New channel writers.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['genres'] = [
                    'required' => false,
                    'description' => esc_html__('New channel genres.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['directors'] = [
                    'required' => false,
                    'description' => esc_html__('New channel directors.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['actors'] = [
                    'required' => false,
                    'description' => esc_html__('New channel actors.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['childchannels'] = [
                    'required' => false,
                    'description' => esc_html__('New channel childchannels.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['categories'] = [
                    'required' => false,
                    'description' => esc_html__('New channel categories.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['playlist'] = [
                    'required' => false,
                    'description' => esc_html__('New channel Playlist.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['video'] = [
                    'required' => false,
                    'description' => esc_html__('New channel video.', 'dotstudiopro-api'),
                    'type' => 'object',
                ];
                $args['channel']['geo'] = [
                    'required' => false,
                    'description' => esc_html__('New channel available countries.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['is_product'] = [
                    'required' => false,
                    'description' => esc_html__('New channel is in subscription?.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['year'] = [
                    'required' => false,
                    'description' => esc_html__('New channel publisher year.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                $args['channel']['language'] = [
                    'required' => false,
                    'description' => esc_html__('New channel language.', 'dotstudiopro-api'),
                    'type' => 'array',
                ];
                break;
            case 'update':
                $args['channels'] = [
                    'required' => true,
                    'description' => esc_html__('The channel info we are updating.', 'dotstudiopro-api'),
                    'type' => 'array',
                    'items' => array(
                        'type' => 'object'
                    )
                ];
                break;
            case 'delete':
                $args['channel']['_id'] = [
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

    /**
     * This function is used to check the videos api's arguments
     *
     * @since 1.0.0
     * @param type $event
     * @return string
     */
    private function dsp_get_video_args($event = null) {

        $args = [];

        switch ($event):
            case 'add':
                $args['video']['_id'] = [
                    'required' => true,
                    'description' => esc_html__('Video ID to update.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                break;
            case 'update':
                $args['video']['_id'] = [
                    'required' => true,
                    'description' => esc_html__('Video ID to update.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                break;
        endswitch;
        return $args;
    }

    /**
     * This function is used to check the channel api's arguments
     *
     * @since 1.6.0
     * @param type $event
     * @return string
     */
    private function dsp_get_user_args($event = null) {

        $args = [];

        switch ($event):
            case 'delete':
                $args['email'] = [
                    'required' => true,
                    'description' => esc_html__('Pass the email ID which you would like to delete.', 'dotstudiopro-api'),
                    'type' => 'string',
                ];
                break;
        endswitch;
        return $args;
    }

}
