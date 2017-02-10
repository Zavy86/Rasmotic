<?php

class Database{

 private $connection;


 public function __construct(){
  global $config;
  try{
   $this->connection=new PDO($config->db_type.":host=".$config->db_host.";port=".$config->db_port.";dbname=".$config->db_name.";charset=utf8",$config->db_user,$config->db_pass);
   $this->connection->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,"SET NAMES utf8");
   $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
   $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
   $_SESSION['log'][]=array("log","PDO connection: connected to ".$config->db_name." ".strtoupper($config->db_type)." database on server ".$config->db_host);
  }catch(PDOException $e){
   $_SESSION['log'][]=array("error","PDO connection: ".$e->getMessage());
   die("PDO connection: ".$e->getMessage());
  }
 }


 public function __call($method,$args){
  $args=implode(",",$args);
  $_SESSION['log'][]=array("warn","Method ".$method."(".$args.") was not found in ".get_class($this)." class");
 }


 public function queryObjects($sql,$debug){
  $_SESSION['log'][]=array("log","PDO queryObjects: ".$sql);
  try{
   $results=$this->connection->query($sql);
   $return=$results->fetchAll(PDO::FETCH_OBJ);
   if($debug){$_SESSION['log'][]=array("log","PDO queryObjects results:\n".var_export($return,TRUE));}
  }catch(PDOException $e){
   $_SESSION['log'][]=array("warn","PDO queryObjects: ".$e->getMessage());
   $return=FALSE;
  }
  return $return;
 }


 public function queryUniqueObject($sql,$debug){
  $sql.=" LIMIT 0,1";
  $_SESSION['log'][]=array("log","PDO queryUniqueObject: ".$sql);
  try{
   $results=$this->connection->query($sql);
   $return=$results->fetch(PDO::FETCH_OBJ);
   if($debug){$_SESSION['log'][]=array("log","PDO queryUniqueObject result:\n".var_export($return,TRUE));}
   }catch(PDOException $e){
   $_SESSION['log'][]=array("warn","PDO queryUniqueObject: ".$e->getMessage());
   $return=FALSE;
  }
  return $return;
 }


 public function queryUniqueValue($sql,$debug){
  $sql.=" LIMIT 0,1";
  $_SESSION['log'][]=array("log","PDO queryUniqueValue: ".$sql);
  try{
   $results=$this->connection->query($sql);
   $return=$results->fetch(PDO::FETCH_NUM)[0];
   if($debug){$_SESSION['log'][]=array("log","PDO queryUniqueValue result: ".$return);}
   }catch(PDOException $e){
   $_SESSION['log'][]=array("warn","PDO queryUniqueValue: ".$e->getMessage());
   $return=FALSE;
  }
  return $return;
 }


 public function queryInsert($table,$object){
  $sql="INSERT INTO `".$table."` (";
  foreach(array_keys(get_object_vars($object)) as $key){$sql.="`".$key."`,";}
  $sql=substr($sql,0,-1).") VALUES (";
  foreach(array_keys(get_object_vars($object)) as $key){$sql.=":".$key.",";}
  $sql=substr($sql,0,-1).")";
  $_SESSION['log'][]=array("log","PDO queryInsert: ".$sql."\n".var_export($object,TRUE));
  try{
   $query=$this->connection->prepare($sql);
   $query->execute(get_object_vars($object));
   $return=$this->connection->lastInsertId();
  }catch(PDOException $e){
   $_SESSION['log'][]=array("error","PDO queryInsert: ".$e->getMessage());
   $return=FALSE;
  }
  return $return;
 }


 public function queryUpdate($table,$object,$idKey="id"){
  $sql="UPDATE `".$table."` SET ";
  foreach(array_keys(get_object_vars($object)) as $key){
   if($key<>$idKey){$sql.="`".$key."`=:".$key.",";}
  }
  $sql=substr($sql,0,-1)." WHERE `".$idKey."`=:".$idKey."";
  $_SESSION['log'][]=array("log","PDO queryUpdate: ".$sql."\n".var_export($object,TRUE));
  try{
   $query=$this->connection->prepare($sql);
   $query->execute(get_object_vars($object));
   $return=$object->$idKey;
  }catch(PDOException $e){
   $_SESSION['log'][]=array("error","PDO queryUpdate: ".$e->getMessage());
   $return=FALSE;
  }
  return $return;
 }


 public function queryDelete($table,$id,$idKey="id"){
  $sql="DELETE FROM `".$table."` WHERE `".$idKey."`='".$id."'";
  $_SESSION['log'][]=array("log","PDO queryDelete: ".$sql);
  try{
   $query=$this->connection->query($sql);
   $_SESSION['log'][]=array("warn","PDO queryDelete: ".$query->rowCount()." rows deleted");
   return TRUE;
  }catch(PDOException $e){
   $_SESSION['log'][]=array("error","PDO queryDelete: ".$e->getMessage());
   $return=FALSE;
  }
  return $return;
 }


 public function rowcount($table,$where=1){
  $sql="SELECT COUNT(*) FROM `".$table."` WHERE ".$where;
  $_SESSION['log'][]=array("log","PDO count: ".$sql);
  try{
   $results=$this->connection->query($sql);
   $return=$results->fetchColumn();
  }catch(PDOException $e){
   $_SESSION['log'][]=array("error","PDO count: ".$e->getMessage());
   $return=FALSE;
  }
  return $return;
 }

}

?>
