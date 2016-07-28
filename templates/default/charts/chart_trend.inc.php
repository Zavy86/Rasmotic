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

 //
 $datay1 = array(17,14,14,18,14,18,14,14,14,21,20,19,18,17,14,18,15,14,14,14,14,21,20,19,19);
 $datay2 = array(14,18,19,14,19,14,18,19,20,14,14,14,14,14,18,14,18,19,20,21,22,14,14,14,14);


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
 $graph->xaxis->SetTickLabels(array('0','','2','','4','','6','','8','','10','','12','','14','','16','','18','','20','','22','','24'));
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

 $graph->legend->Hide();


 // Output
 $graph->Stroke();

?>