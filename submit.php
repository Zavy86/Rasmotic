<?php

 // include api
 require_once("api.inc.php");

 // acquire variables
 $r_act=$_REQUEST['act'];

 //
 switch($r_act){
  // sessions functions
  case "session_login":session_login();break;
  case "session_logout":session_logout();break;

  // standard functions
  case "settings_save":settings_save();break;

  // ajax functions
  case "heating_system_modality_toggle":heating_system_modality_toggle();break;
  case "heating_system_absence_toggle":heating_system_absence_toggle();break;
  case "heating_system_manual_temperature":heating_system_manual_temperature();break;

  // default
  default:exit(header("location: index.php?alert=submitActionNotFound&action=".$r_act));
 }

 // refresh settings and sensors and execute cron after changes
 $settings=api_settings();
 $sensors=api_sensors();
 include("cron.php");


 // session login
 function session_login(){
  // acquire variables
  $r_view=$_REQUEST["view"];
  $r_passcode=$_REQUEST["passcode"];
  // checks
  if(md5($r_passcode)===$GLOBALS['settings']->passcode){
   $_SESSION['access']=TRUE;
   if($r_view=="access"){$r_view="overview";}
  }else{
   $_SESSION['access']=FALSE;
   $r_view="access";
   $v_alert="passcode_invalid";
  }
  // redirect
  exit(header("location: index.php?view=".$r_view."&alert=".$v_alert));
 }

 // session logout
 function session_logout(){
  // unset session variables
  $_SESSION['access']=FALSE;
  // redirect
  exit(header("location: index.php?view=overview"));
 }


 // settings save
 function settings_save(){
  // acquire variables
  $p_heating_system_manual_timeout=$_REQUEST["heating_system_manual_timeout"];
  $p_gallery_path=$_REQUEST["gallery_path"];
  // checks
  if(!is_numeric($p_heating_system_manual_timeout)){
   $_SESSION['log'][]=array("error","UPGRADE heating_system_manual_timeout numeric value needed");
   return false;
  }
  // update settings
  api_setting_update("heating_system_manual_timeout",$p_heating_system_manual_timeout);
  api_setting_update("gallery_path",$p_gallery_path);
  // redirect
  exit(header("location: index.php?view=settings&alert=settings_updated"));
 }


 // update heating_system_modality
 function heating_system_modality_toggle(){
  // acquire variables
  $p_manual_toggle=$_REQUEST["manual_toggle"];
  // checks on converts
  if($p_manual_toggle=="true"){$p_manual_toggle="manual";}else{$p_manual_toggle="auto";}
  // update manual toggle
  api_setting_update("heating_system_modality",$p_manual_toggle);
  // if is manual update started datetime
  if($p_manual_toggle=="manual"){api_setting_update("heating_system_manual_started",api_datetime_now());}
   else{api_setting_update("heating_system_manual_started",NULL);}
 }

 // toggle heating_system_modality modality absent
 function heating_system_absence_toggle(){
  // checks on converts
  if($GLOBALS['settings']->heating_system_modality=="absent"){$v_modality="auto";}else{$v_modality="absent";}
  // update manual toggle
  api_setting_update("heating_system_modality",$v_modality);
  // if is absent reset started datetime
  if($v_modality=="absent"){api_setting_update("heating_system_manual_started",NULL);}
 }

 // update manual temperature
 function heating_system_manual_temperature(){
  // acquire variables
  echo $p_temperature=$_REQUEST["temperature"];
  // checks
  if(!is_numeric($p_temperature)){
   $_SESSION['log'][]=array("error","UPGRADE heating_system_manual_temperature numeric value needed");
   return false;
  }
  // update manual temperature
  api_setting_update("heating_system_manual_temperature",number_format($p_temperature,1,".",","));
 }

 //
 if($_REQUEST['debug']){
  echo "<br><br><div id='debug'>\n <pre>\n";
  foreach($_SESSION['log'] as $log){echo "<code class='".$log[0]."'>".$log[1]."</code>\n";}
  echo "  </pre>\n</div>\n";
 }

?>