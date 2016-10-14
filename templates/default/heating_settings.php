<?php
 // include template header
 include("header.inc.php");
?>

<form action="submit.php?act=heating_settings_save" method="post" class="form-horizontal">

  <div class="form-group">
   <label class="col-xs-12 col-sm-2 control-label">Manual timeout</label>
   <div class="col-xs-12 col-sm-10">
    <select name="heating_manual_timeout" class="form-control">
     <option value="21600"<?php if($settings->heating_manual_timeout==21600){echo " selected";} ?>>6 hours</option>
     <option value="10800"<?php if($settings->heating_manual_timeout==10800){echo " selected";} ?>>3 hours</option>
     <option value="7200"<?php if($settings->heating_manual_timeout==7200){echo " selected";} ?>>2 hours</option>
     <option value="3600"<?php if($settings->heating_manual_timeout==3600){echo " selected";} ?>>1 hour</option>
    </select>
   </div><!-- /col -->
  </div><!-- /form-group -->

  <div class="form-group">
   <label class="col-xs-12 col-sm-2 control-label">Absent temperature</label>
   <div class="col-xs-12 col-sm-10">
    <select name="heating_absent_temperature" class="form-control">
     <?php
      for($degrees=10;$degrees<=20;$degrees++){
       echo "<option value='".$degrees.".0'";
       if($settings->heating_absent_temperature==$degrees){echo " selected";}
       echo ">".$degrees.".0&deg;C</option>\n";
      }
     ?>
    </select>
   </div><!-- /col -->
  </div><!-- /form-group -->

  <div class="form-group">
   <div class="col-xs-12 col-sm-offset-2 col-sm-10">
    <a href="index.php?view=overview" class="btn btn-default">Cancel</a>
    <button type="submit" class="btn btn-primary">Submit</button>
   </div><!-- /col -->
  </div><!-- /form-group -->

</form>

<?php
 // include template footer
 include("footer.inc.php");
?>