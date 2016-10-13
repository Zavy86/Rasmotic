<?php
 // include template header
 include("header.inc.php");

 // definitions
 $strips=array();
 $percentage_total=0;
 $v_last_hour_end="00:00:00";
 $v_strip_removable_counter=0;


 // acquire variables
 $r_day=$_REQUEST['day'];

 // get objects
 $planning=api_heating_planning($r_day);

 // cycle all strips and build strips array
 foreach($planning as $strip){

  if($strip->modality_fk<>NULL){
   $v_strip_removable_counter++;
   $v_last_hour_end=$strip->hour_end;
  }

  $seconds_start=strtotime($strip->hour_start);
  $seconds_end=strtotime($strip->hour_end);
  $strip->time=($seconds_end-$seconds_start);
  $strip->percentage=round($strip->time*100/86400);
  $percentage_total=$percentage_total+$strip->percentage;
  $strips[]=$strip;
 }
 // correct percetage to 100 if round fails
 if($percentage_total<>100){end($strips)->percentage=end($strips)->percentage+100-$percentage_total;}
 // open progress-bar
 echo "<div class='row'>\n";
 echo "<div class='col-xs-12 col-sm-2 control-label'>\n";
 echo date('l',strtotime("Sunday +{$r_day} days"))."\n";
 echo "</div><!-- /col -->\n";
 echo "<div class='col-xs-12 col-sm-10'>";
 // cycle all strips
 foreach($strips as $strip){
  // reset midnight
  if($strip->hour_end=="23:59:59"){$strip->hour_end="24:00:00";}
  // make tooltip and temperature
  $tooltip=substr($strip->hour_start,0,5)."~".substr($strip->hour_end,0,5);
  if(!$strip->hour_start || !$strip->hour_end){$tooltip=NULL;}
  if(!$strip->hour_start || !$strip->hour_end){$tooltip=NULL;}
  // show strip
  echo "<div class='progress-bar progress-bar-striped' style='background-color:".$strip->color.";width:".$strip->percentage."%;' data-toggle='tooltip' data-placement='top' title='".$tooltip."'>&nbsp;</div>\n";
 }
 // close progress-bar
 echo "</div><!-- /col -->\n";
 echo "</div><!-- /row -->\n";
 echo "<br>\n";

?>




<?php if($v_last_hour_end<>"23:59:59"){ ?>

<form action="submit.php?act=heating_planning_save" method="POST" class="form-horizontal">

 <input type="hidden" name="day" value="<?php echo $r_day; ?>">
 <input type="hidden" name="hour_start" value="<?php echo $v_last_hour_end; ?>">

 <div class="form-group">
  <label class="col-xs-12 col-sm-2 control-label">Strip time</label>
  <div class="col-xs-12 col-sm-10">
   <div class="input-group clockpicker">
    <span class="input-group-addon">From <?php echo substr($v_last_hour_end,0,5); ?> to</span>
    <input type="text" name="hour_end" class="form-control" value="23:59" readonly="readonly" style="background-color:#ffffff;">
    <span class="input-group-addon"><span class="glyphicon glyphicon-time"/></span>
   </div><!-- /input-group -->
  </div><!-- /col -->
 </div><!-- /form-group -->

 <div class="form-group">
  <label class="col-xs-12 col-sm-2 control-label">Modality</label>
  <div class="col-xs-12 col-sm-10">
   <select name="modality_fk" class="form-control selectpicker show-menu-arrow">
    <option value="">Select a modality...</option>
<?php
 $modalities=api_heating_modalities();
 foreach($modalities as $modality){
  echo "     <option value='".$modality->id."' data-content=\"<span style='display:inline-block;height:20px;width:20px;background-color:".$modality->color.";' class='progress-bar-striped'>&nbsp;</span> &nbsp; ".$modality->temperature."&deg;C &rarr; ".$modality->name."\">".$modality->temperature."&deg;C &rarr; ".$modality->name."</option>\n";
 }
?>
   </select>
  </div><!-- /col -->
 </div><!-- /form-group -->

 <div class="form-group">
  <div class="col-xs-12 col-sm-offset-2 col-sm-10">
   <a href="index.php?view=heating_plannings_view" class="btn btn-default">Back</a>
   <button type="submit" class="btn btn-primary">Add</button>
<?php if($v_strip_removable_counter){ ?>
   <a href="submit.php?act=heating_planning_delete&day=<?php echo $r_day; ?>" class="btn btn-warning">Remove last</a>
   <a href="submit.php?act=heating_planning_reset&day=<?php echo $r_day; ?>" class="btn btn-danger">Reset</a>
<?php } ?>
  </div><!-- /col -->
 </div><!-- /form-group -->

</form>

<?php }else{ ?>

<div class='row'>
 <div class='col-xs-12 col-sm-offset-2 col-sm-10'>
  <a href="index.php?view=heating_plannings_view" class="btn btn-default">Back</a>
<?php if($v_strip_removable_counter){ ?>
  <a href="submit.php?act=heating_planning_delete&day=<?php echo $r_day; ?>" class="btn btn-warning">Remove last</a>
  <a href="submit.php?act=heating_planning_reset&day=<?php echo $r_day; ?>" class="btn btn-danger">Reset</a>
<?php } ?>
 </div><!-- /col -->
</div><!-- /row -->

<?php } ?>
<script type="text/javascript">
 // clock picker
 $('.clockpicker').clockpicker({
  default:"00:00",
  placement:'bottom',
  align:'right',
  autoclose:true
 });
 // select picker
 $('.selectpicker').selectpicker();
</script>
<?php
 // debug
 if($debug){api_dump($planning);}
 // include template footer
 include("footer.inc.php");
?>