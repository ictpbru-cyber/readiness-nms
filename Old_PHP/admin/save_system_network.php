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
		
		if ($ipaddress<>"" and $communitystr<>"") 
		{

		$time_start = microtime(true);
		$reponse = snmpget("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.3.0",1000000,5);
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$time = number_format($time,0);
		//echo "Delay in $time secondes\n";
		//echo "<br>";
		
		if ($time <> 0) {
			echo $time." Connection Error ";
			echo $ipaddress."/".$communitystr." ไม่ถูกต้อง";
		}else if ($time == 0) {
			echo $time." Connection Succes";
			$type="1";
			$cur_date = date("d/m/Y H:i:s");
	
			$sysDescription = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.2.1.1.1.0");
			$sysDescription = substr($sysDescription,7);	
	
			$sysOIDObject = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.2.1.1.2.0");
			$sysOIDObject = substr($sysOIDObject,5);
	
			$sysName = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.2.1.1.5.0");
			$sysName = substr($sysName,7);
	
			$systemIOSVersion = snmp2_get("$ipaddress", "$communitystr", "iso.3.6.1.4.1.9.2.1.1.0");
			$systemIOSVersion = substr($systemIOSVersion,7);
	
			$sysLocation = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.6.0");
			$sysLocation = substr($sysLocation,7);
		
			$sysUpTime = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.3.0");	
			
			$sysContact = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.4.0");
			$sysContact = substr($sysContact,7);
	
			$cpmCPUTotal5minRev = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.109.1.1.1.1.8.1");
			$cpmCPUTotal5minRev = substr($cpmCPUTotal5minRev,8);
	
			$cpmMemoryLargestFree = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.48.1.1.1.7.1");
			$cpmMemoryLargestFree = substr($cpmMemoryLargestFree,8);
		
			$cpmMemoryPoolFree = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.48.1.1.1.6.1");
			$cpmMemoryPoolFree = substr($cpmMemoryPoolFree,8);
	
			$cpmMemoryUsed = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.48.1.1.1.5.1");
			$cpmMemoryUsed = substr($cpmMemoryUsed,8);			
	
			$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
			$objDB = mysql_select_db("readiness");
					
			$strSQL = "INSERT INTO sysDevice ";
			$strSQL .="(ipaddress,community,type) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$ipaddress."','".$communitystr."','".$type."') ";
			$objQuery = mysql_query($strSQL);

			if($objQuery)
			{
				echo "Save Done.";
				$strSQL = "INSERT INTO sysDetail ";
				$strSQL .="(ipaddress,sysDescription,sysOID,sysName,sysIOS,sysLocation,sysUpTime,sysContact,sysCPULoad";
				$strSQL .=",sysMemoryFree,sysMemoryUse) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$sysDescription."','".$sysOIDObject."','".$sysName."' ";
				$strSQL .=",'".$systemIOSVersion."','".$sysLocation."','".$sysUpTime."' ";
				$strSQL .=",'".$sysContact."','".$cpmCPUTotal5minRev."','".$cpmMemoryPoolFree."' ";
				$strSQL .=",'".$cpmMemoryUsed."') ";
				$objQuery = mysql_query($strSQL);
		
				$strSQL = "INSERT INTO cpmCPULoad ";
				$strSQL .="(ipaddress,sysCPUload,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$cpmCPUTotal5minRev."','".$cur_date."') ";
				$objQuery = mysql_query($strSQL);
				
				$strSQL = "INSERT INTO cpmMemory ";
				$strSQL .="(ipaddress,cpmMemoryUse,cpmMemoryFree,cpmMemoryLargestFree,timestamp) ";
				$strSQL .="VALUES ";
				$strSQL .="('".$ipaddress."','".$cpmMemoryUsed."','".$cpmMemoryPoolFree."','".$cpmMemoryLargestFree."','".$cur_date."') ";
				$objQuery = mysql_query($strSQL);
				
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
			}
			else
			{
				echo "Error Save [".$strSQL."]";
			}
			

		}//end if conection check
		mysql_close($objConnect);
		
		}//end if
?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="0; url=http://readiness.pbru.ac.th/admin/get-network.php">
<title>Readiness Project</title>
</head>

<body>
</body>
</html>