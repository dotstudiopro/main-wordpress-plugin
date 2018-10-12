<?php

/**
 * The file that manage the Category class
 * 
 * Maintain a list of all webhook routes for Category (Ex: delete category, add or update category)
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/REST
 */
class Dsp_Manage_categories {

    /**
     * This Function is used to delete the category when the request is comes form an API Routes.
     * 
     * @version 1.0.0
     * @param type $request
     * 
     * @return json
     */
    public function delete_category($request) {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'category',
            'meta_query' => array(
                array(
                    'key' => 'cat_id',
                    'value' => $request['_id']
                )
            )
        );

        $category = new WP_Query($args);

        if ($category->have_posts()) {
            while ($category->have_posts()) :
                $category->the_post();
                wp_delete_post(get_the_ID());
            endwhile;
            wp_reset_postdata();
            $send_response = array('code' => 'rest_success', 'message' => 'Category deleted successfully.', 'data' => array('status' => 200));
            wp_send_json($send_response, 200);
        }
        else {
            return new WP_Error('rest_not_found', __('Category not found to delete.'), array('status' => 404));
        }
    }

    /**
     * This Function is used to add or update the category when the request is comes form an API Routes.
     * 
     * @version 1.0.0
     * @param type $request
     * 
     * @return json
     */
    public function manage_category($request) {
        $user_ID = 1;
        $message = '';

        if (isset($request['platforms'][0]['website'])) {

            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'category',
                'meta_query' => array(
                    array(
                        'key' => 'cat_id',
                        'value' => $request['_id']
                    )
                )
            );

            $category = new WP_Query($args);
            $posts = $category->posts;

            $new_post = array(
                'post_title' => $request['name'],
                'post_content' => ($request['description']) ? $request['description'] : '',
                'post_status' => 'publish',
                'post_date' => date('Y-m-d H:i:s'),
                'post_author' => $user_ID,
                'post_type' => 'category',
                'post_name' => $request['slug'],
            );

            if (empty($category->have_posts())) {
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

            update_post_meta($post_id, 'cat_id', $request['_id']);
            update_post_meta($post_id, 'cat_wallpaper', $request['wallpaper']);
            update_post_meta($post_id, 'cat_poster', $request['poster']);
            update_post_meta($post_id, 'is_in_cat_menu', $request['menu']);
            update_post_meta($post_id, 'is_on_cat_homepage', $request['homepage']);

            wp_reset_postdata();
            $send_response = array('code' => 'rest_success', 'message' => 'Category data ' . $message, 'data' => array('status' => 200));
            wp_send_json($send_response, 200);
        }
        return new WP_Error('rest_syndication_error', __('Syndication for this category is not enabled for website.'), array('status' => 406));
    }

}
