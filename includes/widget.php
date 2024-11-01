<?php
/**
 * WPSS Widgets
 *
 * @package WPSocialStreamer
 * @subpackage Widgets
 */
 
register_widget('WP_Widget_WPSS_FB_Login');


//
// Widget: Show Game Scores
//
class WP_Widget_WPSS_FB_Login extends WP_Widget { 
  // Constructor
  function WP_Widget_WPSS_FB_Login() {      
    $widget_ops   = array('description' => 'Display the Facebook Login / Logout button.');      
    $this->WP_Widget('wpss_fb_login_button', 'WPSS Facebook Login Button', $widget_ops);

    if (function_exists('wpss_fb_setup')) add_action('wp_footer','wpss_fb_setup');
  }

  // Display Widget
  function widget($args, $instance) {    
    extract($args);        
    $title = apply_filters('widget_title', esc_attr($instance['title']));        
    echo $before_widget;
    
    if ($title)
      echo $before_title.$title.$after_title;
    
    if ( function_exists('wpss_fb_buttons') ) {
      wpss_fb_buttons();
    }
    else {
      echo "<p>Please setup WPSocialStreamer first!</p>";
    }
    echo $after_widget;
  }

  // Update Widget
  function update($new_instance, $old_instance) {    
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);      
    return $instance;
  }

  // Display Widget Control Form
  function form($instance) {
    global $wpdb;      
    $instance = wp_parse_args( (array) $instance, array('title' => '') );
    $title = esc_attr($instance['title']);      
    ?>      
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        Title 
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
      </label>
    </p>
    <?php
  }
}
?>