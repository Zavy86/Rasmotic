<?php
 require_once('../classes/jpgraph/jpgraph.php');
 require_once('../classes/jpgraph/jpgraph_pie.php');

 $umidita_sensore=rand(30,35);

 //$data = array(20,80);
 $data = array(100-$umidita_sensore,$umidita_sensore);

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
$p1->midtitle->Set($umidita_sensore."%");
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