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
<?php $dsp_is_dev_mode_field = get_option('dsp_is_dev_mode_field'); ?>
<label class="switch">
  <input type="checkbox" name="dsp_is_dev_mode_field" <?php if(isset($dsp_is_dev_mode_field) && !empty($dsp_is_dev_mode_field)){ echo 'checked'; } ?>>
  <span class="switch-slider round"></span>
</label>