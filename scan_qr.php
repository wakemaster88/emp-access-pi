<?php			
			//Connection to Pi information - local
			$ab_pis = "SELECT * FROM acc_pis";
			$er_pis = mysqli_query($db,$ab_pis);
			$row_pis = mysqli_fetch_object($er_pis);
			
			//Looking for valid ticket - local
			$ab_tickets = "SELECT * FROM acc_tickets WHERE tic_qr = '".$scan."' AND tic_start <= ".$timestamp." AND tic_end >= ".$timestamp." AND tic_valid >= 1";
			$er_tickets = mysqli_query($db,$ab_tickets);
			$num_tickets = mysqli_num_rows($er_tickets);
			
			if($num_tickets == 0) //No valid ticket found - local
			{
				
				//Add scan into database - local
				$sql = "INSERT INTO acc_scans (sca_code, sca_location, sca_scan_time, sca_grant)
				VALUES ('".$scan."', '".$row_pis->pis_id."', '".$timestamp."','0')";
				$update = mysqli_query($db,$sql);

                $command = escapeshellcmd('python3 /home/pi/Desktop/buzzer_invalid.py');
				shell_exec($command);

			}
			else //Found valid ticket - local
			{

				
				//Open ticket and check valid - local
				$ab_valid = "SELECT * FROM acc_tickets WHERE tic_qr = '".$scan."'";
				$er_valid = mysqli_query($db,$ab_valid);
				$row_valid = mysqli_fetch_object($er_valid);

                //Add scan into database - local
				$sql = "INSERT INTO acc_scans (sca_code, sca_location, sca_scan_time, sca_grant)
				VALUES ('".$scan."', '".$row_pis->pis_id."', '".$timestamp."','".$row_valid->tic_valid."')";
				$update = mysqli_query($db,$sql);
				
				if($row_valid->tic_valid == 1) //Normal valid ticket found - local
				{
					
					// Update valid temporary to 2 for update in cloud - local
					$sql2 = "UPDATE acc_tickets SET tic_valid = '10' WHERE tic_qr = '".$scan."'";
					$update = mysqli_query($db,$sql2);
					
				}

                if($row_valid->tic_valid == 9) //AAA valid ticket found - local
				{
					
					// Update valid temporary to 2 for update in cloud - local
					$sql2 = "UPDATE acc_tickets SET tic_valid = '9' WHERE tic_qr = '".$scan."'";
					$update = mysqli_query($db,$sql2);
					
				}
                                
				
				// Open turnstile and give signal - local
                $command = escapeshellcmd('python3 /home/pi/Desktop/buzzer_valid.py');
				shell_exec($command);
                $command = escapeshellcmd('python3 /home/pi/Desktop/relais.py');
				shell_exec($command);

			}

		?>