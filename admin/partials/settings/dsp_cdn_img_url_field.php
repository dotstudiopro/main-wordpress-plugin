<?php
/**
 *
 * This file is used to setup a settings section
 *
 * @link       https://www.dotstudiopro.com
 * @since      1.0.0
 *
 * @package    Dotstudiopro_Api
 * @subpackage Dotstudiopro_Api/admin/partials
 */
?>
<?php $dsp_cdn_img_url_field = get_option('dsp_cdn_img_url_field'); ?>
<input type="url" required class="dsp-field form-control" name="dsp_cdn_img_url_field" value="<?php echo !empty($dsp_cdn_img_url_field) ? $dsp_cdn_img_url_field : "https://images.dotstudiopro.com/"; ?>">