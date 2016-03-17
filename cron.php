<?php
 // check and refresh every 10 secs
 if($_GET['refresh']){header("Refresh:10");}
 // include api
 require_once("api.inc.php");
 // change modality if manual time left is expired
 if($settings->modality=="manual" && $settings->manual_time_left<60){api_setting_update("modality","auto");}
 // toggle heating system status
 if($settings->modality=="manual"){
  if($sensors->temperature<($settings->manual_temperature-0.0)){$heating_system_status="on";}
  elseif($sensors->temperature>($settings->manual_temperature+0.0)){$heating_system_status="off";}
 }elseif($settings->modality=="auto"){
  // check for strip
  if($settings->heating->strip->id){
   // check sensor temperature
   if($sensors->temperature<($settings->heating->strip->temperature-0.0)){$heating_system_status="on";}
   elseif($sensors->temperature>($settings->heating->strip->temperature+0.0)){$heating_system_status="off";}
  }else{
   // if no strip switch off heating system
   $heating_system_status="off";
  }
 }
 api_setting_update("heating_system_status",$heating_system_status);
 // check and renderize dump
 if($_GET['dump']){
  api_dump($settings,"settings");
  echo "<br><br><div id='debug'>\n <pre>\n";
  foreach($_SESSION['log'] as $log){echo "<code class='".$log[0]."'>".$log[1]."</code>\n";}
  echo "  </pre>\n</div>\n";
 }
?>