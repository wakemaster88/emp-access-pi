<?php
	
	include('dbconnect.php');
	
	$timestamp = time();
	$timestamp = $timestamp - 86400;
	
	$delete = "DELETE FROM acc_scans WHERE sca_scan_time  < ".$timestamp."";
	$delete = mysqli_query($db,$delete);
	
	$delete = "DELETE FROM acc_tickets WHERE tic_end  < ".$timestamp."";
	$delete = mysqli_query($db,$delete);
	
	
?>