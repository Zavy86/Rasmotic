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

  // heating system
  case "heating_settings_save":heating_settings_save();break;
  case "heating_modality_save":heating_modality_save();break;
  case "heating_modality_delete":heating_modality_delete();break;
  case "heating_planning_save":heating_planning_save();break;
  case "heating_planning_delete":heating_planning_delete();break;
  case "heating_planning_reset":heating_planning_reset();break;
  case "heating_planning_clone":heating_planning_clone();break;

  case "heating_modality_toggle":heating_modality_toggle();break;
  case "heating_absence_toggle":heating_absence_toggle();break;
  case "heating_manual_temperature":heating_manual_temperature();break;

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
  if(md5($r_passcode)===$GLOBALS['settings']->system_passcode){
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
  $p_system_language=$_REQUEST["system_language"];
  // checks
  if(!strlen($p_system_language)){$_SESSION['log'][]=array("error","UPGRADE system_language value needed");return FALSE;}
  // update settings
  api_setting_update("system_language",$p_system_language);
  // redirect
  exit(header("location: index.php?view=settings&alert=settings_updated&alert_class=success"));
 }


 // heating system settings save
 function heating_settings_save(){
  // acquire variables
  $r_heating_manual_timeout=$_REQUEST["heating_manual_timeout"];
  $r_heating_absent_temperature=$_REQUEST["heating_absent_temperature"];
  // checks
  if(!is_numeric($r_heating_manual_timeout)){$_SESSION['log'][]=array("error","UPGRADE heating_manual_timeout numeric value needed");return false;}
  if(!is_numeric($r_heating_absent_temperature)){$_SESSION['log'][]=array("error","UPGRADE heating_absent_temperature numeric value needed");return false;}
  // update settings
  api_setting_update("heating_manual_timeout",$r_heating_manual_timeout);
  api_setting_update("heating_absent_temperature",$r_heating_absent_temperature);
  // redirect
  exit(header("location: index.php?view=heating_settings&alert=settings_updated&alert_class=success"));
 }

 // heating system modality save
 function heating_modality_save(){
  // get objects
  $modality=api_heating_modality($_REQUEST['idModality']);
  // check for update or new object
  if($modality->id!==$_GET['idModality']){$modality=new stdClass();}
  // acquire variables
  $modality->name=addslashes(ucfirst($_REQUEST['name']));
  $modality->color=addslashes(strtoupper($_REQUEST['color']));
  $modality->temperature=addslashes($_REQUEST['temperature']);
  // checks
  if(!$modality->name){api_alerts_add("Field name is required","danger");$error=TRUE;}
  if(!$modality->color){api_alerts_add("Field color is required","danger");$error=TRUE;}
  if(!$modality->temperature){api_alerts_add("Field temperature is required","danger");$error=TRUE;}
  if($error){exit(header("location: index.php?view=heating_modalities_edit&idModality=".$modality->id));}


  // insert or update
  if($modality->id){
   $GLOBALS['db']->queryUpdate("heating_modalities",$modality,"id");
   $alert="modality_updated";
  }else{
   $GLOBALS['db']->queryInsert("heating_modalities",$modality);
   $alert="modality_created";
  }
  // redirect
  exit(header("location: index.php?view=heating_modalities_list&idModality=".$modality->id."&alert=".$alert."&alert_class=success"));
 }

 // heating system modality delete
 function heating_modality_delete(){
  // get objects
  $modality=api_heating_modality($_REQUEST['idModality']);
  // check objects
  if($modality->id!==$_GET['idModality']){$_SESSION['log'][]=array("error","DELETE heating_modality needed");return false;}
  // remove null strip
  $GLOBALS['db']->queryDelete("heating_modalities",$modality->id,"id");
  // redirect
  exit(header("location: index.php?view=heating_modalities_list&alert=modality_deleted&alert_class=warning"));
 }

 // heating system planning save
 function heating_planning_save(){
  // build strip object
  $strip=new stdClass();
  $strip->day=addslashes($_REQUEST['day']);
  $strip->hour_start=addslashes($_REQUEST['hour_start']);
  $strip->hour_end=addslashes($_REQUEST['hour_end']);
  $strip->modality_fk=addslashes($_REQUEST['modality_fk']);
  // checks and convert
  if(!$strip->modality_fk){exit(header("location: index.php?view=heating_plannings_edit&day=".$strip->day."&alert=planning_error&alert_class=danger"));}
  if(strtotime($strip->hour_end)<=strtotime($strip->hour_start)){exit(header("location: index.php?view=heating_plannings_edit&day=".$strip->day."&alert=planning_error&alert_class=danger"));}
  if($strip->hour_end=="00:00"){$strip->hour_end="23:59:59";}
  if($strip->hour_end=="23:59"){$strip->hour_end="23:59:59";}
  // remove
  $strip_remove=$GLOBALS['db']->queryUniqueValue("SELECT id FROM `heating_plannings` WHERE `day`='".$strip->day."' AND modality_fk IS NULL");
  if($strip_remove>0){$GLOBALS['db']->queryDelete("heating_plannings",$strip_remove,"id");}
  // add new planning strip
  $GLOBALS['db']->queryInsert("heating_plannings",$strip);
  // if day is not completed add null strip
  if($strip->hour_end<>"23:59:59"){
   $strip_null=new stdClass();
   $strip_null->day=$strip->day;
   $strip_null->hour_start=$strip->hour_end;
   $strip_null->hour_end="23:59:59";
   $strip_null->modality_fk=NULL;
  }
  $GLOBALS['db']->queryInsert("heating_plannings",$strip_null);
  // redirect
  exit(header("location: index.php?view=heating_plannings_edit&day=".$strip->day."&alert=planning_saved"));
 }

 // heating system planning delete
 function heating_planning_delete(){
  // acquire variables
  $r_day=$_REQUEST['day'];
  // remove null strip
  $strip_remove=$GLOBALS['db']->queryUniqueValue("SELECT `id` FROM `heating_plannings` WHERE `day`='".$r_day."' AND `modality_fk` IS NULL");
  if($strip_remove>0){$GLOBALS['db']->queryDelete("heating_plannings",$strip_remove,"id");}
  // remove last strip
  $strip_last_remove=$GLOBALS['db']->queryUniqueValue("SELECT `id` FROM `heating_plannings` WHERE `day`='".$r_day."' AND `modality_fk` IS NOT NULL ORDER BY `hour_end` DESC");
  if($strip_last_remove>0){$GLOBALS['db']->queryDelete("heating_plannings",$strip_last_remove,"id");}
  // get last strip
  $strip_last=$GLOBALS['db']->queryUniqueValue("SELECT `hour_end` FROM `heating_plannings` WHERE `day`='".$r_day."' AND `modality_fk` IS NOT NULL ORDER BY `hour_end` DESC");
  if(!$strip_last){$strip_last="00:00:00";}
  // insert new null strip
  $strip_null=new stdClass();
  $strip_null->day=$r_day;
  $strip_null->hour_start=$strip_last;
  $strip_null->hour_end="23:59:59";
  $strip_null->modality_fk=NULL;
  $GLOBALS['db']->queryInsert("heating_plannings",$strip_null);
  // redirect
  exit(header("location: index.php?view=heating_plannings_edit&day=".$r_day."&alert=planning_saved"));
 }

 // heating system planning reset
 function heating_planning_reset(){
  // acquire variables
  $r_day=$_REQUEST['day'];
  // remove strips
  $strips_remove_array=$GLOBALS['db']->queryObjects("SELECT * FROM `heating_plannings` WHERE `day`='".$r_day."'");
  if(is_array($strips_remove_array)){foreach($strips_remove_array as $strips_remove){$GLOBALS['db']->queryDelete("heating_plannings",$strips_remove->id,"id");}}
  // insert new null strip
  $strip_null=new stdClass();
  $strip_null->day=$r_day;
  $strip_null->hour_start="00:00:00";
  $strip_null->hour_end="23:59:59";
  $strip_null->modality_fk=NULL;
  $GLOBALS['db']->queryInsert("heating_plannings",$strip_null);
  // redirect
  exit(header("location: index.php?view=heating_plannings_edit&day=".$r_day."&alert=planning_saved"));
 }

 // heating system planning clone
 function heating_planning_clone(){
  // get objects
  $planning=api_heating_planning($_REQUEST['day']);
  // acquire variables
  $p_days_array=$_REQUEST['days'];
  // check and convert
  if(!count($planning) || !count($p_days_array)){exit(header("location: index.php?view=heating_plannings_view&alert=planning_cloned_error"));}
  // cycle selected days
  foreach($p_days_array as $day){
   // delete current day planning
   $strips_remove_array=$GLOBALS['db']->queryObjects("SELECT * FROM `heating_plannings` WHERE `day`='".$day."'");
   if(is_array($strips_remove_array)){foreach($strips_remove_array as $strips_remove){$GLOBALS['db']->queryDelete("heating_plannings",$strips_remove->id,"id");}}
   // insert cloned planning
   foreach($planning as $strip){
    // insert strip
    $strip_insert=new stdClass();
    $strip_insert->day=$day;
    $strip_insert->hour_start=$strip->hour_start;
    $strip_insert->hour_end=$strip->hour_end;
    $strip_insert->modality_fk=$strip->modality_fk;
    api_dump($strip_insert);
    $GLOBALS['db']->queryInsert("heating_plannings",$strip_insert);
   }
  }
  // redirect
  exit(header("location: index.php?view=heating_plannings_view&day=".$strip->day."&alert=planning_cloned"));
 }



 // update heating_modality
 function heating_modality_toggle(){
  // acquire variables
  $p_manual_toggle=$_REQUEST["manual_toggle"];
  // checks on converts
  if($p_manual_toggle=="true"){$p_manual_toggle="manual";}else{$p_manual_toggle="auto";}
  // update manual toggle
  api_setting_update("heating_modality",$p_manual_toggle);
  // if is manual update started datetime
  if($p_manual_toggle=="manual"){api_setting_update("heating_manual_started",api_datetime_now());}
   else{api_setting_update("heating_manual_started",NULL);}
 }

 // toggle heating_modality modality absent
 function heating_absence_toggle(){
  // checks on converts
  if($GLOBALS['settings']->heating_modality=="absent"){$v_modality="auto";}else{$v_modality="absent";}
  // update manual toggle
  api_setting_update("heating_modality",$v_modality);
  // if is absent reset started datetime
  if($v_modality=="absent"){api_setting_update("heating_manual_started",NULL);}
 }

 // update manual temperature
 function heating_manual_temperature(){
  // acquire variables
  echo $p_temperature=$_REQUEST["temperature"];
  // checks
  if(!is_numeric($p_temperature)){
   $_SESSION['log'][]=array("error","UPGRADE heating_manual_temperature numeric value needed");
   return false;
  }
  // update manual temperature
  api_setting_update("heating_manual_temperature",number_format($p_temperature,1,".",","));
 }


 //
 if($_REQUEST['debug']){
  echo "<br><br><div id='debug'>\n <pre>\n";
  foreach($_SESSION['log'] as $log){echo "<code class='".$log[0]."'>".$log[1]."</code>\n";}
  echo "  </pre>\n</div>\n";
 }

?>