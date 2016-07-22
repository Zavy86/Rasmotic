<?php
 // include api
 require_once("../../../api.inc.php");
 // include charts class
 require_once('../../../classes/jpgraph/jpgraph.php');
 require_once('../../../classes/jpgraph/jpgraph_pie.php');
 // build data array
 $data=array(100-$sensors->humidity,$sensors->humidity);
 // build pie graph
 $graph=new PieGraph(140,140,rand(1111,9999));
 // set theme
 $theme_class=new UniversalTheme;
 $graph->SetTheme($theme_class);
 $graph->SetBox(false);
 $graph->SetAntiAliasing();
 // create the pie plot
 $p1=new PiePlotC($data);
 // set size of pie
 $p1->SetSize(0.38);
 // Setup the title on the center circle
 $p1->midtitle->Set($sensors->humidity."%");
 $p1->midtitle->SetFont(FF_ARIAL,FS_BOLD,11);
 // set color for mid circle
 $p1->SetMidColor('#F4F4F4');
 $p1->SetSliceColors(array('#7BA9D0','#337AB7'));
 $p1->SetStartAngle(90);
 $p1->SetLabelPos(3); // nasconde allontanandole se trovo il modo per disabilitarle non serve piu
 // add plot to pie graph
 $graph->Add($p1);
 // show pie
 $graph->Stroke();
?>