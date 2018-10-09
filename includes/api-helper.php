<?php

/**
 * Core helper functions
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/includes
 */


/*
*  dsp_nonce_input
*
*  This function will create a basic nonce input
*
*  @type	function
*  @since	1.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/
function dsp_nonce_input($nonce = '') {
    echo '<input type="hidden" name="_dsp_nonce" value="' . wp_create_nonce($nonce) . '" />';
}

/**
  *  dsp_verify_nonce
  *
  *  This function will look at the $_POST['_acf_nonce'] value and return true or false
  *
  *  @type    function
  *  @since   1.0.0
  *
  *  @param   $nonce (string)
  *  @return  (boolean)
  */
function dsp_verify_nonce( $value) {
    
    // vars
    $nonce = dsp_get_post_field('_dsp_nonce');
    
    
    // bail early nonce does not match (post|user|comment|term)
    if( !$nonce || !wp_verify_nonce($nonce, $value) ) return false;
    
    
    // reset nonce (only allow 1 save)
    $_POST['_dsp_nonce'] = false;
    
    
    // return
    return true;
        
}
/**
  *  dsp_get_post_field
  *
  *  This function will return a var if it exists in an array
  *
  *  @type    function
  *  @since   1.0.0
  *
  *  @param   $array (array) the array to look within
  *  @param   $key (key) the array key to look for. Nested values may be found using '/'
  *  @param   $default (mixed) the value returned if not found
  *  @return  $post_id (int)
  */
function dsp_get_post_field( $key = '', $default = null ) {
    
    return isset( $_POST[$key] ) ? $_POST[$key] : $default;
    
}

?>