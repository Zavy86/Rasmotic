<?php

// mysql parameters
$servername = "localhost";
$username = "rasmotic";
$password = "rasmotic";
$dbname = "rasmotic";

// connect to mysql
$mysql=new mysqli($servername,$username,$password,$dbname);
if($mysql->connect_error){die("Connection failed: ".$mysql->connect_error);}

// get heating system status
$sql="SELECT `value` FROM `settings` WHERE `setting`='heating_status'";
$result=$mysql->query($sql);
$row=$result->fetch_object();
$heating_status=($row->value=="on"?1:0);

// disconnect from mysql
$mysql->close();

// set heating status
$output=exec("sudo ./relay.json.py 20 ".$heating_status);
$json=json_decode($output);
// debug
var_dump($json);

?>