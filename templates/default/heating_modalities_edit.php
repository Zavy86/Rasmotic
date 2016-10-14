<?php
 // include template header
 include("header.inc.php");
 // definitions
 $color_array=array('#EF5350','#E53935','#C62828','#EC407A','#D81B60','#AD1457','#AB47BC','#8E24AA','#6A1B9A',
                    '#42A5F5','#1E88E5','#1565C0','#66BB6A','#43A047','#2E7D32','#FFEE58','#FDD835','#F9A825',
                    '#FFA726','#FB8C00','#EF6C00','#8D6E63','#6D4C41','#4E342E','#BDBDBD','#757575','#424242');
 // get objects
 if($_GET['idModality']){$modality=api_heating_modality($_GET['idModality']);}
 // check if modality is used
 foreach($settings->heating->plannings as $planning){foreach($planning as $strip){if($strip->modality_fk==$modality->id){$modality->used=TRUE;}}}
?>
<!-- page title -->
<h4 class="page_title">Modalities</h4>
<!-- form -->
<form action="submit.php?act=heating_modality_save&idModality=<?php echo $modality->id; ?>" method="post" id="heating_modalities_edit" class="form-horizontal">
 <!-- name -->
 <div class="form-group">
  <label class="col-xs-12 col-sm-2 control-label">Name</label>
  <div class="col-xs-12 col-sm-10">
   <input type="text" name="name" class="form-control" id="heating_modalities_edit_name" placeholder="Modality name" value="<?php echo $modality->name;?>">
  </div><!-- /col -->
 </div><!-- /form-group -->
 <!-- color -->
 <div class="form-group">
  <label class="col-xs-12 col-sm-2 control-label">Color</label>
  <div class="col-xs-12 col-sm-10">
   <div class="input-group">
    <span class="input-group-addon" id="colorpicked">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <select name="color" class="form-control selectpicker">
     <option value="">Select a color..</option>
     <?php
      foreach($color_array as $color){
       echo "<option value='".$color."' style='background:".$color."';height:50px;";
       if($modality->color==$color){echo " selected";}
       echo ">".$color."</option>\n";
      }
     ?>
    </select>
   </div><!-- /input-group -->
  </div><!-- /col -->
 </div><!-- /form-group -->
 <!-- temperature -->
 <div class="form-group">
  <label class="col-xs-12 col-sm-2 control-label">Temperature</label>
  <div class="col-xs-12 col-sm-10">
   <select name="temperature" class="form-control">
    <option value="">Select a temperature..</option>
    <?php
     for($degrees=10;$degrees<=30;$degrees+=0.5){
      echo "<option value='".number_format($degrees,1)."'";
      if($modality->temperature==$degrees){echo " selected";}
      echo ">".number_format($degrees,1)."&deg;C</option>\n";
     }
    ?>
   </select>
  </div><!-- /col -->
 </div><!-- /form-group -->
 <!-- controls -->
 <div class="form-group">
  <div class="col-xs-12 col-sm-offset-2 col-sm-10">
   <a href="index.php?view=heating_modalities_list" class="btn btn-default">Back</a>
   <button type="submit" class="btn btn-primary">Save</button>
   <?php
    if($modality->id){
     if(!$modality->used){
      echo "<a href='#' class='btn btn-danger' id='heating_modalities_edit_delete'>Remove</a>";
     }else{
      echo "<a href='#' class='btn btn-danger' disabled>Remove</a>";
     }
    }
   ?>
  </div><!-- /col -->
 </div><!-- /form-group -->
</form>
<!-- remove confirm modal -->
<div class="modal fade" id="planning_clone_modal" tabindex="-1" role="dialog">
 <div class="modal-dialog" role="document">
  <div class="modal-content">
   <div class="modal-body">
    <h4 class="modal-title">Warning</h4>
    <p>Are you sure you want to permanently delete the <?php echo $modality->name; ?> modality?</p>
   </div><!-- /modal-body -->
   <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
    <a href='submit.php?act=heating_modality_delete&idModality=<?php echo $modality->id; ?>' class='btn btn-danger'>Yes</a>
   </div><!-- /modal-footer -->
  </div><!-- /modal-content -->
 </div><!-- /modal-dialog -->
</div><!-- /modal -->
<!-- javascripts -->
<script type="text/javascript">
 $(document).ready(function(){
  // color picker
  $("#colorpicked").css('background-color','<?php echo ($modality->color?$modality->color:"#ffffff"); ?>');
  $("select[name='color']").on('change',function(){
   $("#colorpicked").css('background-color',$(this).val());
  });
  // show remove modal window
  $("#heating_modalities_edit_delete").click(function(){$('#planning_clone_modal').modal('show');});
  //
 });
</script>
<?php
 // debug
 if($debug){api_dump($modality,"modality");}
 // include template footer
 include("footer.inc.php");
?>