<?php
 // include template header
 include("header.inc.php");
 // definitions
 $strips=array();
 $percentage_total=0;
?>

<div class="row">

 <div class="col-xs-12 col-sm-12">
  Current planning<br><br>
  <div class="progress" onClick="tooltip_toggle();">

<?php
 // cycle all strips and build strips array
 foreach($settings->heating->planning as $strip){
  $seconds_start=strtotime($strip->hour_start);
  $seconds_end=strtotime($strip->hour_end);
  // check if current strip
  /*if($strip->id==$settings->heating->strip->id){
   $seconds_now=strtotime(date("H:i:s"));
   // build pre strip
   $strip_pre=clone $strip;
   $strip_pre->hour_end=date("H:i:s");
   $strip_pre->time=($seconds_now-$seconds_start);
   $strip_pre->percentage=round($strip_pre->time*100/86400);
   if($strip_pre->percentage>0){$strip_pre->percentage=$strip_pre->percentage-0.25;}
   $strip_pre->hour_start=NULL;
   $strips[]=$strip_pre;
   // build current strip
   $strip_now=clone $strip;
   $strip_now->temperature=NULL;
   $strip_now->percentage=0.25;
   $strips[]=$strip_now;
   // build post strip
   $strip_post=clone $strip;
   $strip_post->hour_start=date("H:i:s");
   $strip_post->time=($seconds_end-$seconds_now);
   $strip_post->percentage=round($strip_post->time*100/86400);
   if($strip_pre->percentage<0.5){$strip_post->percentage=$strip_post->percentage-0.25;}
   $strip_post->hour_end=NULL;
   $strips[]=$strip_post;
   $percentage_total=$percentage_total+$strip_pre->percentage+$strip_now->percentage+$strip_post->percentage;
  }else{*/
   $strip->time=($seconds_end-$seconds_start);
   $strip->percentage=round($strip->time*100/86400);
   $percentage_total=$percentage_total+$strip->percentage;
   $strips[]=$strip;
  //}
 }
 // correct percetage to 100 if round fails
 if($percentage_total<100){end($strips)->percentage=end($strips)->percentage+100-$percentage_total;}
 // cycle all strips
 foreach($strips as $strip){
 // set temperature class
  if(!$strip->temperature){$color="#666666";}
  elseif($strip->temperature<=16){$color="#5BC0DE";}
  elseif($strip->temperature>16&&$strip->temperature<=18){$color="#337AB7";}
  elseif($strip->temperature>18&&$strip->temperature<=20){$color="#5CB85C";}
  elseif($strip->temperature>20&&$strip->temperature<=22){$color="#F0AD4E";}
  elseif($strip->temperature>22){$color="#D9534F";}
  // make tooltip and temperature
  $tooltip=substr($strip->hour_start,0,5)."~".substr($strip->hour_end,0,5);
  if(!$strip->hour_start || !$strip->hour_end){$tooltip=NULL;}
  // show strip
  echo "<div class='progress-bar progress-bar-striped' style='background-color:".$color.";width:".$strip->percentage."%;' data-toggle='tooltip' data-placement='top' title='".$tooltip."'>".$strip->temperature."°</div>\n";
 }
?>

  </div><!-- /progress -->
 </div><!-- /col -->

 <div class="col-xs-12 col-sm-6">
  <center>
   Trend in the last 24 hours<br>
   <img id="chart_trend" style="margin:0 -5px 0 -5px;width:360px;height:145px;">
  </center>
 </div><!-- /col -->

 <div class="col-xs-6 col-sm-3">
  <center>
   Temperature<br><img id="chart_temperature" style="margin:0 -5px 0 -5px;width:145px;height:145px;">
  </center>
 </div><!-- /col -->

 <div class="col-xs-6 col-sm-3">
  <center>
   Humidity<br><img id="chart_humidity" style="margin:0 -5px 0 -5px;width:145px;height:145px;">
  </center>
 </div><!-- /col -->

 <div class="col-xs-6 col-sm-3">
  <center>
   Modality<br><br>
   <input type="checkbox" <?php if($settings->modality=="manual"){echo "checked";} ?> id="toggle_manual" data-toggle="toggle" data-onstyle="warning" data-offstyle="success" data-width="105" data-size="small" data-off="Automatic<br>Planning">
  </center>
 </div><!-- /col -->

 <div class="col-xs-6 col-sm-3">
  <center>
   <span id="temperature_caption">Planner temperature</span><br><br>
   <div class="input-group" style="width:160px">
    <div class="input-group-btn">
     <button id="temperature_decrease" type="button" class="btn btn-lg btn-primary" onclick="decrease();"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>
    </div><!-- /input-group-btn -->
    <input id="temperature_manual" type="text" class="form-control input-lg" value="0" style="text-align:center;">
    <div class="input-group-btn">
     <button id="temperature_increase" type="button" class="btn btn-lg btn-primary" onclick="increase();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
    </div><!-- /input-group-btn -->
   </div><!-- /input-group -->
  </center>
 </div><!-- /col -->

 <div class="col-xs-6 col-sm-3">
  <center>
   Colonna<br><br>Qui giace una<br>triste colonna vuota
  </center>
 </div><!-- /col -->

 <div class="col-xs-6 col-sm-3">
  <center>
   Heating system<br><br>
   <button type="button" id="heating_system_status" class="btn btn-lg"><span class="glyphicon glyphicon-off" aria-hidden="true"></span>&nbsp;&nbsp;Off</button>
  </center>
 </div><!-- /col -->

</div><!-- /row -->


<script type="text/javascript">

 // ajax request container
 var request;

 // increase manual temperature
 function increase(){
  $("#temperature_manual").val(parseInt($("#temperature_manual").val())+1);
  post_data="temperature="+$('#temperature_manual').val();
  submit_data("manual_temperature",post_data);
 }

 // decrease manual temperature
 function decrease(){
  $("#temperature_manual").val(parseInt($("#temperature_manual").val())-1);
  post_data="temperature="+$('#temperature_manual').val();
  submit_data("manual_temperature",post_data);
 }

 // modality toggle change
 $('#toggle_manual').change(function(){
  post_data="manual_toggle="+$(this).prop('checked');
  submit_data("modality_toggle",post_data);
 });

 // submit data
 function submit_data(submit_act,post_data){
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
  // get updated data after submit
  get_data();
 }

 // get updated data
 function get_data(){
  // update charts
  $('#chart_trend').attr('src','<?php echo TEMPLATE; ?>charts/chart_trend.inc.php?'+Math.random());
  $('#chart_temperature').attr('src','<?php echo TEMPLATE; ?>charts/chart_temperature.inc.php?'+Math.random());
  $('#chart_humidity').attr('src','<?php echo TEMPLATE; ?>charts/chart_humidity.inc.php?'+Math.random());
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
   if(data.settings.heating_system_status==="on"){
    $('#heating_system_status').addClass("btn-primary");
    $('#heating_system_status').html("<span class='glyphicon glyphicon-off' aria-hidden='true'></span>&nbsp;&nbsp;On");
   }else{
    $('#heating_system_status').removeClass("btn-primary");
    $('#heating_system_status').html("<span class='glyphicon glyphicon-off' aria-hidden='true'></span>&nbsp;&nbsp;Off");
   }
   // if modality is auto
   if(data.settings.modality==="auto"){
    // set manual toggle off if checked
    if($('#toggle_manual').prop('checked')===true){$('#toggle_manual').bootstrapToggle('off');}
    // change manual toggle label
    $('#toggle_manual').closest('div').find('label').html("Automatic<br>Planning");
    $('#temperature_manual').prop('disabled',true);
    $('#temperature_increase').prop('disabled',true);
    $('#temperature_decrease').prop('disabled',true);
    $('#temperature_caption').text("Planning temperature");
    // update planned temperature
    $("#temperature_manual").val(<?php echo $settings->heating->strip->temperature; ?>);
   }

   // if modality is manual
   if(data.settings.modality==="manual"){
    // set manual toggle on if not checked
    if($('#toggle_manual').prop('checked')===false){$('#toggle_manual').bootstrapToggle('on');}
    // convert time left to hour and update manual toggle
    var time_left=new Date(null);
    time_left.setSeconds(data.settings.manual_time_left);
    var toggle_label="Manual<br><span class='glyphicon glyphicon-time' aria-hidden='true'></span> "+time_left.toISOString().substr(12,4);
    $('#toggle_manual').closest('div').find('label').html(toggle_label);
    $('#temperature_manual').prop('disabled',false);
    $('#temperature_increase').prop('disabled',false);
    $('#temperature_decrease').prop('disabled',false);
    $('#temperature_caption').text("Manual temperature");
    // update manual temperature
    $("#temperature_manual").val(Math.round(data.settings.manual_temperature));
   }
  });
 }

 // get updated data on start
 get_data();

 // get updated data every minute
 setInterval(function(){
  get_data();
 },5000);

 // toggle planning tooltip
 function tooltip_toggle(){
  $("[data-toggle=tooltip]").tooltip({trigger:'manual'}).tooltip('toggle');
 }

</script>

<?php
 // include template footer
 include("footer.inc.php");
?>