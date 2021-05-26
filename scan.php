<?php
	include('dbconnect.php');
	$timestamp = time();
	
	//get Scan UID - local
    $scan = $_GET['id'];
	
	$ab_pis = "SELECT * FROM acc_pis";
	$er_pis = mysqli_query($db,$ab_pis);
	$num_pis = mysqli_num_rows($er_pis);
	
	//Check if controller is connected and setup is done		
	if($num_pis == 0)
	{
		$pis = explode('"', $scan);
		$sql = "INSERT INTO acc_pis (pis_location, pis_cloud_id, pis_token)
		VALUES ('".$pis[0]."','".$pis[1]."','".$pis[2]."')";
		$update = mysqli_query($db,$sql);
		
		$command = escapeshellcmd('python3 /var/www/html/python_files/buzzer_valid.py');
		shell_exec($command);
		$command = escapeshellcmd('python3 /var/www/html/python_files/buzzer_valid.py');
		shell_exec($command);
		$command = escapeshellcmd('python3 /var/www/html/python_files/buzzer_valid.py');
		shell_exec($command);
		
	}
	else
	{
		$row_pis = mysqli_fetch_object($er_pis);
		if($row_pis->pis_in == 0)
		{
			$valid = 0;
			$invalid = 1;
		}else
		{
			$valid = 1;
			$invalid = 0;
		}
		
		if(is_numeric($scan) && strlen($scan) == 10)
		{
			include('scan_rfid.php'); 
    	}else
		{
	    	include('scan_qr.php');
    	}
		
	}

	echo $scan;
			
?>