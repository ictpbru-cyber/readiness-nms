<?php
	session_start();
	if($_SESSION['UserID'] == "")
	{
		echo "Please Login!";
		exit();
	}

	if($_SESSION['Status'] != "ADMIN")
	{
		echo "This page for Admin only!";
		exit();
	}	
	
	mysql_connect("localhost","nms","permissions");
	mysql_select_db("readiness");
	$strSQL = "SELECT * FROM member WHERE UserID = '".$_SESSION['UserID']."' ";
	$objQuery = mysql_query($strSQL);
	$objResult = mysql_fetch_array($objQuery);
?>

<?php
	
		$host = $_POST['host'];
		$port = $_POST['port'];
		$cur_date = date("d/m/Y H:i:s");
		if (($host!="") and ($port!=""))
		{
	
			$timeout = 2;
			$tbegin = microtime(true); 
			$fp = fsockopen($host, $port, $errno, $errstr, $timeout); 
			$responding = 1;
			if (!$fp) { $responding = 0; } 
			$tend = microtime(true);
			fclose($fp);
						
			$mstime = ($tend - $tbegin) * 1000;
			$mstime = round($mstime, 2);
			
			$Status = "";
			$ICMP = 0;
			
			if($responding)
			{
				$Status = "respond";
			}
			else
			{
				$Status = "not respond";
			}	
	
			$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
			$objDB = mysql_select_db("readiness");

			$strSQL = "INSERT INTO sysTCPUDP ";
			$strSQL .="(Server,Port,Responded,ICMP,Status) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$host."','.$port.','.$mstime.','.$ICMP.','".$Status."') ";
			$objQuery = mysql_query($strSQL);						

		mysql_close($objConnect);

		}//end if

	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="0; url=http://readiness.pbru.ac.th/project01/get-tcp-udp.html">
<title>Readiness Project</title>
</head>

<body>
</body>
</html>