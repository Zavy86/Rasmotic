<?php

 // include api
 require_once("api.inc.php");

 // definitions
 $return=new stdClass();

 // get settings and build object
 $settings_result=$db->queryObjects("SELECT * FROM settings",$debug);
 foreach($settings_result as $setting){$settings->{$setting->setting}=$setting->value;}



 // calculate manual time left
 if($settings->modality=="manual" && $settings->manual_started){
  $settings->manual_time_elapsed=(strtotime(date("Y-m-d H:i:s"))-strtotime($settings->manual_started));
  if($settings->manual_time_elapsed>$settings->manual_timeout){$settings->manual_time_elapsed=$settings->manual_timeout;}
 }else{
  $settings->manual_time_elapsed=0;
 }
 $settings->manual_time_left=$settings->manual_timeout-$settings->manual_time_elapsed;

 // build return object
 $return->settings=$settings;
 $return->planning=NULL;

 // encode in json and return
 echo json_encode($return);

 // check and renderize dump
 if($_GET['dump']){api_dump($return,"return");}

?>