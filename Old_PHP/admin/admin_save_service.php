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

    $objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
    $objDB = mysql_select_db("readiness");
    $strSQL1 = "UPDATE sysTCPUDP SET ";
    $strSQL1 .="Port = '".$_POST["txtPort"]."' ";
    $strSQL1 .="WHERE Server = '".$_GET["ServerID"]."' ";
    $objQuery1 = mysql_query($strSQL1);
	
    if($objQuery1)
    {
        echo "Save Done.";
    }
    else
    {
        echo "Error Save [".$strSQL1."]";
    }
	
    $strSQL2 = "UPDATE service_tcp_udp_log SET ";
    $strSQL2 .="Port = '".$_POST["txtPort"]."' ";
    $strSQL2 .="WHERE Server = '".$_GET["ServerID"]."' ";
    $objQuery2 = mysql_query($strSQL2);	

    if($objQuery2)
    {
        echo "Save Done.";
    }
    else
    {
        echo "Error Save [".$strSQL2."]";
    }

    mysql_close($objConnect);

    ?>
	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="0; url=http://readiness.pbru.ac.th/admin/admin_service.php">
<title>Readiness Project</title>
</head>

<body>
</body>
</html>