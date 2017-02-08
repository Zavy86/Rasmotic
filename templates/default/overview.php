<?php
 // include template header
 include("header.inc.php");
 // definitions
 $strips=array();
 $percentage_total=0;
 // get small devices page
 $page=$_REQUEST['page'];
 if(!$page){$page=1;}
?>

<div class="row">

 <div class="col-xs-12 col-sm-12">
  <div class="progress" id="chart_planning" onClick="tooltip_toggle();">
  </div><!-- /progress -->
 </div><!-- /col -->

</div><!-- /row -->
<div class="row">

 <div class="col-xs-6 col-sm-3 <?php if($page==2){echo "hidden-xs";} ?>">
  <center>
   Temperature<br><a href="index.php?view=overview&page=2"><img id="chart_temperature" style="margin:0 -5px 0 -5px"></a> <?php /*;width:140px;height:140px;*/ ?>
  </center>
 </div><!-- /col -->

 <div class="col-xs-12 col-sm-6 <?php if($page==1){echo "hidden-xs";} ?>">
  <center>
   Trend<br><a href="index.php?view=overview&page=1"><img id="chart_trend" style="margin:0 -5px 0 -5px;"></a>
  </center>
 </div><!-- /col -->

 <div class="col-xs-6 col-sm-3 <?php if($page==2){echo "hidden-xs";} ?>">
  <center>
   Humidity<br><a href="index.php?view=overview&page=2"><img id="chart_humidity" style="margin:0 -5px 0 -5px;"></a>
  </center>
 </div><!-- /col -->

</div><!-- /row -->
<div class="row">

 <div class="col-xs-6 col-sm-3">
  <center>
   Heating system<br><br>
   <button type="button" id="heating_status" class="btn btn-lg">&nbsp;<span class="glyphicon glyphicon-off" aria-hidden="true"></span>&nbsp;&nbsp;Off&nbsp;&nbsp;</button>
   <br><br>
  </center>
 </div><!-- /col -->

 <div class="col-xs-6 col-sm-3">
  <center>
   Modality<br><br>
   <input type="checkbox" <?php if($settings->heating_modality=="manual"){echo "checked";} ?> id="toggle_manual" data-toggle="toggle" data-onstyle="warning" data-offstyle="success" data-width="105" data-size="small" data-off="Automatic<br>Planning">
   <br><br>
  </center>
 </div><!-- /col -->

 <div class="col-xs-8 col-sm-3">
  <center>
   <span id="temperature_caption">Planner temperature</span><br><br>
   <div class="input-group" style="width:160px">
    <div class="input-group-btn">
     <button id="temperature_decrease" type="button" class="btn btn-lg btn-primary" onclick="decrease();"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>
    </div><!-- /input-group-btn -->
    <input id="temperature_manual" type="text" class="form-control input-lg" value="0" style="text-align:center;" readonly="readonly">
    <div class="input-group-btn">
     <button id="temperature_increase" type="button" class="btn btn-lg btn-primary" onclick="increase();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
    </div><!-- /input-group-btn -->
   </div><!-- /input-group -->
  </center>
 </div><!-- /col -->

 <div class="col-xs-4 col-sm-3">
  <center>
   <span id="absent_caption">Absent</span><br><br>
   <button type="button" id="toggle_absence" class="btn btn-lg" onclick="toggle_absence();"><span class="glyphicon glyphicon-send" aria-hidden="true"></span></button>
  </center>
 </div><!-- /col -->

</div><!-- /row -->

<script type="text/javascript">

 // ajax request container
 var request;
 request=null;
 // editable permission
 var editable;
 <?php if($_SESSION['access']){echo "editable=true;\n";}else{echo "editable=false;\n";} ?>

 // check editable permission
 if(editable!==true){
  // disable buttons
  $('#toggle_manual').bootstrapToggle('disable');
  $('#temperature_manual').prop('disabled',true);
  $('#temperature_increase').prop('disabled',true);
  $('#temperature_decrease').prop('disabled',true);
  // change absence button into access button
  $('#absent_caption').text("Access");
  $('#toggle_absence').attr('onclick',"window.location.href='index.php?view=access';");
  $('#toggle_absence').html("<span class='glyphicon glyphicon-lock' aria-hidden='true'></span>");
 }else{
  // change access button into absence button
  $('#absent_caption').text("Absence");
  $('#toggle_absence').attr('onclick',"toggle_absence();");
  $('#toggle_absence').html("<span class='glyphicon glyphicon-send' aria-hidden='true'></span>");
 }

 // increase manual temperature
 function increase(){
  if(editable!==true){return false;}
  $("#temperature_manual").val(parseInt($("#temperature_manual").val())+1);
  post_data="temperature="+$('#temperature_manual').val();
  submit_data("heating_manual_temperature",post_data);
 }

 // decrease manual temperature
 function decrease(){
  if(editable!==true){return false;}
  $("#temperature_manual").val(parseInt($("#temperature_manual").val())-1);
  post_data="temperature="+$('#temperature_manual').val();
  submit_data("heating_manual_temperature",post_data);
 }

 // absence toggle change
 function toggle_absence(){
  if(editable!==true){return false;}
  submit_data("heating_absence_toggle",null);
 }

 // modality toggle change
 $('#toggle_manual').change(function(){
  if(editable!==true){return false;}
  post_data="manual_toggle="+$(this).prop('checked');
  submit_data("heating_modality_toggle",post_data);
 });

 // submit data
 function submit_data(submit_act,post_data){
  if(editable!==true){return false;}
  // abort any pending request
  if(request){request.abort();}
  // log submit action and post data
  console.log("submit_act:\n"+submit_act);
  console.log("post_data:\n"+post_data);
  // execute ajax post
  request=$.ajax({
   url:"submit.php?act="+submit_act,
   type:"post",
   dataType:"html",
   data:post_data
  });
  // log ajax post success
  request.done(function(response,textStatus,jqXHR){
   console.log("AJAX POST Success");
  });
  // log ajax post error
  request.fail(function(xhr,textStatus,thrownError){
   console.error("AJAX POST Error: "+textStatus,thrownError);
  });
  request=null;
  // get updated data after submit
  get_data();
 }

 // get updated data
 function get_data(){
  if(request!==null){return false;}
  // update charts
  if($(window).width()>480){chart_size=180;}else{chart_size=140;}
  $('#chart_planning').load("<?php echo TEMPLATE; ?>charts/chart_planning.inc.php?"+Math.random());
  $('#chart_temperature').attr('src','<?php echo TEMPLATE; ?>charts/chart_temperature.inc.php?size='+chart_size+'&'+Math.random());
  $('#chart_humidity').attr('src','<?php echo TEMPLATE; ?>charts/chart_humidity.inc.php?size='+chart_size+'&'+Math.random());
  if($(window).width()>480){$('#chart_trend').attr('src','<?php echo TEMPLATE; ?>charts/chart_trend.inc.php?width=360&height=200&'+Math.random());}
  else{$('#chart_trend').attr('src','<?php echo TEMPLATE; ?>charts/chart_trend.inc.php?width=260&height=140&'+Math.random());}
  // execute ajax get
  request=$.ajax({
   url:"json.php",
   dataType:"json",
   data:""
  });
  // elaborate response
  request.success(function(data){
   console.log("Updated data");
   console.log(data);
   // update heating system status
   if(data.settings.heating_status==="on"){
    $('#heating_status').addClass("btn-primary");
    $('#heating_status').html("&nbsp;<span class='glyphicon glyphicon-off' aria-hidden='true'></span>&nbsp;&nbsp;On&nbsp;&nbsp;");
   }else{
    $('#heating_status').removeClass("btn-primary");
    $('#heating_status').html("&nbsp;<span class='glyphicon glyphicon-off' aria-hidden='true'></span>&nbsp;&nbsp;Off&nbsp;&nbsp;");
   }
   // if modality is absent
   if(data.settings.heating_modality==="absent"){
    // set absent button class
    $('#toggle_absence').addClass('btn-danger');
    // change manual toggle label
    $('#toggle_manual').closest('div').find('label').html("Absent<br>Modality");
    $('#temperature_caption').text("Absent temperature");
    if(editable===true){
     $('#toggle_manual').bootstrapToggle('disable');
     $('#temperature_manual').prop('disabled',true);
     $('#temperature_increase').prop('disabled',true);
     $('#temperature_decrease').prop('disabled',true);
    }
    // update planned temperature
    $("#temperature_manual").val(Math.round(data.settings.heating.strip.temperature));
   }else{
    // remove absent button class
    $('#toggle_absence').removeClass('btn-danger');
   }
   // if modality is auto
   if(data.settings.heating_modality==="auto"){
    // set manual toggle off if checked
    if($('#toggle_manual').prop('checked')===true){$('#toggle_manual').bootstrapToggle('off');}
    // change manual toggle label
    $('#toggle_manual').closest('div').find('label').html("Automatic<br>Planning");
    $('#temperature_caption').text("Planning temperature");
    if(editable===true){
     $('#toggle_manual').bootstrapToggle('enable');
     $('#temperature_manual').prop('disabled',true);
     $('#temperature_increase').prop('disabled',true);
     $('#temperature_decrease').prop('disabled',true);
    }
    // update planned temperature
    $("#temperature_manual").val(Math.round(data.settings.heating.strip.temperature));
   }
   // if modality is manual
   if(data.settings.heating_modality==="manual"){
    // set manual toggle on if not checked
    if($('#toggle_manual').prop('checked')===false){$('#toggle_manual').bootstrapToggle('on');}
    // convert time left to hour and update manual toggle
    var time_left=new Date(null);
    time_left.setSeconds(data.settings.manual_time_left);
    var toggle_label="Manual<br><span class='glyphicon glyphicon-time' aria-hidden='true'></span> "+time_left.toISOString().substr(12,4);
    $('#toggle_manual').closest('div').find('label').html(toggle_label);
    $('#temperature_caption').text("Manual temperature");
    if(editable===true){
     $('#toggle_manual').bootstrapToggle('enable');
     $('#temperature_manual').prop('disabled',false);
     $('#temperature_increase').prop('disabled',false);
     $('#temperature_decrease').prop('disabled',false);
    }
    // update manual temperature
    $("#temperature_manual").val(Math.round(data.settings.heating_manual_temperature));
   }
  });
  request=null;
 }

 // get updated data on start
 get_data();

 // get updated data every 30 seconds
 setInterval(function(){
  get_data();
 },30000);

 // toggle planning tooltip
 function tooltip_toggle(){
  $("[data-toggle=tooltip]").tooltip({trigger:'manual'}).tooltip('toggle');
 }

</script>
<?php
 // include template footer
 include("footer.inc.php");
?>