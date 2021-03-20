<?php
	include('dbconnect.php');
	$timestamp = time();
	
	//get Scan UID - local
    $scan = $_GET['id'];
	
	$ab_pis = "SELECT * FROM acc_pis";
	$er_pis = mysqli_query($db,$ab_pis);
	$num_pis = mysqli_num_rows($er_pis);
			
	if($num_pis == 0)
	{
		$pis = explode("@", $scan);
		$sql = "INSERT INTO acc_pis (pis_location, pis_cloud_id, pis_token)
		VALUES ('".$pis[0]."','".$pis[1]."','".$pis[2]."')";
		$update = mysqli_query($db,$sql);
		
		$command = escapeshellcmd('python3 /home/pi/Desktop/buzzer_valid.py');
		shell_exec($command);
		$command = escapeshellcmd('python3 /home/pi/Desktop/buzzer_valid.py');
		shell_exec($command);
		$command = escapeshellcmd('python3 /home/pi/Desktop/buzzer_valid.py');
		shell_exec($command);
		
	}
	else
	{
		
		if(is_numeric($scan))
		{
			include('scan_rfid.php'); 
    	}else
		{
	    	include('scan_qr.php');
    	}
		
	}

	echo $scan;
			
?>