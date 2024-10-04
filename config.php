<?php
$username = "rrcgdce";
$password = "tk8JWg6rXL3dhmAHMR";
$hostname = "rrcgdce2023.cm0bksyl0mvc.ap-south-1.rds.amazonaws.com"; 
$db = "rrc_scr_2024"; 
$host = "3390"; 

//connection to the database
$dbhandle = @mysqli_connect($hostname, $username, $password,$db,$host) 
or die("Unable to connect to MySQL");

?>