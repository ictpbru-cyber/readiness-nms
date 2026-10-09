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
<link rel="stylesheet" type="text/css" href="css/menu.css"/>

</head>
<body>

<div class="gridContainer clearfix">
  <div id="LayoutDiv1">

	<ul class="menu">
		<li class="menu-item">
		  <a href="status.php">Home</a>
		</li>
		<li class="menu-item">
		  <a href="network.php">Network</a>
		</li>
		<li class="menu-item">
		  <a href="server.php">Server</a>
		</li>
		<li class="menu-item">
		  <a href="service.php">Service</a>
		</li>
	</ul>

	<?php

	if($_REQUEST['ip_add'] != "")
	{
		$ipaddress = $_REQUEST['ip_add'];
				
		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
				
		$strSQL = "SELECT * FROM sysDetail WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<br>";
		echo "<table border=0>";
		while($objResult = mysql_fetch_array($objQuery)){
            echo "<tr><td width=150>Device address</td>"."<td>".$objResult["ipaddress"]."</td></tr>";
			if ($objResult["sysDescription"]!=""){
				echo "<tr><td>Description</td>"."<td>".$objResult["sysDescription"]."</td></tr>";
			}
			if ($objResult["sysOID"]!=''){
				echo "<tr><td>System OID</td>"."<td>".$objResult["sysOID"]."</td></tr>";
			}
			if ($objResult["sysName"]!=''){
            	echo "<tr><td>System Name</td>"."<td>".$objResult["sysName"]."</td></tr>";
			}
			if ($objResult["sysIOS"]!='') {
            	echo "<tr><td>System OS</td>"."<td>".$objResult["sysIOS"]."</td></tr>";
			}
			if ($objResult["sysLocation"]!=''){
            	echo "<tr><td>System Location</td>"."<td>".$objResult["sysLocation"]."</td></tr>";
			}
			if ($objResult["sysContact"]!=''){
            	echo "<tr><td>System Contact</td>"."<td>".$objResult["sysContact"]."</td></tr>";
			}
			
			if ($objResult["sysCPULoad"]!=''){
				echo "<tr><td>System CPU Util</td>";
				echo "<td><a href='cpm_CPU_detail.php?ipaddress_fw=$ipaddress'>".$objResult["sysCPULoad"]."</a></td></tr>";
			}
			if (($objResult["sysMemoryFree"]!='') and ($objResult["sysMemoryUse"]!=='')){
				$totalMemory = $objResult["sysMemoryFree"]+$objResult["sysMemoryUse"];
				echo "<tr><td>System Total Memory</td>";
				echo "<td><a href='cpm_Memory_detail.php?ipaddress_fw=$ipaddress'>".$totalMemory."</a></td></tr>";			
				echo "<tr><td>System Momory Free</td>";
				echo "<td><a href='cpm_Memory_detail.php?ipaddress_fw=$ipaddress'>".$objResult["sysMemoryFree"]."</a></td></tr>";			
				echo "<tr><td>System Memory Use</td>";
				echo "<td><a href='cpm_Memory_detail.php?ipaddress_fw=$ipaddress'>".$objResult["sysMemoryUse"]."</a></td></tr>";
			}
			if (substr($objResult["sysUpTime"],10)!='') {
				echo "<tr><td>System Up Time</td>"."<td>".substr($objResult["sysUpTime"],10)."</td></tr>";
			}
        }

		echo "</table>";
		echo "<br>";

		$strSQL = "SELECT infid,interfacename,interfacespeed,interfacestatus,interfaceinput,interfaceoutput,interfaceerror FROM sysInterface WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		
		echo "<table border=1>";
		echo "<tr>";
		//echo "<td> Interface </td>";
		echo "<td width='150'> Interface </td>";
		echo "<td width='100'> Speed(Mbps) </td>";
		echo "<td witdh='100'> Status </td>";
		echo "<td width='100'> Data In(Mbps) </td>";
		echo "<td width='100'> Data Out(Mbps) </td>";
		//echo "<td width='100'> Error </td>";		
		echo "</tr>";
		while($objResult = mysql_fetch_array($objQuery)){
        	echo "<tr>";
			
			//echo "<td>".$objResult["infid"]."</td>";
			
			$infid = $objResult["infid"];
			$speed = $objResult["interfacespeed"]/1000000;
			$status = $objResult["interfacestatus"];
			$input = (($objResult["interfaceinput"])/(60*5))/(1024*1024);
			$output = (($objResult["interfaceoutput"])/(60*5))/(1024*1024);
			$error = (($objResult["interfaceerror"])/(60*5))/(1024*1024);	
			echo "<td>".$objResult["interfacename"]."</td>";
			echo "<td>".number_format($speed,0)."</td>";
			
			//echo "<td><a href='infdetail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".$objResult["interfacename"]."</a></td>";
			//echo "<td><a href='inf_speed_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".number_format($speed,0)."</a></td>";
			//echo "<td><a href='inf_status_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".$status."</a></td>";
			
			if ($status == '1'){
				echo "<td><a href='inf_status_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>"."UP"."</a></td>";
			}else if ($status == '2'){
				echo "<td><a href='inf_status_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>"."<font color=#FF0000>Down</font>"."</a></td>";
			}
			
			echo "<td><a href='inf_input_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".number_format($input,2)."</a></td>";
			echo "<td><a href='inf_output_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".number_format($output,2)."</a></td>";
			//echo "<td><a href='inf_error_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".number_format($error,4)."</a></td>";
        	echo "</tr>";
        }
		echo "</tr>";
		echo "</table>";
		echo "<br>";

		mysql_close($objConnect);
		
	}//end if
	
	

    ?> 
  </div>
</div>
</body>
</html>
