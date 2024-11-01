<?php
/**
 * Exstension for MyArcadePlugin
 *
 * This file extends WPSocialStreamer to support WP Favorite Posts actions
 *
 * @package WPSocialStreamer
 * @subpackage ExstensionWordPress
 */
 
// No direct access!
if ( !defined('ABSPATH') ) exit;

// Register needed actions
add_action( 'wpfp_after_add', 'wpsocialstreamer_wpfp_after_add');

/**
 * Called when user adds a post to his favorites
 */
function wpsocialstreamer_wpfp_after_add( $postID ) {
  global $wpss;
  
  // Check if extension event is active
  if ( $wpss->is_active( 'wp_favorite_posts', 'wpfp_after_add' ) ) {
  
    $wpss->set_raw_message( 'wp_favorite_posts', 'wpfp_after_add' );
  
    // Generate data that should be shared
    $wpss->set_data( array (
        'POST_NAME' => get_the_title($postID),
        /* Every extension must contain the LINK field. Values: link or false */
        'LINK'   => get_permalink( $postID )
      )
    );
    
    // Generate and share the message
    $wpss->generate_and_share();
  }
}
?>