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
$dotstudiopro_api_key = get_option('dotstudiopro_api_key');
$active = $dotstudiopro_api_key ? true : false;

if ($active) {
    ?>
    <input type="password" id="dotstudiopro_api_key" name="dotstudiopro_api_key" class="dsp-field valid-api-key form-control" value="<?php echo !empty($dotstudiopro_api_key) ? $dotstudiopro_api_key : ""; ?>">
    <span toggle="#dotstudiopro_api_key" class="fa fa-fw fa-eye field-icon toggle-password-field"></span>
       <?php } else { ?>
    <input type="text" id="dotstudiopro_api_key" required name="dotstudiopro_api_key" class="dsp-field" value="<?php echo !empty($dotstudiopro_api_key) ? $dotstudiopro_api_key : ""; ?>">
<?php } ?>
