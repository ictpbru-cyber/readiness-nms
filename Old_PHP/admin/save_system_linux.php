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
	
		$ipaddress = $_POST['address'];
		$communitystr = $_POST['community'];
		$cur_date = date("d/m/Y H:i:s");
		$type = 2; 
		// type=1 netdeive, type=2 linux,windows
		echo "<br>";
		
		if (($ipaddress!="") and ($communitystr!=""))
		{
		
			$sysDescription = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.2.1.1.1.0");
			$sysDescription =substr($sysDescription,7);	
			$sysOIDObject = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.2.1.1.2.0");
			$sysOIDObject = substr($sysOIDObject,5);	
			$sysUpTime = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.3.0");	
			$sysContact = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.4.0");
			$sysContact = substr($sysContact,7);	
			$sysName = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.2.1.1.5.0");
			$sysName = substr($sysName,7);			
			$sysLocation = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.6.0");
			$sysLocation = substr($sysLocation,7);
	
			$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
			$objDB = mysql_select_db("readiness");

			$strSQL = "INSERT INTO sysDevice ";
			$strSQL .="(ipaddress,community,type) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$communitystr."','".$type."') ";
			$objQuery = mysql_query($strSQL);
			
			$strSQL = "INSERT INTO hrDetail ";
			$strSQL .="(ipaddress,sysDescription,sysOID,sysUpTime,sysContact,sysName,sysLocation) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$sysDescription."','".$sysOIDObject."','".$sysUpTime."' ";
			$strSQL .=",'".$sysContact."','".$sysName."','".$sysLocation."') ";
			$objQuery = mysql_query($strSQL);
			
			/**		
			$infindex = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.1");
			$interface = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.2");
			$ifspeed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.5");
			$ifAdminStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.7");
			$ifOperStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.8");
			$infin = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.10");
			$infout = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.16");
			$ifErrorStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.20");

			$hrStorageIndex = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.1");
			$hrStorageGroupOID = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.2");
			$hrStorageDescr = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.3");
			$hrStorageAlloctionUnits = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.4");
			$hrStorageSize = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.5");
			$hrStorageUsed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.6");
			**/
			
			$infindex = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.1");
			for (reset($infindex); $i = key($infindex); next($infindex)) {
				$strSQL = "INSERT INTO sysInterfaceIndex ";
				$strSQL .="(ipaddress,oid,infid) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($infindex[$i],8)."') ";
				$objQuery = mysql_query($strSQL);		
			}

			$interface = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.2");
			for (reset($interface); $i = key($interface); next($interface)) {
				$strSQL = "INSERT INTO sysInterface ";
				$strSQL .="(ipaddress,interfaceoid,infid,interfacename) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($interface[$i],7)."') ";
				$objQuery = mysql_query($strSQL);		
			}

			$ifspeed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.5");			
			for (reset($ifspeed); $i = key($ifspeed); next($ifspeed)) {
				$strSQL = "INSERT INTO sysInterfaceSpeed ";
				$strSQL .="(ipaddress,oid,infid,speed,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($ifspeed[$i],8)."','".$cur_date."') ";
				$objQuery = mysql_query($strSQL);		
			}
	
			$ifAdminStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.7");
			for (reset($ifAdminStatus); $i = key($ifAdminStatus); next($ifAdminStatus)) {
				$strSQL = "INSERT INTO sysinfAdminstatus ";
				$strSQL .="(ipaddress,oid,infid,status,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($ifAdminStatus[$i],8)."','".$cur_date."') ";			
				$objQuery = mysql_query($strSQL);		
			}
			
			$ifOperStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.8");
			for (reset($ifOperStatus); $i = key($ifOperStatus); next($ifOperStatus)) {
				$strSQL = "INSERT INTO sysinfOperstatus";	
				$strSQL .="(ipaddress,oid,infid,status,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($ifOperStatus[$i],8)."','".$cur_date."') ";			
				$objQuery = mysql_query($strSQL);		
			}
			
			$infin = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.10");
			for (reset($infin); $i = key($infin); next($infin)) {
				$strSQL = "INSERT INTO sysInterfacein";	
				$strSQL .="(ipaddress,oid,infid,datain,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,23)."','".substr($infin[$i],10)."','".$cur_date."') ";			
				$objQuery = mysql_query($strSQL);		
			}

			$infout = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.16");	
			for (reset($infout); $i = key($infout); next($infout)) {
				$strSQL = "INSERT INTO sysInterfaceout";	
				$strSQL .="(ipaddress,oid,infid,dataout,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,23)."','".substr($infout[$i],10)."','".$cur_date."') ";			
				$objQuery = mysql_query($strSQL);		
			}

			$ifErrorStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.20");					
			for (reset($ifErrorStatus); $i = key($ifErrorStatus); next($ifErrorStatus)) {
				$strSQL = "INSERT INTO sysInterfaceerror";	
				$strSQL .="(ipaddress,oid,infid,error,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,23)."','".substr($ifErrorStatus[$i],10)."','".$cur_date."') ";			
				$objQuery = mysql_query($strSQL);		
			}
					
			$cpmCPUUtilID = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.3.3.1.1");
			for (reset($cpmCPUUtilID); $i = key($cpmCPUUtilID); next($cpmCPUUtilID)) {
				$strSQL = "INSERT INTO hrCPUDetail";	
				$strSQL .="(ipaddress,OID,cpmCPUIndex,cpmCPUUtils,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,25)."','".substr($cpmCPUUtilID[$i],4)."','".$cur_date."') ";			
				$objQuery = mysql_query($strSQL);		
			}
				
				
			$cpmCPUUtilUsed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.3.3.1.2");
			for (reset($cpmCPUUtilUsed); $i = key($cpmCPUUtilUsed); next($cpmCPUUtilUsed)) {
				$strSQL = "INSERT INTO hrCPULoad";	
				$strSQL .="(ipaddress,OID,cpmCPUIndex,cpmCPULoad,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,25)."','".substr($cpmCPUUtilUsed[$i],8)."','".$cur_date."') ";			
				$objQuery = mysql_query($strSQL);		
			}
			
			$hrStorageIndex = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.1");
			for (reset($hrStorageIndex); $i = key($hrStorageIndex); next($hrStorageIndex)) {
				$strSQL = "INSERT INTO hrStorage";	
				$strSQL .="(ipaddress,OID,hrStorageindex,value) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($hrStorageIndex[$i],8)."','".$hrStorageIndex[$i]."') ";			
				$objQuery = mysql_query($strSQL);		
			}		
			
			$hrStorageGroupOID = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.2");
			for (reset($hrStorageGroupOID); $i = key($hrStorageGroupOID); next($hrStorageGroupOID)) {
				$strSQL = "INSERT INTO hrStorageGOID";	
				$strSQL .="(ipaddress,OID,hrStorageindex,hrStorageGroupOID) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,25)."','".$hrStorageGroupOID[$i]."') ";			
				$objQuery = mysql_query($strSQL);		
			}	

			$hrStorageDescr = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.3");
			for (reset($hrStorageDescr); $i = key($hrStorageDescr); next($hrStorageDescr)) {
				$strSQL = "INSERT INTO hrStorageDescr";	
				$strSQL .="(ipaddress,OID,hrStorageindex,hrStorageDescr) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,25)."','".substr($hrStorageDescr[$i],7)."') ";			
				$objQuery = mysql_query($strSQL);		
			}		

			$hrStorageAlloctionUnits = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.4");
			for (reset($hrStorageAlloctionUnits); $i = key($hrStorageAlloctionUnits); next($hrStorageAlloctionUnits)) {
				$strSQL = "INSERT INTO hrStorageUnits";	
				$strSQL .="(ipaddress,OID,hrStorageindex,hrStorageUnits) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,25)."','".substr($hrStorageAlloctionUnits[$i],8)."') ";			
				$objQuery = mysql_query($strSQL);		
			}
					
			$hrStorageSize = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.5");
			for (reset($hrStorageSize); $i = key($hrStorageSize); next($hrStorageSize)) {
				$strSQL = "INSERT INTO hrStorageSize";	
				$strSQL .="(ipaddress,OID,hrStorageindex,hrStorageSize) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,25)."','".substr($hrStorageSize[$i],8)."') ";			
				$objQuery = mysql_query($strSQL);		
			}

			$hrStorageUsed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.25.2.3.1.6");
			for (reset($hrStorageUsed); $i = key($hrStorageUsed); next($hrStorageUsed)) {
				$strSQL = "INSERT INTO hrStorageUsed";	
				$strSQL .="(ipaddress,OID,hrStorageindex,hrStorageUsed,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$i."','".substr($i,25)."','".substr($hrStorageUsed[$i],8)."','".$cur_date."') ";
				$objQuery = mysql_query($strSQL); 		
			}		

		mysql_close($objConnect);

		}//end if
	
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="0; url=http://readiness.pbru.ac.th/project01/get-linux.html">
<title>Readiness Project</title>
</head>

<body>
</body>
</html>