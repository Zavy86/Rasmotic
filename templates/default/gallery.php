<?php
 // get files in directory
 $files=scandir($settings->gallery_path);
 // build image array
 foreach($files as $file){
  if(in_array($file,array(".",".."))){continue;}
  if(!in_array(strtolower(substr($file,-3)),array("jpg","peg","png"))){continue;}
  $image=new stdClass();
  $image->path=$file;
  //$image->exif=exif_read_data($directory.$image->path,NULL,FALSE,FALSE);
  $image->timestamp=(exif_read_data($directory.$image->path)["DateTimeOriginal"]?exif_read_data($directory.$image->path)["DateTimeOriginal"]:date("Y:m:d H:i:s",filemtime($directory.$image->path)));
  $images_array[]=$image;
 }
 // debug
 if($_GET['debug']){
  include("header.inc.php");
  api_dump($settings,"settings");
  api_dump($files,"files");
  api_dump($images_array,"images_array");
  include("footer.inc.php");
  die();
 }
?>
<!DOCTYPE html>
<html>
 <head>
  <title>Slideshow Gallery</title>
  <script type="text/javascript" src="<?php echo TEMPLATE; ?>js/jquery-1.12.0.min.js"></script>
  <script type="text/javascript" src="<?php echo TEMPLATE; ?>js/jquery.flux-1.4.4.min.js"></script>
  <script type="text/javascript">
   $(function(){
    if(!flux.browser.supportsTransitions){
     alert("Flux Slider requires a browser that supports CSS3 transitions");
    }
    window.f=new flux.slider('#slider',{
     autoplay:true,
     pagination:false,
     controls:false,
     delay:<?php echo $settings->gallery_delay; ?>
    });
    // Setup a listener for user requested transitions
    $('div#transitions').bind('click', function(event){
     event.preventDefault();
     // If this is a 3D transform and the browser doesn't support 3D then inform the user
     if($(event.target).closest('ul').is('ul#trans3d') && !flux.browser.supports3d){
      alert("The '"+event.target.innerHTML+"' transition requires a browser that supports 3D transforms");
      return;
     }
     window.f.next(event.target.href.split('#')[1]);
    });
   });
  </script>
  <style>
   html,body,div{margin:0;padding:0;}
   body{background-color:#000000;height:100%;width:100%;}
   #outer{display:table;position:absolute;height:100%;width:100%;}
   #middle{display:table-cell;vertical-align:middle;}
  </style>
 </head>
 <body>
  <div id="outer">
   <div id="middle">
    <center>
     <div id="slidercontainer">
      <div id="slider" onClick="javascript:window.location.href='index.php';">
<?php foreach($images_array as $image){echo "       <img src='img.php?i=".$image->path."&w=".$settings->monitor_width."&h=".$settings->monitor_height."'/>\n";} ?>
      </div>
     </div>
    </center>
   </div>
  </div>
 </body>
</html>