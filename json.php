<?php
 // include api
 require_once("api.inc.php");
 // definitions
 $return=new stdClass();
 // build return object
 $return->settings=$settings;
 // encode in json and return
 echo json_encode($return);
 // check and renderize dump
 if($_GET['dump']){api_dump($return,"return");}
?>