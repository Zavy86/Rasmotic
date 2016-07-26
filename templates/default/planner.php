<?php
 // include template header
 include("header.inc.php");
?>
 <div class='row'>
  <div class='col-xs-12 col-sm-2'>
   Modalities
  </div><!-- /col -->
  <div class='col-xs-12 col-sm-10'>
<?php
 $modalities=api_heating_modalities();
 foreach($modalities as $modality){
  echo "   <span style='display:inline-block;height:20px;width:20px;background-color:".$modality->color.";' class='progress-bar-striped'>&nbsp;</span> ".$modality->temperature."&deg;C\n";
 }
?>
  </div><!-- /col -->
 </div><!-- /row -->
 <br>
<?php
 // cycle all plannings
 foreach($settings->heating->plannings as $day=>$planning){
  // definitions
  $strips=array();
  $percentage_total=0;
  // cycle all strips and build strips array
  foreach($planning as $strip){
   $seconds_start=strtotime($strip->hour_start);
   $seconds_end=strtotime($strip->hour_end);
   $strip->time=($seconds_end-$seconds_start);
   $strip->percentage=round($strip->time*100/86400);
   $percentage_total=$percentage_total+$strip->percentage;
   $strips[]=$strip;
  }
  // correct percetage to 100 if round fails
  if($percentage_total<>100){end($strips)->percentage=end($strips)->percentage+100-$percentage_total;}
  // open progress-bar
  echo "<div class='row'>\n";
  echo "<div class='col-xs-9 col-sm-2'>\n";
  echo date('l',strtotime("Sunday +{$day} days"))."\n";
  echo "</div><!-- /col -->\n";
  echo "<div class='col-xs-3 visible-xs text-right'>\n";
  echo "<small><a href='index.php?view=heating_planning_edit&day=".$day."'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a></small>\n &nbsp; \n";
  echo "<small><a href='#'><span class='glyphicon glyphicon-duplicate' aria-hidden='true'></span></a></small>\n";
  echo "</div><!-- /col -->\n";
  echo "<div class='col-xs-12 col-sm-9'>\n";
  // cycle all strips
  foreach($strips as $strip){
   // reset midnight
   if($strip->hour_end=="23:59:59"){$strip->hour_end="24:00:00";}
   // make tooltip and temperature
   $tooltip=substr($strip->hour_start,0,5)."~".substr($strip->hour_end,0,5);
   if(!$strip->hour_start || !$strip->hour_end){$tooltip=NULL;}
   if(!$strip->hour_start || !$strip->hour_end){$tooltip=NULL;}
   // show strip
   echo "<div class='progress-bar progress-bar-striped' style='background-color:".$strip->color.";width:".$strip->percentage."%;' data-toggle='tooltip' data-placement='top' title='".$tooltip."'>&nbsp;</div>\n";
  }
  // close progress-bar
  echo "</div><!-- /col -->\n";
  echo "<div class='hidden-xs col-sm-1 text-right'>\n";
  echo "<small><a href='index.php?view=heating_planning_edit&day=".$day."'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a></small>\n &nbsp; \n";
  echo "<small><a href='#'><span class='glyphicon glyphicon-duplicate' aria-hidden='true'></span></a></small>\n";
  echo "</div><!-- /col -->\n";
  echo "</div><!-- /row -->\n<div class='br'></div>\n";
 }
 // debug
 if($debug){api_dump($settings);}
 // include template footer
 include("footer.inc.php");
?>