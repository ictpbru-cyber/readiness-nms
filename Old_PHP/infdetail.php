<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--[if lt IE 7]> <html class="ie6 oldie"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldie"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldie"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="">
<!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title></title>
<link href="boilerplate.css" rel="stylesheet" type="text/css">
<link href="css/fluidGird.css" rel="stylesheet" type="text/css">
<!-- 
To learn more about the conditional comments around the html tags at the top of the file:
paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/

Do the following if you're using your customized build of modernizr (http://www.modernizr.com/):
* insert the link to your js here
* remove the link below to the html5shiv
* add the "no-js" class to the html tags at the top
* you can also remove the link to respond.min.js if you included the MQ Polyfill in your modernizr build 
-->
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script src="respond.min.js"></script>


</head>
<body>

<div class="gridContainer clearfix">
  <div id="LayoutDiv1">
	<?php
	echo "<br>";
	echo "| <a href=index.php>Home</a> | <a href=#>Server Monitor</a> | <a href=#>Service Monitor</a> |";
	echo "<br>";
	
	$ipaddress=$_REQUEST['ipaddress_fw'];
	$infid=$_REQUEST['infid_fw'];

	echo "Data request ipaddress : ".$ipaddress."<br>";
	echo "Data request infid: ".$infid."<br>";


	/**
	if($_REQUEST['ip_add'] != "")
	{
		$ipaddress = $_REQUEST['ip_add'];
		echo "Data request : ".$ipaddress."<br>";
				
		$objConnect = mysql_connect("localhost","root","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
				
		$strSQL = "SELECT * FROM sysDetail WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<br>";
		echo "<table border=1>";
		while($objResult = mysql_fetch_array($objQuery)){
            echo "<tr><td width=150>ip address</td>"."<td>".$objResult["ipaddress"]."</td></tr>";
			echo "<tr><td>Description</td>"."<td>".$objResult["sysDescription"]."</td></tr>";
            echo "<tr><td>System Name</td>"."<td>".$objResult["sysName"]."</td></tr>";
            echo "<tr><td>System OS</td>"."<td>".$objResult["sysIOS"]."</td></tr>";
            echo "<tr><td>System Location</td>"."<td>".$objResult["sysLocation"]."</td></tr>";
            echo "<tr><td>System Contact</td>"."<td>".$objResult["sysContact"]."</td></tr>";
			echo "<tr><td>System CPU Util</td>"."<td>".$objResult["sysCPULoad"]."</td></tr>";
			$totalMemory = $objResult["sysMemoryFree"]+$objResult["sysMemoryUse"];
			echo "<tr><td>System Total Memory</td>"."<td>".$totalMemory."</td></tr>";
			echo "<tr><td>System Momory Free</td>"."<td>".$objResult["sysMemoryFree"]."</td></tr>";
			echo "<tr><td>System Memory Use</td>"."<td>".$objResult["sysMemoryUse"]."</td></tr>";  
			echo "<tr><td>System Up Time</td>"."<td>".substr($objResult["sysUpTime"],10)."</td></tr>";
        }

		$strSQL = "SELECT infid,interfacename FROM sysInterface WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		while($objResult = mysql_fetch_array($objQuery)){
        	echo "<tr>";
			//echo "<td>".$i."</td>";
            //echo "<td><a href='status.php?ip_add=$objResult[ipaddress]'>".$objResult["ipaddress"]."</a></td>";
			echo "<td>Interface: ".$objResult["infid"]."</td>";
			echo "<td>".$objResult["interfacename"]."</td>";  
        	echo "</tr>";
        }
        echo "</table>";

		echo "<br>";

		mysql_close($objConnect);
		
	}//end if
	
	**/
	

    ?> 
  </div>
</div>
</body>
</html>
