<?php

 // include api
 require_once("api.inc.php");

 // acqurie variables
 $view=$_REQUEST['view'];
 if(!$view){$view="overview";}

 // defines constants
 define('DIR',$config->dir);
 define('CHARTS',DIR."charts/");
 define('ROOT',realpath(dirname(__FILE__))."/");
 define('TEMPLATE',"templates/".$config->template."/");
 define("VIEW",$view);

 // get settings and build object
 /*$settings_result=$db->queryObjects("SELECT * FROM settings",$debug);
 foreach($settings_result as $setting){$settings->{$setting->setting}=$setting->value;}
 messo in api*/

 // include view
 require_once(ROOT.TEMPLATE.$view.".php");

?>