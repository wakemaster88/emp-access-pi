<?php
	
	include('dbconnect.php');
	$scan= '12343245';
				
	$ab_relais = "SELECT * FROM acc_relais WHERE rel_ticket = '".$scan."'";
	$er_relais = mysqli_query($db, $ab_relais);
	$row_relais = mysqli_fetch_object($er_relais);
	$num_relais = mysqli_num_rows($er_relais);
	
	if($num_relais == 0)
	{
		$ab_relais_e = "SELECT * FROM acc_relais WHERE rel_status IS NULL ORDER BY rel_id ASC LIMIT 1";
		$er_relais_e = mysqli_query($db, $ab_relais_e);
		$row_relais_e = mysqli_fetch_object($er_relais_e);

		// Update locker
		$sql3 = "UPDATE acc_relais SET 
		rel_ticket = '".$scan."',
		rel_status = '1' 
		WHERE rel_id = '".$row_relais_e->rel_id."'";
		$update = mysqli_query($db,$sql3);
		
		echo '<script>
		alert("Dein Schließfach ist die Nummer '.$row_relais_e->rel_id.'");
		</script>';
		
		$num_relais = $row_relais->rel_id;
		
	}else
	{
		$num_relais = $row_relais->rel_id;
		
		echo '<script>
		alert("Dein Schließfach Nummer '.$row_relais->rel_id.' wurde geöffnet!");
		</script>';
		
	}
	
	echo "output";
?>