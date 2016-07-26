<?php

 session_start();
 error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

 // definitions
 global $debug;
 global $config;
 global $db;
 global $settings;
 global $sensors;

 $config=new stdClass();

 // reset session logs
 $_SESSION['log']=NULL;

 // include configuration file
 require_once("config.inc.php");

 // include database class
 require_once("classes/database.class.php");

 // build database instance
 $db=new Database();

 // build globals variables
 $settings=api_settings();
 $sensors=api_sensors();

 // show errors
 ini_set("display_errors",$debug);
 error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

 /**
  * Renderize a variable dump into a pre tag
  *
  * @param string $variable variable to dump
  * @param string $label dump label
  * @param API_DUMP_PRINTR|API_DUMP_VARDUMP $function dump function
  * @param string $class pre dump class
  */
 function api_dump($variable,$label=NULL,$function=API_DUMP_PRINTR,$class=NULL){
  echo "\n\n<!-- dump -->\n";
  echo "<pre class='".$class."'>\n";
  if($label<>NULL){echo "<strong>".$label."</strong><br>";}
  switch($function){
   case API_DUMP_PRINTR:print_r($variable);break;
   case API_DUMP_VARDUMP:var_dump($variable);break;
   default:echo $variable."\n";
  }
  echo "</pre>\n<!-- /dump -->\n\n";
 }

 /**
  * api_dump contants
  *
  * @const API_DUMP_PRINTR dump with print_r()
  * @const API_DUMP_VARDUMP dump with var_dump()
  */
 define(API_DUMP_PRINTR,1);
 define(API_DUMP_VARDUMP,2);


/**
 * Datetime Now
 *
 * @param integer $format coordinator module
 * @return current timestamp
 */
 function api_datetime_now(){
  return date("Y-m-d H:i:s");
 }


/**
 * Sensors
 *
 * @return object sensors
 */
 function api_sensors(){
  // definitions
  $sensors=new stdClass();
  // set temperature for test
  $sensors->temperature=20.5;
  // set humidity for test
  $sensors->humidity=rand(30,35);
  // return settings
  return $sensors;
 }


/**
 * Settings
 *
 * @return object settings
 */
 function api_settings(){
  // definitions
  $settings=new stdClass();
  // get settings and build object
  $settings_result=$GLOBALS['db']->queryObjects("SELECT * FROM `settings` ORDER BY `setting` ASC",$GLOBALS['debug']);
  foreach($settings_result as $setting){$settings->{$setting->setting}=$setting->value;}
  // calculate manual time left
  if($settings->heating_system_modality=="manual" && $settings->heating_system_manual_started){
   $settings->manual_time_elapsed=(strtotime(date("Y-m-d H:i:s"))-strtotime($settings->heating_system_manual_started));
   if($settings->manual_time_elapsed>$settings->heating_system_manual_timeout){$settings->manual_time_elapsed=$settings->heating_system_manual_timeout;}
  }else{
   $settings->manual_time_elapsed=0;
  }
  $settings->manual_time_left=$settings->heating_system_manual_timeout-$settings->manual_time_elapsed;

  // get plannings
  $settings->heating->plannings=api_plannings();

  // set current planning
  $settings->heating->planning=$settings->heating->plannings[date("w")];

  // set current strip
  foreach($settings->heating->planning as $strip){
   $seconds_start=strtotime($strip->hour_start);
   $seconds_end=strtotime($strip->hour_end);
   $seconds_now=strtotime(date("H:i:s"));
   if($seconds_now>$seconds_start && $seconds_now<$seconds_end){
    $settings->heating->strip=$strip;
   }
  }

  // override current strip if modality is absent
  if($settings->heating_system_modality=="absent"){
   $strip=new stdClass();
   $strip->id=0;
   $strip->day="absent";
   $strip->hour_start="00:00:00";
   $strip->hour_end="59:59:59";
   $strip->temperature=$settings->heating_system_absent_temperature;
   unset($settings->heating->planning);
   $settings->heating->planning[0]=$strip;
   $settings->heating->strip=$strip;
  }

  // return settings
  return $settings;
 }

/**
 * Setting Update
 *
 * @param string $setting setting to update
 * @param string $value setting value
 */
 function api_setting_update($setting,$value){
  if(!strlen($setting)){return false;}
  if(!$value){$value=NULL;}
  // build update object
  $update_obj=new stdClass();
  $update_obj->setting=$setting;
  $update_obj->value=$value;
  // execute query
  $GLOBALS['db']->queryUpdate("settings",$update_obj,"setting");
 }

/**
 * Plannings
 *
 * @return object plannings
 */
 function api_plannings(){
  // definitions
  $plannings=array();
  // get settings and build object
  $plannings_result=$GLOBALS['db']->queryObjects("SELECT * FROM `heating_plannings` ORDER BY `day` ASC,`hour_start` ASC",$GLOBALS['debug']);
  foreach($plannings_result as $planning){$plannings[$planning->day][$planning->id]=$planning;}
  // return plannings
  return $plannings;
 }



?>