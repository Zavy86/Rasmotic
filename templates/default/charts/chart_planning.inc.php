<?php
 // include api
 require_once("../../../api.inc.php");
 // definitions
 $strips=array();
 $percentage_total=0;
 // cycle all strips and build strips array
 foreach($settings->heating->planning as $strip){
  $seconds_start=strtotime($strip->hour_start);
  $seconds_end=strtotime($strip->hour_end);
  // check if current strip
  if($strip->id==$settings->heating->strip->id){
   $seconds_now=strtotime(date("H:i:s"));
   // build pre strip
   $strip_pre=clone $strip;
   $strip_pre->hour_end=date("H:i:s");
   $strip_pre->time=($seconds_now-$seconds_start);
   $strip_pre->percentage=round($strip_pre->time*100/86400);
   if($strip_pre->percentage>0){$strip_pre->percentage=$strip_pre->percentage-0.25;}
   $strip_pre->hour_start=NULL;
   $strips[]=$strip_pre;
   // build current strip
   $strip_now=clone $strip;
   $strip_now->temperature=NULL;
   $strip_now->percentage=0.25;
   $strips[]=$strip_now;
   // build post strip
   $strip_post=clone $strip;
   $strip_post->hour_start=date("H:i:s");
   $strip_post->time=($seconds_end-$seconds_now);
   $strip_post->percentage=round($strip_post->time*100/86400);
   if($strip_pre->percentage<0.5){$strip_post->percentage=$strip_post->percentage-0.25;}
   $strip_post->hour_end=NULL;
   $strips[]=$strip_post;
   $percentage_total=$percentage_total+$strip_pre->percentage+$strip_now->percentage+$strip_post->percentage;
  }else{
   $strip->time=($seconds_end-$seconds_start);
   $strip->percentage=round($strip->time*100/86400);
   $percentage_total=$percentage_total+$strip->percentage;
   $strips[]=$strip;
  }
 }
 // correct percetage to 100 if round fails
 if($percentage_total<100){end($strips)->percentage=end($strips)->percentage+100-$percentage_total;}
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
?>