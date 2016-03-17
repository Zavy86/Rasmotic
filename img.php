<?php

 // include api
 require_once("api.inc.php");

	// definitions
	$source=NULL;
	$max_width=800;
	$max_height=600;
	$watermark=NULL;

	// acquire variables
	if(isset($_GET['i'])){$source=$settings->gallery_path."/".$_GET['i'];}
	if(isset($_GET['w'])){$max_width=$_GET['w'];}
	if(isset($_GET['h'])){$max_height=$_GET['h'];}

	// check image
	if(!file_exists($source)){echo "image not found.. {".$source."}";die();}

	// get images metadata
	list($width,$height,$image_type)=getimagesize($source);

	// file type
	switch($image_type){
  case 1:$src=imagecreatefromgif($source);break;
  case 2:$src=imagecreatefromjpeg($source);break;
  case 3:$src=imagecreatefrompng($source);break;
  default:return null;
	}

	// scale image
	$x_ratio=$max_width/$width;
	$y_ratio=$max_height/$height;
	if(($x_ratio*$height)<$max_height){
	$tn_height=ceil($x_ratio*$height);
	$tn_width=$max_width;
	}else{
	$tn_width=ceil($y_ratio*$width);
	$tn_height=$max_height;
	}

	// center image
	$new_x=($max_width-$tn_width)/2;
	$new_y=($max_height-$tn_height)/2;

	// build image
	$tmp=imagecreatetruecolor($max_width,$max_height);
	$black_bg=imagecolorallocate($tmp,0,0,0);
	imagefilledrectangle($tmp,0,0,$tn_width,$tn_height,$black_bg);
	imagecopyresampled($tmp,$src,$new_x,$new_y,0,0,$tn_width,$tn_height,$width,$height);

 	// check if watermark file exist
	if(file_exists($watermark)){
		$watermark_img=imagecreatefrompng($watermark);
		imagealphablending($tmp,true);
		$x=imagesx($tmp)-imagesx($watermark_img)-20;
		$y=imagesy($tmp)-imagesy($watermark_img)-20;
		imagecopy($tmp,$watermark_img,$x,$y,0,0,imagesx($watermark_img),imagesy($watermark_img));
	}

	// output scaled image
	header('Content-Type: image/jpeg');
	imagejpeg($tmp,NULL,100);

?>