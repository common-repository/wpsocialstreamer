<?php
/**
 * Plugin Name:  WPSocialStreamer Lite
 * Plugin URI:   http://exells.com
 * Description:  Post user activities to user facebook page
 * Version:      1.1.0
 * Author:       Daniel Bakovic, Ben Reed, Onedin Ibrocevic
 * Author URI:   http://exells.com
 * Requires at least: 3.4
 * Tested up to: 3.5
 *
 * Text Domain:  wpsocialstreamer
 * Domain Path:  /languages/
 *
 * @package WPSocialStreamer
 * @category Core
 * @author Daniel Bakovic
*/

// No direct access!
if ( !defined('ABSPATH') ) exit;

if ( !class_exists('WPSocialStreamer') ) {
  
  /**
   * Main WPSocialStreamer Class
   */
  class WPSocialStreamer {
    
    /**
     * Plugin version number
     */
    var $version = '1.1.0';
    
    /**
     * Plugin paths
     */
    var $file;
    var $basename;
    var $plugin_dir;
    var $plugin_url;
    var $lang_dir;
    var $includes_dir;
    var $includes_url;
       
    /**
     * Current user
     * @var stdClass|WP_User Empty when not logged int; WP_User object when logged in
     */
    var $current_user;
    
    /**
     * Current user event settings
     *
     * @var array
     */
    var $user_events;    
    
    /**
     * Stores data that are needed to generate a message
     *
     * @var array
     */
    private $data;
     
    
    /**
     * Stores the raw message with playceholders
     *
     */
    private $raw_message;
    
    /**
     * Stores the message that should be shared on social networks
     *
     * @var object
     */
    private $message;
    
    /**
     * Stores available extensions and events
     * 
     * @var array
     */
    public $extensions;
    
    /**
     * Stores current global settings
     * 
     * @var array
     */
    public $settings;
     
    
    /**
     * WPSocialStreamer Constructor
     *
     * Gets things started
     */
    function __construct() {
            
      // Setup globla variables
      $this->setup_globals();      
      
      // Include required files
      $this->includes();
      
      // Installation
      if ( is_admin() && !defined('DOING_AJAX') ) $this->install();
      
      add_action( 'init', array( &$this, 'init' ), 0 );
      
      // Loaded action
      do_action('wpsocialstreamer_ready');
    }
    
    function init() {
      // This must be done within an action...
      // Initialite the user object (don't copy, make a reference only)
      $this->current_user = &wp_get_current_user();
      
      $this->init_user_events();
      
      // Initialize Widgets
      require( $this->includes_dir . 'widget.php');       
    }
    
    /**
     * Populate the user events array with all available events and stored settings
     * If a setting doesn't exists then set it to the default value.
     */
    function init_user_events() {
    
      if ( !empty($this->current_user) && isset($this->current_user->ID) ) {
        
        $this->user_events = get_user_meta($this->current_user->ID, 'wpsocialstreamer', true);
        
        if ( empty( $this->user_events ) ) {
          // Initalize user events
          foreach ( $this->extensions as $extension => $values ) {
            // There are no user settings available yet. Use default options
            foreach ( $values['events'] as $event_name => $val ) {
              $this->user_events[$extension][$event_name] = 'no';
            }
          }
          
          // Create user event settings
          update_user_meta($this->current_user->ID, 'wpsocialstreamer', $this->user_events);
        }
      }
    }
    
    /**
    * Set up global variables
    */
    function setup_globals() {
    
      // Define version constant
      define('WPSOCIALSTREAMER_VERSION', $this->version);
      
      // Paths - plugin
      $this->file       = __FILE__;
      $this->basename   = apply_filters( 'wpsocialstreamer_basename',         plugin_basename( $this->file ) );
      $this->plugin_dir = apply_filters( 'wpsocialstreamer_plugin_dir_path',  plugin_dir_path( $this->file ) );
      $this->plugin_url = apply_filters( 'wpsocialstreamer_plugin_dir_url',   plugin_dir_url ( $this->file ) );
      
      // Paths - languages
      $this->lang_dir   = apply_filters( 'wpsocialstreamer_lang_dir', trailingslashit( $this->plugin_dir . 'languages' ) );
      
      // Includes
      $this->includes_dir = apply_filters( 'wpsocialstreamer_includes_dir', trailingslashit( $this->plugin_dir . 'includes' ) );
      $this->includes_url = apply_filters( 'wpsocialstreamer_includes_url', trailingslashit( $this->plugin_url . 'includes' ) );
      
      // Other globals
      $this->data         = array();
      $this->message      = new stdClass();
      $this->raw_message  = '';
      
      // Init Settings and Extensions arrays
      $this->settings     = get_option('wpsocialstreamer_settings');
      $this->extensions   = get_option('wpsocialstreamer_extensions');
    }
    
    /**
     * Include required core files
     */
    function includes() {
    
      // Admin includes
      if ( is_admin() ) {
        require( $this->includes_dir . 'admin/admin.php' );
      }
      
      // Include frontend files
      if ( !is_admin() || defined('DOING_AJAX') ) {
        // Include custom FB Login script if Ultimate Facebook is not active
        $fb_appID = $this->settings['facebook_appID'];
        if( !function_exists('wdfb_dashboard_permissions_widget') && !empty( $fb_appID ) ){
          require( $this->includes_dir . 'fb_login.php');
        }
        
        require( $this->includes_dir . 'buddypress-options.php');
      }
    
      // Init Extensions
      require( $this->includes_dir . 'extensions/wordpress.php');
      require( $this->includes_dir . 'extensions/gd-star-rating.php');
      require( $this->includes_dir . 'extensions/wp-favorite-posts.php');
      
      do_action('wpsocialstreamer_init_extensions');
    }
    
    /**
     * Install upon activation
     */
    function install() {
      register_activation_hook( $this->file, 'install_wpsocialstreamer');
      
      if ( get_option('wpsocialstreamer_version') != $this->version ) {
        add_action('init', 'install_wpsocialstreamer');
      }
    }
    
    /**
     * Generate a new message from raw_message and available data
     *
     * @return boolean true on success | false on fail
     */
    function generate() {
      
      // Check if user is logged in
      if ( empty($this->current_user) ) {
        return false;
      }
      
      if ( !empty($this->raw_message) ) {
        
        // Copy the raw message to the message var
        $this->message->text = $this->raw_message;
        
        //______________________________________________________________________          
        // Set the message link
        $link = $this->get_data_value('LINK');

        if ( $link ) {          
          $this->message->link  = $link;
        }
        else {
          $this->message->link = get_site_url();
        }
        
        // Try to get a picture for this message
        $this->message->picture = false;
        
        if ( $this->get_data_value('PICTURE') ) {
          $this->message->picture = $this->get_data_value('PICTURE');
        }
        else {
          global $post;        
        
          if ( isset($post->ID) ) {
            // Get post thumbnail
            $post_thumbnail_id = get_post_thumbnail_id( $post->ID );
            if ( $post_thumbnail_id ) {
              $this->message->picture = wp_get_attachment_url( $post_thumbnail_id );              
            }
            else {
              // try to get the game thumb
              $thumb = get_post_meta($post->ID, 'mabp_thumbnail_url', true);
              if ( $thumb ) {
                $this->message->picture = $thumb;
              }
            }
          }
        }

        //______________________________________________________________________
        // Generate message content        
        
        // Get all placeholders from the current raw message
        preg_match_all('@%(.*)%@siU', $this->raw_message, $matches);
        
        if ( !empty($matches[1]) ) {

          $count = count( $matches[1] );
          
          for ($i = 0; $i < $count; $i++) {
            $placeholder = $matches[1][$i];
            $placeholder_content = $this->get_data_value( $placeholder );
            
            if ( $placeholder && $placeholder_content ) {
              $this->message->text  = str_replace( "%".$placeholder."%", $placeholder_content, $this->message->text );
            }
          }
          
          $this->message->text = stripslashes($this->message->text);
          $this->message->text = html_entity_decode($this->message->text, ENT_COMPAT, 'utf-8');
                             
          // Check if there are unreplaced placeholders?
          preg_match('@%(.*)%@siU', $this->message->text, $matches);
          
          if ( !empty($matches) ) {
            // ERROR: Not all placeholders have been replaced
            //$this->debug_to_file("MESSAGE PLACEHOLDER ERROR: " . $this->message->text);
            // Delete Message
            $this->message = new stdClass();
            return false;
          }
        }
      }
           
      return true;
    }
    
    /**
     * Share the current message on social networks. 
     * 
     * @uses $message
     */
    function share() {
    
      // Check if user is logged in
      if( !empty($this->current_user) && !empty($this->message->text) ) {
        // Check if facebook keys are set
        if ( function_exists('wdfb_dashboard_permissions_widget') && class_exists('Wdfb_OptionsRegistry') ) {
          $this->data =& Wdfb_OptionsRegistry::get_instance();
          $appId  = trim($this->data->get_option('wdfb_api', 'app_key'));
          $secret = trim($this->data->get_option('wdfb_api', 'secret_key'));
        }
        else {
          if ( !empty( $this->settings['facebook_appID'] ) && !empty( $this->settings['facebook_secret'] ) ) {
            $appId  = $this->settings['facebook_appID'];
            $secret = $this->settings['facebook_secret'];
          }
          else {
            return false;
          }
        }
        
        if ( $appId && $secret ) {
        
          // Include the Facebook API
          require_once ( $this->includes_dir . 'facebook-php-sdk/class-facebook-wp.php' );
          
          $facebook = new Facebook_WP_Extend( array(
              'appId'   => $appId,
              'secret'  => $secret
            )
          );
          
          $user_id = $facebook->getUser();
          
          if ($user_id) {
            // We have a user ID, so probably a logged in user.
            // If not, we'll get an exception, which we handle below.
            try {
              
              $args = array (
                  'link'    => $this->message->link,
                  'message' => $this->message->text
              );
              
              if ( $this->message->picture ) {
                  $args['picture'] = $this->message->picture;
              }
              
              // message, picture, link, name, caption, description, source, place, tags
              $ret_obj = $facebook->api('/me/feed', 'POST', $args );
            } 
            catch(FacebookApiException $e) {
              //$this->debug_to_file( $e->getType() );
              //$this->debug_to_file( $e->getMessage() );
            }
          }
          else {
            //$this->debug_to_file("Error: Can't get FB user.");
          }
        }
        else {
          //$this->debug_to_file("missing keys");
        }
      }
    }
    
    /**
     * Generate and share a message with a single function call
     */
    function generate_and_share() {
      if ( $this->generate() ) {
        //add_action( 'wp_footer', array( &$this, 'share' ), 0 );
        $this->share();
      }
    }
    
    //__________________________________________________________________________
    // Helper Functions
    
    /**
     * Set the message which should be shared on social networks
     *
     * @var string Message
     */
    /*function set_message( $message ) {
      $this->message = $message; 
    }*/

    /**
     * Get the current event message
     *
     * @return string|false;
     */
    function get_message() {
      return $this->message; 
    }
    
    /**
     * Delete the current message
     */
    function reset_message() {
      $this->message = new stdClass();
    }
    
    /**
     * Set raw message
     */
    function set_raw_message( $extension, $event ) {
      if ( isset($this->extensions[$extension]['events'][$event]) ) {
        $this->raw_message = $this->extensions[$extension]['events'][$event];
      }
      else {
        // Raw message doesn't exist
        $this->raw_message = '';
      }
    }
    
    /**
     * Get raw message
     */
    function get_raw_message() {
      return $this->raw_message;
    }
    
    /**
     * Delete raw message
     */
    function reset_raw_message() {
      $this->raw_message = '';
    }
    
    /**
     * Set data array
     */
    function set_data( $data ) {
      if ( is_array($data) ) {
        $this->data = $data;
      }
    }
    
    /**
     * Get data array
     */
    function get_data() {
      return $this->data;
    }
    
    /**
     * Reset data array
     */
    function reset_data() {
      $this->data = array();
    }
    
    /**
     * Get data key value
     */
    function get_data_value( $key ) {
      if ( empty( $this->data[$key] ) ) {
        return false;
      }
      else {
        return $this->data[$key];
      }
    }
    
    /**
     * Checks if an extension is active and returns true or false
     * 
     * @param string $extension
     * @return boolean true when extension is active
     */
    function is_active_extension($extension) {
      if ( isset( $this->extensions[$extension]['active'] ) ) {
        if ( $this->extensions[$extension]['active'] == true ) {
          return true;
        }
      }
      else {
        // New extension?
        // Set to active
        return false;
      }
      
      return false;
    }
    
    /**
     * Get the template of an extension event
     * 
     * @param string $extension, $event
     * @return template text 
     */
    function get_event_template($extension, $eventname) {
      if ( isset( $this->extensions[$extension]['events'][$eventname] ) ) {
        return stripslashes( $this->extensions[$extension]['events'][$eventname] );
      }
      else {
        return false;
      }  
    }
    
    /**
     * Checks if an event has been activated by the user and returns true or false
     *
     * @param string $extension
     * @param string $event
     * @return boolean true on activated or false on deactivated
     */
    function is_active_event($extension, $event) {
      if ( !empty($this->current_user) && isset($this->current_user->ID) ) {
        if ( isset( $this->user_events[$extension] ) && isset( $this->user_events[$extension][$event] ) ) {
          if ( $this->user_events[$extension][$event] == 'yes' ) {
            return true;
          }
        }
      }
      
      return false;
    }
    
    /**
     * Helper function to check if an extension is active globaly and if the event
     * is activated by the user.
     * 
     * @param string $extension Extension name
     * @param strong $event Extension event
     */
    function is_active($extension, $event) {
      if ( $this->is_active_extension($extension) ) {
        if ( !empty($event) && $this->is_active_event($extension, $event) ) {
          return true;
        }
      }
      return false;
    }
    
    /**
     * Returns all user eve
     * @param string $extension Extension name
     * @return array
     */
    function get_user_events( $extension ) {
      if ( isset( $this->user_events[$extension] ) ) {
        return $this->user_events[$extension];
      }
      
      return array();
    }
    
    /**
     * Debug output function
     */
    function debug($var) {
      echo "<pre>";
      print_r($var);
      echo "</pre>";
    }
    
    /**
     * Writes a message to a file
     * 
     * @param string $message
     */
    function debug_to_file( $message ) {
      $logfile = $this->plugin_dir .  '/debug.txt';
      
      if ( !is_file($logfile) ) {
        // File doesn't exist. Create a log file
        $fp = fopen($logfile, 'w+');
      }
      else if ( is_writable($logfile) && is_file($logfile) ) {
        // Open existing file
        $fp = fopen($logfile, 'a+');
      }
      
      if ( $fp ) {
        // Log the message
        $content = "\n\n";
        $content .= "===========".date('l dS \of F Y h:i:s A')."===========";
        $content .= "\n\n";
        $content .= $message;
        fwrite($fp,$content);
        fclose($fp);
      }
    }
    
    
  } // END class
} // END if class exists

// Init WPSocialStreamer
global $wpss;
$wpss = new WPSocialStreamer();
?>