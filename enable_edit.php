<?php 
session_start();
include_once "config.php";
//echo "<pre>";print_r($_GET);die;
$regId = $_GET['id'];
mysqli_query($dbhandle,'UPDATE personal_details SET updated=0 WHERE registration_id = '.$regId.' ');
echo "<script>alert('Edit Enabled !!!');window.location='dashboard.php';</script>";
?>
