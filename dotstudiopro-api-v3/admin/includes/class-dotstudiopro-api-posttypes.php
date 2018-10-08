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
    public function create_category_metabox() {
        add_meta_box('category_metabox', 'Category: Additional details', array($this, 'create_category_metabox_callback'), 'category', 'normal', 'high');
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

        wp_nonce_field('category_metabox_nonce', 'category_metabox');
        ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Category ID</th>
                    <td><input type="text" class="dsp-field" readonly  name="cat_id" id="cat_id" value="<?php echo $cat_id; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Poster</th>
                    <td><input type="text" name="cat_poster" class="dsp-field" id="cat_poster" value="<?php echo $cat_poster; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Wallpaper</th>
                    <td><input type="text" name="cat_wallpaper" class="dsp-field" id="cat_wallpaper" value="<?php echo $cat_wallpaper; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Show on homepage</th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="is_on_cat_homepage" <?php echo!empty($on_homepage) ? 'checked' : ''; ?>>
                            <span class="switch-slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Show on menu(s)</th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="is_in_cat_menu" <?php echo!empty($in_menu) ? 'checked' : ''; ?>>
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

        if (!isset($_POST['category_metabox']) || !wp_verify_nonce($_POST['category_metabox'], 'category_metabox_nonce'))
            return;

        if (!current_user_can('edit_post', $post_id))
            return;

        if (isset($_POST['cat_poster']))
            update_post_meta($post_id, 'cat_poster', esc_attr($_POST['cat_poster']));

        if (isset($_POST['cat_id']))
            update_post_meta($post_id, 'cat_id', esc_attr($_POST['cat_id']));

        if (isset($_POST['cat_wallpaper']))
            update_post_meta($post_id, 'cat_wallpaper', esc_attr($_POST['cat_wallpaper']));

        $in_menu = isset($_POST['is_in_cat_menu']) && $_POST['is_in_cat_menu'] ? '1' : '';
        update_post_meta($post_id, 'is_in_cat_menu', $in_menu);

        $on_homepage = isset($_POST['is_on_cat_homepage']) && $_POST['is_on_cat_homepage'] ? '1' : '';
        update_post_meta($post_id, 'is_on_cat_homepage', $on_homepage);
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
                    jQuery(jQuery(".wrap .page-title-action")).after("<button id='import_categories' class='add-new-h2 import_categories' data-target='<?php echo admin_url('admin-ajax.php'); ?>' data-nonce='<?php echo wp_create_nonce('import_catagory'); ?>' data-action='import_category_post_data'><i class='fa fa-cloud-download' aria-hidden='true'></i> Import Categories</button>");
                });
            </script>
            <?php
        } elseif ($current_screen->post_type == 'channel') {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($)
                {
                    jQuery(jQuery(".wrap .page-title-action")).after("<button id='import_channels' class='add-new-h2' data-target='<?php echo admin_url('admin-ajax.php'); ?>' data-nonce='<?php echo wp_create_nonce('dsp_reset_token'); ?>' data-action='import_category_post_data'><i class='fa fa-cloud-download' aria-hidden='true'></i> Import Channels</button>");
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

        $columns['category_id'] = 'Category ID';
        $new = array();
        $tags = $columns['category_id'];  // save the tags column
        unset($columns['category_id']);   // remove it from the columns list

        foreach ($columns as $key => $value) {
            if ($key == 'author') {  // when we find the date column
                $new['category_id'] = $tags;  // put the tags column before it
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
                    echo $add_count . ' Categories added.<br/>' . $update_count . ' Categories Updated';
                    exit;
                } else {
                    echo 'Something Went wrong.';
                    exit;
                }
            } else {
                echo 'Something Went wrong.';
                exit;
            }
        } else {
            echo 'Something Went wrong.';
            exit;
        }
    }

}
