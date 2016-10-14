<?php
 // include template header
 include("header.inc.php");
 // get objects
 $modalities=api_heating_modalities();
 if($_GET['idModality']){$modality_selected=api_heating_modality($_GET['idModality']);}
?>
<h4 class="page_title">Modalities</h4>
<table class="table table-hover table-condensed">
<?php foreach($modalities as $modality){ ?>
 <tr>
  <td><span style="display:inline-block;height:20px;width:20px;background-color:<?php echo $modality->color; ?>" class="progress-bar-striped">&nbsp;</span></td>
  <td><?php echo number_format($modality->temperature,1); ?>&deg;C</td>
  <td width="100%"><?php echo $modality->name; ?></td>
  <td><a href="index.php?view=heating_modalities_edit&idModality=<?php echo $modality->id; ?>"><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a></td>
 </tr>
<?php } ?>
</table>
<a href="index.php?view=heating_plannings_view" class="btn btn-default">Back</a>
<a href="index.php?view=heating_modalities_edit" class="btn btn-primary">Add new modality</a>
<?php
//<td><a href="index.php?view=heating_modalities_edit"><span class='glyphicon glyphicon-plus-sign' aria-hidden='true'></span></a></td>
 // debug
 if($debug){api_dump($modalities,"modalities");}
 // include template footer
 include("footer.inc.php");
?>