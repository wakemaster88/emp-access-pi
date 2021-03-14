<?php
	
	//Database connection - online
	$servername_on = "h2676003.stratoserver.net";
	$username_on = "dbo00101156";
	$password_on = "PHfR6axnAjydqZQu";
	$database_on = "db00101156";

	// Create connection - online
	$db_on = new mysqli($servername_on, $username_on, $password_on, $database_on);

	//Check connection - online
	if($db->connect_error) 
	{
		$database_info2 = die("Connection failed: " . $db->connect_error);
	}else
	{ 
		$database_info2 = "CLOUD MYSQL DATABASE";
	}
?>
