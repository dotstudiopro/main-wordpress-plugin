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
                    'key' => 'chnl_id',
                    'value' => $dsp_channel->_id
                )
            )
        );

        $channel = new WP_Query($args);
        $transients_keys = array();
        if ($channel->have_posts()) {
            while ($channel->have_posts()) :
                $channel->the_post();
                $post_id = get_the_ID();
                $channel_id =  get_post_meta($post_id, 'chnl_id')[0];
                wp_delete_post(get_the_ID());
                $this->delete_custom_transient($post_id, $channel_id);
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
            $wallpaper = isset($dsp_channel->wallpaper) ? $dsp_channel->wallpaper : '';
            $childchannels = isset($dsp_channel->childchannels) ? $dsp_channel->childchannels : '';
            $categories = isset($dsp_channel->categories) ? $dsp_channel->categories : '';
            $dspro_channel_id = isset($dsp_channel->dspro_id) ? $dsp_channel->dspro_id : '';
            $weightings = isset($dsp_channel->weightings) ? $dsp_channel->weightings : '';
            $geo = isset($dsp_channel->geo) ? $dsp_channel->geo : '';
            $is_product = isset($dsp_channel->is_product) ? $dsp_channel->is_product : '';
            $year = isset($dsp_channel->year) ? $dsp_channel->year : '';
            $language = isset($dsp_channel->language) ? $dsp_channel->language : '';

            $this->delete_custom_transient($post_id, $channel_id);

            $video_id = array();

            if (!empty($dsp_channel->playlist)) {
                foreach ($dsp_channel->playlist as $key => $video):
                    $video_id[] = isset($video->_id) ? $video->_id : '';
                    $vidoeArr = array();
                    $vidoeArr['title'] = isset($video->title) ? $video->title : '';
                    $vidoeArr['description'] = isset($video->description) ? $video->description : '';
                    $vidoeArr['slug'] = isset($video->slug) ? $video->slug : '';
                    $vidoeArr['bypass_channel_lock'] = isset($video->bypass_channel_lock) ? $video->bypass_channel_lock : '';
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
                $vidoeArr['bypass_channel_lock'] = isset($video->bypass_channel_lock) ? $video->bypass_channel_lock : '';
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
            update_post_meta($post_id, 'chnl_wallpaper', $wallpaper);
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

    /**
     * This function to update all the channels by page count when the request is comes form an API Routes or Cron.
     * @since 1.1.7
     * @param type $request
     */

    public function update_all_channel() {

        global $user_ID, $wpdb;

        $dsp = new Dotstudiopro_Api();
        $dsp_video_table = $dsp->get_Dotstudiopro_Video_Table();

        $current_date = current_time('d-m-Y');

        $dsp_import_date = get_option('cron_dsp_plugin_date');
        $date = empty($dsp_import_date) ? $current_date : $dsp_import_date;

        $dsp_import_flag = get_option('cron_dsp_plugin_complete');
        $flag = empty($dsp_import_flag) ? 'false' : $dsp_import_flag;

        $dsp_import_limit_field = get_option('cron_dsp_plugin_limit'); 
        $limit = empty($dsp_import_limit_field) ? 100 : $dsp_import_limit_field;

        $dsp_import_page = get_option('cron_dsp_plugin_page'); 
        $page = empty($dsp_import_page) ? 0 : $dsp_import_page;

        $dsp_import_hash_key = get_option('cron_dsp_plugin_hash_key');
        $hash_key = empty($dsp_import_hash_key) ? time() .'_'. rand() : $dsp_import_hash_key;

        if($current_date != $date){
            $flag = 'false';
            $page = 0;
            $hash_key = time() .'_'. rand();
        }

        if($flag == 'false'){
            $dspExternalApiClass = new Dsp_External_Api_Request();
            $channels = $dspExternalApiClass->get_channels('', $limit, $page+1);
            if (!is_wp_error($channels)) {
                $add_count = 0;
                $update_count = 0;
                foreach ($channels['channels'] as $channel) {
                    $args = array(
                        'fields' => 'ids',
                        'post_type' => 'channel',
                        'meta_query' => array(
                            array(
                                'key' => 'chnl_id',
                                'value' => $channel['_id']
                            )
                        )
                    );
                    $my_query = new WP_Query($args);
                    $posts = $my_query->posts;
                    $new_post = array(
                        'post_title' => $channel['title'],
                        'post_content' => isset($channel['description']) ? $channel['description'] : '',
                        'post_status' => 'publish',
                        'post_date' => date('Y-m-d H:i:s'),
                        'post_author' => $user_ID,
                        'post_type' => 'channel',
                        'post_name' => $channel['slug'],
                    );
                    if (empty($my_query->have_posts())) {
                        $post_id = wp_insert_post($new_post);
                        $add_count++;
                    } else {
                        $new_post['ID'] = $posts[0];
                        $post_id = wp_update_post($new_post);
                        $update_count++;
                    }

                    $channel_id = isset($channel['_id']) ? $channel['_id'] : '';
                    $company_id = isset($channel['company_id']) ? $channel['company_id'] : '';
                    $writers = isset($channel['writers']) ? implode(',', $channel['writers']) : '';
                    $genres = isset($channel['genres']) ? implode(',', $channel['genres']) : '';
                    $directors = isset($channel['directors']) ? implode(',', $channel['directors']) : '';
                    $actors = isset($channel['actors']) ? implode(',', $channel['actors']) : '';
                    $poster = isset($channel['poster']) ? $channel['poster'] : '';
                    $spotlight_poster = isset($channel['spotlight_poster']) ? $channel['spotlight_poster'] : '';
                    $wallpaper = isset($channel['wallpaper']) ? $channel['wallpaper'] : '';
                    $channel_logo = isset($channel['channel_logo']) ? $channel['channel_logo'] : '';
                    $childchannels = isset($channel['childchannels']) ? $channel['childchannels'] : '';
                    $categories = isset($channel['categories']) ? $channel['categories'] : '';
                    $dspro_channel_id = isset($channel['dspro_id']) ? $channel['dspro_id'] : '';
                    $weightings = isset($channel['weightings']) ? $channel['weightings'] : '';
                    $geo = isset($channel['geo']) ? $channel['geo'] : '';
                    $is_product = isset($channel['is_product']) ? $channel['is_product'] : '';
                    $language = isset($channel['language']) ? $channel['language'] : '';
                    $year = isset($channel['year']) ? $channel['year'] : '';

                    $video_id = array();

                    if (!empty($channel['playlist'])) {
                        foreach ($channel['playlist'] as $key => $video):
                            $video_id[] = isset($video['_id']) ? $video['_id'] : '';
                            $vidoeArr = array();
                            $vidoeArr['title'] = isset($video['title']) ? $video['title'] : '';
                            $vidoeArr['description'] = isset($video['description']) ? $video['description'] : '';
                            $vidoeArr['slug'] = isset($video['slug']) ? $video['slug'] : '';
                            $vidoeArr['bypass_channel_lock'] = isset($video['bypass_channel_lock']) ? $video['bypass_channel_lock'] : '';
                            $vidoeArr['thumb'] = isset($video['thumb']) ? get_option('dsp_cdn_img_url_field'). $video['thumb'] : '';
                            $videoData = base64_encode(maybe_serialize($vidoeArr));
                            $data = array('video_id' => $video['_id'], 'video_detail' => $videoData);
                            $is_video_exists = $wpdb->get_results("SELECT * FROM $dsp_video_table WHERE video_id = '" . $video['_id'] . "'");
                            if ($wpdb->num_rows > 0)
                                $wpdb->update($dsp_video_table, $data, array('video_id' => $video['_id']));
                            else
                                $wpdb->insert($dsp_video_table, $data);

                        endforeach;
                        update_post_meta($post_id, 'chnl_videos', implode(',', $video_id));
                    }
                    elseif (!empty($channel['video'])) {
                        $video = $channel['video'];
                        $vidoeArr = array();
                        $video_id[] = isset($video['_id']) ? $video['_id'] : '';
                        $vidoeArr['title'] = isset($video['title']) ? $video['title'] : '';
                        $vidoeArr['description'] = isset($video['description']) ? $video['description'] : '';
                        $vidoeArr['slug'] = isset($video['slug']) ? $video['slug'] : '';
                        $vidoeArr['bypass_channel_lock'] = isset($video['bypass_channel_lock']) ? $video['bypass_channel_lock'] : '';
                        $vidoeArr['thumb'] = isset($video['thumb']) ? get_option('dsp_cdn_img_url_field'). $video['thumb'] : '';

                        $videoData = base64_encode(maybe_serialize($vidoeArr));
                        $data = array('video_id' => $video['_id'], 'video_detail' => $videoData);
                        $is_video_exists = $wpdb->get_results("SELECT * FROM $dsp_video_table WHERE video_id = '" . $video['_id'] . "'");
                        if ($wpdb->num_rows > 0)
                            $wpdb->update($dsp_video_table, $data, array('video_id' => $video['_id']));
                        else
                            $wpdb->insert($dsp_video_table, $data);
                        update_post_meta($post_id, 'chnl_videos', implode(',', $video_id));
                    }

                    update_post_meta($post_id, 'chnl_id', $channel_id);
                    update_post_meta($post_id, 'chnl_writers', $writers);
                    update_post_meta($post_id, 'chnl_geners', $genres);
                    update_post_meta($post_id, 'chnl_directors', $directors);
                    update_post_meta($post_id, 'chnl_actors', $actors);
                    update_post_meta($post_id, 'chnl_poster', $poster);
                    update_post_meta($post_id, 'chnl_logo', $channel_logo);
                    update_post_meta($post_id, 'chnl_spotlight_poster', $spotlight_poster);
                    update_post_meta($post_id, 'chnl_wallpaper', $wallpaper);
                    update_post_meta($post_id, 'chnl_comp_id', $company_id);
                    update_post_meta($post_id, 'dspro_channel_id', $dspro_channel_id);
                    update_post_meta($post_id, 'dspro_channel_geo', $geo);
                    update_post_meta($post_id, 'dspro_is_product', $is_product);
                    update_post_meta($post_id, 'dspro_channel_language', $language);
                    update_post_meta($post_id, 'dspro_channel_year', $year);
                    update_post_meta($post_id, 'dsp_import_hash', $hash_key);

                    if (!empty($categories)) {
                        $category = array();
                        foreach ($categories as $cat) {
                            $category[] = $cat['slug'];
                        }
                        update_post_meta($post_id, 'chnl_categories', ',' . implode(',', $category) . ',');
                    }
                    if (!empty($childchannels)) {
                        $childchannel = array();
                        foreach ($childchannels as $child) {
                            $childchannel[] = $child['slug'];
                        }
                        update_post_meta($post_id, 'chnl_child_channels', implode(',', $childchannel));
                    }
                    $weightingsArr = array();
                    if (!empty($weightings)) {
                        foreach ($weightings as $key => $weighting):
                            $weightingsArr[$key] = isset($weighting) ? $weighting : '';
                        endforeach;
                        $weightingData = maybe_serialize($weightingsArr);
                        update_post_meta($post_id, 'chnl_weightings', $weightingData);
                    }
                }

                update_option('cron_dsp_plugin_date', current_time('d-m-Y'));
                update_option('cron_dsp_plugin_limit', $limit);
                update_option('cron_dsp_plugin_page', $page+1);
                update_option('cron_dsp_plugin_hash_key', $hash_key);
                update_option('cron_dsp_plugin_complete', 'false');

                $send_response = array();
                if($channels['pages']['page'] == $channels['pages']['pages']){
                    $send_response['status'] = 'complete';
                    $send_response['message'] = ' Channels Updated Sucesfully.';

                    update_option('cron_dsp_plugin_complete', 'true');

                    $hashkey_args = array(
                        'fields' => 'ids',
                        'post_type' => 'channel',
                        'meta_query' => array(
                            'relation' => 'OR',
                            array(
                             'key' => 'dsp_import_hash',
                             'compare' => 'NOT EXISTS', 
                             'value' => ''
                            ),
                            array(
                                'key' => 'dsp_import_hash',
                                'value' => $hash_key,
                                'compare' => '!='
                            )
                        )
                    );
                    $hashkey_query = new WP_Query($hashkey_args);
                    $hashkey_posts = $hashkey_query->posts;

                    foreach ($hashkey_posts as $channel) {
                        wp_delete_post($channel, true);
                    }
                }
                else{
                  $send_response['status'] = 'pending';
                  $send_response['page'] = $channels['pages']['page'];
                  $send_response['hash_key'] = $hash_key;
                  $send_response['pages'] = $channels['pages']['pages'];  
                }
                wp_send_json_success($send_response, 200);
           } else {
               $send_response = array('message' => 'Server Error : ' . $channels->get_error_message());
               wp_send_json_error($send_response, 403);
            }

        } 
        else{
            $send_response = array('message' => 'Cron is completed for this date.');
            wp_send_json_success($send_response, 200);
        }       
    }

    /**
     * This function is to delete all custom transient of channel
     */
    public function delete_custom_transient($post_id, $channel_id){
        global $wpdb;
        $sql = "SELECT * FROM $wpdb->options WHERE option_name LIKE '%\_transient\_%' AND ( option_value LIKE '%$post_id%' OR option_value LIKE '%$channel_id%' )";
        $transients = $wpdb->get_results($sql);
        if($transients){
            foreach($transients as $t){
                $wpdb->query("DELETE FROM $wpdb->options WHERE option_name = '" . $t->option_name . "'");
            }
        }
    }
}
