<?php
 // include api
 require_once("../../../api.inc.php");
 // include charts class
 require_once('../../../classes/jpgraph/jpgraph.php');
 require_once('../../../classes/jpgraph/jpgraph_pie.php');
 // acquire variables
 $r_size=$_REQUEST['size'];
 // check variables
 if(!$r_size){$r_size="140";}
 // check modality and get pointing temperature
 if($settings->heating_modality=="manual"){$pointing_temperature=$settings->heating_manual_temperature;}
 else{$pointing_temperature=$sensors->heating->planning->temperature;}
 // per fare in modo che il grafico sia al 100% anche nel caso in cui siamo oltre la temperatura voluta
 if($sensors->temperature>$pointing_temperature){$pointing_temperature=$sensors->temperature;}
 $percentage_temperature=$sensors->temperature*100/$pointing_temperature;
 // build data array
 $data=array(100-$percentage_temperature,$percentage_temperature);
 // build pie graph
 $graph=new PieGraph($r_size,$r_size,rand(1111,9999));
 // set theme
 $theme_class=new UniversalTheme;
 $graph->SetTheme($theme_class);
 $graph->SetBox(false);
 $graph->SetAntiAliasing();
 // create the pie plot
 $p1=new PiePlotC($data);
 // set size of pie
 $p1->SetSize(0.38);
 // setup the title on the center circle
 $p1->midtitle->Set($sensors->temperature."°");
 $p1->midtitle->SetFont(FF_ARIAL,FS_BOLD,11);
 // set color for mid circle
 $p1->SetMidColor('#F4F4F4');
 $p1->SetSliceColors(array('#7BA9D0','#337AB7'));
 $p1->SetStartAngle(90);
 $p1->SetLabelPos(3);  // nasconde allontanandole se trovo il modo per disabilitarle non serve piu
 // add plot to pie graph
 $graph->Add($p1);
 // show pie
 $graph->Stroke();
?>