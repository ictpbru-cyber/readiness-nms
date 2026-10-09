<?php
	
		$cur_date = date("d-m-Y H:i:s");

		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
				
		$strSQL = "SELECT * FROM sysTCPUDP";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		
		while($objResult = mysql_fetch_array($objQuery)){
			
			$host = $objResult["Server"];
			$port = $objResult["Port"];
				
			$timeout = 6;
			$tbegin = microtime(true); 
			$fp = fsockopen($host, $port, $errno, $errstr, $timeout); 
			$responding = 1;
			if (!$fp) { $responding = 0; } 
			$tend = microtime(true);
			fclose($fp);
						
			$mstime = ($tend - $tbegin) * 1000;
			$mstime = round($mstime, 2);	
			$Status = "";
			$ICMP = 0;
			$ICMP6 = 0;
			
			if($responding)
			{
				$Status = "respond";
			}
			else
			{
				$Status = "not respond";
			}	

			$retval=-1;
			$output=array();
			exec("ping $host -c2 -w2 2>&1",$output,$retval);
			$ICMP = $retval;
			
			$retval=-1;
			$output=array();
			exec("ping6 $host -c2 -w2 2>&1",$output,$retval);
			$ICMP6 = $retval;
			
			$strSQL1 = "UPDATE sysTCPUDP SET ";
			$strSQL1 .="Responded='".$mstime."' ";
			$strSQL1 .=",ICMP='".$ICMP."' ";
			$strSQL1 .=",ICMP6='".$ICMP6."' ";
			$strSQL1 .=",Status='".$Status."' ";
			$strSQL1 .="WHERE Server='".$host."' ";
			$objQuery1 = mysql_query($strSQL1);

			$strSQL2 = "INSERT INTO service_tcp_udp_log ";
			$strSQL2 .="(Server,Port,Responded,ICMP,ICMP6,Status,Date_in) ";
			$strSQL2 .="VALUES ";
			$strSQL2 .="('".$host."','.$port.','.$mstime.','.$ICMP.','.$ICMP6.','".$Status."',now()) ";
			$objQuery2 = mysql_query($strSQL2);						


        }//end while

		mysql_close($objConnect);		
		
?>
 
