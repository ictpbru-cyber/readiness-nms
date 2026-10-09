
<?php
	
		$cur_date = date("d-m-Y H:i:s");

		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
				
		$strSQL = "SELECT * FROM sysDevice";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		
		while($objResult = mysql_fetch_array($objQuery)){
			
			$ipaddress = $objResult["ipaddress"];
			$communitystr = $objResult["community"];

			$time_start = microtime(true);
			$reponse = snmpget("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.3.0",1000000,5);
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$time = number_format($time,0);
			$status="";
						
			if ($time <> 0) {
				//echo $time." Connection Error ";
				$status="2";
				$strSQL01 = "UPDATE sysDetail SET ";
				$strSQL01 .="status='".$status."' ";
				$strSQL01 .="WHERE ipaddress='".$ipaddress."' ";
				$objQuery01 = mysql_query($strSQL01);
			}else if ($time == 0) {
				//echo $time." Connection Succes";
				$status="1";

				$sysDescription = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.1.0");
				$sysDescription = trim(substr($sysDescription,8),'"');
	
				$sysOIDObject = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.2.0");
				$sysOIDObject = substr($sysOIDObject,5);
		
				$sysName = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.5.0");
				$sysName = trim(substr($sysName,8),'"');
		
				$systemIOSVersion = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.2.1.1.0");
				$systemIOSVersion = trim(substr($systemIOSVersion,8),'"');
		
				$sysLocation = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.6.0");
				$sysLocation = trim(substr($sysLocation,8),'"');
			
				$sysUpTime = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.3.0");	
				
				$sysContact = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.4.0");
				$sysContact = trim(substr($sysContact,8),'"');
		
				$cpmCPUTotal5minRev = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.109.1.1.1.1.8.1");
				$cpmCPUTotal5minRev = trim(substr($cpmCPUTotal5minRev,8));
		
				$cpmMemoryLargestFree = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.48.1.1.1.7.1");
				$cpmMemoryLargestFree = trim(substr($cpmMemoryLargestFree,8));
			
				$cpmMemoryPoolFree = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.48.1.1.1.6.1");
				$cpmMemoryPoolFree = trim(substr($cpmMemoryPoolFree,8));
		
				$cpmMemoryUsed = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.48.1.1.1.5.1");
				$cpmMemoryUsed = trim(substr($cpmMemoryUsed,8));				
				
				$strSQL1 = "UPDATE sysDetail SET ";
				$strSQL1 .="sysDescription='".$sysDescription."' ";
				$strSQL1 .=",sysOID='".$sysOIDObject."' ";
				$strSQL1 .=",sysName='".$sysName."' ";
				$strSQL1 .=",sysIOS='".$systemIOSVersion."' ";
				$strSQL1 .=",sysLocation='".$sysLocation."' ";
				$strSQL1 .=",sysUpTime='".$sysUpTime."' ";
				$strSQL1 .=",sysContact='".$sysContact."' ";
				$strSQL1 .=",sysCPULoad='".$cpmCPUTotal5minRev."' ";
				$strSQL1 .=",sysMemoryFree='".$cpmMemoryPoolFree."' ";
				$strSQL1 .=",sysMemoryUse='".$cpmMemoryUsed."' ";
				$strSQL1 .=",status='".$status."' ";
				$strSQL1 .="WHERE ipaddress='".$ipaddress."' ";
				$objQuery1 = mysql_query($strSQL1);
				
				$strSQL2 = "INSERT INTO cpmCPULoad ";
				$strSQL2 .="(ipaddress,sysCPUload,timestamp) ";
				$strSQL2 .="VALUES ";
				$strSQL2 .="('".$ipaddress."','".$cpmCPUTotal5minRev."',now()) ";
				$objQuery2 = mysql_query($strSQL2);
				
				$strSQL3 = "INSERT INTO cpmMemory ";
				$strSQL3 .="(ipaddress,cpmMemoryUse,cpmMemoryFree,cpmMemoryLargestFree,timestamp) ";
				$strSQL3 .="VALUES ";
				$strSQL3 .="('".$ipaddress."','".$cpmMemoryUsed."','".$cpmMemoryPoolFree."','".$cpmMemoryLargestFree."',now()) ";
				$objQuery3 = mysql_query($strSQL3);
				
				$ifspeed = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.5");
				$ifAdminStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.7");
				$ifOperStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.8");
				$infin = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.10");
				$infout = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.16");
				$ifErrorStatus = snmpwalkoid("$ipaddress", "$communitystr", "iso.3.6.1.2.1.2.2.1.20");
				
				for (reset($ifspeed); $i = key($ifspeed); next($ifspeed)) {
					$strSQL4 = "INSERT INTO sysInterfaceSpeed ";
					$strSQL4 .="(ipaddress,oid,infid,speed,timestamp) ";
					$strSQL4 .="VALUES ";
					$strSQL4 .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($ifspeed[$i],8)."',now()) ";
					$objQuery4 = mysql_query($strSQL4);
	
					$strSQL41 = "UPDATE sysInterface SET ";
					$strSQL41 .="interfacespeed='".substr($ifspeed[$i],8)."' ";
					$strSQL41 .="WHERE (ipaddress='".$ipaddress."') and (infid='".substr($i,22)."') ";
					$objQuery41 = mysql_query($strSQL41);		
				}
		
				for (reset($ifAdminStatus); $i = key($ifAdminStatus); next($ifAdminStatus)) {
					$strSQL5 = "INSERT INTO sysinfAdminstatus ";
					$strSQL5 .="(ipaddress,oid,infid,status,timestamp) ";
					$strSQL5 .="VALUES ";
					$strSQL5 .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($ifAdminStatus[$i],8)."',now()) ";			
					$objQuery5 = mysql_query($strSQL5);		
	
					$strSQL51 = "UPDATE sysInterface SET ";
					$strSQL51 .="interfacestatus_admmin='".substr($ifAdminStatus[$i],8)."' ";
					$strSQL51 .="WHERE (ipaddress='".$ipaddress."') and (infid='".substr($i,22)."') ";
					$objQuery51 = mysql_query($strSQL51);
	
				}
				
				for (reset($ifOperStatus); $i = key($ifOperStatus); next($ifOperStatus)) {
					$strSQL6 = "INSERT INTO sysinfOperstatus";	
					$strSQL6 .="(ipaddress,oid,infid,status,timestamp) ";
					$strSQL6 .="VALUES ";
					$strSQL6 .="('".$ipaddress."','".$i."','".substr($i,22)."','".substr($ifOperStatus[$i],8)."',now()) ";			
					$objQuery6 = mysql_query($strSQL6);		
	
					$strSQL61 = "UPDATE sysInterface SET ";
					$strSQL61 .="interfacestatus='".substr($ifOperStatus[$i],8)."' ";
					$strSQL61 .="WHERE (ipaddress='".$ipaddress."') and (infid='".substr($i,22)."') ";
					$objQuery61 = mysql_query($strSQL61);
				}
		
				for (reset($infin); $i = key($infin); next($infin)) {
					$strSQL7 = "INSERT INTO sysInterfacein";	
					$strSQL7 .="(ipaddress,oid,infid,datain,timestamp) ";
					$strSQL7 .="VALUES ";
					$strSQL7 .="('".$ipaddress."','".$i."','".substr($i,23)."','".substr($infin[$i],10)."',now()) ";			
					$objQuery7 = mysql_query($strSQL7);		
	
					$strSQL71 = "UPDATE sysInterface SET ";
					$strSQL71 .="interfaceinput='".substr($infin[$i],10)."' ";
					$strSQL71 .="WHERE (ipaddress='".$ipaddress."') and (infid='".substr($i,23)."') ";
					$objQuery71 = mysql_query($strSQL71);
				}
		
				for (reset($infout); $i = key($infout); next($infout)) {
					$strSQL8 = "INSERT INTO sysInterfaceout";	
					$strSQL8 .="(ipaddress,oid,infid,dataout,timestamp) ";
					$strSQL8 .="VALUES ";
					$strSQL8 .="('".$ipaddress."','".$i."','".substr($i,23)."','".substr($infout[$i],10)."',now()) ";			
					$objQuery8 = mysql_query($strSQL8);	
	
					$strSQL81 = "UPDATE sysInterface SET ";
					$strSQL81 .="interfaceoutput='".substr($infout[$i],10)."' ";
					$strSQL81 .="WHERE (ipaddress='".$ipaddress."') and (infid='".substr($i,23)."') ";
					$objQuery81 = mysql_query($strSQL81);	
				}
						
				for (reset($ifErrorStatus); $i = key($ifErrorStatus); next($ifErrorStatus)) {
					$strSQL9 = "INSERT INTO sysInterfaceerror";	
					$strSQL9 .="(ipaddress,oid,infid,error,timestamp) ";
					$strSQL9 .="VALUES ";
					$strSQL9 .="('".$ipaddress."','".$i."','".substr($i,23)."','".substr($ifErrorStatus[$i],10)."',now()) ";			
					$objQuery9 = mysql_query($strSQL9);		
	
					$strSQL91 = "UPDATE sysInterface SET ";
					$strSQL91 .="interfaceerror='".substr($ifErrorStatus[$i],10)."' ";
					$strSQL91 .="WHERE (ipaddress='".$ipaddress."') and (infid='".substr($i,23)."') ";
					$objQuery91 = mysql_query($strSQL91);
				}							

			}// if else
			
        }//end while

		mysql_close($objConnect);		
		
?>
 
