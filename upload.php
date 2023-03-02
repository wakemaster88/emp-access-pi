<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Database exchange</title>
		<meta charset="UTF-8">
        <meta http-equiv="refresh" content="1" />
    </head>
    <body bgcolor="#ffffff">
	
		<?php
			include('dbconnect.php');
			$timestamp = time();

// Connection to local Pi information
			$ab_pis = "SELECT * FROM acc_pis";
			$er_pis = mysqli_query($db,$ab_pis);
			$row_pis = mysqli_fetch_object($er_pis);
			
			if($row_pis !== false) {
				if($row_pis->pis_in != 0) {
					$access = $row_pis->pis_in;
				} else {
					$access = $row_pis->pis_out;
				}
			} else {
				$access = 0;
			}
			
			$ab_since = "SELECT * FROM acc_tickets ORDER BY tic_version DESC LIMIT 1";
			$er_since = mysqli_query($db, $ab_since);
			$row_since = mysqli_fetch_object($er_since);
			
			if($row_since !== false) {
				$since = $row_since->tic_version;
				$since = str_replace(" ", "%20",$since);
			} else {
				$since = '';
			}
			
			// Display connections
			echo $database_info;
			echo " <--> ";
			echo "API to Cloud Database";
			echo "<hr>";



// Get PI information from cloud server
			$json_pi = @file_get_contents('http://'.$row_pis->pis_location.'.emp-access.de/api_pi_get.php?token='.$row_pis->pis_token.'&id='.$row_pis->pis_cloud_id.'');
			if ($json_pi === false) {
				die('Failed to retrieve PI information from cloud server');
			}
			
			$json_pi = json_decode($json_pi, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				die('Error decoding PI information from cloud server');
			}
			
			if($json_pi['pis_version'] > $row_pis->pis_version)
			{
				// Update local pi information
				$status = "UPDATE acc_pis SET 
				pis_name = '".$json_pi['pis_name']."',
				pis_type = '".$json_pi['pis_type']."',
				pis_in = '".$json_pi['pis_in']."',
				pis_active = '".$json_pi['pis_active']."',
				pis_version = '".$json_pi['pis_version']."',
				pis_out = '".$json_pi['pis_out']."',
				pis_task = '".$json_pi['pis_task']."',
				pis_firmware = '".$firmware."'
				WHERE pis_cloud_id = '".$json_pi['pis_id']."'";
				$update = mysqli_query($db,$status);
				
				echo 'Updated PI information!<br>';
			}
				
			//Turnstile cloud opening
			if($json_pi['pis_task'] == 1)
			{
				$command = escapeshellcmd('python3 /var/www/html/python_files/buzzer.py');
				shell_exec($command);
				$command = escapeshellcmd('python3 /var/www/html/python_files/relais.py');
				shell_exec($command);
				
				// Update local pi information
				$status = "UPDATE acc_pis SET 
				pis_task = '0'
				WHERE pis_cloud_id = '".$json_pi['pis_id']."'";
				$update = mysqli_query($db,$status);
			}



// Upload PI information to cloud server
			$url = 'http://' . $row_pis->pis_location . '.emp-access.de/api_post_pis.php';
			$ch = curl_init($url);
			
			$ab_upload = "SELECT * FROM acc_pis";
			$er_upload = mysqli_query($db, $ab_upload);
			$row_upload = mysqli_fetch_object($er_upload);
			$data = array(
				'pis_id' => $row_pis->pis_cloud_id,
				'pis_task' => $row_pis->pis_task,
				'pis_update' => $timestamp,
				'pis_firmware' => $row_pis->pis_firmware,
			);
			
			$scans = json_encode(array("pis" => $data));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $scans);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			
			if ($result === false) {
				// If there was an error in the request, log it and return an error message.
				error_log("curl_exec failed: " . curl_error($ch));
				echo "Error uploading PI information. Please try again later.";
			} else {
				$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($http_status != 200) {
					// If there was an error in the request, log the error message and the HTTP status code, and return an error message.
					error_log("curl_exec failed with HTTP status code $http_status: $result");
					echo "Error uploading PI information. Please try again later.";
				} else {
					// If there were no errors, output a success message.
					if ($result == 1) {
						echo "Uploaded PI information!<br>";
					}
				}
			}
			
			curl_close($ch);

			
// Get tickets from cloud server
			$json_tickets = @file_get_contents('http://'.$row_pis->pis_location.'.emp-access.de/api_tickets_get.php?token='.$row_pis->pis_token.'&access='.$access.'&since='.$since.'');
			
			if ($json_tickets === false) {
				// Handle the error
				echo "Error: Failed to fetch tickets from the server";
			} else {
				$json_tickets = json_decode($json_tickets, true);
			
				if (is_null($json_tickets)) {
					// Handle the error
					echo "Error: Failed to decode tickets from the server";
				} else {
					print_r($json_tickets);
			
					foreach ($json_tickets as $ticket) {
						$ab_check = "SELECT * FROM acc_tickets WHERE tic_cloud_id = '".$ticket['tic_id']."'";
						$er_check = mysqli_query($db, $ab_check);
						$num_check = mysqli_num_rows($er_check);
			
						if ($num_check == 0) {
							$quer4 = mysqli_query($db, "INSERT INTO acc_tickets 
								(tic_cloud_id, tic_qr, tic_rfid, tic_user, tic_start, tic_end, tic_access, tic_name, tic_valid) 
								VALUES (
								'".$ticket['tic_id']."',
								'".$ticket['tic_qr']."',
								'".$ticket['tic_rfid']."',
								'".$ticket['tic_user']."',
								'".$ticket['tic_start']."',
								'".$ticket['tic_end']."',
								'".$ticket['tic_access']."',
								'".$ticket['tic_name']."',
								'".$ticket['tic_valid']."')" );
			
							if ($quer4 === false) {
								// Handle the error
								echo "Error: Failed to insert new ticket into the database";
							} else {
								echo "Downloaded 1 new Ticket: - QR:".$ticket['tic_qr']." - RFID:".$ticket['tic_rfid']." - User:".$ticket['tic_user']." - Start:".$ticket['tic_start']." - End:".$ticket['tic_end']." - Access:".$ticket['tic_access']." - Valid:".$ticket['tic_valid']."<br> - Version:".$ticket['tic_version']."<br>";
							}
						} else {
							$status = "UPDATE acc_tickets SET 
							tic_qr = '".$ticket['tic_qr']."',
							tic_rfid = '".$ticket['tic_rfid']."',
							tic_user = '".$ticket['tic_user']."',
							tic_start = '".$ticket['tic_start']."',
							tic_end = '".$ticket['tic_end']."',
							tic_access = '".$ticket['tic_access']."',
							tic_name = '".$ticket['tic_name']."',
							tic_valid = '".$ticket['tic_valid']."'
							WHERE tic_cloud_id = '".$ticket['tic_id']."'";
							$update = mysqli_query($db, $status);
			
							if ($update === false) {
								// Handle the error
								echo "Error: Failed to update ticket in the database";
							} else {
								echo "<br>Ticket erfolgreich lokal aktualisiert!";
							}
						}
					}
				}
			}

				

// Upload scans to cloud server
			$url = 'http://' . $row_pis->pis_location . '.emp-access.de/api_post_scans.php';
			$ch = curl_init($url);
			$i = 0;
			
			$ab_upload = "SELECT * FROM acc_scans WHERE sca_upload = 0";
			$er_upload = mysqli_query($db, $ab_upload);
			
			$data = array();
			while ($row_upload = mysqli_fetch_object($er_upload)) {
				$data[] = array(
					'sca_id' => $row_upload->sca_cloud_id,
					'sca_code' => $row_upload->sca_code,
					'sca_location' => $row_upload->sca_location,
					'sca_scan_time' => $row_upload->sca_scan_time,
					'sca_grant' => $row_upload->sca_grant
				);
				$i++;
			}
			
			if (!empty($data)) {
				$scans = json_encode(array("scans" => $data));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $scans);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
				if ($result === false) {
					echo "Error: " . curl_error($ch) . "<br>";
				} elseif ($httpcode != 200) {
					echo "Error: Failed to upload scans. HTTP response code: " . $httpcode . "<br>";
				} elseif ($result == 1) {
					echo "Uploaded " . $i . " Scans<br>";
					$quer3 = mysqli_query($db, "UPDATE acc_scans SET sca_upload = '1' WHERE sca_upload = '0'");
				} else {
					echo "Error: Failed to upload scans. Response: " . $result . "<br>";
				}
			} else {
				echo "No scans to upload.<br>";
			}
			
			curl_close($ch);

			

// Upload ticket changes to cloud server
			$url = 'http://' . $row_pis->pis_location . '.emp-access.de/api_post_tickets.php';
			$ch = curl_init($url);
			$i = 0;
			
			$ab_upload = "SELECT * FROM acc_tickets WHERE tic_valid = '10'";
			$er_upload = mysqli_query($db, $ab_upload);
			
			if (!$er_upload) {
				echo "Error: " . mysqli_error($db);
				exit;
			}
			
			while ($row_upload = mysqli_fetch_object($er_upload)) {
				$data = array(
					'tic_id' => $row_upload->tic_cloud_id,
					'tic_valid' => 0
				);
			
				$i++;
			}
			
			$scans = json_encode(array("tickets" => $data));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $scans);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			
			if (!$result) {
				echo "Error: " . curl_error($ch);
				exit;
			}
			
			curl_close($ch);
			
			echo $result;
			
			if ($result == 1) {
				echo "Uploaded " . $i . " ticket informations<br>";
			
				$status = "UPDATE acc_tickets SET tic_valid = '0' WHERE tic_valid = '10'";
				$update = mysqli_query($db, $status);
			
				if (!$update) {
					echo "Error: " . mysqli_error($db);
					exit;
				}
			}
			
			// Upload ticket changes to cloud server
			$url = 'http://' . $row_pis->pis_location . '.emp-access.de/api_post_tickets.php';
			$ch = curl_init($url);
			$i = 0;
			
			$ab_upload = "SELECT * FROM acc_tickets WHERE tic_valid = '11'";
			$er_upload = mysqli_query($db, $ab_upload);
			
			if (!$er_upload) {
				echo "Error: " . mysqli_error($db);
				exit;
			}
			
			while ($row_upload = mysqli_fetch_object($er_upload)) {
				$data = array(
					'tic_id' => $row_upload->tic_cloud_id,
					'tic_valid' => $row_pis->pis_again
				);
			
				$i++;
			}
			
			$scans = json_encode(array("tickets" => $data));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $scans);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			
			if (!$result) {
				echo "Error: " . curl_error($ch);
				exit;
			}
			
			curl_close($ch);
			
			echo $result;
			
			if ($result == 1) {
				echo "Uploaded " . $i . " ticket informations<br>";
			
				$status = "UPDATE acc_tickets SET tic_valid = '" . $row_pis->pis_again . "' WHERE tic_valid = '11'";
				$update = mysqli_query($db, $status);
			
				if (!$update) {
					echo "Error: " . mysqli_error($db);
					exit;
				}
			}

	
		?>

	</body>
</html>