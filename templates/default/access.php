<?php
 // include template header
 include("header.inc.php");
?>

<form action="submit.php?act=session_login" method="POST" class="form-horizontal">

 <input type="hidden" name="view" value="<?php echo $_REQUEST['view']; ?>">

 <div class="form-group">
  <label class="col-xs-12 col-sm-3 control-label">Password</label>
  <div class="col-xs-12 col-sm-9">
   <input type="password" name="passcode" class="form-control" placeholder="Insert device password..">
  </div><!-- /col -->
 </div><!-- /form-group -->

 <div class="form-group">
  <div class="col-xs-12 col-sm-offset-3 col-sm-9">
   <button type="submit" class="btn btn-primary">Access</button>
   <a href="index.php?view=overview" class="btn btn-default">Cancel</a>
  </div><!-- /col -->
 </div><!-- /form-group -->

</form>

<?php
 // include template footer
 include("footer.inc.php");
?>