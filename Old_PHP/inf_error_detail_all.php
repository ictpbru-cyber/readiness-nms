<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
		<?php
		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");

		$strSQL = "SELECT ipaddress,infid,error,timestamp FROM sysInterfaceerror WHERE (ipaddress='".$ipaddress."') and (infid='".$infid."');";
		
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width='150'> ip address </td>";
		echo "<td width='150'> infid </td>";
		echo "<td width='100'> Data Error </td>";
		echo "<td witdh='100'> Date-Time </td>";
		echo "</tr>";
		
		while($objResult = mysql_fetch_array($objQuery)){
        	echo "<tr>";
			echo "<td>".$objResult["ipaddress"]."</td>";
			echo "<td>".$objResult["infid"]."</td>";  
			echo "<td>".$objResult["error"]."</td>";  
			echo "<td>".$objResult["timestamp"]."</td>";  
        	echo "</tr>";
        }
        echo "</table>";
		echo "<br>";
		?>
</body>
</html>