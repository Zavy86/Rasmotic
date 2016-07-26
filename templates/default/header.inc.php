<!DOCTYPE html>
<html lang="en">
 <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="<?php echo TEMPLATE; ?>images/favicon.ico">

  <title><?php echo $GLOBALS['config']->title." - ".ucwords(VIEW); ?></title>

  <!-- jQuery -->
  <script src="<?php echo TEMPLATE; ?>js/jquery-1.12.0.min.js"></script>

  <!-- Bootstrap -->
  <link href="<?php echo TEMPLATE; ?>css/bootstrap-3.3.5.min.css" rel="stylesheet">
  <script src="<?php echo TEMPLATE; ?>js/bootstrap-3.3.5.min.js"></script>

  <!-- Bootstrap Toggle -->
  <script src="<?php echo TEMPLATE; ?>js/bootstrap-toggle-2.2.0.min.js"></script>
  <link href="<?php echo TEMPLATE; ?>css/bootstrap-toggle-2.2.0.min.css" rel="stylesheet">

  <!-- Bootstrap Clock Picker -->
  <script src="<?php echo TEMPLATE; ?>js/bootstrap-clockpicker-0.0.7.min.js"></script>
  <link href="<?php echo TEMPLATE; ?>css/bootstrap-clockpicker-0.0.7.min.css" rel="stylesheet">

  <!-- Bootstrap Select -->
  <script src="<?php echo TEMPLATE; ?>js/bootstrap-select-1.10.0.min.js"></script>
  <link href="<?php echo TEMPLATE; ?>css/bootstrap-select-1.10.0.min.css" rel="stylesheet">

  <!-- CSS -->
  <link href="<?php echo TEMPLATE; ?>css/style.css" rel="stylesheet">

  <!-- FIX per browser fuori standard -->
  <script src="<?php echo TEMPLATE; ?>js/ie10-viewport-bug-workaround.js"></script>
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
 </head>

 <body>

  <!-- navbar -->
  <nav class="navbar navbar-default navbar-static-top">
   <div class="container">
    <div class="navbar-header">

     <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
     </button>

     <a class="navbar-brand" id="nav_brand_logo" href="#"><img alt="Brand" src="<?php echo TEMPLATE; ?>images/brand.png" width="20"></a>
     <a class="navbar-brand" id="nav_brand_title" href="index.php"><?php echo $GLOBALS['config']->title; ?></a>

    </div><!--/navbar-header -->

    <div id="navbar" class="navbar-collapse collapse">
     <ul class="nav navbar-nav">
      <li<?php if(VIEW=="overview"){echo " class='active'";} ?>><a href="index.php?view=overview">Overview</a></li>
      <li<?php if(VIEW=="planner"){echo " class='active'";} ?>><a href="index.php?view=planner">Planner</a></li>
      <li<?php if(VIEW=="gallery"){echo " class='active'";} ?>><a href="index.php?view=gallery">Gallery</a></li>
      <li<?php if(VIEW=="settings"){echo " class='active'";} ?>><a href="index.php?view=settings">Settings</a></li>
      <?php if($_SESSION['access']){ ?><li><a href="submit.php?act=session_logout">Logout</a></li><?php } ?>
      <li class="dropdown">
       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Altro <span class="caret"></span></a>
       <ul class="dropdown-menu">
        <li><a href="#">Action</a></li>
        <li><a href="#">Another action</a></li>
        <li><a href="#">Something else here</a></li>
        <li role="separator" class="divider"></li>
        <li class="dropdown-header">Nav header</li>
        <li><a href="#">Separated link</a></li>
        <li><a href="#">One more separated link</a></li>
       </ul>
      </li>
     </ul>
     <ul class="nav navbar-nav navbar-right">
      <li><a href="#" id="nav_datetime"><?php include("now.inc.php"); ?></a></li>
     </ul>
    </div><!--/navbar-collapse -->

   </div><!--/container -->
  </nav>

  <!-- container -->
  <div class="container">