<!doctype html>
<!--[if lt IE 7]> <html class="ie6 oldie"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldie"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldie"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="">
<!--<![endif]-->
<head>
<meta http-equiv="refresh" content="80">
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
		  <a href="index.php">Home</a>
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

		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
		//$strSQL = "SELECT sysDetail.ipaddress, sysDetail.sysName, hrmanFac.hrname, sysDetail.sysDescription FROM sysDetail, hrmanFac WHERE sysDetail.sysOID=hrmanFac.hrOID";
		$strSQL = "SELECT sysDetail.sysOID, sysDetail.ipaddress, sysDetail.sysName, hrmanFac.hrname, sysDetail.sysDescription, sysDetail.sysLocation, sysDetail.status, sysDetail.sysUpTime FROM sysDetail, hrmanFac WHERE sysDetail.sysOID=hrmanFac.hrOID ORDER BY sysDetail.sysOID DESC";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<br>";
		echo "<table border=0 width='90%'>";
		//echo "<tr>";
		//echo "<td width=20>NO</td>";
		//echo "<td>ip address</td>";
		//echo "<td width=140>System name</td>";
		//echo "<td width=50>Manufacture</td>";
		//echo "<td width=250>Description</td>";
		//echo "<td>os version</td>";
		//echo "<td width=100>Location</td>";
		//echo "<td>Contact</td>";
		//echo "<td>CPU Load</td>";
		//echo "<td>Total Memory</td>";
		//echo "<td>Memory Used</td>";
		//echo "<td>Memory Free</td>";
		//echo "<td width=100>System up time</td>";
		//echo "<td>Status</td>";
		//echo "</tr>";
		//$i=0;
		while($objResult = mysql_fetch_array($objQuery)){
			$i++;
        	echo "<tr>";
			echo "<td>".$i." </td>";
			$status=$objResult["status"];
			if ($status == '1'){
				//echo "<td width=50> "."<font color=#006600>Up</font>"."</td>";
				echo "<td><a href='status.php?ip_add=$objResult[ipaddress]'>".$objResult["sysName"]."</a></td>";			
				echo "<td>".$objResult["hrname"]." </td>";
				echo "<td>".trim($objResult["sysDescription"], '"')."</td>";
				$sysUpTime = substr($objResult["sysUpTime"],10);
				$first_position = strrpos($sysUpTime,"(");
				$second_position = strrpos($sysUpTime,")");
				echo "<td>".substr($sysUpTime,$second_position+2)."</td>";				
			}else if ($status == '2'){
				//echo "<td width=50> "."<font color=#FF0000>Down</font>"."</td>";
				echo "<td><font color=#FF0000>".$objResult["sysName"]."</font></td>";			
				echo "<td><font color=#FF0000>".$objResult["hrname"]."</font></td>";
				echo "<td><font color=#FF0000>".trim($objResult["sysDescription"], '"')."</font></td>";
				//$sysUpTime = substr($objResult["sysUpTime"],10);
				//$first_position = strrpos($sysUpTime,"(");
				//$second_position = strrpos($sysUpTime,")");
				//echo "<td><font color=#FF0000>".substr($sysUpTime,$second_position+2)."</font></td>";
				echo "<td></td>";
			}	
            //echo "<td><a href='status.php?ip_add=$objResult[ipaddress]'>".$objResult["sysName"]."</a></td>";			
            //echo "<td><a href='status.php?ip_add=$objResult[ipaddress]'>".$objResult["ipaddress"]."</a></td>";
			//echo "<td>".$objResult["sysOID"]."</td>";
			///echo "<td>".$objResult["hrname"]." </td>";
			///echo "<td>".trim($objResult["sysDescription"], '"')."</td>";
            //echo "<td>".$objResult["sysName"]."</td>";
            //echo "<td>".$objResult["sysIOS"]."</td>";
            //echo "<td>".$objResult["sysLocation"]."</td>";
            //echo "<td>".$objResult["sysContact"]."</td>";
			//echo "<td>".$objResult["sysCPULoad"]."</td>";
			//$totalMemory = $objResult["sysMemoryFree"]+$objResult["sysMemoryUse"];
			//echo "<td>".$totalMemory."</td>";
			//echo "<td>".$objResult["sysMemoryFree"]."</td>";
			//echo "<td>".$objResult["sysMemoryUse"]."</td>";
			///$sysUpTime = substr($objResult["sysUpTime"],10);
			///$first_position = strrpos($sysUpTime,"(");
			///$second_position = strrpos($sysUpTime,")");
			//echo "<td>".substr($objResult["sysUpTime"],10)."</td>";
			//echo "<td>".$objResult["status"]."</td>";
			///echo "<td>".substr($sysUpTime,$second_position+2)."</td>";
			//echo "<td>UP</td>";
        	echo "</tr>";
        }
        //echo "</table>";

		$strSQL = "SELECT hrDetail.sysOID, hrDetail.ipaddress, hrDetail.sysName, hrmanFac.hrname, hrDetail.sysDescription, hrDetail.sysContact, hrDetail.sysUpTime, hrDetail.status FROM hrDetail, hrmanFac WHERE hrDetail.sysOID=hrmanFac.hrOID ORDER BY hrDetail.sysOID ASC";		
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		//echo "<br>";
		//echo "<table border=0>";
			//echo "<tr>";
			//echo "<td width=20>NO</td>";
			//echo "<td width=120>System Name</td>";
			//echo "<td width=40>Manufacture</td>";
			//echo "<td width=250>Description</td>";
			//echo "<td>System UpTime</td>";
			//echo "<td>Contact</td>";
			//echo "<td>System Location</td>";
		//echo "</tr>";
		while($objResult = mysql_fetch_array($objQuery)){
			$i++;
        	echo "<tr>";
			echo "<td>".$i." </td>";
			$status=$objResult["status"];
			if ($status == '1'){
				//echo "<td width=50> "."<font color=#006600>Up</font>"."</td>";
				echo "<td><a href='status-server.php?ip_add=$objResult[ipaddress]'>".$objResult["sysName"]."</a></td>";
				echo "<td>".$objResult["hrname"]." </td>";
				echo "<td>".trim($objResult["sysDescription"], '"')."</td>";
				$sysUpTime = substr($objResult["sysUpTime"],11);
				$first_position = strrpos($sysUpTime,"(");
				$second_position = strrpos($sysUpTime,")");
				echo "<td>".substr($sysUpTime,$second_position+2)."</td>";
			}else if ($status == '2'){
				//echo "<td width=50> "."<font color=#FF0000>Down</font>"."</td>";
				echo "<td><font color=#FF0000>".$objResult["sysName"]."</font></td>";
				echo "<td><font color=#FF0000>".$objResult["hrname"]."</font></td>";
				echo "<td><font color=#FF0000>".trim($objResult["sysDescription"], '"')."</font></td>";
				//$sysUpTime = substr($objResult["sysUpTime"],11);
				//$first_position = strrpos($sysUpTime,"(");
				//$second_position = strrpos($sysUpTime,")");
				//echo "<td>".substr($sysUpTime,$second_position+2)."</td>";
				echo "<td></td>";
			}	

			///echo "<td><a href='status-server.php?ip_add=$objResult[ipaddress]'>".$objResult["sysName"]."</a></td>";
			///echo "<td>".$objResult["hrname"]." </td>";
			///echo "<td>".trim($objResult["sysDescription"], '"')."</td>";
            //echo "<td>".$objResult["sysLocation"]."</td>";
            //echo "<td>".$objResult["sysContact"]."</td>";
			///$sysUpTime = substr($objResult["sysUpTime"],11);
			///$first_position = strrpos($sysUpTime,"(");
			///$second_position = strrpos($sysUpTime,")");
			//echo "<td width='10'>".$objResult["status"]."</td>";
			///$status=$objResult["status"];
			///if ($status == '1'){
			///	echo "<td width=50> "."<font color=#006600>Up</font>"."</td>";
			///}else if ($status == '2'){
			///	echo "<td width=50> "."<font color=#FF0000>Down</font>"."</td>";
			///}	
			///echo "<td>".substr($sysUpTime,$second_position+2)."</td>";
			//echo "<td>".substr($objResult["sysUpTime"],11)."</td>";
        	echo "</tr>";
        }
        echo "</table>";
		echo "<br>";
		
		mysql_close($objConnect);
		
    ?> 
    
    <div class="footer">
    <?php
      	include 'getClientEnv.php';
    ?>
    </div>
        
  </div>
</div>
</body>
</html>
