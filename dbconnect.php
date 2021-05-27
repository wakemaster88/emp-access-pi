<?php
	
	//Database connection - local
	$servername = "localhost:3306";
	$username = "phpmyadmin";
	$password = "pi";
	$database = "emp_access";
	$firmware = 2;

	//Create connection - local
	$db = new mysqli($servername, $username, $password, $database);

	//Check connection - local
	if($db->connect_error) 
	{
		$database_info = die("Connection failed: " . $db->connect_error);
	}else
	{ 
		$database_info = "LOCAL MYSQL DATABASE";
	}
?>
