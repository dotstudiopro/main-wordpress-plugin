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
<?php $dsp_video_color_field = get_option('dsp_video_color_field'); ?>
<input type="text" name="dsp_video_color_field" value="<?php echo !empty($dsp_video_color_field) ? $dsp_video_color_field : ""; ?>" class="color-field">
