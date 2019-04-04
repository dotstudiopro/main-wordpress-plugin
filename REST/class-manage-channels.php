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
class Dsp_Manage_channels {

    /**
     * This Function is used to delete the category when the request is comes form an API Routes.
     *
     * @version 1.0.0
     * @param type $request
     *
     * @return json
     */
    public function delete_channel($request) {

        $dsp_channel = json_decode(json_encode($request['channel']));

        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'channel',
            'meta_query' => array(
                array(
                    'key' => 'dspro_channel_id',
                    'value' => $dsp_channel->_id
                )
            )
        );

        $channel = new WP_Query($args);

        if ($channel->have_posts()) {
            while ($channel->have_posts()) :
                $channel->the_post();
                wp_delete_post(get_the_ID());
            endwhile;
            wp_reset_postdata();
            $send_response = array('code' => 'rest_success', 'message' => 'Channel deleted successfully.', 'data' => array('status' => 200));
            wp_send_json($send_response, 200);
        }
        else {
            return new WP_Error('rest_not_found', __('Channel not found to delete.'), array('status' => 404));
        }
    }

    /**
     * This Function is used to add or update the channel when the request is comes form an API Routes.
     *
     * @version 1.0.0
     * @param type $request
     *
     * @return json
     */
    public function manage_channel($request, $type = null) {
        global $wpdb;
        $dsp_video_table = $wpdb->prefix . 'videos';

        $dsp_channels = json_decode(json_encode($request['channels']));

        $user_ID = 1;
        $results = [];

        foreach($dsp_channels as $dsp_channel) {

            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'channel',
                'meta_query' => array(
                    array(
                        'key' => 'chnl_id',
                        'value' => $dsp_channel->_id
                    )
                )
            );

            $channel = new WP_Query($args);
            $posts = $channel->posts;

            $new_post = array(
                'post_title' => $dsp_channel->title,
                'post_content' => ($dsp_channel->description) ? $dsp_channel->description : '',
                'post_status' => 'publish',
                'post_date' => date('Y-m-d H:i:s'),
                'post_author' => $user_ID,
                'post_type' => 'channel',
                'post_name' => $dsp_channel->slug,
            );

            if (empty($channel->have_posts())) {
                $post_id = wp_insert_post($new_post);
                $results[$dsp_channel->_id] = 'Added!';
                if (is_wp_error($post_id)) {
                    $results[$dsp_channel->_id] = "Post could not be added.";
                    continue;
                }
            } else {
                $new_post['ID'] = $posts[0]->ID;
                $post_id = wp_update_post($new_post);
                $results[$dsp_channel->_id] = 'Updated!';
                if (is_wp_error($post_id)) {
                    $results[$dsp_channel->_id] = "Post could not be updated.";
                    continue;
                }
            }

            $channel_id = isset($dsp_channel->_id) ? $dsp_channel->_id : '';
            $company_id = isset($dsp_channel->company_id) ? $dsp_channel->company_id : '';
            $channel_logo = isset($dsp_channel->channel_logo) ? $dsp_channel->channel_logo : '';
            $writers = implode(',', $dsp_channel->writers);
            $genres = implode(',', $dsp_channel->genres);
            $directors = implode(',', $dsp_channel->directors);
            $actors = implode(',', $dsp_channel->actors);
            $poster = isset($dsp_channel->poster) ? $dsp_channel->poster : '';
            $spotlight_poster = isset($dsp_channel->spotlight_poster) ? $dsp_channel->spotlight_poster : '';
            $childchannels = isset($dsp_channel->childchannels) ? $dsp_channel->childchannels : '';
            $categories = isset($dsp_channel->categories) ? $dsp_channel->categories : '';
            $dspro_channel_id = isset($dsp_channel->dspro_id) ? $dsp_channel->dspro_id : '';
            $weightings = isset($dsp_channel->weightings) ? $dsp_channel->weightings : '';
            $geo = isset($dsp_channel->geo) ? $dsp_channel->geo : '';
            $is_product = isset($dsp_channel->is_product) ? $dsp_channel->is_product : '';
            $year = isset($dsp_channel->year) ? $dsp_channel->year : '';
            $language = isset($dsp_channel->language) ? $dsp_channel->language : '';   
            
            $video_id = array();

            if (!empty($dsp_channel->playlist)) {
                foreach ($dsp_channel->playlist as $key => $video):
                    $video_id[] = isset($video->_id) ? $video->_id : '';
                    $vidoeArr = array();
                    $vidoeArr['title'] = isset($video->title) ? $video->title : '';
                    $vidoeArr['description'] = isset($video->description) ? $video->description : '';
                    $vidoeArr['slug'] = isset($video->slug) ? $video->slug : '';
                    $vidoeArr['thumb'] = isset($video->thumb) ? get_option('dsp_cdn_img_url_field'). $video->thumb : '';
                    $videoData = base64_encode(maybe_serialize($vidoeArr));
                    $data = array('video_id' => $video->_id, 'video_detail' => $videoData);
                    $is_video_exists = $wpdb->get_results("SELECT * FROM $dsp_video_table WHERE video_id = '" . $video->_id . "'");
                    if ($wpdb->num_rows > 0)
                        $wpdb->update($dsp_video_table, $data, array('video_id' => $video->_id));
                    else
                        $wpdb->insert($dsp_video_table, $data);

                endforeach;
                update_post_meta($post_id, 'chnl_videos', implode(',', $video_id));
            }
            elseif (!empty($dsp_channel->video)) {
                $video = $dsp_channel->video;
                $vidoeArr = array();
                $video_id[] = isset($video->_id) ? $video->_id: '';
                $vidoeArr['title'] = isset($video->title) ? $video->title : '';
                $vidoeArr['description'] = isset($video->description) ? $video->description : '';
                $vidoeArr['slug'] = isset($video->slug) ? $video->slug : '';
                $vidoeArr['thumb'] = isset($video->thumb) ? get_option('dsp_cdn_img_url_field'). $video->thumb : '';

                $videoData = base64_encode(maybe_serialize($vidoeArr));
                $data = array('video_id' => $video->_id, 'video_detail' => $videoData);
                $is_video_exists = $wpdb->get_results("SELECT * FROM $dsp_video_table WHERE video_id = '" . $video->_id . "'");
                if ($wpdb->num_rows > 0)
                    $wpdb->update($dsp_video_table, $data, array('video_id' => $video->_id));
                else
                    $wpdb->insert($dsp_video_table, $data);
                update_post_meta($post_id, 'chnl_videos', implode(',', $video_id));
            }
            else{
                update_post_meta($post_id, 'chnl_videos', null);
            }
            
            update_post_meta($post_id, 'chnl_id', $channel_id);
            update_post_meta($post_id, 'chnl_writers', $writers);
            update_post_meta($post_id, 'chnl_geners', $genres);
            update_post_meta($post_id, 'chnl_directors', $directors);
            update_post_meta($post_id, 'chnl_actors', $actors);
            update_post_meta($post_id, 'chnl_poster', $poster);
            update_post_meta($post_id, 'chnl_spotlight_poster', $spotlight_poster);
            update_post_meta($post_id, 'chnl_comp_id', $company_id);
            update_post_meta($post_id, 'chnl_logo', $channel_logo);
            update_post_meta($post_id, 'dspro_channel_id', $dspro_channel_id);
            update_post_meta($post_id, 'dspro_channel_geo', $geo);
            update_post_meta($post_id, 'dspro_is_product', $is_product);
            update_post_meta($post_id, 'dspro_channel_year', $year);
            update_post_meta($post_id, 'dspro_channel_language', $language);

            if (!empty($categories)) {
                $category = array();
                foreach ($categories as $cat) {
                    $category[] = $cat->slug;
                }
                update_post_meta($post_id, 'chnl_categories', ',' . implode(',', $category) . ',');
            }
            else{
                update_post_meta($post_id, 'chnl_categories', null);
            }

            if (!empty($childchannels)) {
                $childchannel = array();
                foreach ($childchannels as $child) {
                    $childchannel[] = $child->slug;
                }
                update_post_meta($post_id, 'chnl_child_channels', implode(',', $childchannel));
            }
            else{
                update_post_meta($post_id, 'chnl_child_channels', null);
            }

            $weightingsArr = array();
            if (!empty($weightings)) {
                foreach ($weightings as $key => $weighting):
                    $weightingsArr[$key] = isset($weighting) ? $weighting : '';
                endforeach;
                $weightingData = maybe_serialize($weightingsArr);

                update_post_meta($post_id, 'chnl_weightings', $weightingData);
            }
            wp_reset_postdata();
        }

        if (empty($type)) {
            $send_response = array('code' => 'rest_success', 'message' => 'Results: ' . json_encode($results), 'data' => array('status' => 200));
            wp_send_json($send_response, 200);
        }
    }

    /**
     * This function to update channel order when the request is comes form an API Routes.
     * @since 1.0.0
     * @param type $request
     */

    public function order_channel($request) {

        $dsp_channel = json_decode(json_encode($request['channel']));

        foreach ($channels as $channel):
            $this->manage_channel($channel, 'order');
        endforeach;
        $send_response = array('code' => 'rest_success', 'message' => 'Channel order updated succesfully. ', 'data' => array('status' => 200));
        wp_send_json($send_response, 200);
    }

}
