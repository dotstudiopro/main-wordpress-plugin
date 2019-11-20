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
$is_demo = get_option('import_sample_content');
$active = $dotstudiopro_api_key ? true : false;
$nonce = $active ? 'deactivate_dotstudiopro_api_key' : 'activate_dotstudiopro_api_key';
$button = $active ? __('Update Api Key', 'dotstudiopro-api') : __('Activate Api Key', 'dotstudiopro-api');
?>
<div class="wrap dsp-settings-wrap">

    <?php settings_errors(); ?>
    <h1><?php _e('dotstudioPRO API Settings', 'dotstudiopro-api'); ?></h1>
    <?php $dotstudiopro_api_key = get_option('dotstudiopro_api_key'); ?>

    <div class="dsp-box dsp-box-hidden" style="display: none;">
        <div class="inner">
            <div class="ajax-resp"></div>
        </div>
    </div>

    <div class="dsp-box" id="dsp-license-information">
        <div class="title">
            <h3><?php _e('API key information', 'dotstudiopro-api'); ?></h3>
        </div>
        <div class="inner">
            <form action="#" class="dsp_api" id="dsp_api_form">
                <input type='hidden' name='action' value='validate_dotstudiopro_api' />
                <input type='hidden' name='dpro_api_key' value='c681a9f6a3b9d51502cc3978298feaccfa9f500b' />
                <?php
                    if($is_demo){
                        if(!$active){
                            submit_button(__('Import Sample Content', 'dotstudiopro-api'), 'primary', 'import-sample-api-data');
                            ?>
                            <p>
                            OR
                            <br/>
                        <?php } ?>
                        <?php printf(__('Please add your dotstudioPRO API key here. If you don\'t have API key <a href="%s" target="_blank">click here</a>.', 'dotstudiopro-api'), esc_url('https://www.dotstudiopro.com/user/register')); ?></p>
                        
                        <div class="dsp-hidden">
                            <?php dsp_nonce_input($nonce); ?>
                            <input type='hidden' name='action' value='validate_dotstudiopro_api' />
                            <input type='hidden' name='dpro_apy_key' value='c681a9f6a3b9d51502cc3978298feaccfa9f500b' />
                            <input type="hidden" name="_category_nonce" value="<?php echo wp_create_nonce('import_catagory'); ?>">
                            <input type="hidden" name="_channel_nonce" value="<?php echo wp_create_nonce('import_channel'); ?>">
                        </div>
                        <?php do_settings_sections('dsp-api-key-section'); ?>
                        <div class="buttons">
                            <?php submit_button($button, 'primary', 'submit-api-data'); ?>
                            <?php if ($active): ?>
                                <?php submit_button(__('Deactivate Api Key', 'dotstudiopro-api'), 'primary', 'deactivate-api-data'); ?>
                            <?php endif; ?>
                        </div>
                    <?php 
                    }
                    else{
                        $nonce = 'deactivate_dotstudiopro_api_key';
                        dsp_nonce_input($nonce); 
                        echo __('You have imported the sample channel and categories using our demo API key. If you have your own dotstudioPRO API key, then remove sample content and import the live content using the API key.', 'dotstudiopro-api');
                        echo '<br/>';
                        printf(__('If you don\'t have API key <a href="%s" target="_blank">click here</a>.', 'dotstudiopro-api'), esc_url('https://www.dotstudiopro.com/user/register'));
                        submit_button(__('Remove Sample Content', 'dotstudiopro-api'), 'primary', 'remove-sample-api-data');
                    }
                ?>
            </form>
        </div>
    </div>

    <?php if ($active && $is_demo):?>
        <div class="dsp-box" id="dsp-license-information">
            <div class="title">
                <h3><?php _e('Development Mode Options', 'dotstudiopro-api'); ?></h3>
            </div>
            <div class="inner">
                <p><?php _e('Please note: Any options set here will override normal settings. Please make sure to turn these settings off when you are done testing.', 'dotstudiopro-api') ?></p>
                <form method="post" action="options.php">
                    <input type="hidden" name="option_page" value="dsp-dev-mode-section">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('dsp-dev-mode-section-options'); ?>">
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
                    <input type="hidden" name="option_page" value="dsp-setting-section">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('dsp-setting-section-options'); ?>">
                    <?php do_settings_sections('dsp-setting-section'); ?>
                    <?php submit_button('Save Changes', 'primary', 'submit-general-data'); ?>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
