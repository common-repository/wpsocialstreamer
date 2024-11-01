<?php
/**
 *
 * WPSocialStreamer Custom Login Validation
 *
 * This file handles the validation of the custom login scripts
 *
 */
require_once("../../../../wp-load.php");
/**
 * If user has logged in via Facebook
 */
if(isset($_POST['fblo'])){
	
  // If have access to users email address
  if($_POST['fblo_email']){
	  
	// Get the users login details 
    $userid = get_user_id_from_string( $_POST['fblo_email'] );
    $user_info = get_userdata( $userid );
   
    // Try to log in the user
    wp_set_auth_cookie( $user_info->ID, 0, 0 );
	
	// If user does not exist then register new user and log in
    if ( is_wp_error($user) ){
      $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
	  $user_id = wp_create_user( $_POST['fblo_user'], $random_password, $_POST['fblo_email'] );
      wp_set_auth_cookie( $user_id, 0, 0 );
    }
	$userid = get_user_id_from_string( $_POST['fblo_email'] );
    $user_info = get_userdata( $userid );
	update_user_meta($user_info->ID,'FB_loggedin','true');
  }
}
/**
 * If user has logged out in via Facebook
 */
if(isset($_POST['fblo_out'])){
  // Log out current user
  wp_logout();
}
?>