<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Raspberry Pi Web-Interface</title>
  	</head>
  	<body bgcolor="#21888f">
  	<script>
            var qr = '';
            document.addEventListener("keypress", function(event) {
            if(event.key != 'Enter')
            {
                qr = qr + event.key;
            }
            if(event.key == 'Enter')
            {
                var objXMLHttpRequest = new XMLHttpRequest();
                objXMLHttpRequest.open("GET", "scan.php?id=" + qr, true);
                objXMLHttpRequest.send();
                qr = '';
            }
            });
  	</script>
  		<div align="center">
	  		<h1>
	  		
	  		<?php
		  	
		  	include('dbconnect.php');

                        if(isset($_POST['qr']))
                        {
                            include('scan_qr.php');
                        }
		  	
		  	//Connection to Pi information
			$ab_pis = "SELECT * FROM acc_pis";
			$er_pis = mysqli_query($db,$ab_pis);
			$row_pis = mysqli_fetch_object($er_pis);
			echo $row_pis->pis_name;


	?>  		
  			</h1>
  		</div>
  		<hr>
  		<div align="center">
  			<button onClick="window.location.reload();">Refresh Page</button>
  			<button onClick="window.location.reload();">Drehkreuz öffnen</button>
  			<button onClick="window.location.reload();">Drehkreuz sperren</button>
  		</div>
  		<hr><br>
  		
  		<iframe src="upload.php" width="100%" height="200" style="border:1px solid black;"></iframe>
  		<br><br>
  		<iframe src="activity.php" width="100%" height="300" style="border:1px solid black;"></iframe>
	</body>
</html>

