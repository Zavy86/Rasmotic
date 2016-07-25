<?php

 // include api
 require_once("api.inc.php");

 // acqurie variables
 $r_view=$_REQUEST['view'];
 if(!$r_view){$r_view="overview";}

 // check view
 if(!in_array($r_view,array("access","overview"))){
  // check host
  if($_SERVER['REMOTE_ADDR']<>$_SERVER['SERVER_ADDR']."test"){
   // check session
   if(!$_SESSION['access']){
    $r_view="access";
   }
  }
 }

 // defines constants
 define('DIR',$config->dir);
 define('CHARTS',DIR."charts/");
 define('ROOT',realpath(dirname(__FILE__))."/");
 define('TEMPLATE',"templates/".$config->template."/");
 define("VIEW",$r_view);

 // get settings and build object
 /*$settings_result=$db->queryObjects("SELECT * FROM settings",$debug);
 foreach($settings_result as $setting){$settings->{$setting->setting}=$setting->value;}
 messo in api*/

 // include view
 require_once(ROOT.TEMPLATE.$r_view.".php");

?>