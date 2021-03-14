<?php
	include('dbconnect.php');
	$timestamp = time();

	//get Scan UID - local
    $scan = $_GET['id'];
    
    if(is_numeric($scan))
    {
		include('scan_rfid.php'); 
    }else
    {
	    include('scan_qr.php');
    }

			
?>