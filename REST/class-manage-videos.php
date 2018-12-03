<?php

/**
 * The file that manage the Channels class
 *
 * Maintain a list of all webhook routes for Channels (Ex: delete channel, add or update channel)
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 *
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/REST
 */
class Dsp_Manage_videos {

    /**
     * This Function is used to add or update the channel when the request is comes form an API Routes.
     *
     * @version 1.0.0
     * @param type $request
     *
     * @return json
     */
    public function manage_videos($request) {
        if ($request) {
            global $wpdb;
            $vidoeArr = array();
            $dsp_video_table = $wpdb->prefix . 'videos';
            $is_video_exists = $wpdb->get_results("SELECT * FROM $dsp_video_table WHERE video_id = '" . $request['_id'] . "'");
            $vidoeArr['title'] = isset($request['title']) ? $request['title'] : '';
            $vidoeArr['description'] = isset($request['description']) ? $request['description'] : '';
            $vidoeArr['slug'] = isset($request['slug']) ? $request['slug'] : '';
            $vidoeArr['thumb'] = isset($request['thumb']) ? $request['thumb'] : '';
            $videoData = base64_encode(maybe_serialize($vidoeArr));
            $data = array('video_id' => $request['_id'], 'video_detail' => $videoData);
            if ($wpdb->num_rows > 0) {
                $wpdb->update($dsp_video_table, $data, array('video_id' => $request['_id']));
                $send_response = array('message' => 'Video Detail Updated Succesfully.');
                wp_send_json_error($send_response, 200);
            } else {
                $wpdb->insert($dsp_video_table, $data);
                $send_response = array('message' => 'Video Detail Added Succesfully.');
                wp_send_json_error($send_response, 200);
            }
        }
    }

}
