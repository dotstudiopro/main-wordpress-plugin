<?php

/**
 * The file that defines the Custom Post Type class
 *
 * A class definition that includes attributes and functions used across the dashboard to use and create custom post types.
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/admin/includes
 */
class Dsp_Custom_Posttypes {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct() {
        $this->dspExternalApiClass = new Dsp_External_Api_Request();
    }

    /**
     * Creating custom posttypes (Channels | Category)
     * 
     * @since 1.0.0 
     */
    public function create_dotstudiopro_post_types() {
        $labels = array(
            'name' => _x('Channels', 'Post Type General Name', 'dotstudiopro-api'),
            'singular_name' => _x('Channel', 'Post Type Singular Name', 'dotstudiopro-api'),
            'menu_name' => __('Channels', 'dotstudiopro-api'),
            'name_admin_bar' => __('Channels', 'dotstudiopro-api'),
        );
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => true,
            'capability_type' => 'page',
            'has_archive' => false,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-format-video',
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'revisions', 'page-attributes'),
        );
        register_post_type('channel', $args);

        $labels = array(
            'name' => _x('Categories', 'Post Type General Name', 'dotstudiopro-api'),
            'singular_name' => _x('Category', 'Post Type Singular Name', 'dotstudiopro-api'),
            'menu_name' => __('Categories', 'dotstudiopro-api'),
            'name_admin_bar' => __('Categories', 'dotstudiopro-api'),
        );
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => true,
            'capability_type' => 'page',
            'has_archive' => false,
            'menu_position' => 26,
            'menu_icon' => 'dashicons-playlist-video',
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'revisions', 'page-attributes')
        );
        register_post_type('category', $args);
    }

    /**
     * Create MetaBox for Category Post Type
     * 
     * @since 1.0.0
     */
    public function create_custom_metabox() {
        add_meta_box('category_metabox', 'Category: Additional details', array($this, 'create_category_metabox_callback'), 'category', 'normal', 'high');
        add_meta_box('channel_metabox', 'Channel: Additional details', array($this, 'create_channel_metabox_callback'), 'channel', 'normal', 'high');
    }

    /**
     * Custom callback function to generate metabox fields for category post type
     * 
     * 
     * global type $post
     * @since 1.0.0
     */
    function create_category_metabox_callback() {

        global $post;

        $values = get_post_custom($post->ID);
        $cat_poster = isset($values['cat_poster'][0]) ? $values['cat_poster'][0] : '';
        $cat_wallpaper = isset($values['cat_wallpaper'][0]) ? esc_attr($values['cat_wallpaper'][0]) : '';
        $cat_id = isset($values['cat_id'][0]) ? esc_attr($values['cat_id'][0]) : '';
        $in_menu = isset($values['is_in_cat_menu'][0]) ? esc_attr($values['is_in_cat_menu'][0]) : '';
        $on_homepage = isset($values['is_on_cat_homepage'][0]) ? esc_attr($values['is_on_cat_homepage'][0]) : '';

        wp_nonce_field('custom_metabox_nonce', 'custom_metabox');
        ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Category ID</th>
                    <td><input type="text" class="dsp-field" readonly  name="cat_id" id="cat_id" value="<?php echo $cat_id; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Poster</th>
                    <td><input type="text" name="cat_poster" readonly class="dsp-field" id="cat_poster" value="<?php echo $cat_poster; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Wallpaper</th>
                    <td><input type="text" name="cat_wallpaper" readonly class="dsp-field" id="cat_wallpaper" value="<?php echo $cat_wallpaper; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Show on homepage</th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" disabled name="is_on_cat_homepage" <?php echo!empty($on_homepage) ? 'checked' : ''; ?>>
                            <span class="switch-slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Show on menu(s)</th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" disabled name="is_in_cat_menu" <?php echo!empty($in_menu) ? 'checked' : ''; ?>>
                            <span class="switch-slider round"></span>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php
    }

    /**
     * Custom function to save metabox values for category post type
     * 
     * @param type $post_id
     * @since 1.0.0
     */
    function category_metabox_save($post_id) {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        if (!isset($_POST['custom_metabox']) || !wp_verify_nonce($_POST['custom_metabox'], 'custom_metabox_nonce'))
            return;
        if (!current_user_can('edit_post', $post_id))
            return;
        if ($_POST['post_type'] == 'category' || $_POST['post_type'] == 'channel') {
            return;
        }
    }

    /**
     * Function to add a custom action button to custom post types (Category | Channels)
     * 
     * @global type $current_screen
     * @since 1.0.0
     */
    public function add_button_to_custom_posttypes() {
        global $current_screen;
        if ($current_screen->post_type == 'category') {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($)
                {
                    jQuery(jQuery(".wrap .wp-heading-inline")).after("<button id='import_categories' class='add-new-h2 import_categories' data-nonce='<?php echo wp_create_nonce('import_catagory'); ?>' data-action='import_category_post_data'><i class='fa fa-cloud-download' aria-hidden='true'></i> Import Categories</button>");
                });
            </script>
            <?php
        } elseif ($current_screen->post_type == 'channel') {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($)
                {
                    jQuery(jQuery(".wrap .wp-heading-inline")).after("<button id='import_channels' class='add-new-h2 import_channels' data-nonce='<?php echo wp_create_nonce('import_channel'); ?>' data-action='import_channel_post_data'><i class='fa fa-cloud-download' aria-hidden='true'></i> Import Channels</button>");
                });
            </script>
            <?php
        } else {
            return;
        }
    }

    /**
     * Add Custom Column Title (Category ID) to Category Post Type List. 
     * 
     * @param array $columns
     * @return array
     * @since 1.0.0
     */
    public function dsp_category_table_head($columns) {
        foreach ($columns as $key => $value) {
            if ($key == 'author') {  // when we find the date column
                $new['category_id'] = 'Category ID';  // put the tags column before it
                $new['slug'] = 'Slug';  // put the tags column before it
            }
            $new[$key] = $value;
        }
        return $new;
    }

    /**
     * Add Custom Column Value (Category ID) to Category Post Type List. 
     * 
     * @param type $column_name
     * @param type $post_id
     * @since 1.0.0
     */
    public function dsp_category_table_content($column_name, $post_id) {
        if ($column_name == 'category_id') {
            $cat_id = get_post_meta($post_id, 'cat_id', true);
            echo (!empty($cat_id)) ? $cat_id : '-';
        }
        if ($column_name == 'slug') {
            echo basename(get_permalink($post_id));
        }
    }

    /**
     * Add Custom Column Title (Channel ID) to Category Post Type List. 
     * 
     * @param array $columns
     * @return array
     * @since 1.0.0
     */
    public function dsp_channel_table_head($columns) {
        foreach ($columns as $key => $value) {
            if ($key == 'author') {  // when we find the date column
                $new['channel_id'] = 'Channel ID';  // put the tags column before it
                $new['type'] = 'Type';
                $new['slug'] = 'Slug';
            }
            $new[$key] = $value;
        }
        return $new;
    }

    /**
     * Add Custom Column Value (Category ID) to Category Post Type List. 
     * 
     * @param type $column_name
     * @param type $post_id
     * @since 1.0.0
     */
    public function dsp_channel_table_content($column_name, $post_id) {
        if ($column_name == 'channel_id') {
            $cat_id = get_post_meta($post_id, 'chnl_id', true);
            echo (!empty($cat_id)) ? $cat_id : '-';
        }
        if ($column_name == 'type') {
            $cat_id = get_post_meta($post_id, 'chnl_child_channels', true);
            echo (!empty($cat_id)) ? 'Parent' : 'Single';
        }
        if ($column_name == 'slug') {
            echo basename(get_permalink($post_id));
        }
    }

    /**
     * This function is used for import the Category data form DSP External API to Custom Post Type (Category).
     * 
     * @global type $user_ID
     * @since 1.0.0
     */
    public function import_category_post_data() {
        global $user_ID;

        if (wp_verify_nonce($_POST['nonce'], 'import_catagory')) {

            $country = $this->dspExternalApiClass->get_country();

            if (!is_wp_error($country)) {

                $categories = $this->dspExternalApiClass->get_categories();

                if (!is_wp_error($categories)) {
                    $add_count = 0;
                    $update_count = 0;
                    foreach ($categories['categories'] as $category) {
                        if (isset($category['platforms'][0]['website'])) {
                            $args = array(
                                'fields' => 'ids',
                                'post_type' => 'category',
                                'meta_query' => array(
                                    array(
                                        'key' => 'cat_id',
                                        'value' => $category['_id']
                                    )
                                )
                            );
                            $my_query = new WP_Query($args);
                            $posts = $my_query->posts;
                            $new_post = array(
                                'post_title' => $category['name'],
                                'post_content' => $category['description'],
                                'post_status' => 'publish',
                                'post_date' => date('Y-m-d H:i:s'),
                                'post_author' => $user_ID,
                                'post_type' => 'category',
                                'post_name' => $category['slug'],
                            );
                            if (empty($my_query->have_posts())) {
                                $post_id = wp_insert_post($new_post);
                                $add_count++;
                            } else {
                                $new_post['ID'] = $posts[0];
                                $post_id = wp_update_post($new_post);
                                $update_count++;
                            }
                            update_post_meta($post_id, 'cat_id', $category['_id']);
                            update_post_meta($post_id, 'cat_wallpaper', $category['wallpaper']);
                            update_post_meta($post_id, 'cat_poster', $category['poster']);
                            update_post_meta($post_id, 'is_in_cat_menu', $category['menu']);
                            update_post_meta($post_id, 'is_on_cat_homepage', $category['homepage']);
                        }
                    }
                    $send_response = array('success' => true, 'message' => $add_count . ' Categories added.<br/>' . $update_count . ' Categories Updated');
                    wp_send_json_success($send_response, 200);
                } else {
                    $send_response = array('success' => false, 'message' => 'Could not connect to update server.' . $categories->get_error_message());
                    wp_send_json_error($send_response, 403);
                }
            } else {
                $send_response = array('success' => false, 'message' => 'Could not connect to update server.' . $country->get_error_message());
                wp_send_json_error($send_response, 403);
            }
        } else {
            $send_response = array('success' => false, 'message' => 'Internal Server Error.');
            wp_send_json_error($send_response, 500);
        }
    }

    /**
     * This function is used for import the Channel data form DSP External API to Custom Post Type (Channel).
     * 
     * @global type $user_ID
     * @since 1.0.0
     */
    public function import_channel_post_data() {

        global $user_ID;

        if (wp_verify_nonce($_POST['nonce'], 'import_channel')) {

            $country = $this->dspExternalApiClass->get_country();

            if (!is_wp_error($country)) {

                $channels = $this->dspExternalApiClass->get_channels();

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
                            'post_content' => ($channel['description']) ? $channel['description'] : '',
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
                        $channel_id = $channel['_id'];
                        $company_id = $channel['company_id'];
                        $writers = implode(',', $channel['writers']);
                        $genres = implode(',', $channel['genres']);
                        $directors = implode(',', $channel['directors']);
                        $actors = implode(',', $channel['actors']);
                        $poster = $channel['poster'];
                        $childchannels = $channel['childchannels'];
                        $categories = $channel['categories'];

                        update_post_meta($post_id, 'chnl_id', $channel_id);
                        update_post_meta($post_id, 'chnl_writers', $writers);
                        update_post_meta($post_id, 'chnl_geners', $genres);
                        update_post_meta($post_id, 'chnl_directors', $directors);
                        update_post_meta($post_id, 'chnl_actors', $actors);
                        update_post_meta($post_id, 'chnl_poster', $poster);
                        update_post_meta($post_id, 'chnl_comp_id', $company_id);

                        if (isset($categories)) {
                            $category = array();
                            foreach ($categories as $cat) {
                                $category[] = $cat['slug'];
                            }
                            update_post_meta($post_id, 'chnl_catagories', implode(',', $category));
                        }
                        if (isset($childchannels)) {
                            $childchannel = array();
                            foreach ($childchannels as $child) {
                                $childchannel[] = $child['slug'];
                            }
                            update_post_meta($post_id, 'chnl_child_channels', implode(',', $childchannel));
                        }
                    }
                    $send_response = array('success' => true, 'message' => $add_count . ' Channels added.<br/>' . $update_count . ' Channels Updated.');
                    wp_send_json_success($send_response, 200);
                } else {
                    $send_response = array('success' => false, 'message' => 'Could not connect to update server.' . $channels->get_error_message());
                    wp_send_json_error($send_response, 403);
                }
            } else {
                $send_response = array('success' => false, 'message' => 'Could not connect to update server.' . $country->get_error_message());
                wp_send_json_error($send_response, 403);
            }
        } else {
            $send_response = array('success' => false, 'message' => 'Internal Server Error.');
            wp_send_json_error($send_response, 500);
        }
    }

    /**
     * Custom callback function to generate metabox fields for channel post type
     * 
     * 
     * global type $post
     * @since 1.0.0
     */
    function create_channel_metabox_callback() {

        global $post;

        $values = get_post_custom($post->ID);
        $chnl_id = isset($values['chnl_id'][0]) ? esc_attr($values['chnl_id'][0]) : '';
        $chnl_comp_id = isset($values['chnl_comp_id'][0]) ? esc_attr($values['chnl_comp_id'][0]) : '';
        $chnl_poster = isset($values['chnl_poster'][0]) ? $values['chnl_poster'][0] : '';
        $chnl_writers = isset($values['chnl_writers'][0]) ? $values['chnl_writers'][0] : '';
        $chnl_geners = isset($values['chnl_geners'][0]) ? $values['chnl_geners'][0] : '';
        $chnl_directors = isset($values['chnl_directors'][0]) ? $values['chnl_directors'][0] : '';
        $chnl_catagories = isset($values['chnl_catagories'][0]) ? $values['chnl_catagories'][0] : '';
        $chnl_actors = isset($values['chnl_actors'][0]) ? $values['chnl_actors'][0] : '';
        $chnl_child_channels = isset($values['chnl_child_channels'][0]) ? $values['chnl_child_channels'][0] : '';

        wp_nonce_field('category_metabox_nonce', 'category_metabox');
        ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Channel ID</th>
                    <td><input type="text" class="dsp-field"  name="chnl_id" id="chnl_id" value="<?php echo $chnl_id; ?>" readonly/></td>
                </tr>
                <tr>
                    <th scope="row">Company ID</th>
                    <td><input type="text" name="chnl_comp_id" class="dsp-field" id="chnl_comp_id" value="<?php echo $chnl_comp_id; ?>" readonly /></td>
                </tr>
                <tr>
                    <th scope="row">Writers</th>
                    <td><input type="text" name="chnl_writers" class="dsp-field" id="chnl_writers" value="<?php echo $chnl_writers; ?>" readonly data-role="tagsinput"/></td>
                </tr>
                <tr>
                    <th scope="row">Geners</th>
                    <td><input type="text" name="chnl_geners" class="dsp-field" id="chnl_geners" value="<?php echo $chnl_geners; ?>" readonly data-role="tagsinput"/></td>
                </tr>
                <tr>
                    <th scope="row">Directors</th>
                    <td><input type="text" name="chnl_directors" class="dsp-field" id="chnl_directors" value="<?php echo $chnl_directors; ?>" readonly data-role="tagsinput"/></td>
                </tr>
                <tr>
                    <th scope="row">Categories</th>
                    <td><input type="text" name="chnl_catagories" class="dsp-field" id="chnl_catagories" value="<?php echo $chnl_catagories; ?>" readonly data-role="tagsinput"/></td>
                </tr>
                <tr>
                    <th scope="row">Actors</th>
                    <td><input type="text" name="chnl_actors" class="dsp-field" id="chnl_actors" value="<?php echo $chnl_actors; ?>" readonly data-role="tagsinput"/></td>
                </tr>
                <tr>
                    <th scope="row">Poster</th>
                    <td><input type="text" name="chnl_poster" class="dsp-field" id="chnl_poster" value="<?php echo $chnl_poster; ?>" readonly/></td>
                </tr>
                <tr>
                    <th scope="row">Child Chanels</th>
                    <td><input type="text" name="chnl_child_channels" class="dsp-field" id="chnl_child_channels" value="<?php echo $chnl_child_channels; ?>" readonly data-role="tagsinput"/></td>
                </tr>
            </tbody>
        </table>

        <?php
    }

    /**
     * Function to remove Add new Submenu form Channel and Category Custom post type
     * 
     * @since 1.0.0
     * 
     */
    public function remove_submenus() {
        global $submenu;
        unset($submenu['edit.php?post_type=category'][10]); // Removes 'Add New'.
        unset($submenu['edit.php?post_type=channel'][10]); // Removes 'Add New'.
    }

}
