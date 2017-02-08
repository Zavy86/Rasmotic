<?php

$g_act=$_GET['act'];
if(!$g_act){$g_act="OFF";}

$output=exec("sudo ./relay.json.py 20 ".$g_act);
$json=json_decode($output);
// debug
var_dump($json);

?>