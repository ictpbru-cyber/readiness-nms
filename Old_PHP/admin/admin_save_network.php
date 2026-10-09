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
    $strSQL = "UPDATE sysDevice SET ";
    $strSQL .="community = '".$_POST["txtCommunity"]."' ";
    $strSQL .="WHERE ipaddress = '".$_GET["ServerID"]."' ";
    $objQuery = mysql_query($strSQL);
	
    if($objQuery)
    {
        echo "Save Done.";
    }
    else
    {
        echo "Error Save [".$strSQL."]";
    }

    mysql_close($objConnect);

    ?>
	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="0; url=http://readiness.pbru.ac.th/admin/admin_network.php">
<title>Readiness Project</title>
</head>

<body>
</body>
</html>