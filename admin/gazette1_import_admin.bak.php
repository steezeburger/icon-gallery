<?php
  // Config file location
  $config_file_loc = plugin_dir_path(__FILE__) . 'configuration_example.json';
  
  function generate_html($config_file) {
    // Check if config file exists
    if (file_exists($config_file)) : 
      // Get file contents as string and decode json into array of objects
      $videos = json_decode(file_get_contents($config_file));
      $video_count = count(get_object_vars($videos));
      //Generate proper HTML
    ?>
      <form class = "column" name="gazette1_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
      <input type="hidden" name="gazette1_hidden" value="Y<?php echo $video_count; ?>">
      <?php 
      $count = 0;
      foreach($videos as $video): ?>
        <div class="portlet">
        <div class="portlet-header"><?php echo $video->title; ?></div>
          <div class="portlet-content">
          <p>
            <?php _e( "Title: " ); ?>
            <input type="text" name="<?php echo $count;?>title" value="<?php echo $video->title; ?>" size="50" placeholder="ex: Steve's Pub">
          </p>
          <p>
            <?php _e( "Icon URL: " ); ?>
            <input type="text" name="<?php echo $count;?>iconurl" value="<?php echo $video->iconURL; ?>" size="50" placeholder="ex: http://okgazette.com/wp-content/uploads/picture.jpg">
          </p>
          <p>
            <?php _e( "youTube URL: " ); ?>
            <input type="text" name="<?php echo $count;?>youtubeurl" value="<?php echo $video->youTubeURL; ?>" size="50" placeholder="ex: https://www.youtube.com/embed/IFwORyKak-0">
          </p>
          </div>
          </div>
          
       <?php $count++; ?>  
       <?php endforeach; ?>  
        
        <p class="submit">
        <input type="submit" name="gazette1_submit" value="<?php _e('Update', 'gazette1_trdom' ) ?>" />
        </p>
      </form>
      
    <?php endif; } ?>

 <body>
  <div class="wrap">
    <?php echo "<h2>" . __( 'Gazette1 Icon and Video Gallery Options', 'gazette1_trdom' ) . "</h2>"; ?>
    <hr>
    <p>Sort videos by clicking, dragging, and dropping the tiles below.</p>
    <p>The top video will the Featured Video.</p>
    <?php generate_html($config_file_loc); ?>
   </div>
</body>

<?php 
// User submits form
if($_POST['gazette1_hidden'][0] == 'Y') {
  // Get video count for looping
  $video_count = (int)$_POST['gazette1_hidden'][1];
  // Init empty array
  $arr = array();
  // For number of videos, store proper key-value pairs for current video in loop
  for ($i = 0; $i < $video_count; $i++) {
    foreach($_POST as $name => $value) {
      if ($name[0] == $i && $name[0] != 'g') {
        switch (substr($name, 1)) {
          case "title":
            $title = $value;
            break;
          case "iconurl":
            $iconurl = $value;
            break;
          case "youtubeurl":
            $youtubeurl = $value;
            break;
          }
        }
      }
      $arr["vid{$i}"] =  array('title' => $title,
                        'iconURL' => $iconurl,
                        'youTubeURL' => $youtubeurl
                       );
    }
  
  var_dump($arr);
  //TODO: json encode and write to file
  
  $contents = json_encode($arr);
  // Open and write to file. Erases the contents of the file or creates a new file if 
  //   it doesn't exist. File pointer starts at the beginning of the file
  $file_handler = fopen($config_file_loc, "w");
  fwrite($file_handler, $contents);
  fclose($file_handler);
  generate_html($config_file_loc);
}
?>
