<?php

 // include api
 require_once("../api.inc.php");

 // include charts class
 require_once('../classes/jpgraph/jpgraph.php');
 require_once('../classes/jpgraph/jpgraph_pie.php');

 
 // get settings and build object
 $settings_result=$db->queryObjects("SELECT * FROM settings",$debug);
 foreach($settings_result as $setting){$settings->{$setting->setting}=$setting->value;}
  
 //
 if($settings->modality=="manual"){
  $temperatura_voluta=$settings->manual_temperature;
 }else{
  $temperatura_voluta=21;  // acquisire temperatura del planning
 }
 
 
 $temperatura_sensore=20.5;
 //$temperatura_sensore=rand(14,21);
 
 
 
 // per fare in modo che il grafico sia al 100% anche nel caso in cui siamo oltre la temperatura voluta
 if($temperatura_sensore>$temperatura_voluta){$temperatura_voluta=$temperatura_sensore;}
 
 $temperatura_percentuale=$temperatura_sensore*100/$temperatura_voluta;
 
 //$data = array(20,80);
 $data = array(100-$temperatura_percentuale,$temperatura_percentuale);

 // A new pie graph
 $graph = new PieGraph(145,145,rand(1111,9999));

 $theme_class=new UniversalTheme;

 $graph->SetTheme($theme_class);
 //$graph->img->SetAntiAliasing(false);
 $graph->SetBox(false);

 //$graph->title->Set('Step Line');
 
 $graph->SetAntiAliasing();
 
 // Create the pie plot
 $p1 = new PiePlotC($data);

 // Set size of pie
 $p1->SetSize(0.38);


 // Setup the title on the center circle
 $p1->midtitle->Set($temperatura_sensore."°");
 $p1->midtitle->SetFont(FF_ARIAL,FS_BOLD,11);

 // Set color for mid circle
 $p1->SetMidColor('#F4F4F4');


 $p1->SetSliceColors(array('#7BA9D0','#337AB7'));
 $p1->SetStartAngle(90);

 $p1->SetLabelPos(3);  // nasconde allontanandole se trovo il modo per disabilitarle non serve piu

 // Add plot to pie graph
 $graph->Add($p1);

 $graph->Stroke();
 
?>