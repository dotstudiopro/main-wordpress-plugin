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
<?php $dsp_api_url_field = get_option('dsp_api_url_field') ?: "https://api.myspotlight.tv/"; ?>
<input type="text" name="dsp_api_url_field" value="<?php echo !empty($dsp_api_url_field) ? $dsp_api_url_field : ""; ?>">
