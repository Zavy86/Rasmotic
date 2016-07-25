<?php
 // include template header
 include("header.inc.php");
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
  if($percentage_total<100){end($strips)->percentage=end($strips)->percentage+100-$percentage_total;}
  // open progress-bar
  echo "<div class='row'>\n";
  echo "<div class='col-xs-12 col-sm-2'>\n";
  echo date('l',strtotime("Sunday +{$day} days"))."\n";
  echo "</div><!-- /col -->\n";
  echo "<div class='col-xs-12 col-sm-10'>\n";
  // cycle all strips
  foreach($strips as $strip){
  // set temperature class
   if(!$strip->temperature){$color="#666666";}
   elseif($strip->temperature<=16){$color="#5BC0DE";}
   elseif($strip->temperature>16&&$strip->temperature<=18){$color="#337AB7";}
   elseif($strip->temperature>18&&$strip->temperature<=20){$color="#5CB85C";}
   elseif($strip->temperature>20&&$strip->temperature<=22){$color="#F0AD4E";}
   elseif($strip->temperature>22){$color="#D9534F";}
   // reset grade if percentage less than 4%
   if($strip->percentage<4){$strip->temperature=NULL;}
   // reset midnight
   if($strip->hour_end=="23:59:59"){$strip->hour_end="24:00:00";}
   // make tooltip and temperature
   $tooltip=substr($strip->hour_start,0,5)."~".substr($strip->hour_end,0,5);
   if(!$strip->hour_start || !$strip->hour_end){$tooltip=NULL;}
   if(!$strip->hour_start || !$strip->hour_end){$tooltip=NULL;}
   // show strip
   echo "<div class='progress-bar progress-bar-striped' style='background-color:".$color.";width:".$strip->percentage."%;' data-toggle='tooltip' data-placement='top' title='".$tooltip."'>".$strip->temperature.($strip->temperature?"°":NULL)."</div>\n";
  }
  // close progress-bar
  echo "</div><!-- /col -->\n</div><!-- /row -->\n<br>\n";
 }
 // include template footer
 include("footer.inc.php");
?>