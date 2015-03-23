<?php
  $_POST = array_map('stripslashes_deep', $_POST);
  // Config file location
  $config_file_loc = plugin_dir_path(__FILE__) . 'configuration.json';
  
  function generate_html($config_file) {
    // Check if config file exists
    if (file_exists($config_file)) : 
      // Get file contents as string and decode json into array of objects
      $videos = json_decode(file_get_contents($config_file));
      $video_count = count(get_object_vars($videos));
      //Generate proper HTML
    ?>
     
      <form class = "column" name="gazette1_form" method="post" 
            action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
      <input type="hidden" name="gazette1_hidden" value="Y<?php echo $video_count; ?>">
      <?php 
      $count = 0;
      foreach($videos as $video): ?>
        <div class="portlet">
        <div class="portlet-header"><?php echo $video->title; ?></div>
          <div class="portlet-content">
          <p>
            <?php _e( "Title: " ); ?>
            <input type="text" name="video[vid<?php echo $count;?>][title]" 
                value="<?php echo $video->title; ?>" size="50" placeholder="ex: Steve's Pub">
          </p>
          <p>
            <?php _e( "Icon URL: " ); ?>
            <input type="text" name="video[vid<?php echo $count;?>][iconURL]" 
                value="<?php echo $video->iconURL; ?>" size="50" placeholder="ex: http://okgazette.com/wp-content/uploads/picture.jpg">
          </p>
          <p>
            <?php _e( "youTube URL: " ); ?>
            <input type="text" name="video[vid<?php echo $count;?>][youTubeURL]" 
                  value="<?php echo $video->youTubeURL; ?>" size="50" placeholder="ex: https://www.youtube.com/embed/IFwORyKak-0">
          </p>
          </div>
          </div>
          
       <?php $count++; ?>  
       <?php endforeach; ?>  
        
        <p class="submit">
        <input type="submit" name="gazette1_submit" 
            value="<?php _e('Update', 'gazette1_trdom' ) ?>" />
        </p>
      </form>
      
    <?php endif;   
    } ?>

 <body>
  <div class="wrap">
    <?php echo "<h2>" . __( 'Gazette1 Icon and Video Gallery Options', 'gazette1_trdom' ) . "</h2>"; ?>
    <hr>
    <ul>
      <li><p>Order videos by clicking, dragging, and dropping the tiles below. The first video will be used as the Featured Video</p></li>
      <li><p>Edit the entries by clicking the Plus Sign icon and changing the values in the input fields and clicking Update.</p></li>
      <li><p>Delete an entry by clicking the Trash icon to the right of the entry title.</p></li>
      <li></li>
    </ul>
    <p>NOTE: Changes will not persist unless you click Update.</p>
  
    <button id="create-entry" type="button">Add New</button>

    <?php 
    // User submits form
    if($_POST['gazette1_hidden'][0] == 'Y') {
      // Get video count for looping
      $video_count = (int)$_POST['gazette1_hidden'][1];
      // Init empty array
      $arr = array();
      // For number of videos, store proper key-value pairs for current video in loop
      $arr = ($_POST['video']);
      $contents = json_encode($arr);
      // Open and write to file. Erases the contents of the file or creates 
      // a new file if it doesn't exist. File pointer starts at the beginning
      $file_handler = fopen($config_file_loc, "w");
      fwrite($file_handler, $contents);
      fclose($file_handler);
    }
    // Generate HTML for page
    generate_html($config_file_loc);

    ?>
   </div>
</body>