<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Database exchange</title>
        <meta http-equiv="refresh" content="1" />
    </head>
    <body bgcolor="#ffffff">
	
		<?php
	
			include('dbconnect_online.php');
			include('dbconnect.php');
			echo $database_info;
			echo " <--> ";
			echo $database_info2;
			echo "<hr>";
			$timestamp = time();

			//Connection to Pi information
			$ab_pis = "SELECT * FROM acc_pis";
			$er_pis = mysqli_query($db,$ab_pis);
			$row_pis = mysqli_fetch_object($er_pis);

			//Drehkreuz cloud Öffnung
			if($row_pis->pis_task == 1)
			{
				$command = escapeshellcmd('python3 /home/pi/Desktop/buzzer.py');
				shell_exec($command);
				$command = escapeshellcmd('python3 /home/pi/Desktop/relais.py');
				shell_exec($command);
				$status = "UPDATE pis SET pis_task = 0 WHERE pis_id = ".$row_pis->pis_id."";
				$update = mysqli_query($db_on,$status);
			}
	
			// Upload scans
			$ab_upload = "SELECT * FROM acc_scans WHERE sca_upload = 0";
			$er_upload = mysqli_query($db, $ab_upload);
			while($row_upload = mysqli_fetch_object($er_upload))
			{
		
				$quer2=mysqli_query($db_on,"INSERT INTO scans (sca_code, sca_location, sca_scan_time, sca_grant)
				VALUES ('".$row_upload->sca_code."', '".$row_upload->sca_location."', '".$row_upload->sca_scan_time."', '".$row_upload->sca_grant."')" );

				$quer3=mysqli_query($db,"UPDATE acc_scans SET sca_upload = '1' WHERE sca_id = '".$row_upload->sca_id."'");

				echo "Uploaded 1 Scan<br>";
			}
			
			$ab_check = "SELECT * FROM acc_tickets ORDER BY tic_version DESC LIMIT 1";
			$er_check = mysqli_query($db,$ab_check);
			$row_check = mysqli_fetch_object($er_check);
			
			// Update tickets	
			$ab_tickets = "SELECT * FROM tickets WHERE tic_access = ".$row_pis->pis_id." AND tic_version > '".$row_check->tic_version."'";
			$er_tickets = mysqli_query($db_on,$ab_tickets);
			while($row_tickets = mysqli_fetch_object($er_tickets))
			{
				echo $row_tickets->tic_version;
				$ab_check = "SELECT * FROM acc_tickets WHERE tic_qr = '".$row_tickets->tic_qr."'";
				$er_check = mysqli_query($db,$ab_check);
				$num_check = mysqli_num_rows($er_check);
				$row_check = mysqli_fetch_object($er_check);
				
				if($num_check == 0)
				{
					
					$quer4=mysqli_query($db,"INSERT INTO acc_tickets (tic_qr,tic_rfid,tic_user,tic_start,tic_end,tic_access,tic_time,tic_name,tic_valid,tic_version) VALUES ('".$row_tickets->tic_qr."','".$row_tickets->tic_rfid."','".$row_tickets->tic_user."','".$row_tickets->tic_start."','".$row_tickets->tic_end."','".$row_tickets->tic_access."','".$timestamp."','".$row_tickets->tic_name."','".$row_tickets->tic_valid."','".$row_tickets->tic_version."')" );

					echo "Download 1 Ticket: - QR:".$row_tickets->tic_qr." - RFID:".$row_tickets->tic_rfid." - User:".$row_tickets->tic_user." - Start:".$row_tickets->tic_start." - End:".$row_tickets->tic_end." - Access:".$row_tickets->tic_access." - Valid:".$row_tickets->tic_valid."<br> - Version:".$row_tickets->tic_version."<br>";
				
				}elseif($row_tickets->tic_version > $row_check->tic_version)
				{
					$status = "UPDATE acc_tickets SET 
					tic_qr = '".$row_tickets->tic_qr."',
					tic_rfid = '".$row_tickets->tic_rfid."',
					tic_user = '".$row_tickets->tic_user."',
					tic_start = '".$row_tickets->tic_start."',
					tic_end = '".$row_tickets->tic_end."',
					tic_access = '".$row_tickets->tic_access."',
					tic_time = '".$timestamp."',
					tic_name = '".$row_tickets->tic_name."',
					tic_valid = '".$row_tickets->tic_valid."',
					tic_version = '".$row_tickets->tic_version."'
					WHERE tic_qr = '".$row_tickets->tic_qr."'";
					$update = mysqli_query($db,$status);
					echo "<br>Ticket erfolgreich lokal aktualisiert!";
					
				}	
			}
			
			$ab_tickets_valid = "SELECT * FROM acc_tickets WHERE tic_valid = 10";
			$er_tickets_valid = mysqli_query($db,$ab_tickets_valid);
			while($row_valid = mysqli_fetch_object($er_tickets_valid))
			{
				$status = "UPDATE tickets SET tic_valid = '0' WHERE tic_qr = '".$row_valid->tic_qr."'";
				$update = mysqli_query($db_on,$status);
				echo "<br>Ticket erfolgreich online aktualisiert!";
				
				$status = "UPDATE acc_tickets SET tic_valid = '0' WHERE tic_qr = '".$row_valid->tic_qr."'";
				$update = mysqli_query($db,$status);
			}
			

			// Update user
			$ab_id_usr = "SELECT * FROM acc_user ORDER BY usr_id DESC LIMIT 1";
			$er_id_usr = mysqli_query($db,$ab_id_usr);
			$last_id_usr = mysqli_fetch_object($er_id_usr);
			$last_id_usr = $last_id_usr->usr_id;
			if($last_id_usr == "")
			{
				$last_id_usr = 0;
			}
	
			$ab_user = "SELECT * FROM user WHERE usr_id > ".$last_id_usr."";
			$er_user = mysqli_query($db_on,$ab_user);
			while($row_user = mysqli_fetch_object($er_user))
			{
	
				$quer5=mysqli_query($db,"INSERT INTO acc_user 
				(usr_rfid, usr_name) VALUES 
				('".$row_user->usr_rfid."','".$row_user->usr_name."')" );

				echo "Download 1 User<br>";	
			}

			// Update status
			$status = "UPDATE pis SET pis_update = ".$timestamp." WHERE pis_id = ".$row_pis->pis_id."";
			$update = mysqli_query($db_on,$status);
			
			// Update status oofline
			$status = "UPDATE acc_pis SET pis_update = ".$timestamp." WHERE pis_id = ".$row_pis->pis_id."";
			$update = mysqli_query($db,$status);


			// Update pi offline
			$ab_pis_on = "SELECT * FROM pis WHERE pis_id = ".$row_pis->pis_id."";
			$er_pis_on = mysqli_query($db_on,$ab_pis_on);
			$row_pis_on = mysqli_fetch_object($er_pis_on);
			$status = "UPDATE acc_pis SET 
			pis_task = ".$row_pis_on->pis_task.",
			pis_active = ".$row_pis_on->pis_active."
			WHERE pis_id = ".$row_pis->pis_id."";
			$update = mysqli_query($db,$status);
	
		?>

	</body>
</html>