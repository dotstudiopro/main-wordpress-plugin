<?php

/**
 * Register all Request for external Aips
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 *
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/includes
 */
class Dsp_External_Api_Request {

    public $country;
    public $api_key;
    public $token;
    public $common_url;

    function __construct() {

        $this->api_key = get_option('dotstudiopro_api_key');
        $this->common_url = get_option('dsp_api_url_field') ?: "https://api.myspotlight.tv/";

    }

    /**
     * Check Api key is valid or not
     *
     * @return String|Boolean The API access token, or false if it couldn't get one
     * @since 1.0.0
     */
    function check_api_key($body = null) {
        return $this->api_request_post('token', null, null, $body);
    }

    /**
     * Get an access token from the API
     *
     * @return String|Boolean The API access token, or false if it couldn't get one
     */
    private function get_token() {
        // If we don't have an api key, we can't get a token
        if (empty($this->api_key))
            return false;
        $body = array(
            'key' => $this->api_key
        );
        return $this->api_request_post('token', null, null, $body);
    }

    /**
     * Set the token variable if we have the value outside of the class
     *
     * @param string $token The token to set
     *
     * @return String|Boolean Returns the 2 letter country code, or false if there was an issue
     */
    private function set_token($token) {
        $this->token = $token;
    }

    /**
     * Get a new token from the API key we have
     *
     * @return void
     */
    function api_new_token() {
        // Acquire an API token and save it for later use.
        $token = $this->get_token();
        update_option('dotstudiopro_api_token', $token['token']);
        update_option('dotstudiopro_api_token_time', time());
        return $token['token'];
    }

    /**
     * Check if we have a token and if it is expired, and get a new one if expired or missing
     *
     * @return String|Bool The access token or false if something went wrong
     */
    function api_token_check() {
        $token = get_option('dotstudiopro_api_token');
        $token_time = !$token ? 0 : get_option('dotstudiopro_api_token_time');
        $difference = floor((time() - $token_time) / 84600);
        if (!$token || $difference >= 25) {
            $token = $this->api_new_token();
            if (empty($token))
                return false;
        }
        return $token;
    }



    /**
     * Get the company analytics config for use on player/page analytics
     *
     * @return Object|Boolean Returns company analytics config object, or false if there was an issue
     */
    function get_analytics_config() {
        $token = $this->api_token_check();

        // If we don't have a token, we can't get a country
        if (empty($token)) {
            return false;
        }

        $headers = array(
            'x-access-token' => $token
        );

        $config = $this->api_request_get('companies/analytics/config', null, $headers, null);
        if (!is_wp_error($config)) {
            return $config;
        }

        return false;
    }

    /**
     * Get the country code of the user
     *
     * @return boolean
     */
    function get_country($token = null) {

        if (empty($token))
            $token = $this->api_token_check();

        /** DEV MODE * */
        $dev_check = get_option("dsp_is_dev_mode_field");
        $dev_country = get_option("dsp_country_code_field");
        if ($dev_check) {
            $this->country = $dev_country;
            return $this->country;
        }
        /** END DEV MODE * */
        // If we don't have a token, we can't get a country
        if (empty($token))
            return false;

        $body = array(
            'ip' => $this->get_ip(),
        );

        $headers = array(
            'x-access-token' => $token
        );

        if(isset($_SESSION['dsp_theme_country']) && !is_array($_SESSION['dsp_theme_country'])) {
            $this->country = $_SESSION['dsp_theme_country'];
            return $this->country;
        }

        $country = $this->api_request_post('country', null, $headers, $body);
        if (!is_wp_error($country)) {
            $this->country = $country['data']['countryCode'];
            return $this->country;
        }
        else{
            $this->country = 'US';
            return $this->country;
        }

        return false;
    }

    /**
     * Get an array with all of the categories in a company
     *
     * @return Array Returns an array of with the categories, or an empty array if something is wrong or there are no categories
     */
    function get_categories() {

        $token = $this->api_token_check();

        $this->get_country($token);

        // If we have no token, or we have no country, the API call will fail, so we return an empty array
        if (!$token || !$this->country)
            return array();

        $path = 'categories/' . $this->country;

        $headers = array(
            'x-access-token' => $token
        );

        return $this->api_request_get($path, null, $headers);
    }

    /**
     * Get an array with all of the published channels in a company
     *
     * @param string $detail The level of detail we want from the channel call
     *
     * @return Array Returns an array of channels, or an empty array if something is wrong or there are no channels
     */
    function get_channels($detail = 'partial', $limit = '', $page = '') {

        $token = $this->api_token_check();

        $this->get_country($token);

        // If we have no token, or we have no country, the API call will fail, so we return an empty array
        if (!$token || !$this->country) {
            return array($token, $this->country);
        }

        // We don't use the country here, although we call it before here in case we need
        // to re-up our token or get the country for another call later. Instead, we use
        // ALL because it gives us every channel, so we can put it in the DB
        $path = 'channels/ALL';

        $headers = array(
            'x-access-token' => $token
        );

        $query = '';
        if(!empty($limit) && !empty($page))
            $query = array('limit' => $limit, 'page' => $page);

        return $this->api_request_get($path, $query, $headers);
    }

    /**
     * Function to get recommendation by channel or video id
     * @since 1.0.0
     * @param type $type
     * @param type $id
     * @return type
     */
    function get_recommendation($type = NULL, $id) {

        $token = $this->api_token_check();

        // If we have no token, the API call will fail, so we return an empty array
        if (!$token)
            return array();

        if ($type == 'channel')
            $path = '/find/recommendations/'.$id.'/website';
        else
            $path = '/find/recommendations/video/'.$id;

        $headers = array(
            'x-access-token' => $token
        );

        return $this->api_request_get($path, $query, $headers);
    }

    /**
     * function to get video detail by video id
     * @since 1.0.0
     * @param type $id
     * @return type
     */
    function get_video_by_id($id) {

        $token = $this->api_token_check();

        // If we have no token, or we have no country, the API call will fail, so we return an empty array
        if (!$token)
            return array();

        $path = 'video/play/' . $id .'/1920/1080';

        $headers = array(
            'x-access-token' => $token
        );

        return $this->api_request_get($path, null, $headers);
    }

    /**
     * function to serch the channels or videos
     * @since 1.0.0
     * @param type $type
     * @param type $size
     * @param type $from
     * @param type $q
     * @return type
     */
    function search($type, $size, $from, $q) {

        $token = $this->api_token_check();

        // If we have no token, the API call will fail, so we return an empty array
        if (!$token)
            return array();

        if ($type == 'channel'){
            $path = 'find/channels/website';
            $query = array('size' => $size, 'from' => $from, 'q' => $q);
        }
        else{
            $path = 'find/videos';
            $query = array('size' => $size, 'page' => $from, 'q' => $q);
        }

        $headers = array(
            'x-access-token' => $token
        );

        return $this->api_request_get($path, $query, $headers);
    }

    /**
     * function to search suggestion
     * @since 1.0.0
     * @param type $q
     * @return type
     */
    function search_suggestion($q) {

        $token = $this->api_token_check();

        // If we have no token, the API call will fail, so we return an empty array
        if (!$token)
            return array();

        $path = 'search/s';

        $headers = array(
            'x-access-token' => $token
        );

        $query = array('q' => $q);

        return $this->api_request_get($path, $query, $headers);
    }

    /**
     * function to update client token
     * @since 1.0.0
     * @return type
     */
    function refresh_client_token($client_token) {
        $token = $this->api_token_check();

        if (!$client_token && !$token) {
            return array();
        }

        $path = 'users/token/refresh';

        $headers = array(
            'x-client-token' => $client_token,
            'x-access-token' => $token
        );

        return $this->api_request_post($path, null, $headers);
    }

    /**
     * Get the IP of the user
     *
     * @return String The IP address of the user
     */
    private function get_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * function to check subscriptiom status
     * @since 1.0.0
     * @param type $client_token
     * @param type $channel_id
     * @return type
     */
    public function check_subscription_status($client_token = null, $channel_id) {

        $token = $this->api_token_check();

        if (!$token)
            return array();

        $path = 'subscriptions/check/' . $channel_id;

        $headers = array(
            'x-access-token' => $token,
        );

        if (!empty($client_token)) {
            $headers['x-client-token'] = $client_token;
        }

        return $this->api_request_get($path, null, $headers, null);
    }

    /**
     * function to remove a user from the system
     * @since 1.2.7
     * @param type $client_token
     * @param type $channel_id
     * @return type
     */
    public function remove_user_from_system($client_token) {

        $token = $this->api_token_check();

        if (!$token && !$client_token)
            return array();

        $path = 'users/account/purge';

        $headers = array(
            'x-access-token' => $token,
            'x-client-token' => $client_token
        );

        return $this->api_request_delete($path, null, $headers);
    }

    /**
     * function to to submit device login codes
     * @since 1.3.0
     * @param type $client_token
     * @param type $channel_id
     * @return type
     */
    public function submit_device_code($code, $customer_id) {

        $token = $this->api_token_check();

        if (!$token)
            return array();

        $path = 'device/codes/customer';

        $headers = array(
            'x-access-token' => $token
        );

        $body = array(
            'customer_id' => $customer_id,
            'code' => $code
        );

        return $this->api_request_post($path, null, $headers, $body);
    }

    /**
     * function to get the account deletion date for a user
     * @since 1.2.7
     * @param type $client_token
     * @param type $channel_id
     * @return type
     */
    public function get_user_account_deletion_date($client_token) {

        $token = $this->api_token_check();

        if (!$token && !$client_token)
            return array();

        $path = 'users/account/purge';

        $headers = array(
            'x-access-token' => $token,
            'x-client-token' => $client_token
        );
        return $this->api_request_get($path, null, $headers);
    }

    /**
     * function to get Home page Data
     * @since 1.2.2
     * @param type $client_token
     * @param type $channel_id
     * @return type
     */
    public function homepage($client_token = null) {

        $token = $this->api_token_check();

        $this->get_country($token);

        if (!$token || !$this->country) {
            return array();
        }

        $path = 'homepage/'.$this->country.'/website';

        $headers = array(
            'x-access-token' => $token,
        );

        if (!empty($client_token)) {
            $headers['x-client-token'] = $client_token;
        }

        return $this->api_request_get($path, null, $headers, null);
    }

    /**
     * function to get user's watchlist
     * @since 1.0.0
     * @param type $client_token
     * @return type
     */
    function get_user_watchlist($client_token) {
        $token = $this->api_token_check();

        if (!$token && !$client_token)
            return array();

        $path = 'watchlist/channels';

        $headers = array(
            'x-access-token' => $token,
            'x-client-token' => $client_token
        );

        return $this->api_request_get($path, null, $headers);
    }

    /**
     * function to to add channel to user's watchlist
     * @since 1.0.0
     * @param type $client_token
     * @param type $channel_id
     * @return type
     */
    function add_to_user_list($client_token, $channel_id, $parent_channel_id = null) {
        $token = $this->api_token_check();

        if (!$token && !$client_token)
            return array();

        $path = 'watchlist/channels/add';

        $headers = array(
            'x-access-token' => $token,
            'x-client-token' => $client_token
        );

        $body = array(
            'channel_id' => $channel_id,
        );
        if (!empty($parent_channel_id)) {
            $body['parent_channel_id'] = $parent_channel_id;
        }

        return $this->api_request_post($path, null, $headers, $body);
    }

    /**
     * function to remove data from user's watchlist
     * @since 1.0.0
     * @param type $client_token
     * @param type $channel_id
     * @return type
     */
    function remove_from_user_list($client_token, $channel_id) {
        $token = $this->api_token_check();

        if (!$token && !$client_token)
            return array();

        $path = 'watchlist/channels/delete';

        $headers = array(
            'x-access-token' => $token,
            'x-client-token' => $client_token
        );

        $body = array(
            'channel_id' => $channel_id,
        );

        return $this->api_request_delete($path, null, $headers, $body);
    }

    /**
     * function to get recent view data
     * @since 1.0.0
     * @param type $client_token
     * @return type
     */
    function get_recent_viewed_data($client_token) {
        $token = $this->api_token_check();

        if (!$token && !$client_token)
            return array();

        $path = 'users/resumption/videos';

        $headers = array(
            'x-access-token' => $token,
            'x-client-token' => $client_token
        );

        return $this->api_request_get($path, null, $headers);
    }

    /**
     * function to store the point data of video
     * @param type $client_token
     * @param type $video_id
     * @param type $point
     * @return type
     */
    public function create_point_data($client_token, $video_id, $point) {

        $token = $this->api_token_check();

        if (!$client_token && !$token) {
            return array();
        }

        $path = 'users/videos/point/' . $video_id . '/' . $point;

        $headers = array(
            'x-client-token' => $client_token,
            'x-access-token' => $token
        );

        return $this->api_request_post($path, null, $headers);
    }

    /**
     *
     */
    public function get_recent_viewed_data_video($client_token, $video_id) {

        $token = $this->api_token_check();

        if (!$client_token && !$token) {
            return array();
        }

        $path = 'users/videos/point/' . $video_id;

        $headers = array(
            'x-client-token' => $client_token,
            'x-access-token' => $token
        );

        return $this->api_request_get($path, null, $headers);
    }

    /**
     * Get Default Subscription Behavior
     *
     * @return Array Returns an array of with the Subscription Behavior
     */
    function get_default_subscription_behavior() {

        $token = $this->api_token_check();

        // If we have no token, or we have no country, the API call will fail, so we return an empty array
        if (!$token)
            return array();

        $path = 'subscriptions/default';

        $headers = array(
            'x-access-token' => $token
        );

        return $this->api_request_get($path, null, $headers);
    }

    /**
     * Get user's subscriptions.
     * @since 1.0.0
     * @param type $client_token
     * @return type
     */
    function get_user_subscription($client_token) {

        $token = $this->api_token_check();

        if (!$token && !$client_token)
            return array();

        $path = 'subscriptions/users/active_subscriptions';

        $headers = array(
            'x-access-token' => $token,
            'x-client-token' => $client_token
        );

        return $this->api_request_get($path, null, $headers);
    }

    /**
     * function to destroy a user's login session
     * @since 1.0.0
     * @return type
     */
    function destroy_user_login_session($client_token) {
        $token = $this->api_token_check();

        if (!$client_token && !$token) {
            return array();
        }

        $path = 'drm/sessions/logout';

        $headers = array(
            'x-client-token' => $client_token,
            'x-access-token' => $token
        );

        return $this->api_request_delete($path, null, $headers);
    }

    /**
     * function to destroy every login session for a user
     * @since 1.0.0
     * @return type
     */
    function destroy_every_user_login_session($client_token) {
        $token = $this->api_token_check();

        if (!$client_token && !$token) {
            return array();
        }

        $path = 'drm/sessions/clear';

        $headers = array(
            'x-client-token' => $client_token,
            'x-access-token' => $token
        );

        $query = array('new_token' => "true");

        return $this->api_request_delete($path, $query, $headers);
    }

    /**
     * This is common function to use POST request of External DSP API.
     *
     * @param type $path
     * @param type $body
     * @param type $headers
     * @return \WP_Error or Json Responce
     */
    public function api_request_post($path = null, $query = null, $headers = null, $body = null) {

        // vars
        $url = $this->common_url . $path;

        if ($query) {
            $url = add_query_arg($query, $url);
        }

        $raw_response = wp_remote_post($url, array(
            'body' => $body,
            'headers' => $headers,
            'timeout' => 50
        ));

        // wp error
        if (is_wp_error($raw_response)) {
            return $raw_response;
        }
        // http error
        elseif (wp_remote_retrieve_response_code($raw_response) != 200) {
            $this->checkSessionStatus($raw_response);
            $this->write_log('URL', $url);
            $this->write_log('Header Parameters', $headers);
            $this->write_log('Body Parameters', $body);
            $this->write_log('API Responce', wp_remote_retrieve_body($raw_response));
            $error_message = $this->error_message($raw_response);
            return new WP_Error('server_error', $error_message);
        }

        // decode response
        $json = json_decode(wp_remote_retrieve_body($raw_response), true);

        // allow non json value
        if ($json === null) {
            return wp_remote_retrieve_body($raw_response);
        }
        // return
        return $json;
    }

    /**
     * This is common function to use PUT request of External DSP API.
     *
     * @param type $path
     * @param type $body
     * @param type $headers
     * @return \WP_Error or Json Responce
     */
    public function api_request_put($path = null, $query = null, $headers = null, $body = null) {

        // vars
        $url = $this->common_url . $path;

        if ($query) {
            $url = add_query_arg($query, $url);
        }

        $raw_response = wp_remote_request($url, array(
            'method' => 'PUT',
            'body' => $body,
            'headers' => $headers,
            'timeout' => 50
        ));

        // wp error
        if (is_wp_error($raw_response)) {
            return $raw_response;
        }
        // http error
        elseif (wp_remote_retrieve_response_code($raw_response) != 200) {
            $this->checkSessionStatus($raw_response);
            $this->write_log('URL', $url);
            $this->write_log('Header Parameters', $headers);
            $this->write_log('Body Parameters', $body);
            $this->write_log('API Responce', wp_remote_retrieve_body($raw_response));
            $error_message = $this->error_message($raw_response);
            return new WP_Error('server_error', $error_message);
        }

        // decode response
        $json = json_decode(wp_remote_retrieve_body($raw_response), true);

        // allow non json value
        if ($json === null) {
            return wp_remote_retrieve_body($raw_response);
        }
        // return
        return $json;
    }

    /**
     * This is common function to use GET request of External DSP API.
     *
     * @param type $path
     * @param type $headers
     * @return \WP_Error or  Json Responce
     */
    public function api_request_get($path = null, $query = null, $headers = null) {

        // vars
        $url = $this->common_url . $path;

        if ($query) {
            $url = add_query_arg($query, $url);
        }

        $raw_response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 50,
        ));

        // wp error
        if (is_wp_error($raw_response)) {
            return $raw_response;
        }
        // http error
        elseif (wp_remote_retrieve_response_code($raw_response) != 200) {
            $this->checkSessionStatus($raw_response);
            $this->write_log('URL', $url);
            $this->write_log('Header Parameters', $headers);
            $this->write_log('API Responce', wp_remote_retrieve_body($raw_response));
            $error_message = $this->error_message($raw_response);
            return new WP_Error('server_error', $error_message);
        }

        // decode response
        $json = json_decode(wp_remote_retrieve_body($raw_response), true);

        // allow non json value
        if ($json === null) {
            return wp_remote_retrieve_body($raw_response);
        }
        // return
        return $json;
    }

    /**
     * This is common function to use DELETE request of External DSP API.
     *
     * @param type $path
     * @param type $body
     * @param type $headers
     * @return \WP_Error or Json Responce
     */
    public function api_request_delete($path = null, $query = null, $headers = null, $body = null) {
        // vars
        $url = $this->common_url . $path;

        if ($query) {
            $url = add_query_arg($query, $url);
        }

        $raw_response = wp_remote_request($url, array(
            'method' => 'DELETE',
            'body' => $body,
            'headers' => $headers,
            'timeout' => 50
        ));

        // wp error
        if (is_wp_error($raw_response)) {
            return $raw_response;
        }
        // http error
        elseif (wp_remote_retrieve_response_code($raw_response) != 200) {
            $this->checkSessionStatus($raw_response);
            $this->write_log('URL', $url);
            $this->write_log('Header Parameters', $headers);
            $this->write_log('Body Parameters', $body);
            $this->write_log('API Responce', wp_remote_retrieve_body($raw_response));
            $error_message = $this->error_message($raw_response);
            return new WP_Error('server_error', $error_message);
        }

        // decode response
        $json = json_decode(wp_remote_retrieve_body($raw_response), true);

        // allow non json value
        if ($json === null) {
            return wp_remote_retrieve_body($raw_response);
        }
        // return
        return $json;
    }

    /**
     * Function to write the error log in file.
     *
     * @param type $log
     */
    private function write_log($message, $log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log($message . "-----" . print_r($log, true));
            } else {
                error_log($message . "-----" . $log);
            }
        }
    }

    /**
     * Function to handel error responce which comes form DSP API
     *
     * @since 1.0.0
     * @param type $raw_response
     * @return string
     */
    public function error_message($raw_response) {

        $responce_body = wp_remote_retrieve_body($raw_response);
        $send_res = json_decode($responce_body);
        if (isset($send_res->reason)) {
            return $send_res->reason;
        } elseif (isset($send_res->error)) {
            if (is_object($send_res->error)) {
                return $send_res->error->name . ':' . $send_res->error->message;
            } else {
                return $send_res->error;
            }
        } elseif (isset($send_res->message)) {
            return $send_res->message;
        } else {
            return 'Internal Server error.';
        }
    }

    /**
     * Function to check session status in header and set cookie
     *
     * @param type $raw_response
     */
    function checkSessionStatus($raw_response) {
      $header_array_data = $this->accessProtected($raw_response['headers'], 'data');
      if(isset($header_array_data['x-must-reauthenticate'])) {
         if(!isset($_COOKIE['dsp_session_expired'])) {
           setcookie('dsp_session_expired', true);
         }
      }
    }

    /**
     * Function to access protected.
     *
     * @param type $obj
     * @param type $prop
     */
    function accessProtected($obj, $prop) {
      $reflection = new ReflectionClass($obj);
      $property = $reflection->getProperty($prop);
      $property->setAccessible(true);
      return $property->getValue($obj);
    }

}
