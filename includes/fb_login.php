<?php
/**
 *
 * WPSocialStreamer Custom Facebook Login Script
 *
 * This file adds the ability to login with Facebook if Ultimate Arcade is not active
 *
 */
 
/**
 * Setup FB functions
 */
 global $wpss_settings;
function wpss_fb_setup(){
  wp_register_style( 'wpsocialstreamer_fb', plugins_url('assets/css/wpsocialstreamer_fb.css', __FILE__) );
  wp_enqueue_style( 'wpsocialstreamer_fb' );
  wp_enqueue_script( 'jquery' );
  wpss_fb_init();
  wpss_fb_includes();
  wpss_fb_functions();
} 
add_action('login_footer','wpss_fb_setup');

/**
 * Initiate the FB JS SDK
 */
function wpss_fb_init(){
	// Aquire FB app ID
    global $wpss;
	
	// Output FB initiation script
	echo "
	  <div id=\"fb-root\"></div>
	  <script>
      window.fbAsyncInit = function() {
        FB.init({ appId: '".$wpss->settings['facebook_appID']."', 
          status: true, 
          cookie: true,
          xfbml: true,
          oauth: true
		});
      };
      </script>
	";
}

/**
 * Include FB JS SDK source
 */
function wpss_fb_includes(){
	// Output FB JS SDK script
	echo "
	  <script>
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
      </script>
	";
}

/**
 * Include additional FB functions
 */
function wpss_fb_functions(){	
  // Output additional FB script
  if(!is_user_logged_in()){
    echo "
    <script>
	function loginUser() { 
	  FB.login(function(response) { 
	    if (response.authResponse) {
          //User logged in and fully authorized
		  FB.api('/me', function(response) {
		    var fblo = 'true';
	        var fblo_email = response.email;
	        var fblo_user = response.username;
            jQuery.post('".plugins_url( 'includes/validate.php' , dirname(__FILE__) )."', { 'fblo': fblo, 'fblo_email': fblo_email, 'fblo_user': fblo_user },function(data) { 
			  window.location.replace('".home_url()."');
			});
		  });
		} else {
          //User cancelled login or did not fully authorize
		}
	  }, {scope:'user_about_me,email,publish_stream'});
	}
	function logoutUser() { 
	  var fblo_out = 'true';
	  jQuery.post('".plugins_url( 'includes/validate.php' , dirname(__FILE__) )."', { 'fblo_out': fblo_out },function(data) { 
		window.location.replace('".home_url()."');
	  });
	}
    </script>
    ";
  }
}

/**
 * Include FB login buttons
 */
function wpss_fb_buttons(){
  // Outpu FB login buttons
  if(!is_user_logged_in()){
    echo '
      <div id="facebook_login">
        <p><a href="javascript:void(0);" class="wpss_fb" onClick="loginUser();">Connect with Facebook</a></p>
      </div>
    ';
  } else{
	echo '
      <div id="facebook_login">
        <p><a href="javascript:void(0);" class="wpss_fb" onClick="logoutUser();">Logout of Facebook</a></p>
      </div>
    ';
  }
}
add_action('login_form','wpss_fb_buttons');

/**
 * Display FB buttons
 */
function wpss_display_fb_buttons(){
  wpss_fb_buttons();
  add_action('wp_footer','wpss_fb_setup');
}

/**
 * Remove Facebook logged in user meta
 */
function wpss_fb_logging_out(){
  // Remove FB login meta
  $current_user = get_currentuserinfo();
  delete_user_meta($current_user->ID,'FB_loggedin');
} add_action('clear_auth_cookie','wpss_fb_logging_out');

if(!function_exists('get_user_id_from_string')){
  function get_user_id_from_string( $string ) {
	$user_id = 0;
	if ( is_email( $string ) ) {
	  $user = get_user_by('email', $string);
	  if ( $user )
	    $user_id = $user->ID;
    } elseif ( is_numeric( $string ) ) {
	  $user_id = $string;
    } else {
	  $user = get_user_by('login', $string);
	  if ( $user )
		$user_id = $user->ID;
	}
    return $user_id;
  }
}
?>