<?php

 // include api
 require_once("api.inc.php");

 // acquire variables
 $g_act=$_REQUEST['act'];

 //
 switch($g_act){
  // standard functions
  case "settings_save":settings_save();break;

  // ajax functions
  case "modality_toggle":modality_toggle();break;
  case "manual_temperature":manual_temperature();break;

  // default
  default:exit(header("location: index.php?alert=submitActionNotFound&action=".$g_act));
 }

 // refresh settings and sensors and execute cron after changes
 $settings=api_settings();
 $sensors=api_sensors();
 include("cron.php");


 // settings save
 function settings_save(){
  // acquire variables
  $p_manual_timeout=$_REQUEST["manual_timeout"];
  $p_gallery_path=$_REQUEST["gallery_path"];
  // checks
  if(!is_numeric($p_manual_timeout)){
   $_SESSION['log'][]=array("error","UPGRADE manual_timeout numeric value needed");
   return false;
  }
  // update settings
  api_setting_update("manual_timeout",$p_manual_timeout);
  api_setting_update("gallery_path",$p_gallery_path);
  // redirect
  exit(header("location: index.php?view=settings"));
 }






 // update modality
 function modality_toggle(){
  // acquire variables
  $p_manual_toggle=$_REQUEST["manual_toggle"];
  // checks on converts
  if($p_manual_toggle=="true"){$p_manual_toggle="manual";}else{$p_manual_toggle="auto";}
  // update manual toggle
  api_setting_update("modality",$p_manual_toggle);
  // if is manual update started datetime
  if($p_manual_toggle=="manual"){api_setting_update("manual_started",api_datetime_now());}
   else{api_setting_update("manual_started",NULL);}
 }

 // update manual temperature
 function manual_temperature(){
  // acquire variables
  echo $p_temperature=$_REQUEST["temperature"];
  // checks
  if(!is_numeric($p_temperature)){
   $_SESSION['log'][]=array("error","UPGRADE manual_temperature numeric value needed");
   return false;
  }
  // update manual temperature
  api_setting_update("manual_temperature",number_format($p_temperature,1,".",","));
 }

 //
 if($_REQUEST['debug']){
  echo "<br><br><div id='debug'>\n <pre>\n";
  foreach($_SESSION['log'] as $log){echo "<code class='".$log[0]."'>".$log[1]."</code>\n";}
  echo "  </pre>\n</div>\n";
 }

?>