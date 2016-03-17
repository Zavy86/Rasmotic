<?php
 require_once('../../../classes/jpgraph/jpgraph.php');
 require_once('../../../classes/jpgraph/jpgraph_line.php');

 //$datay1 = array(19,20,21,22,24,23,22,21,19,17,19,20,19,20,21,22,24,23,22,21,19,17,19,20,20);
 $datay1 = array(17,14,14,18,14,18,14,14,14,21,20,19,18,17,14,18,15,14,14,14,14,21,20,19,19);
 $datay2 = array(14,18,19,14,19,14,18,19,20,14,14,14,14,14,18,14,18,19,20,21,22,14,14,14,14);
 $datay3 = array(18,18,18,18,18,18,18,21,21,18,18,18,18,18,18,18,18,21,21,21,21,21,21,18,18);

 // Setup the graph
 $graph = new Graph(360,145,rand(1111,9999));
 $graph->SetScale("textlin",14,24);

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
 $p1=new LinePlot($datay1);
 $graph->Add($p1);
 $p1->SetStepStyle();
 $p1->SetColor("#337AB7");
 $p1->SetFillColor('#7BA9D0');
 $p1->SetLegend('Off');

 // Create the second line
 $p2=new LinePlot($datay2);
 $graph->Add($p2);
 $p2->SetStepStyle();
 $p2->SetColor("#337AB7");
 $p2->SetFillColor('#337AB7');
 $p2->SetLegend('On');

 // Create the third line
 $p3=new LinePlot($datay3);
 $graph->Add($p3);
 $p3->SetStepStyle();
 $p3->SetColor("#D9534F");
 $p3->SetLegend('Planning');

 $graph->legend->Hide();


 // Output line
 $graph->Stroke();

?>