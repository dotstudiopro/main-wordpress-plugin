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
<?php $dsp_enable_search_field = get_option('dsp_enable_search_field'); ?>
<label class="switch">
  <input type="checkbox" name="dsp_enable_search_field" <?php echo !empty($dsp_enable_search_field) ? 'checked' : ""; ?>>
  <span class="switch-slider round"></span>
</label>