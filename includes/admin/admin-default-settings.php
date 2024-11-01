<?php
/**
 * Defines the array of default settings
 */

$wpss_default_settings = array(
  'facebook_appID'              => '',
);

$wpss_extensions = array( 
  'wordpress' => array (
    /* Set the title that will be displayed on the settings page */
    'title'     => __('WordPress', 'wpsocialstreamer'),
    /* Set a function that should be used to checked if the plugin is installed */
    'function'	=> false,
    /* Set the link to the plugin */
    'link'		=> 'http://wordpress.org',
    /* Extension status */
    'active' => true,
    /* Define an array with available events / actions */
    /* ATTENTION: events should be named exactly like the corresponding action! */      
    'events' => array (
      'publish_post'  => array (
        /* Set the default message template */
        'template'  => __("Hey, I just added %POST_NAME%!", 'wpsocialstreamer'),
        /* Set the event description for user settings page. */
        /* Share this event when: */
        'title'     => __("I publish a new post.", 'wpsocialstreamer'),
        /* Define some placeholders that admins can use to generate message text */
        /* Set to false if no placeholders are available: 'placeholders' => false */
        'placeholders' => array ( 
          /* Set placeholder name and a short description */
          'POST_NAME' => __("Name of the post", 'wpsocialstreamer')
        ) /* END placeholders */
      ), /* END event */
      'comment_post'  => array (
        /* Set the default message template */
        'template'  => __("Wohoo, I just commented on %POST_LINK%!", 'wpsocialstreamer'),
        /* Set the event description for user settings page. */
        /* Share this event when: */
        'title'     => __("I post a new comment.", 'wpsocialstreamer'),
        /* Define some placeholders that admins can use to generate message text */
        /* Set to false if no placeholders are available: 'placeholders' => false */
        'placeholders' => array ( 
          /* Set placeholder name and a short description */
          'POST_NAME' => __("Name of the post", 'wpsocialstreamer'),
          'CONTENT'   => __("Post content", 'wpsocialstreamer')
        ) /* END placeholders */
      ) /* END event */
    )
  ),
  'gd_star_rating' => array (
    /* Set the title that will be displayed on the settings page */
    'title'     => __('GD Star Rating', 'wpsocialstreamer'),
    /* Set a function that should be used to checked if the plugin is installed */
    'function'	=> 'wp_gdsr_render_rating_results',
    /* Set the link to the plugin */
    'link'		=> 'http://www.dev4press.com/plugins/gd-star-rating/',
    /* Extension status */
    'active' => false,
    /* Define an array with available events / actions */
    /* ATTENTION: events should be named exactly like the corresponding action! */      
    'events' => array (
      'gdsr_vote_rating_article'  => array (
        /* Set the default message template */
        'template'  => __('Hey, I just rated "%POST_NAME%"!', 'wpsocialstreamer'),
        /* Set the event description for user settings page. */
        /* Share this event when: */
        'title'     => __("I rate a post.", 'wpsocialstreamer'),
        /* Define some placeholders that admins can use to generate message text */
        /* Set to false if no placeholders are available: 'placeholders' => false */
        'placeholders' => array ( 
          /* Set placeholder name and a short description */
          'POST_NAME' => __("Name of the post", 'wpsocialstreamer'),
          'VOTE'      => __("User rating", 'wpsocialstreamer')
        ) /* END placeholders */
      ) /* END event */
    ) /* END events */
  ),
  'wp_favorite_posts' => array (
    /* Set the title that will be displayed on the settings page */
    'title'     => __('WP Favorite Posts', 'wpsocialstreamer'),
    /* Set a function that should be used to checked if the plugin is installed */
    'function'	=> 'wpfp_add_favorite',
    /* Set the link to the plugin */
    'link'		=> 'http://wordpress.org/extend/plugins/wp-favorite-posts/',
    /* Extension status */
    'active' => false,
    /* Define an array with available events / actions */
    /* ATTENTION: events should be named exactly like the corresponding action! */      
    'events' => array (
      'wpfp_after_add'  => array (
        /* Set the default message template */
        'template'  => __('Hey, I just added "%POST_NAME%" to my favorites!', 'wpsocialstreamer'),
        /* Set the event description for user settings page. */
        /* Share this event when: */
        'title'     => __("I add a post to my favorites.", 'wpsocialstreamer'),
        /* Define some placeholders that admins can use to generate message text */
        /* Set to false if no placeholders are available: 'placeholders' => false */
        'placeholders' => array ( 
          /* Set placeholder name and a short description */
          'POST_NAME' => __("Name of the post", 'wpsocialstreamer')
        ) /* END placeholders */
      ) /* END event */
    ) /* END events */
  )
);
?>