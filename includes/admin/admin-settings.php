<?php
/**
 * Functions for the settings page in admin
 *
 * @package WPSocialStreamer
 * @subpackage Settings
 */
 
// No direct access!
if ( !defined('ABSPATH') ) exit; 

global $wpss_extensions;
include ('admin-default-settings.php');

/**
 * Settings page
 *
 * Handles the display of the main WPSocialStreamer settings page in admin
 */
function wpsocialstreamer_settings() {
  global $wpss;
  
  $tab_index_start = 2;  
  ?>    
  <script>
  jQuery(document).ready(function($) {
    $(function() {
		$("#tabs").tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
		$("#tabs li").removeClass('ui-corner-top').addClass('ui-corner-left');
      
		$(".cb-enable").click(function(){
          var parent = $(this).parents('.switch');
          $('.cb-disable',parent).removeClass('selected');
          $(this).addClass('selected');
          $('.checkbox',parent).attr('checked', true);
		  $('.checkbox',parent).val('1');
		  var tabid = '#'+$(parent).parent().attr('id');
		  $(tabid + ' textarea').each(function(){
		    //$(this).attr('disabled', false);
		  });
    	});
    	$(".cb-disable").click(function(){
          var parent = $(this).parents('.switch');
          $('.cb-enable',parent).removeClass('selected');
          $(this).addClass('selected');
          $('.checkbox',parent).attr('checked', false);
		  $('.checkbox',parent).val('');
		  var tabid = '#'+$(parent).parent().attr('id');
		  $(tabid + ' textarea').each(function(){
		    //$(this).attr('disabled', true);
		  });
    	});
    });
  });
  </script>
  <div class="wrap">
    
    <form name="wpsocialstreamer_form" id="wpsocialstreamer_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
        
        <?php wp_nonce_field('wpsocialstreamer_settings_save'); ?>
        
        <div id="tabs">
          <div id="wpsocialstreamer_head">
            <div class="icon32" id="icon-options-general"></div>  
            <h2><?php echo _e('WPSocialStreamer Lite', 'wpsocialstreamer'); ?></h2>
            <?php
            if( isset( $_POST['wpsocialstreamer-submit'] ) ) {
              // Check the nonce field
              if ( wp_verify_nonce( $_POST['_wpnonce'], 'wpsocialstreamer_settings_save') ) {
                // Update settings              
                save_settings($_POST);
                ?>
                <div class="updated"><p><strong><?php _e( 'Options saved.', 'wpsocialstreamer' ); ?></strong></p></div>  
                <?php                  
              }
              else {
                ?>
                <div class="updated"><p><strong><?php _e( 'Error! Try Again!', 'wpsocialstreamer' ); ?></strong></p></div>  
                <?php
              }
            }
            ?>
          </div>
          
          <div style="border-bottom: 2px solid #999;padding:10px;font-weight:bold;text-align:center">
            If you need more supported plugins like BuddyPress, bbPress, MyArcadePlugin, MyArcadeContest and other check <a href="http://exells.com/shop/community/wpsocialstreamer/" title="WPSocialStreamer" target="_blank">WPSocialStreamer</a>.
          </div>
          
          <ul>
            <?php // Show Facebook Settings 
            if( !function_exists('wdfb_dashboard_permissions_widget') ) { ?>
              <li><a href="#tabs-1"><?php _e( 'Facebook Settings', 'wpsocialstreamer' ); ?></a></li>
            <?php } ?>
            
            <?php
            // Output Dynamic Menu Tabs
            generate_extension_menu_tab_title( $tab_index_start );
            ?>
          </ul>       

          <?php // Show Facebook Settings 
          if( !function_exists('wdfb_dashboard_permissions_widget') ) { ?>
            <div id="tabs-1">
              <h4><?php _e( 'Facebook Login Settings', 'wpsocialstreamer' ); ?></h4>
              
              <div class="wpsocialstreamer_alert">
                <?php  _e('WPSocialStreamer has a minimal Facbook Login integration. To get more features and extra widgets you should intall <a href="http://premium.wpmudev.org?ref=DanOne-94904" target="_blank" title="Ultimate Facebook">Ultimate Facebook</a>.' ,'wpsocialstreamer'); ?>
              </div>
              
              <div id="fb_settings">
                <strong><?php _e( 'This step must be completed before using the plugin. You must make a Facebook Application to continue.', 'wpsocialstreamer' ); ?></strong><br /><br />
                <?php _e( 'Before we begin, you need to create a', 'wpsocialstreamer' ); ?> <a href="http://www.facebook.com/developers/createapp.php" target="_blank"><?php _e( 'Facebook Application.', 'wpsocialstreamer' ); ?></a><br /><br />
                 <?php _e( 'To do so, follow these steps:', 'wpsocialstreamer' ); ?>
                <ul>
                  <li><a href="http://www.facebook.com/developers/createapp.php" target="_blank"><?php _e( 'Create your application', 'wpsocialstreamer' ); ?></a></li>
                  <li><?php _e( 'Look for Site URL field in the Web Site tab and enter your site URL in this field:', 'wpsocialstreamer' ); ?> <span><?php echo home_url(); ?></span></li>
                  <li><?php _e( 'After this, go to the', 'wpsocialstreamer' ); ?> <a href="http://www.facebook.com/developers/apps.php" target="_blank"><?php _e( 'Facebook Application List page', 'wpsocialstreamer' ); ?></a> <?php _e( 'and select your newly created application', 'wpsocialstreamer' ); ?></li>
                  <li><?php _e( 'Copy the values from the fields: App ID/API key and App Secret, and enter it here:', 'wpsocialstreamer' ); ?></li>
                </ul>
              </div>
              <p><?php _e( 'Facebook App ID/API Key:', 'wpsocialstreamer' ); ?></p>
              <input type="text" name="facebook_appID" value="<?php if ( isset($wpss->settings['facebook_appID']) ) echo $wpss->settings['facebook_appID']; else echo ''; ?>" /> 
              <p><?php _e( 'Facebook App Secret:', 'wpsocialstreamer' ); ?></p>
              <input type="text" name="facebook_secret" value="<?php if (isset($wpss->settings['facebook_secret']) ) echo $wpss->settings['facebook_secret']; else echo ''; ?>" /> 
            </div>
          <?php } ?>
          
          <?php 
          // Output Dynamic Menu Tabs
          generate_extension_tab_content( $tab_index_start );
          ?>
          <div class="clear"></div>
        </div>
      
        <div id="wpsocialstreamer_foot">
          <input type="submit" name="wpsocialstreamer-submit" id="wpsocialstreamer-submit" class="prim_btn" value="<?php _e( 'Save Settings', 'wpsocialstreamer' ) ?>" />
        </div>
    </form>    
  </div>
  <?php
  
}

/**
 * Save the new settings
 */
function save_settings( $submitted_settings ) {
  global $wpss;
  
  //____________________________________________________________________________
  // Update general settings
	
  if( !function_exists('wdfb_dashboard_permissions_widget') ) {
    // Save Facebook Settings
    $wpss->settings['facebook_appID']   = isset($submitted_settings['facebook_appID']) ? $submitted_settings['facebook_appID'] : '';
    $wpss->settings['facebook_secret']  = isset($submitted_settings['facebook_secret']) ? $submitted_settings['facebook_secret'] : '';
  }
  
  update_option('wpsocialstreamer_settings', $wpss->settings);  
  
  //____________________________________________________________________________
  // Update extension settings
  if ( isset( $submitted_settings['extension'] ) && is_array($submitted_settings['extension']) ) {
    foreach ($submitted_settings['extension'] as $extension => $setting) {
      
      if ( isset($setting['active']) ) {
        $extension_active = true; 
      }
      else {
        $extension_active = false;
      }
      
      $wpss->extensions[$extension] = array( 
        'active' => $extension_active,
        'events' => $setting['events']
      );
    }
    
    update_option('wpsocialstreamer_extensions', $wpss->extensions);  
  }
}

/**
 * Create Menu Tabs
 */
function generate_extension_menu_tab_title( $menu_tab_index = false ) {
  global $wpss, $wpss_extensions;
  
  if ( !empty( $menu_tab_index ) and ( $menu_tab_index > 0 ) ) {
    foreach ($wpss_extensions as $extension) {
      echo '<li><a href="#tabs-'.$menu_tab_index.'">'.$extension['title'].'</a></li>';
      $menu_tab_index++;
    }
  } //else do nothing
}

/**
 * Create Tab Content
 */
function generate_extension_tab_content( $menu_tab_index = false ) {
  global $wpss, $wpss_extensions;
  
  if ( !empty( $menu_tab_index ) and ( $menu_tab_index > 0 ) ) {
    foreach ($wpss_extensions as $extensionname => $extension) {
      echo '<div id="tabs-'.$menu_tab_index.'">';
      
      // Check if function exists
      if ( $extension['function'] && !function_exists( $extension['function'] ) ) {
        ?>
        <div class="wpsocialstreamer_alert">
          <?php echo sprintf( __('To be able to use this extension you must install: <a href="%s" target="_blank">%s</a>!' ,'wpsocialstreamer'),  $extension['link'], $extension['title']); ?>
        </div>
        <?php
      }
      
      // Check if extension is active
      if( $wpss->is_active_extension($extensionname) ) { $active = true; } else{ $active = false; }
      
      echo '<h4>'.__('Activate ', 'wpsocialstreamer' ).$extension['title'].'</h4>'; 
      ?>
      <p class="field switch">
        <label class="cb-enable <?php if($active){echo 'selected';} ?>"><span><?php _e( 'On', 'wpsocialstreamer' ); ?></span></label>
        <label class="cb-disable <?php if(!$active){echo 'selected';} ?>"><span><?php _e( 'Off', 'wpsocialstreamer' ); ?></span></label>
        <input type="checkbox" class="checkbox" name="extension[<?php echo $extensionname; ?>][active]" value="<?php if ($active) echo "true"; else echo "false"; ?>" <?php if($active){echo 'checked="checked"';} ?> />
      </p>
      <p class="checkbox-desc"><em><?php _e( 'Select to activate/deactivate streaming events', 'wpsocialstreamer' ); ?></em></p>
      <br />
      <?php 
      generate_extension_event_content( $extensionname );
      echo '</div>';
      $menu_tab_index++;
    }
  }
}

/**
 * Create Event Content
 */
function generate_extension_event_content( $extensionname = false ) {
  global $wpss, $wpss_extensions;
  
  if ( !empty( $extensionname ) ) {
    
    foreach ($wpss_extensions[$extensionname]['events'] as $eventname => $event) {
      $textareaswitch = '';
      echo '<h4>'.$event['title'].'</h4>';
      // Output list of Placeholders with description
      echo '<p class="variables">'.__( 'Available Variables:', 'wpsocialstreamer' ).'<br />
      <em>';
      foreach ( $event['placeholders'] as $placeholdername => $placeholderdesc ) {
        echo '<strong>%'.$placeholdername.'%</strong> - '.$placeholderdesc.'<br />';
      }
      echo '</em>
      </p>'; 
      // Output textarea
      /*if( !$wpss->is_active_extension($extensionname, false) ){ 
        $textareaswitch = ' disabled="disabled"';
      }*/
      //$textareaswitch = '';
      
      $event_template = $wpss->get_event_template($extensionname, $eventname);
      
      if ( !$event_template ) {
        // Use defaule template
        $event_template = $event['template'];
      }
      
      echo '<textarea name="extension['.$extensionname.'][events]['.$eventname.']"'.$textareaswitch.'>'.$event_template.'</textarea>';
    }
  }
}
?>