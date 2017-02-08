<?php

$output=exec("sudo ./dht.json.py 2302 21");
$json=json_decode($output);
// debug
var_dump($json);

// set timestamp
$timestamp=time();
$datetime=date("Y-m-d H:i:s",$timestamp);

// mysql parameters
$servername = "localhost";
$username = "rasmotic";
$password = "rasmotic";
$dbname = "rasmotic";

// connect to mysql
$mysql=new mysqli($servername,$username,$password,$dbname);
if($mysql->connect_error){die("Connection failed: ".$mysql->connect_error);}

// get last timestamp
$sql="SELECT `timestamp` FROM `detections` WHERE `typology`='temperature' ORDER BY `timestamp` DESC LIMIT 0,1";
$result=$mysql->query($sql);
$row=$result->fetch_object();
$last_timestamp=(int)$row->timestamp;

// check timestamp
if($timestamp<$last_timestamp){die("Timestamp error");}

echo "<br><br>Timestamp: ";
var_dump($timestamp);
echo "<br><br>Last timestamp: ";
var_dump($last_timestamp);

// get last timestamp
$sql="SELECT `value` FROM `settings` WHERE `setting`='heating_status'";
$result=$mysql->query($sql);
$row=$result->fetch_object();
$heating_status=($row->value=="on"?1:0);

echo "<br><br>Heating System Status: ";
var_dump($heating_status);

// save heating system status
$sql="INSERT INTO `detections`(`typology`, `timestamp`, `datetime`, `value`) VALUES ('heating_status','".$timestamp."','".$datetime."','".$heating_system_status."')";
$mysql->query($sql);

// save temperature
if($json->temperature || $json->humidity){
 $sql="INSERT INTO `detections`(`typology`, `timestamp`, `datetime`, `value`) VALUES ('temperature','".$timestamp."','".$datetime."','".$json->temperature."')";
 $mysql->query($sql);
}

// save temperature
if($json->temperature || $json->humidity){
 $sql="INSERT INTO `detections`(`typology`, `timestamp`, `datetime`, `value`) VALUES ('humidity','".$timestamp."','".$datetime."','".$json->humidity."')";
 $mysql->query($sql);
}

// disconnect from mysql
$mysql->close();

?>