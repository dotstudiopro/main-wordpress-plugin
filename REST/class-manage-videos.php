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
        if (isset($request['vid']) && !empty($request['vid'])) {
            $dsp_video = json_decode(json_encode($request['vid']));
            global $wpdb;
            $vidoeArr = array();
            $dsp_video_table = $wpdb->prefix . 'videos';
            $is_video_exists = $wpdb->get_results("SELECT * FROM $dsp_video_table WHERE video_id = '" . $dsp_video->_id . "'");
            $vidoeArr['title'] = isset($dsp_video->title) ? $dsp_video->title : '';
            $vidoeArr['description'] = isset($dsp_video->description) ? $dsp_video->description : '';
            $vidoeArr['slug'] = isset($dsp_video->slug) ? $dsp_video->slug : '';
            $vidoeArr['bypass_channel_lock'] = isset($dsp_video->bypass_channel_lock) ? $dsp_video->bypass_channel_lock : '';
            $vidoeArr['thumb'] = isset($dsp_video->thumb) ?  get_option('dsp_cdn_img_url_field') . '/' . $dsp_video->thumb : '';
            $videoData = base64_encode(maybe_serialize($vidoeArr));
            $data = array('video_id' => $dsp_video->_id, 'video_detail' => $videoData);
            if ($wpdb->num_rows > 0) {
                $wpdb->update($dsp_video_table, $data, array('video_id' => $dsp_video->_id));
                $send_response = array('message' => 'Video Detail Updated Succesfully.');
                wp_send_json_success($send_response, 200);
            } else {
                $wpdb->insert($dsp_video_table, $data);
                $send_response = array('message' => 'Video Detail Added Succesfully.');
                wp_send_json_success($send_response, 200);
            }
        }else{
            $send_response = array('message' => 'Request Parameter Error');
            wp_send_json_error($send_response, 422);
        }
    }

}
