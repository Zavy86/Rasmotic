<?php
 // include template header
 include("header.inc.php");
?>

<form action="submit.php?act=settings_save" method="POST" class="form-horizontal">

 <div class="form-group">
  <label class="col-xs-12 col-sm-2 control-label">Language</label>
  <div class="col-xs-12 col-sm-10">
   <select name="system_language" class="form-control">
    <option value="en"<?php if($settings->system_language=="en"){echo " selected";} ?>>English</option>
    <option value="it"<?php if($settings->system_language=="it"){echo " selected";} ?>>Italiano</option>
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