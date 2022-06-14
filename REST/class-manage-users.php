<?php

/**
 * The file that manage the Users class
 *
 * Maintain a list of all webhook routes for Channels (Ex: delete channel, add or update channel)
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 *
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/REST
 */
class Dsp_Manage_users {

    /**
     * This Function is used to delete the user when the request is comes form an API Routes.
     *
     * @version 1.0.0
     * @param type $request
     *
     * @return json
     */
    public function delete_user($request) {

        if(isset($request['email']) && !empty($request['email'])){
            $searchuser = get_users( array( 'search' => $request['email'] ) );
            if($searchuser){
                $check_user_role = $this->user_has_role($searchuser[0]->ID, 'subscriber');
                if($check_user_role){
                    wp_delete_user($searchuser[0]->ID);
                    $send_response = array('code' => 'rest_success', 'message' => 'User deleted successfully.', 'data' => array('status' => 200));
                    wp_send_json($send_response, 200);
                }else{
                    return new WP_Error('rest_not_found', __('User role is not a subscriber so we can not delete this user.'), array('status' => 401));
                }
            }else{
                return new WP_Error('rest_not_found', __('User not found to delete.'), array('status' => 404));
            }
        }else{
            return new WP_Error('rest_missing_callback_param', __('Pass the email ID which you would like to delete.'), array('status' => 400));
        }

    }

    public function user_has_role($user_id, $role_name)
    {
        $user_meta = get_userdata($user_id);
        $user_roles = $user_meta->roles;
        return in_array($role_name, $user_roles);
    }

}
