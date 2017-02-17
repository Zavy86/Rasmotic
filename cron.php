<?php
 // check and refresh every 10 secs
 if($_GET['refresh']){header("Refresh:10");}
 // include api
 require_once("api.inc.php");
 /**
  * Heating system
  */
 // change modality if manual time left is expired
 if($settings->heating_modality=="manual" && $settings->manual_time_left<60){api_setting_update("heating_modality","auto");}
 // toggle heating system status
 if($settings->heating_modality=="manual"){
  if($sensors->temperature<($settings->heating_manual_temperature-0.0)){$heating_status="on";}
  elseif($sensors->temperature>($settings->heating_manual_temperature+0.0)){$heating_status="off";}
 }
 if($settings->heating_modality=="auto" || $settings->heating_modality=="absent"){
  // check for strip
  if($settings->heating->strip->id){
   // check sensor temperature
   if($sensors->temperature<($settings->heating->strip->temperature-0.0)){$heating_status="on";}
   elseif($sensors->temperature>($settings->heating->strip->temperature+0.0)){$heating_status="off";}
  }else{
   // if no strip switch off heating system
   $heating_status="off";
  }
 }
 // check if status was changed
 if($heating_status<>$settings->heating_status){
  api_setting_update("heating_status",$heating_status);
  api_notification_telegram("New heating system status: ".$heating_status);
 }
 // update relay status
 api_relay_update(20,($heating_status=="on"?TRUE:FALSE));   // 20 relay gpio pin
 /**
  * XXX system
  */
 // check and renderize dump
 if($_GET['dump']){
  api_dump($settings,"settings");
  echo "<br><br><div id='debug'>\n <pre>\n";
  foreach($_SESSION['log'] as $log){echo "<code class='".$log[0]."'>".$log[1]."</code>\n";}
  echo "  </pre>\n</div>\n";
 }
?>