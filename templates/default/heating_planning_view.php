<?php
 // include template header
 include("header.inc.php");
?>
<!-- planning clone modal -->
<div class="modal fade" id="planning_clone_modal" tabindex="-1" role="dialog">
 <div class="modal-dialog" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="planning_clone_modalLabel">Clone <?php echo date('l',strtotime("Sunday +{$_GET['day']} days"))?></h4>
   </div>
   <div class="modal-body">
    <form action="submit.php?act=heating_planning_clone" method="post" class="form-horizontal" id="heating_planning_clone">
     <input type="hidden" name="day" value="<?php echo $_GET['day']; ?>">
     <div class="form-group">
      <label class="col-xs-12 col-sm-2 control-label">Overwrite this days:</label>
      <div class="col-xs-12 col-sm-10">
       <?php
        for($day=1;$day<=7;$day++){
         if($day==$_GET['day']){continue;}
         echo "       <div class='checkbox checkbox-primary'>";
         echo "<input type='checkbox' name='days[]' id='checkbox_".$day."' value='".$day."'>";
         echo "<label for='checkbox_".$day."'>".date('l',strtotime("Sunday +{$day} days"))."</label></div>\n";
        }
       ?>
      </div><!-- /col -->
     </div><!-- /form-group -->
    </form>
   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-primary" id="heating_planning_clone_submit">Clone</button>
   </div>
  </div>
 </div>
</div>
<!-- modalities -->
<div class='row'>
 <div class='col-xs-12 col-sm-2'>
  Modalities
 </div><!-- /col -->
 <div class='col-xs-12 col-sm-10'>
<?php
 $modalities=api_heating_modalities();
 foreach($modalities as $modality){
  echo "  <span style='display:inline-block;height:20px;width:20px;background-color:".$modality->color.";' class='progress-bar-striped'>&nbsp;</span> ".$modality->temperature."&deg;C\n";
 }
?>
 </div><!-- /col -->
</div><!-- /row -->
<br>
<!-- plannings -->
<?php
 // cycle all plannings
 foreach($settings->heating->plannings as $day=>$planning){
  // definitions
  $strips=array();
  $percentage_total=0;
  // cycle all strips and build strips array
  foreach($planning as $strip){
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
  echo "<div class='col-xs-9 col-sm-2'>\n";
  echo date('l',strtotime("Sunday +{$day} days"))."\n";
  echo "</div><!-- /col -->\n";
  echo "<div class='col-xs-3 visible-xs text-right'>\n";
  echo "<small><a href='index.php?view=heating_planning_view&act=clone&day=".$day."'><span class='glyphicon glyphicon-duplicate' aria-hidden='true' title='Clone'></span></a></small>\n&nbsp;&nbsp;&nbsp;\n";
  echo "<small><a href='index.php?view=heating_planning_edit&day=".$day."'><span class='glyphicon glyphicon-edit' aria-hidden='true' title='Edit'></span></a></small>\n";
  echo "</div><!-- /col -->\n";
  echo "<div class='col-xs-12 col-sm-9'>\n";
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
  echo "<div class='hidden-xs col-sm-1 text-right'>\n";
  echo "<small><a href='index.php?view=heating_planning_view&act=clone&day=".$day."'><span class='glyphicon glyphicon-duplicate' aria-hidden='true' title='Clone'></span></a></small>\n&nbsp;&nbsp;&nbsp;\n";
  echo "<small><a href='index.php?view=heating_planning_edit&day=".$day."'><span class='glyphicon glyphicon-edit' aria-hidden='true' title='Edit'></span></a></small>\n";
  echo "</div><!-- /col -->\n";
  echo "</div><!-- /row -->\n<div class='br'></div>\n";
 }
 // debug
 if($debug){api_dump($settings);}
?>
<script type="text/javascript">
 $(document).ready(function(){
  // show clone modal window
  <?php if($_GET['act']=="clone" && $_GET['day']>0){echo "$('#planning_clone_modal').modal('show');\n";} ?>
  $("#heating_planning_clone_submit").click(function(){
   $("#heating_planning_clone").submit();
  });
 });
</script>
<?php
 // include template footer
 include("footer.inc.php");
?>