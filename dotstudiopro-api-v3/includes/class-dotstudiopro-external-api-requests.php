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

    
    public $api_key;
    public $common_url;

    function __construct() {

        $this->api_key = get_option('dotstudiopro_api_key');
        $this->common_url = "https://api.myspotlight.tv/";
    }

    /**
     * Check Api key is valid or not
     *
     * @return String|Boolean The API access token, or false if it couldn't get one
     * @since 1.0.0
     */
    function check_api_key($body = null) {
        return $this->api_request_post('token', $body);
    }

    /**
     * This is common function to use POST request of External DSP API.
     * 
     * @param type $query
     * @param type $body
     * @param type $headers
     * @return \WP_Error or Json Responce
     */
    private function api_request_post($query = null, $body = null, $headers = null) {

        // vars
        $url = $this->common_url . $query;

        $raw_response = wp_remote_post($url, array(
            'body' => $body,
            'headers' => $headers
        ));

        // wp error
        if (is_wp_error($raw_response)) {
            return $raw_response;
        }
        // http error
        elseif (wp_remote_retrieve_response_code($raw_response) != 200) {
            return new WP_Error('server_error', wp_remote_retrieve_response_message($raw_response));
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

}
