<?php
	
$account = "twincable-beckum";
$timestamp = time();
$time = date("H:I",$timestamp);

// Admin = 1
// Seilbahn A = 2
//Seilbahn B = 3
//Übungslift = 4
//Browser = 5
//Kasse 1 = 6
//Kasse 2 = 7
//Kasse Büro = 8
//Drehkreuz = 19
$interface_id = 2;

// Gate = gate
// 
$interface = "gate";
$interface_type = "gate";

// RFID Armband User
// RFID Aaron: 1002193100
// RFID Cedric: 3151755834 
// RFID Frank: 2948834538
//$rfid = 1002193100;
$rfid = $scan;
$rfid *= 1;
//$rfid = $_GET['scan'];

$json = file_get_contents('https://'.$account.'.wakesys.com/files_for_admin_and_browser/sql_query/query_operator.php?interface='.$interface.'&interface_id='.$interface_id.'&controller_interface_type='.$interface_type.'&id='.$rfid.'');
$json = json_decode($json, true);
		
		//if($json[data][value][card_valid] == "yes" || isset($json[data][value][next_tickets][0]) || $json[data][value][is_valid] == 1)
		if($json[data][value][card_valid] == "yes" || isset($json[data][value][next_tickets][0]) || ($json[data][value][valid_until] >= $time))
		{
			//echo "valid: yes";
			$ticket_wakesys = 1;
		}else
		{
			//echo "valid: no";
			$ticket_wakesys = 0;
		}
		
		/*echo "<pre>";
		print_r($json);
		echo "</pre>";*/
?>