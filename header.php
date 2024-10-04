<?php 
session_start();
include_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	<noscript>
<meta http-equiv="refresh" content="0; URL=nojavascript.php">
</noscript>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>RRC</title>
		<meta name="generator" content="Bootply" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/font-awesome.min.css" rel="stylesheet">
        
		<script src="js/jquery-3.1.0.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="js/jquery-ui.min.css">

		<link rel="stylesheet" type="text/css" href="css/jquery.datepick.css"> 
		<script type="text/javascript" src="js/jquery.plugin.js"></script> 
        <script type="text/javascript" src="js/jquery.datepick.js"></script>

		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link href="css/styles.css" rel="stylesheet">
	</head>
	<body oncontextmenu="return false;">
<style>
	.navbar-collapse ul li.dropdown:hover ul.dropdown-menu {
		display: block;
	}
	.navbar-collapse ul li ul li a{
		padding: 6px 15px;
	}
	.navbar-collapse ul li ul li{
		border-right: 0;
	}
	.navbar-collapse ul li ul{
		border-right: 0;
		background: -webkit-linear-gradient(#185086, #092f61);
		background: -o-linear-gradient(#185086, #092f61);
		background: -moz-linear-gradient(#185086, #092f61);
		background: linear-gradient(#185086, #092f61);
	}
	
	.wrap{
		float: left;
    margin: 13px;
}
 .wrap h4{
	color: #000 !important;
	font-size: 20px !important;
}
	
.container{
	padding-left: 0;
	padding-right: 0;
}	
	
</style>
<div class="wrapper">
<div id="masthead">  
  <div class="container">
  
  <img src="images/top-stip.png" class="img-responsive">
	<div class="text-center" style=" background: #fff;overflow: hidden;width: 100%;">
	  <img src="images/rrc_logo.jpg" style="width:100px;height:100px;float: left;padding:10px 0px 10px 10px">
	  <div class="wrap">
		  <h4 class="text-left">RAILWAY RECRUITMENT CELL (RRC)</h4>
		  <h4 class="text-left">SOUTH CENTRAL RAILWAY</h4>
		  <h4 class="text-left">SPORTS QUOTA RECRUITMENT - 2024-25</h4>
		</div> 
		<img src="images/logo.png" style="width:100px;height:100px;float: right;padding:10px 0px 10px 10px">
 </div>
  <div class="container">
    <nav class="collapse navbar-collapse" role="navigation">
      <ul class="nav navbar-nav">
			   <li>
					<a href="index.php">Home</a>
				</li>
				<?php 
			if(isset($_SESSION['admin_name'])){
		
		?>
       
         
		<li style="
    left: 77%;
    position: absolute;
"> 
          <a href="admin_logout.php">Logout</a>
        </li> 
		<?php
		  }
		  ?>
      </ul>
    </nav>
  </div>
</div>



