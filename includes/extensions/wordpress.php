<?php
/**
 * Exstension for MyArcadePlugin
 *
 * This file extends WPSocialStreamer to support WordPress actions
 *
 * @package WPSocialStreamer
 * @subpackage ExstensionWordPress
 */
 
// No direct access!
if ( !defined('ABSPATH') ) exit;

// Register needed hooks
add_action( 'publish_post', 'wpsocialstreamer_wordpress_publish_post');  
add_action( 'comment_post', 'wpsocialstreamer_wordpress_comment_post', 10, 2);
 
/**
 * Called when user publishes a post
 */
function wpsocialstreamer_wordpress_publish_post( $postID ) {
   global $wpss;
               
   if ( empty($postID) ) return;
   
  // Check if extension event is active
  if ( $wpss->is_active('wordpress', 'publish_post') ) {
  
    $wpss->set_raw_message( 'wordpress', 'publish_post' );
  
    $wpss->set_data( array(
        'POST_NAME' => get_the_title( $postID ),
        /* Every extension must contain the LINK field. Values: link or false */
        'LINK'   => get_permalink( $postID )
      )
    );

    // Generate and share the message
    $wpss->generate_and_share();
  }
}

/**
 * Called when user posts a comment
 *
 * @param int $commentID
 * @param string $approval_status: "spam" , 0 = disapproved, 1 = approved
 */
function wpsocialstreamer_wordpress_comment_post($commentID, $approval_status) {
  global $wpss;
     
  // Check if extension event is active
  if ( $wpss->is_active('wordpress', 'comment_post') ) {    
    // Check if the comment is approved
    if ( $approval_status == 1 ) {
    
      $wpss->set_raw_message( 'wordpress', 'comment_post' );
      
      // Get the comment
      $comment = get_comment( $commentID );
           
      // Set needed data
      $wpss->set_data( array(
        'POST_NAME' => get_the_title( $comment->comment_post_ID ),
        'CONTENT' => $comment->comment_content,
        /* Every extension must contain the LINK field. Values: link or false */
        'LINK'   => get_permalink( $comment->comment_post_ID )
        )
      );      

      // Generate and share the message
      $wpss->generate_and_share();
    }
  }
}
?>