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
            $company_logo = isset($dsp_channel->company_logo) ? $dsp_channel->company_logo : '';
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

            update_post_meta($post_id, 'chnl_id', $channel_id);
            update_post_meta($post_id, 'chnl_writers', $writers);
            update_post_meta($post_id, 'chnl_geners', $genres);
            update_post_meta($post_id, 'chnl_directors', $directors);
            update_post_meta($post_id, 'chnl_actors', $actors);
            update_post_meta($post_id, 'chnl_poster', $poster);
            update_post_meta($post_id, 'chnl_spotlight_poster', $spotlight_poster);
            update_post_meta($post_id, 'chnl_comp_id', $company_id);
            update_post_meta($post_id, 'chnl_logo', $company_logo);
            update_post_meta($post_id, 'dspro_channel_id', $dspro_channel_id);

            if (!empty($categories)) {
                $category = array();
                foreach ($categories as $cat) {
                    $category[] = $cat->slug;
                }
                update_post_meta($post_id, 'chnl_catagories', ',' . implode(',', $category) . ',');
            }

            if (!empty($childchannels)) {
                $childchannel = array();
                foreach ($childchannels as $child) {
                    $childchannel[] = $child->slug;
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
