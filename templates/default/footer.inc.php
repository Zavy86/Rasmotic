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
   // update timestamp every 10 sec
   setInterval(function(){
    $("#nav_datetime").load("now.inc.php");
   },50000);
  </script>

 </body>
</html>