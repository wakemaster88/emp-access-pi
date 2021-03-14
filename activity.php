<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="refresh" content="1" />
		<title>Scan Activity</title>
	</head>
	<body bgcolor="#ffffff">

		<?php
			
			include('dbconnect.php');
			echo $database_info;
			
			//Table header - local
			echo '<hr>
			<table>
				<tr>
					<th width="50">ID</th>
					<th width="450">Code</th>
					<th width="200">User</th>
					<th width="150">Time</th>
					<th width="100">Valid</th>
				</tr>';

			//load scans -local
			$ab_scans = "SELECT * FROM acc_scans ORDER BY sca_id DESC LIMIT 10";
			$er_scans = mysqli_query($db, $ab_scans);
			while($row_scan = mysqli_fetch_object($er_scans))
			{
				$ab_tickets = "SELECT * FROM acc_tickets WHERE tic_code = '".$row_scan->sca_code."'";
				$er_tickets = mysqli_query($db,$ab_tickets);
				$row_tickets = mysqli_fetch_object($er_tickets);
		
				$ab_user = "SELECT * FROM acc_user WHERE usr_id = ".$row_tickets->tic_user."";
				$er_user = mysqli_query($db,$ab_user);
				$row_user = mysqli_fetch_object($er_user);

				echo "<tr>
					<td>".$row_scan->sca_id."</td>
					<td>".$row_scan->sca_code."</td>
					<td>".$row_tickets->tic_user."</td>
					<td>".$row_scan->sca_time."</td>
					<td>".$row_scan->sca_grant."</td>
				</tr>";

			}
			
			echo "</table>";
			
		?>
		
	</body>
</html>