<?php
    /*
    Plugin Name: Gazette1 Icon Gallery
    Plugin URI: http://jessesnyder.me/wordpress
    Description: Plugin for displaying featured video and icon grids for Gazette1 companies. Allows you to specify a youTube video for each icon. When clicked, the video will open in a lightbox. Dependent upon WonderPlugin Lightbox
    Author: Jesse Snyder
    Version: 0.1
    Author URI: http://www.jessesnyder.me
    */


function gazette1_admin() {
  // Generates html for admin page view
  include('admin/gazette1_import_admin.php');
}

function my_admin_enqueue() {
  // If not on admin page view for gazette1, don't load styles/scripts
  $screen = get_current_screen();
  if( $screen->id != 'settings_page_gazette1-icon-gallery') {
    return;
  }
  // Styles
  wp_register_style('gazette1_admin_styles', plugin_dir_url(__FILE__) . 'admin/css/gazette1_styles.css');
  wp_enqueue_style('gazette1_admin_styles');
  // WP doesn't include the necessary jquery-ui stylings. Google CDN ftw
  wp_enqueue_style('jquery_ui_styles', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css');
  // Scripts
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-widget');
  wp_enqueue_script('jquery-ui-mouse');
  wp_register_script('gazette1_admin_js',  plugin_dir_url(__FILE__) . 'admin/js/gazette1_admin.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse'));
  wp_enqueue_script('gazette1_admin_js');
}

// Add to admin scripts
add_action('admin_enqueue_scripts', 'my_admin_enqueue');

// Add menu page
function gazette1_admin_functions() {
  add_options_page("Gazette1 Icon Gallery", "Gazette1 Icon Gallery", 1, "gazette1-icon-gallery", "gazette1_admin");
}

add_action('admin_menu', 'gazette1_admin_functions');

// Shortcode generation
function gazette1_shortcode_handler() {
  // Config file location
  $config_file_loc = plugin_dir_path(__FILE__) . 'admin/configuration.json';

  // Get file contents as string and decode json into array of objects
  $videos = json_decode(file_get_contents($config_file_loc));
  $video_count = count(get_object_vars($videos));

  ob_start(); ?>
    <h2>Featured</h2>
      <img src="<?php echo $videos->vid0->iconURL; ?>" alt="<?php echo $videos->vid0->title; ?>" height="151" />

      <center>
        <iframe src="<?php echo $videos->vid0->youTubeURL; ?>" width="500" height="281" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
      </center>
    <h2>Previous Videos</h2>
    <ul class="rig columns-3">

    <?php 
    // Create icon entry for each video
    $counter = 0;
    foreach($videos as $video) : 
      // Skip first video because it is featured
      if ($counter++ == 0) continue;
    ?>
      <li>
        <a class="wplightbox" title="<?php echo $video->title; ?>" href="<?php echo $video->youTubeURL; ?>" data-width="640" data-height="360">
          <img src="<?php echo $video->iconURL; ?>" alt="<?php echo $video->title; ?>" height="151" />
        </a>
      </li>
    <?php endforeach; ?>
    </ul>

  <?php
  $buffer_contents = ob_get_contents();
  ob_end_clean();
  return $buffer_contents;
}

add_shortcode('gazette1', 'gazette1_shortcode_handler');
?>