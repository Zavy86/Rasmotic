<?php
 // include template header
 include("header.inc.php");
?>

<form action="submit.php?act=settings_save" method="POST" class="form-horizontal">

  <div class="form-group">
   <label class="col-sm-3 control-label">Manual timeout</label>
   <div class="col-sm-9">
    <select name="manual_timeout" class="form-control">
     <option value="21600"<?php if($settings->manual_timeout==21600){echo " selected";} ?>>6 hours</option>
     <option value="10800"<?php if($settings->manual_timeout==10800){echo " selected";} ?>>3 hours</option>
     <option value="7200"<?php if($settings->manual_timeout==7200){echo " selected";} ?>>2 hours</option>
     <option value="3600"<?php if($settings->manual_timeout==3600){echo " selected";} ?>>1 hour</option>
   </select>
   </div><!-- /col -->
  </div><!-- /form-group -->

  <div class="form-group">
   <label class="col-sm-3 control-label">Gallery path</label>
   <div class="col-sm-9">
    <input type="text" name="gallery_path" class="form-control" placeholder="Gallery path placeholder" value="<?php echo $settings->gallery_path; ?>">
   </div><!-- /col -->
  </div><!-- /form-group -->

  <div class="form-group">
   <div class="col-sm-offset-3 col-sm-9">
    <button type="submit" class="btn btn-primary">Submit</button>
    <button type="reset" class="btn btn-default">Reset</button>
   </div><!-- /col -->
  </div><!-- /form-group -->

</form>

<?php
 // include template footer
 include("footer.inc.php");
?>