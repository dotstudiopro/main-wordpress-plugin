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
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'channel',
            'meta_query' => array(
                array(
                    'key' => 'chnl_id',
                    'value' => $request['_id']
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

        $user_ID = 1;
        $message = '';

        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'channel',
            'meta_query' => array(
                array(
                    'key' => 'chnl_id',
                    'value' => $request['_id']
                )
            )
        );

        $channel = new WP_Query($args);
        $posts = $channel->posts;

        $new_post = array(
            'post_title' => $request['title'],
            'post_content' => ($request['description']) ? $request['description'] : '',
            'post_status' => 'publish',
            'post_date' => date('Y-m-d H:i:s'),
            'post_author' => $user_ID,
            'post_type' => 'channel',
            'post_name' => $request['slug'],
        );

        if (empty($channel->have_posts())) {
            $post_id = wp_insert_post($new_post);
            $message = 'Added!';
        } else {
            $new_post['ID'] = $posts[0]->ID;
            $post_id = wp_update_post($new_post);
            $message = 'Updated!';
        }

        if (is_wp_error($post_id)) {
            return new WP_Error('rest_internal_server_error', __('Internal Server Error.'), array('status' => 500));
        }

        $channel_id = isset($request['_id']) ? $request['_id'] : '';
        $company_id = isset($request['company_id']) ? $request['company_id'] : '';
        $company_logo = isset($request['company_logo']) ? $request['company_logo'] : '';
        $writers = implode(',', $request['writers']);
        $genres = implode(',', $request['genres']);
        $directors = implode(',', $request['directors']);
        $actors = implode(',', $request['actors']);
        $poster = isset($request['poster']) ? $request['poster'] : '';
        $spotlight_poster = isset($request['spotlight_poster']) ? $request['spotlight_poster'] : '';
        $childchannels = isset($request['childchannels']) ? $request['childchannels'] : '';
        $categories = isset($request['categories']) ? $request['categories'] : '';
        $dspro_channel_id = isset($request['dspro_id']) ? $request['dspro_id'] : '';
        $weightings = isset($request['weightings']) ? $request['weightings'] : '';

        update_post_meta($post_id, 'chnl_id', $channel_id);
        update_post_meta($post_id, 'chnl_writers', $writers);
        update_post_meta($post_id, 'chnl_geners', $genres);
        update_post_meta($post_id, 'chnl_directors', $directors);
        update_post_meta($post_id, 'chnl_actors', $actors);
        update_post_meta($post_id, 'chnl_poster', $poster);
        update_post_meta($post_id, 'chnl_spotlisgt_poster', $spotlight_poster);
        update_post_meta($post_id, 'chnl_comp_id', $company_id);
        update_post_meta($post_id, 'chnl_logo', $company_logo);
        update_post_meta($post_id, 'dspro_channel_id', $dspro_channel_id);

        if (!empty($categories)) {
            $category = array();
            foreach ($categories as $cat) {
                $category[] = $cat['slug'];
            }
            update_post_meta($post_id, 'chnl_catagories', ',' . implode(',', $category) . ',');
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
        wp_reset_postdata();

        if (empty($type)) {
            $send_response = array('code' => 'rest_success', 'message' => 'Channel data ' . $message, 'data' => array('status' => 200));
            wp_send_json($send_response, 200);
        }
    }
    
    /**
     * This function to update channel order when the request is comes form an API Routes.
     * @since 1.0.0
     * @param type $request
     */

    public function order_channel($request) {

        $channels = $request['channels'];
        foreach ($channels as $channel):
            $this->manage_channel($channel, 'order');
        endforeach;
        $send_response = array('code' => 'rest_success', 'message' => 'Channel order updated succesfully. ', 'data' => array('status' => 200));
        wp_send_json($send_response, 200);
    }

}
