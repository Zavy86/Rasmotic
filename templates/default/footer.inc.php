<?php

 // show debug logs
 if($debug){
  echo "<br><br><div id='debug'>\n <pre>\n";
  foreach($_SESSION['log'] as $log){echo "<code class='".$log[0]."'>".$log[1]."</code>\n";}
  echo "  </pre>\n</div>\n";
 }

?>

  </div> <!-- /container -->

  <script type="text/javascript">
   // update timestamp
   function timestampUpdate(){
    // check screen resolution
    if($(window).width()>480){
     $("#nav_datetime").show();
     $("#nav_brand_logo").show();
     $("#nav_brand_title").text("<?php echo $GLOBALS['config']->title; ?>");
     $("#nav_datetime").load("now.inc.php");
    }else{
     $("#nav_datetime").hide();
     $("#nav_brand_logo").hide();
     $("#nav_brand_title").load("now.inc.php");
    }
   }
   // update timestamp every 5 sec
   setInterval(function(){timestampUpdate();},5000);
   // active tooltip
   $("[data-toggle=tooltip]").tooltip({trigger:'manual'});
   // dismiss alerts
   $(".alert").delay(5000).fadeOut(1000,function(){
    $(this).alert('close');
   });
  </script>

 </body>
</html>