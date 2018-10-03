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
<?php $dsp_video_autoplay_field = get_option('dsp_video_autoplay_field'); ?>
<label class="switch">
  <input type="checkbox" name="dsp_video_autoplay_field" <?php if(isset($dsp_video_autoplay_field) && !empty($dsp_video_autoplay_field)){ echo 'checked'; } ?>>
  <span class="switch-slider round"></span>
</label>