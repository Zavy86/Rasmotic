<?php

 session_start();

 // definitions
 global $debug;
 global $develop;
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

 // check for debug
 if($_GET['debug']){$debug=TRUE;}

 // show errors
 ini_set("display_errors",($debug||$develop?TRUE:FALSE));
 error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

 // build database instance
 $db=new Database();

 // build globals variables
 $settings=api_settings();
 $sensors=api_sensors();

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
 * Alerts Add
 *
 * @param string $message alert message
 * @param string $class alert class
 * @return boolean alert saved status
 */
 function api_alerts_add($message,$class="info"){
  // checks
  if(!$message){return FALSE;}
  if(!is_array($_SESSION['alerts'])){$_SESSION['alerts']=array();}
  // build alert object
  $alert=new stdClass();
  $alert->timestamp=api_datetime_now();
  $alert->message=$message;
  $alert->class=$class;
  $_SESSION['alerts'][]=$alert;
  // return
  return TRUE;
 }

 /**
 * Notification Telegram
 *
 * @param string $alert alert message
 * @param string $class alert class
 * @return boolean alert saved status
 */
 function api_notification_telegram($alert){
  // check token
  if(!$GLOBALS['config']->token_rpinotify){return false;}
  // status
  $message="Timestamp: ".date("Y-m-d H:i")."\n\n";
  $message.="Heating System\n";
  $message.="- Temperature: ".$GLOBALS['sensors']->temperature."%2AC\n";
  $message.="- Humidity: ".$GLOBALS['sensors']->humidity."%25\n";
  $message.="- Modality: ".ucfirst($GLOBALS['settings']->heating_modality)."\n";
  $message.="- Status: ".ucfirst($GLOBALS['settings']->heating_status)."\n\n";
  $message.="Alert: ".$alert;
  // text
  $text=str_replace(array(" ","\n"),array("%20","%0A"),$message);
  // make notification url
  $url="http://api.rpinotify.it/notification/".$GLOBALS['config']->token_rpinotify."/text/".$text;
  /*api_dump($message);
  api_dump($url);*/
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_HEADER,0);
  curl_exec($ch);
  curl_close($ch);
 }

/**
 * Sensors
 *
 * @return object sensors
 */
 function api_sensors(){
  // definitions
  $sensors=new stdClass();
  // get last temperature
  $sensors->temperature=$GLOBALS['db']->queryUniqueValue("SELECT `value` FROM `detections` WHERE `typology`='temperature' ORDER BY `timestamp` DESC",$GLOBALS['debug']);
  // get last humidity
  $sensors->humidity=$GLOBALS['db']->queryUniqueValue("SELECT `value` FROM `detections` WHERE `typology`='humidity' ORDER BY `timestamp` DESC",$GLOBALS['debug']);

  /*
   * @todo verificare che la data non sia troppo vecchia
   */

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
  if($settings->heating_modality=="manual" && $settings->heating_manual_started){
   $settings->manual_time_elapsed=(strtotime(date("Y-m-d H:i:s"))-strtotime($settings->heating_manual_started));
   if($settings->manual_time_elapsed>$settings->heating_manual_timeout){$settings->manual_time_elapsed=$settings->heating_manual_timeout;}
  }else{
   $settings->manual_time_elapsed=0;
  }
  $settings->manual_time_left=$settings->heating_manual_timeout-$settings->manual_time_elapsed;
  // get plannings
  $settings->heating->plannings=api_heating_plannings();
  // set current planning
  $today=(date("w")?date("w"):7);
  $settings->heating->planning=$settings->heating->plannings[$today];
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
  if($settings->heating_modality=="absent"){
   $strip=new stdClass();
   $strip->id=0;
   $strip->day="absence";
   $strip->hour_start="00:00:00";
   $strip->hour_end="23:59:59";
   $strip->name="Absence";
   $strip->color="#E5E5E5";
   $strip->temperature=$settings->heating_absent_temperature;
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
 * @return boolean request status
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
  return TRUE;
 }

/**
 * Relay Update
 *
 * @param integer $relay relay gpio number to update
 * @param boolean $active relay active status
 * @return boolean request status
 */
 function api_relay_update($relay,$active){
  if(!is_integer($relay)){return false;}
  api_dump("NEW RELAY STATUS: ".($active?"ACTIVE":"INACTIVE"));
  //
  api_dump("sudo ./scripts/relay.json.py ".$relay." ".($active?"ON":"OFF"));
  $output=exec("sudo ./scripts/relay.json.py ".$relay." ".($active?"ON":"OFF"));
  $json=json_decode($output);
  // debug
  api_dump($json);

  // verificare se nn ci sono errori ed eseguire
  return TRUE;
 }

/**
 * Heating Modality
 *
 * @param mixed $modality modality object or id
 * @return object modality
 */
 function api_heating_modality($modality){
  // get object
  if(is_numeric($modality)){$modality=$GLOBALS['db']->queryUniqueObject("SELECT * FROM `heating_modalities` WHERE `id`='".$modality."'",$GLOBALS['debug']);}
  if(!$modality->id){
   $modality=new stdClass();
   $modality->name="Off";
   $modality->color="#666666";
   $modality->temperature=NULL;
  }
  // check and convert
  $modality->name=stripslashes($modality->name);
  $modality->color=stripslashes($modality->color);
  // return modality
  return $modality;
 }

/**
 * Heating Modalities
 *
 * @return array of modality objects
 */
 function api_heating_modalities(){
  // definitions
  $modalities=array();
  // get all modalities
  $modalities_result=$GLOBALS['db']->queryObjects("SELECT * FROM `heating_modalities` ORDER BY `id` ASC",$GLOBALS['debug']);
  foreach($modalities_result as $modality){$modalities[$modality->id]=api_heating_modality($modality);}
  // return modalities
  return $modalities;
 }

/**
 * Heating Planning
 *
 * @param mixed $day planning day (1 monday, 2 tuesday, 3 wednesday, 4 thursday, 5 friday, 6 saturday 7 sunday)
 * @return object planning
 */
 function api_heating_planning($day){
  // definitions
  $planning_array=array();
  // get object
  if(is_numeric($day)){$plannings_result=$GLOBALS['db']->queryObjects("SELECT * FROM `heating_plannings` WHERE `day`='".$day."' ORDER BY `hour_start` ASC",$GLOBALS['debug']);}
  // check results
  if(!is_array($plannings_result)){return FALSE;}
  // cycle all results
  foreach($plannings_result as $planning){
   $modality=api_heating_modality($planning->modality_fk);
   $planning->name=$modality->name;
   $planning->color=$modality->color;
   $planning->temperature=$modality->temperature;
   $planning_array[$planning->id]=$planning;
  }
  // return day planning
  return $planning_array;
 }

/**
 * Heating Plannings
 *
 * @return array of planning objects
 */
 function api_heating_plannings(){
  // definitions
  $plannings=array();
  // get plannings and build object
  $plannings_result=$GLOBALS['db']->queryObjects("SELECT * FROM `heating_plannings` ORDER BY `day` ASC,`hour_start` ASC",$GLOBALS['debug']);
  foreach($plannings_result as $planning){
   $modality=api_heating_modality($planning->modality_fk);
   $planning->name=$modality->name;
   $planning->color=$modality->color;
   $planning->temperature=$modality->temperature;
   $plannings[$planning->day][$planning->id]=$planning;
  }
  // return plannings
  return $plannings;
 }

?>