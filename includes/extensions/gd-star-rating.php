<?php
/**
 * Exstension for MyArcadePlugin
 *
 * This file extends WPSocialStreamer to support GD Star Rating actions
 *
 * @package WPSocialStreamer
 * @subpackage ExstensionWordPress
 */
 
// No direct access!
if ( !defined('ABSPATH') ) exit;

// Register needed actions
add_action( 'gdsr_vote_rating_article', 'wpsocialstreamer_gd_star_rating_vote_article', 10, 3);
add_action( 'gdsr_vote_thumb_article', 'wpsocialstreamer_gd_star_rating_vote_article', 10, 3);

/**
 * Extend the extension array
 * 
 * @param type $extensions_array
 * @return type
 */

/**
 * Called when user rates a post
 */
function wpsocialstreamer_gd_star_rating_vote_article( $postID, $user, $votes ) {
  global $wpss;
   
  // Check if extension event is active
  if ( $wpss->is_active( 'gd_star_rating', 'gdsr_vote_rating_article' ) ) {
      
    $wpss->set_raw_message( 'gd_star_rating', 'gdsr_vote_rating_article' );
  
    // Generate data that should be shared
    $wpss->set_data( array (
        'POST_NAME' => get_the_title($postID),
        'VOTE'      => $votes,
        /* Every extension must contain the LINK field. Values: link or false */
        'LINK'   => get_permalink( $postID )   
      )
    );
    
    // Generate and share the message
    $wpss->generate_and_share();
  }
}
?>