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
<?php $dsp_import_limit_field = get_option('dsp_import_limit_field'); ?>
<input type="number" required class="dsp-field form-control" name="dsp_import_limit_field" value="<?php echo !empty($dsp_import_limit_field) ? $dsp_import_limit_field : "100"; ?>">