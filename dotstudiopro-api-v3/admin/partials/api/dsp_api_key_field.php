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
    <input type="password" id="dotstudiopro_api_key" readonly name="dotstudiopro_api_key" class="dsp-field valid-api-key" value="<?php
    if (isset($dotstudiopro_api_key) && !empty($dotstudiopro_api_key)) {
        echo $dotstudiopro_api_key;
    }
    ?>">
       <?php } else { ?>
    <input type="text" id="dotstudiopro_api_key" required name="dotstudiopro_api_key" class="dsp-field" value="<?php
           if (isset($dotstudiopro_api_key) && !empty($dotstudiopro_api_key)) {
               echo $dotstudiopro_api_key;
           }
           ?>">
<?php } ?>
