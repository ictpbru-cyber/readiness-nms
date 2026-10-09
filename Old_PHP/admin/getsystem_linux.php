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

<!doctype html>
<!--[if lt IE 7]> <html class="ie6 oldie"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldie"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldie"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="">
<!--<![endif]-->
<head>
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
	
		$ipaddress = $_POST['address'];
		$communitystr = $_POST['community'];
		$cur_date = date("d/m/Y H:i:s");
		echo "<br>";
		
		if (($ipaddress!="") and ($communitystr!=""))
		{
		
		echo "<table border=0>";
	
		echo "<tr> <td>IP Address</td> <td>".$ipaddress."</td> </tr>";
		echo "<tr> <td>Community String</td> <td>".$communitystr."</td> </tr>";
		
		echo "<tr> <td>Curent Date-Time</td> <td>".$cur_date."</td> </tr>";
		echo "<tr> <td>&nbsp;</td>  <td>&nbsp;</td> </tr>";
		
		$sysDescription = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.2.1.1.1.0");
		echo "<tr><td width=130> System Description </td>  <td>".substr($sysDescription,7)."</td></tr>";		
		$sysDescription =substr($sysDescription,7);

		$sysOIDObject = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.2.1.1.2.0");
		echo "<tr><td width=130> System OID Object </td>  <td>".substr($sysOIDObject,5)."</td></tr>";		
		$sysOIDObject = substr($sysOIDObject,5);

		$sysUpTime = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.3.0");
		echo "<tr><td> System Up Time </td>  <td>".$sysUpTime."</td></tr>";

		$sysContact = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.4.0");
		echo "<tr><td> System Contact </td>  <td>".substr($sysContact,7)."</td></tr>";
		$sysContact = substr($sysContact,7);

		$sysName = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.2.1.1.5.0");
		echo "<tr><td> System name </td>  <td>".substr($sysName,7)."</td></tr>";
		$sysName = substr($sysName,7);
		
		$sysLocation = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.6.0");
		echo "<tr><td> System Location </td>  <td>".substr($sysLocation,7)."</td></tr>";
		$sysLocation = substr($sysLocation,7);

		echo "<tr> <td>&nbsp;</td>  <td>&nbsp;</td> </tr>";
		echo "</table>";
		echo "<br>";	
				
		
		$infindex = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.1");
		$interface = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.2");
		$ifspeed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.5");
		$ifAdminStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.7");
		$ifOperStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.8");
		$infin = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.10");
		$infout = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.16");
		$ifErrorStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.20");

		for (reset($infindex); $i = key($infindex); next($infindex)) {
			$strSQL = "INSERT INTO sysInterfaceIndex ";
			$strSQL .="(ipaddress,oid,infid) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$i."','".substr($infindex[$i],8)."') ";
			$objQuery = mysql_query($strSQL);		
		}
		
		for (reset($interface); $i = key($interface); next($interface)) {
			$strSQL = "INSERT INTO sysInterface ";
			$strSQL .="(ipaddress,interfaceoid,infid,interfacename) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($interface[$i],7)."') ";
			$objQuery = mysql_query($strSQL);		
		}
		
		for (reset($ifspeed); $i = key($ifspeed); next($ifspeed)) {
			$strSQL = "INSERT INTO sysInterfaceSpeed ";
			$strSQL .="(ipaddress,oid,infid,speed,timestamp) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($ifspeed[$i],8)."','".$cur_date."') ";
			$objQuery = mysql_query($strSQL);		
		}

		for (reset($ifAdminStatus); $i = key($ifAdminStatus); next($ifAdminStatus)) {
			$strSQL = "INSERT INTO sysinfAdminstatus ";
			$strSQL .="(ipaddress,oid,infid,status,timestamp) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($ifAdminStatus[$i],8)."','".$cur_date."') ";			
			$objQuery = mysql_query($strSQL);		
		}
		
		for (reset($ifOperStatus); $i = key($ifOperStatus); next($ifOperStatus)) {
			$strSQL = "INSERT INTO sysinfOperstatus";	
			$strSQL .="(ipaddress,oid,infid,status,timestamp) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($ifOperStatus[$i],8)."','".$cur_date."') ";			
			$objQuery = mysql_query($strSQL);		
		}

		for (reset($infin); $i = key($infin); next($infin)) {
			$strSQL = "INSERT INTO sysInterfacein";	
			$strSQL .="(ipaddress,oid,infid,datain,timestamp) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$i."','".substr($i,23)."','".substr($infin[$i],10)."','".$cur_date."') ";			
			$objQuery = mysql_query($strSQL);		
		}

		for (reset($infout); $i = key($infout); next($infout)) {
			$strSQL = "INSERT INTO sysInterfaceout";	
			$strSQL .="(ipaddress,oid,infid,dataout,timestamp) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$i."','".substr($i,23)."','".substr($infout[$i],10)."','".$cur_date."') ";			
			$objQuery = mysql_query($strSQL);		
		}
				
		for (reset($ifErrorStatus); $i = key($ifErrorStatus); next($ifErrorStatus)) {
			$strSQL = "INSERT INTO sysInterfaceerror";	
			$strSQL .="(ipaddress,oid,infid,error,timestamp) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$i."','".substr($i,23)."','".substr($ifErrorStatus[$i],10)."','".$cur_date."') ";			
			$objQuery = mysql_query($strSQL);		
		}		


		$hrStorageIndex = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.1");
		$hrStorageGroupOID = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.2");
		$hrStorageDescr = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.3");
		$hrStorageAlloctionUnits = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.4");
		$hrStorageSize = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.5");
		$hrStorageUsed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.6");
		$cpmCPUUtilID = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.3.3.1.1");
		$cpmCPUUtilUsed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.3.3.1.2");
		
		echo "CPU Util<br>";
		
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=200><font color=#0000CC>CPM index</font></td>";		
		echo "<td width=200><font color=#0000CC>CPM CPU Utils</font></td>";
		echo "<td width=200><font color=#0000CC>date-time</font></td>";
		echo "</tr>";		
		for (reset($cpmCPUUtilID); $i = key($cpmCPUUtilID); next($cpmCPUUtilID)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,25)."</td>";
			echo "<td>".substr($cpmCPUUtilID[$i],4)."</td>";
			echo "<td>".$cur_date."</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br>";
		
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>CPM CPU index</font></td>";		
		echo "<td width=200><font color=#0000CC>CPM CPU Load</font></td>";
		echo "<td width=200><font color=#0000CC>date-time</font></td>";
		echo "</tr>";		
		for (reset($cpmCPUUtilUsed); $i = key($cpmCPUUtilUsed); next($cpmCPUUtilUsed)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,25)."</td>";
			echo "<td>".substr($cpmCPUUtilUsed[$i],8)."</td>";
			echo "<td>".$cur_date."</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br>";

		echo "Networks Interface<br>";		
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>inf index</font></td>";
		echo "</tr>";
		for (reset($infindex); $i = key($infindex); next($infindex)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($infindex[$i],8)."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<br>";

		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>inf index</font></td>";
		echo "<td width=200><font color=#0000CC>inf name</font></td>";
		echo "</tr>";		
		for (reset($interface); $i = key($interface); next($interface)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,22)."</td>";
			echo "<td>".substr($interface[$i],7)."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<br>";
		
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>inf index</font></td>";
		echo "<td width=200><font color=#0000CC>inf speed</font></td>";
		echo "</tr>";
		for (reset($ifspeed); $i = key($ifspeed); next($ifspeed)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,22)."</td>";
			echo "<td>".substr($ifspeed[$i],8)."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<br>";
		
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>inf index</font></td>";
		echo "<td width=200><font color=#0000CC>inf Admin Status</font></td>";
		echo "</tr>";
		for (reset($ifAdminStatus); $i = key($ifAdminStatus); next($ifAdminStatus)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,22)."</td>";
			echo "<td>".substr($ifAdminStatus[$i],8)."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<br>";

		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>inf index</font></td>";
		echo "<td width=200><font color=#0000CC>inf Operator Status</font></td>";
		echo "</tr>";	
		for (reset($ifOperStatus); $i = key($ifOperStatus); next($ifOperStatus)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,22)."</td>";
			echo "<td>".substr($ifOperStatus[$i],8)."</td>";
			echo "</tr>";		
		}
		echo "</table>";
		echo "<br>";

		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>inf index</font></td>";
		echo "<td width=200><font color=#0000CC>inf data input</font></td>";
		echo "<td width=200><font color=#0000CC>date-time</font></td>";
		echo "</tr>";	
		for (reset($infin); $i = key($infin); next($infin)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,23)."</td>";
			echo "<td>".substr($infin[$i],10)."</td>";
			echo "<td>".$cur_date."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<br>";

		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>inf index</font></td>";
		echo "<td width=200><font color=#0000CC>inf data output</font></td>";
		echo "<td width=200><font color=#0000CC>date-time</font></td>";
		echo "</tr>";			
		for (reset($infout); $i = key($infout); next($infout)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,23)."</td>";
			echo "<td>".substr($infout[$i],10)."</td>";
			echo "<td>".$cur_date."</td>";
			echo "</tr>";		
		}		
		echo "</table>";
		echo "<br>";
		
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>inf index</font></td>";
		echo "<td width=200><font color=#0000CC>inf Error</font></td>";
		echo "<td width=200><font color=#0000CC>date-time</font></td>";
		echo "</tr>";		
		for (reset($ifErrorStatus); $i = key($ifErrorStatus); next($ifErrorStatus)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,23)."</td>";
			echo "<td>".substr($ifErrorStatus[$i],10)."</td>";
			echo "<td>".$cur_date."</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br>";
		
		echo "Disk Allocation<br>";
		//$hrStorageIndex = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.1");
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>hrStorage index</font></td>";		
		echo "<td width=200><font color=#0000CC>hrStorage index value</font></td>";
		echo "</tr>";		
		for (reset($hrStorageIndex); $i = key($hrStorageIndex); next($hrStorageIndex)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($hrStorageIndex[$i],8)."</td>";
			echo "<td>".$hrStorageIndex[$i]."</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br>";

		//$hrStorageGroupOID = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.1");
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>hrStorage index</font></td>";		
		echo "<td width=200><font color=#0000CC>hrStorage Group OID</font></td>";
		echo "</tr>";		
		for (reset($hrStorageGroupOID); $i = key($hrStorageGroupOID); next($hrStorageGroupOID)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,25)."</td>";
			echo "<td>".$hrStorageGroupOID[$i]."</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br>";

		//$hrStorageDescr = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.3");
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>hrStorage index</font></td>";		
		echo "<td width=200><font color=#0000CC>hrStorage Descr</font></td>";
		echo "</tr>";		
		for (reset($hrStorageDescr); $i = key($hrStorageDescr); next($hrStorageDescr)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,25)."</td>";
			echo "<td>".substr($hrStorageDescr[$i],7)."</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br>";

		//$hrStorageAlloctionUnits = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.4");
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>hrStorage index</font></td>";		
		echo "<td width=200><font color=#0000CC>hrStorage Allowcation Units</font></td>";
		echo "</tr>";		
		for (reset($hrStorageAlloctionUnits); $i = key($hrStorageAlloctionUnits); next($hrStorageAlloctionUnits)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,25)."</td>";
			echo "<td>".substr($hrStorageAlloctionUnits[$i],8)."</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br>";

		//$hrStorageSize = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.5");
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>hrStorage index</font></td>";		
		echo "<td width=200><font color=#0000CC>hrStorage Size</font></td>";
		echo "</tr>";		
		for (reset($hrStorageSize); $i = key($hrStorageSize); next($hrStorageSize)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,25)."</td>";
			echo "<td>".substr($hrStorageSize[$i],8)."</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br>";

		//$hrStorageUsed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.6");
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
		echo "<td width=200><font color=#0000CC>OID</font></td>";
		echo "<td width=100><font color=#0000CC>hrStorage index</font></td>";		
		echo "<td width=200><font color=#0000CC>hrStorage Used</font></td>";
		echo "<td width=200><font color=#0000CC>date-time</font></td>";
		echo "</tr>";		
		for (reset($hrStorageUsed); $i = key($hrStorageUsed); next($hrStorageUsed)) {
			echo "<tr>";
			echo "<td>".$ipaddress."</td>";
			echo "<td>".$i."</td>";
			echo "<td>".substr($i,25)."</td>";
			echo "<td>".substr($hrStorageUsed[$i],8)."</td>";
			echo "<td>".$cur_date."</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br>";

		}//end if
	
	?>
 

  
  </div>
</div>
</body>
</html>
