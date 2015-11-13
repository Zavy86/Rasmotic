<?php

 session_start();

 // definitions
 global $debug;
 global $config;
 global $db;

 $config=new stdClass();

 // reset session logs
 $_SESSION['log']=NULL;

 // include configuration file
 require_once("config.inc.php");

 // include database class
 require_once("classes/database.class.php");

 // build database instance
 $db=new Database();


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
 define(API_DUMP_VARDUMP,1);


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
 * Datetime Now
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

?>