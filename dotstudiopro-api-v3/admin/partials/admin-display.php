<?php
/**
 *
 * This file is used to setup the main settings area
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/admin/partials
 */
$dotstudiopro_api_key = get_option('dotstudiopro_api_key');
$active = $dotstudiopro_api_key ? true : false;
$nonce = $active ? 'deactivate_dotstudiopro_api_key' : 'activate_dotstudiopro_api_key';
$button = $active ? __('Deactivate Api Key', 'dotstudiopro-api') : __('Activate Api Key', 'dotstudiopro-api');
?>
<div class="wrap dsp-settings-wrap">
    <?php settings_errors(); ?>
    <h1><?php _e('Dotstudiopro API Settings', 'dotstudiopro-api'); ?></h1>
    <?php $dotstudiopro_api_key = get_option('dotstudiopro_api_key'); ?>
    <div class="dsp-box" id="dsp-license-information">
        <div class="title">
            <h3><?php _e('API key information', 'dotstudiopro-api'); ?></h3>
        </div>
        <div class="inner">
            <p><?php printf(__('Please add you Dotstudio Pro API key here. If you don\'t have API key <a href="%s" target="_blank">click here</a>.', 'dotstudiopro-api'), esc_url('https://www.dotstudiopro.com/user/register')); ?></p>
            <form action="<?php echo get_admin_url() . 'admin-post.php'; ?>" method="post">
                <div class="dsp-hidden">
                    <input type='hidden' name='action' value='validate_dotstudiopro_api' />
                    <?php dsp_nonce_input($nonce); ?>
                </div>
                <?php do_settings_sections('dsp-api-key-section'); ?>
                <?php submit_button($button, 'primary', 'submit-api-data'); ?>
            </form>
        </div>
    </div>

    <?php if ($active): ?>
        <!-- -->
        <div class="dsp-box" id="dsp-license-information">
            <div class="title">
                <h3><?php _e('Development Mode Options', 'dotstudiopro-api'); ?></h3>
            </div>
            <div class="inner">
                <p><?php _e('Please note: Any options set here wil override normal settings. Please make sure to turn these settings off when you done testing.', 'dotstudiopro-api') ?></p>
                <form method="post" action="options.php">
                    <?php settings_fields('dsp-dev-mode-section'); ?>
                    <?php do_settings_sections('dsp-dev-mode-section'); ?>
                    <?php submit_button('Save Changes', 'primary', 'submit-dev-mode-data'); ?>
                </form>
            </div>
        </div>

        <div class="dsp-box" id="dsp-license-information">
            <div class="title">
                <h3><?php _e('General Options', 'dotstudiopro-api'); ?></h3>
            </div>
            <div class="inner">
                <form method="post" action="options.php">
                    <?php settings_fields('dsp-setting-section'); ?>
                    <?php do_settings_sections('dsp-setting-section'); ?>
                    <?php submit_button('Save Changes', 'primary', 'submit-general-data'); ?>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
