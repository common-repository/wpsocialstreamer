<?php
/**
 * Extend BuddyPress user settings page
 *
 * @package WPSocialStreamer
 * @subpackage BuddyPressFrontEnd
 */

/**
 * Add our globals to BuddyPress
 * 
 * @global type $bp
 */
function wpspcialstreamer_add_options_nav() {
  global $bp;
  
  bp_core_new_subnav_item(
    array(
      'name'            => __('Social Streamer', 'wpsocialstreamer'),
      'slug'            => 'wpsocialstreamer',
      'parent_url'      => $bp->loggedin_user->domain . BP_SETTINGS_SLUG . '/',
      'parent_slug'     => BP_SETTINGS_SLUG,
      'screen_function' => 'wpsocialstreamer_user_settings',
      'position'        => 10,
      'user_has_access' => bp_is_my_profile()
    )
  );  
}
add_action('bp_init', 'wpspcialstreamer_add_options_nav');


/**
 * Handle Settings Page on BuddPress user profiles
 * 
 * @global type $bp
 */
function wpsocialstreamer_user_settings() {
  global $bp;
  
  if ($bp->displayed_user->id != $bp->loggedin_user->id) {
    header( 'location' . get_site_url() );
  }
  
  add_action('bp_template_title',   'wpsocialstreamer_screen_title');
  add_action('bp_template_content', 'wpsocialstreamer_screen_content');
  
  bp_core_load_template( apply_filters('bp_core_template_pugin', 'members/single/plugins') );
}

/**
 * Generate the title for user settings page
 */
function wpsocialstreamer_screen_title() {
  __('Social Streamer', 'wpsocialstreamer');
}

/**
 * Include content for the settings page
 * 
 */
function wpsocialstreamer_screen_content() {
  global $wpss, $wpss_extensions;
  
  // Include settings
  include( $wpss->includes_dir . 'admin/admin-default-settings.php' );
  
  if ( isset($_POST['wpsocialstreamer-user-submit']) ) {
    
    // Check the nonce field
    if ( wp_verify_nonce( $_POST['_wpnonce'], 'wpsocialstreamer_user_settings_save') ) {
      
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
      
  <h3><?php _e('Social Streamer Settings', 'wpsocialstreamer'); ?></h3>
  
  <form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/wpsocialstreamer/'; ?>" method="post" class="standard-form" id="settings-form">
    
    <?php wp_nonce_field('wpsocialstreamer_user_settings_save'); ?>
    
    <p><?php _e("Select activities that you want to share on your Facebook wall with your friends.", 'wpsocialstreamer'); ?></p>
    <p><?php _e("Post to Facebook when:", 'wpsocialstreamer'); ?></p>
    
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
                  <td class="yes"><input type="radio" name="wpsocialstreamer-<?php echo $extension; ?>[<?php echo $event; ?>]" value="yes" <?php echo $yes ?> /></td>
                  <td class="no"><input type="radio" name="wpsocialstreamer-<?php echo $extension; ?>[<?php echo $event; ?>]" value="no" <?php echo $no; ?> /></td>              
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
    
    <?php //do_action('wpsocialstreamer_user_settings'); ?>
    
    <div class="submit">
      <input type="submit" name="wpsocialstreamer-user-submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="wpsocialstreamer-user-submit" class="auto" />
    </div>
  </form>
  <?php
  
}
?>
