<?php
 // include api
 require_once("../../../api.inc.php");
 // include charts class
 require_once('../../../classes/jpgraph/jpgraph.php');
 require_once('../../../classes/jpgraph/jpgraph_line.php');
 // definitions
 $planning_array=array();
 $planning_datas=array();
 // acquire variables
 $r_width=$_REQUEST['width'];
 $r_height=$_REQUEST['height'];
 // check variables
 if(!$r_width){$r_width="360";}
 if(!$r_height){$r_height="200";}
 // cycle al strips to build planning datas
 foreach($settings->heating->planning as $strip){
  // convert hour to minutes
  $time=explode(":",$strip->hour_start);
  $minutes=($time[0]*60)+$time[1];
  // check temperature between minutes
  if($minutes>=0 && $minutes<60){$planning_array[0]=$strip->temperature;}
  if($minutes>=60 && $minutes<120){$planning_array[1]=$strip->temperature;}
  if($minutes>=120 && $minutes<180){$planning_array[2]=$strip->temperature;}
  if($minutes>=180 && $minutes<240){$planning_array[3]=$strip->temperature;}
  if($minutes>=240 && $minutes<300){$planning_array[4]=$strip->temperature;}
  if($minutes>=300 && $minutes<360){$planning_array[5]=$strip->temperature;}
  if($minutes>=360 && $minutes<420){$planning_array[6]=$strip->temperature;}
  if($minutes>=420 && $minutes<480){$planning_array[7]=$strip->temperature;}
  if($minutes>=480 && $minutes<540){$planning_array[8]=$strip->temperature;}
  if($minutes>=540 && $minutes<600){$planning_array[9]=$strip->temperature;}
  if($minutes>=600 && $minutes<660){$planning_array[10]=$strip->temperature;}
  if($minutes>=660 && $minutes<720){$planning_array[11]=$strip->temperature;}
  if($minutes>=720 && $minutes<780){$planning_array[12]=$strip->temperature;}
  if($minutes>=780 && $minutes<840){$planning_array[13]=$strip->temperature;}
  if($minutes>=840 && $minutes<900){$planning_array[14]=$strip->temperature;}
  if($minutes>=900 && $minutes<960){$planning_array[15]=$strip->temperature;}
  if($minutes>=960 && $minutes<1020){$planning_array[16]=$strip->temperature;}
  if($minutes>=1020 && $minutes<1080){$planning_array[17]=$strip->temperature;}
  if($minutes>=1080 && $minutes<1140){$planning_array[18]=$strip->temperature;}
  if($minutes>=1140 && $minutes<1200){$planning_array[19]=$strip->temperature;}
  if($minutes>=1200 && $minutes<1260){$planning_array[20]=$strip->temperature;}
  if($minutes>=1260 && $minutes<1320){$planning_array[21]=$strip->temperature;}
  if($minutes>=1320 && $minutes<1380){$planning_array[22]=$strip->temperature;}
  if($minutes>=1380 && $minutes<1440){$planning_array[23]=$strip->temperature;}
 }
 // add temperature to null hours
 for($h=0;$h<25;$h++){
  $planning_datas[$h]=$planning_array[$h];
  if(!$planning_datas[$h]){$planning_datas[$h]=$planning_datas[$h-1];}
 }

 // definitions
 $temperatures_array=array();
 $heating_status_array=array();
 $labels_array_tmp=array();
 $labels_array=array();
 $hours_array=array();
 $hours_array_on=array();

 // now timestamp
 $b=time();
 // yesterday timestamp
 $a=strtotime("-1 day")+1;

 /*api_dump(date("Y-m-d H:i:s",$b)." ".$b);
 api_dump(date("Y-m-d H:i:s",$a)." ".$a);
 die();*/

 // get temperature from database
 $temperatures_results=$GLOBALS['db']->queryObjects("SELECT * FROM `detections` WHERE `typology`='temperature' AND `timestamp` BETWEEN '".$a."' AND '".$b."' ORDER BY `timestamp` ASC",$GLOBALS['debug']);
 foreach($temperatures_results as $temperature){$temperatures_array[$temperature->id]=$temperature;}

 // get heating status from database
 $heating_status_results=$GLOBALS['db']->queryObjects("SELECT * FROM `detections` WHERE `typology`='heating_status' AND `timestamp` BETWEEN '".$a."' AND '".$b."' ORDER BY `timestamp` ASC",$GLOBALS['debug']);
 foreach($heating_status_results as $heating_status){$heating_status_array[$heating_status->timestamp]=$heating_status;}

 // build labels array
 foreach($temperatures_array as $temperature){$labels_array_tmp[date("dH",$temperature->timestamp)]=date("H",$temperature->timestamp);}

 // save values into temperature array
 foreach($temperatures_array as $temperature){
  if(!is_array($hours_array[date("dH",$temperature->timestamp)])){$hours_array[date("dH",$temperature->timestamp)]=array();}
  $hours_array[date("dH",$temperature->timestamp)][]=$temperature->value;
  $hours_array_on[date("dH",$temperature->timestamp)]+=$heating_status_array[$temperature->timestamp]->value;
 }

 // set only odds label
 foreach($hours_array as $hour=>$values){
  if($labels_array_tmp[$hour]%2==0){$labels_array[]=$labels_array_tmp[$hour];}else{$labels_array[]="";}
 }

 // calculate hours average
 foreach($hours_array as $hour=>$values){
  $datay1[]=round(array_sum($values)/count($values),1);
  $datay2[]=round(($hours_array_on[$hour]*(round(array_sum($values)/count($values),1)-14)/count($values))+14,1);
  /** 14 è la base del grafico vedi sotto */
 }

 // duplicate last data for last hour
 $datay1[]=end($datay1);
 $datay2[]=end($datay2);
 if(date("H")%2==0){$labels_array[]=date("H");}

 /*api_dump($hours_array_on);
 api_dump($datay1);
 api_dump($datay2);
 die();*/

 /*
 api_dump($planning_array);
 api_dump($planning_datas);
 api_dump($settings->heating->planning);
 die();
 */

 // Setup the graph
 $graph = new Graph($r_width,$r_height,rand(1111,9999));
 $graph->SetScale("textlin",14,24); // andare 1 sotto la minima e 1 sopra la massima (o in base 2 vediamo...)

 $theme_class=new UniversalTheme;

 $graph->SetTheme($theme_class);
 $graph->img->SetAntiAliasing(false);
 $graph->SetBox(false);

 $graph->img->SetAntiAliasing();

 $graph->ygrid->Show(true);
 $graph->yaxis->HideZeroLabel();
 $graph->yaxis->HideLine(false);
 $graph->yaxis->HideTicks(false,false);

 $graph->xgrid->Show();
 $graph->xaxis->HideTicks(false,false);
 $graph->xgrid->SetLineStyle("solid");
 $graph->xaxis->SetTickLabels($labels_array);
 $graph->xgrid->SetColor('#E3E3E3');

 // Create the first line
 $p1=new LinePlot($datay1);   // spento
 $graph->Add($p1);
 $p1->SetStepStyle();
 $p1->SetColor("#337AB7");
 $p1->SetFillColor('#7BA9D0');
 $p1->SetLegend('Off');

 // Create the second line
 $p2=new LinePlot($datay2);  // acceso
 $graph->Add($p2);
 $p2->SetStepStyle();
 $p2->SetColor("#337AB7");
 $p2->SetFillColor('#337AB7');
 $p2->SetLegend('On');

 // Create the third line
 $p3=new LinePlot($planning_datas);  // planning
 $graph->Add($p3);
 $p3->SetStepStyle();
 $p3->SetColor("#D9534F");
 $p3->SetLegend('Planning');

 // hide legend
 $graph->legend->Hide();

 // Output
 $graph->Stroke();

?>