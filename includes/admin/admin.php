<?php
/**
 * Admin Class
 *
 * @package WPSocialStreamer
 * @subpackage Admin
 */
 
// No direct access!
if ( !defined('ABSPATH') ) exit;

//global $wpss_settings;
add_action( 'admin_menu', 'wpsocialstreamer_admin_menu' );
add_action( 'admin_enqueue_scripts', 'wpsocialstreamer_admin_scripts' );

function wpsocialstreamer_admin_menu() {
  global $wpss;
  
  // Add user options menu
  add_menu_page(
    __('Social Streamer', 'wpsocialstreamer'), 
    __('Social Streamer', 'wpsocialstreamer'), 
    'read', 
    'wpsocialstramer_user_settings_page', 
    'wpsocialstramer_user_settings_page',
    $wpss->includes_url . 'admin/images/wpsocialstreamer_mini.png'
  );  
  
  add_submenu_page(
    'wpsocialstramer_user_settings_page',
    __('Admin Settings', 'wpsocialstreamer'),
    __('Admin Settings', 'wpsocialstreamer'),
    'manage_options',
    basename( $wpss->file ),
    'wpsocialstreamer_settings_page'
  );
}

function wpsocialstreamer_admin_scripts() {
  
  // Get admin screen id
  $screen = get_current_screen();
      
  if ( $screen->id == 'social-streamer_page_wpsocialstreamer' ) {
	// Include admin scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-tabs');
	
	// Include admin styles
	wp_enqueue_style( 'wpsocialstreamer_style', plugins_url('css/style.css', __FILE__) );
  }
}

function wpsocialstreamer_settings_page() {
  include_once('admin-default-settings.php');
  include_once('admin-settings.php');
  
  wpsocialstreamer_settings();
}

function install_wpsocialstreamer() {
  global $wpss, $wpss_default_settings, $wpss_extensions;
  
  include_once('admin-default-settings.php');
  
  if( !$wpss->settings ) {
    $wpss->settings = $wpss_default_settings;
    add_option('wpsocialstreamer_settings', $wpss->settings);
  }
  
  if ( !$wpss->extensions ) {
    foreach ($wpss_extensions as $wpss_extensionname => $wpss_extension) {
      // extract events and templates
      $extractevents = array();
      foreach ($wpss_extension['events'] as $eventname => $events) {
        $extractevents[$eventname] = $events['template'];
      }
      $wpss->extensions[$wpss_extensionname] = array( 'active' => $wpss_extension['active'],
                                                      'events' => $extractevents,
                                                    );
    }
    add_option('wpsocialstreamer_extensions', $wpss->extensions);    
  }
  
  add_option('wpsocialstreamer_version', $wpss->version);
  
}

/**
 * Display a user settings page where user can activate / deactivat events
 */
function wpsocialstramer_user_settings_page() {
  global $wpss, $wpss_extensions;
  
  // Include settings
  include( $wpss->includes_dir . 'admin/admin-default-settings.php' );
  
  ?>
  <style type="text/css">
    table.notification-settings { margin-top: 10px; border-spacing: 7px; text-align:left; }
    .notification-settings th.title { min-width: 300px;}
    .notification-settings thead, .notification-settings td.yes,.notification-settings td.no { text-align: center; }
    input[type="radio"] { display: none; }
    input[type="radio"] + label { background:url('<?php echo $wpss->includes_url . 'admin/images/checkbox_empty.png'; ?>') no-repeat; padding:0; display: inline-block; appearance:none; -moz-appearance:none; -webkit-appearance:none; width:15px; height:15px; vertical-align:middle; }
    input[type="radio"]:checked + label { background:url('<?php echo $wpss->includes_url . 'admin/images/checkbox_full.png'; ?>') no-repeat; }
  </style>
  <script>
  jQuery(document).ready(function() {
    jQuery('label').click(function(){
	  jQuery(this).prev().attr('checked', true);
	});
  });
  </script>
  <div class="wrap">
    <h2><?php _e("Social Streamer User Settings", "wpsocialstreamer"); ?></h2>
    <p><?php _e("Here you can set up your personal settings. Activate or deactivate events that you want to share on your Facebook wall.", 'wpsocialstreamer'); ?></p>
    
    <?php
    if ( isset($_POST['wpsocialstreamer-user-submit-backend']) ) {
      // Check the nonce field
      if ( wp_verify_nonce( $_POST['_wpnonce'], 'wpsocialstreamer_user_settings_save_backend') ) {
        $user_events = array();

        // Update user settings
        foreach ( $wpss_extensions as $extension => $values ) {        
          if ( isset( $_POST['wpsocialstreamer-'.$extension] ) && is_array( $_POST['wpsocialstreamer-'.$extension] ) ) {
            foreach ( $_POST['wpsocialstreamer-'.$extension] as $u_event => $u_setting ) {
              $user_events[$extension][$u_event] = $u_setting;
            }
          }
        }

        if ( !empty($user_events) ) {
          update_user_meta($wpss->current_user->ID, 'wpsocialstreamer', $user_events);
        }      

        // Init user events again
        $wpss->init_user_events();        
        
        
        ?>
        <div class="message updated">
          <p><?php _e("Settings updated..", "wpsocialstreamer"); ?></p>
        </div>
        <?php
      }
      else {
        ?>
        <p class="error">
          <?php _e('Form verification failed! Please go back and try again', 'wpsocialstreamer'); ?>
        </p>
        <?php
      }
    }    
    ?>
    
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" name="myscore" id="myscore">
      <?php wp_nonce_field('wpsocialstreamer_user_settings_save_backend'); ?>
      
      <?php
      foreach ( $wpss_extensions as $extension => $values ) {
        
        // Check if this extension is enabled
        if ( $wpss->is_active_extension($extension) ) {
          
          // Get user settings
          $user_events = $wpss->get_user_events($extension);
          
          // Display Settings on the user page
          ?>
          <table class="notification-settings" id="wpsocialstreamer-<?php echo $extension ?>-settings">
            <thead>
              <tr>
                <th class="icon">&nbsp;</th>
                <th class="title"><?php echo $values['title']; ?> <?php _e("Activities / Events", 'wpsocialstreamer'); ?></th>
                <th class="yes"><?php _e("Yes", 'wpsocialstreamer'); ?></th>
                <th class="no"><?php _e("No", 'wpsocialstreamer'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($values['events'] as $event => $data) {
                // Find the event in user settings
                if ( isset( $user_events[$event] ) ) {
                  $yes  = checked($user_events[$event], 'yes', false);
                  $no   = checked($user_events[$event], 'no', false);
                }
                else {
                  $yes = '';
                  $no  = ' checked="checked"';
                }
                ?>
                <tr>
                  <td>&nbsp;</td>
                  <td><?php echo $data['title']; ?></td>
                  <td class="yes"><input type="radio" name="wpsocialstreamer-<?php echo $extension; ?>[<?php echo $event; ?>]" value="yes" <?php echo $yes ?> /><label></label></td>
                  <td class="no"><input type="radio" name="wpsocialstreamer-<?php echo $extension; ?>[<?php echo $event; ?>]" value="no" <?php echo $no; ?> /><label></label></td>              
                </tr>
                <?php          
              }        
              ?>
            </tbody>
          </table>
          <?php
        }
      }
      ?>      
      
      <?php //do_action( 'wpsocialstreamer_user_settings'); ?>       
      <div class="submit">
        <input type="submit" name="wpsocialstreamer-user-submit-backend" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="wpsocialstreamer-user-submit-backend" class="auto button-primary" />
      </div>       
    </form>
  </div>
  <?php
}
?>