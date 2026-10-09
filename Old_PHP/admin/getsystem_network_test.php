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
<link rel="stylesheet" type="text/css" href="css/menu.css"/>

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

	<ul class="menu">
		<li class="menu-item">
		  <a href="admin.php">Home</a>
		</li>
		<li class="menu-item">
		  <a href="admin_network.php">Status </a>
		</li>
		<li class="menu-item">
		  <a href="get-network.php">Add</a>
		</li>
		<li class="menu-item">
		  <a href="get-network-test.php">Test</a>
		</li>
		<li class="menu-item">
		  <a href="logout.php">logout</a>
		</li>
	</ul>
  

	<?php
	
		$ipaddress = $_POST['address'];
		$communitystr = $_POST['community'];
		$cur_date = date("d-m-Y H:i:s");
		
		if ($ipaddress<>"" and $communitystr<>"")
		{

		$time_start = microtime(true);
		$reponse = snmpget("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.3.0",1000000,5);
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$time = number_format($time,0);
		echo "Delay in $time secondes\n";
		echo "<br>";
		
		if ($time <> 0) {
			echo $time." Connection Error ";
			echo $ipaddress."/".$communitystr." ไม่ถูกต้อง";
		}else if ($time == 0) {
			echo $time." Connection Succes";
				
			echo "<table border=0>";
			
			echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
		
			echo "<tr><td>IP Address</td><td>:</td><td>".$ipaddress."</td></tr>";
			echo "<tr><td>Community String</td><td>:</td><td>".$communitystr."</td></tr>";
			
			echo "<tr><td>Curent Date-Time</d><td>:</td><td>".$cur_date."</td></tr>";
			
			echo "<tr>";
			$sysDescription = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.1.0");
			$sysDescription = substr($sysDescription,8);
			$sysDescription = trim($sysDescription, '"');
			echo "<td width=130>System Description</td><td>: </td><td>".$sysDescription."</td>";		
			echo "</tr>";
	
			echo "<tr>";
			$sysOIDObject = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.2.0");
			echo "<td width=130>System OID Object</td><td>: </td><td>".substr($sysOIDObject,5)."</td>";		
			echo "</tr>";
			$sysOIDObject = substr($sysOIDObject,5);
	
			echo "<tr>";
			$sysName = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.5.0");
			$sysName = substr($sysName,8);
			$sysName = trim($sysName,'"');
			echo "<td>System name</td><td>: </td><td>".$sysName."</td>";
			echo "</tr>";
	
			echo "<tr>";
			$systemIOSVersion = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.2.1.1.0");
			$systemIOSVersion = substr($systemIOSVersion,8);
			$systemIOSVersion = trim($systemIOSVersion,'"');
			echo "<td>System IOS Version</td><td>: </td><td>".$systemIOSVersion."</td>";
			echo "</tr>";
	
			echo "<tr>";	
			$sysLocation = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.6.0");
			$sysLocation = substr($sysLocation,8);
			$sysLocation = trim($sysLocation,'"');
			echo "<td>System Location</td><td>: </td><td>".$sysLocation."</td>";
			echo "</tr>";
		
			echo "<tr>";
			$sysUpTime = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.3.0");
			echo "<td>System Up Time</td><td>: </td><td>".$sysUpTime."</td>";
			echo "</tr>";
			
			echo "<tr>";	
			$sysContact = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.1.4.0");
			$sysContact = substr($sysContact,8);
			$sysContact = trim($sysContact,'"');
			echo "<td>System Contact</td><td>: </td><td>".$sysContact."</td>";
			echo "</tr>";
	
			echo "<tr>";	
			$cpmCPUTotal5minRev = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.109.1.1.1.1.8.1");
			echo "<td>CPU Total 5 min Rev</td><td>: </td><td>".substr($cpmCPUTotal5minRev,8)."</td>";
			echo "</tr>";
			$cpmCPUTotal5minRev = substr($cpmCPUTotal5minRev,8);
		
			echo "<tr>";
			$cpmMemoryUsed = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.48.1.1.1.5.1");
			echo "<td>Mmemory Used</td><td>: </td><td>".substr($cpmMemoryUsed,8)."</td>";
			echo "</tr>";
			$cpmMemoryUsed = substr($cpmMemoryUsed,8);
	
			echo "<tr>";
			$cpmMemoryPoolFree = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.48.1.1.1.6.1");
			echo "<td>Memory Pool Free</td><td>: </td><td>".substr($cpmMemoryPoolFree,8)."</td>";
			echo "</tr>";
			$cpmMemoryPoolFree = substr($cpmMemoryPoolFree,8);
	
			$cpmMemoryPoolLargestFree = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.4.1.9.9.48.1.1.1.7.1");
			echo "<td>Memory Pool Largest Free</td><td>: </td><td>".substr($cpmMemoryPoolLargestFree,8)."</td>";
			echo "</tr>";
			$cpmMemoryPoolLargestFree = substr($cpmMemoryPoolLargestFree,8);
	
			echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			echo "</table>";
			echo "<br>";	
			
			/**
			root@LINUXreadiness:~# snmpwalk -v2c -c $communitystr 192.168.18.1 1.3.6.1.2.1.31.1.1.1.1
			iso.3.6.1.2.1.31.1.1.1.1.1 = STRING: "lo"
			iso.3.6.1.2.1.31.1.1.1.1.2 = STRING: "eth0"
			iso.3.6.1.2.1.31.1.1.1.1.3 = STRING: "eth1"
			root@LINUXreadiness:~# snmpwalk -v2c -c $communitystr 192.168.18.1 1.3.6.1.2.1.2.2.1.2
			iso.3.6.1.2.1.2.2.1.2.1 = STRING: "lo"
			iso.3.6.1.2.1.2.2.1.2.2 = STRING: "eth0"
			iso.3.6.1.2.1.2.2.1.2.3 = STRING: "eth1"
			**/
			
			/**
			$interface001 = snmp2_get ("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.2.1");
			echo "<tr><td>interface1 name</td><td>: </td><td>".$interface001."</td></tr>";
			$get_Byte_in_for_nic1 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.10.1");
			echo "<tr><td>get byte in for nic 1</td><td>: </td><td>".$get_Byte_in_for_nic1."</td></tr>";
			$get_Byte_out_for_nic1 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.16.1");
			echo "<tr><td>get byte out for nic 1</td><td>: </td><td>".$get_Byte_out_for_nic1."</td></tr>";
	
			
			$interface002 = snmp2_get ("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.2.2");
			echo "<tr><td>interface2 name</td><td>: </td><td>".$interface002."</td></tr>";
			$get_Byte_in_for_nic2 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.10.2");
			echo "<tr><td>get byte in for nic 2</td><td>: </td><td>".$get_Byte_in_for_nic2."</td></tr>";
			$get_Byte_out_for_nic2 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.16.2");
			echo "<tr><td>get byte out for nic 2</td><td>: </td><td>".$get_Byte_out_for_nic2."</td></tr>";
	
			
			$interface003 = snmp2_get ("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.2.3");
			echo "<tr><td>interface3 name</td><td>: </td><td>".$interface003."</td></tr>";
			$get_Byte_in_for_nic3 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.10.3");
			echo "<tr><td>get byte in for nic 3</td><td>: </td><td>".$get_Byte_in_for_nic3."</td></tr>";
			$get_Byte_out_for_nic3 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.16.3");
			echo "<tr><td>get byte out for nic 3</td><td>: </td><td>".$get_Byte_out_for_nic3."</td></tr>";
			**/
	
			/**	
			$interface002 = snmp2_get ("$ipaddress", "$communitystr","1.3.6.1.2.1.2.2.1.2.2");
			echo "interface2 name: ".$interface002."<br>";
			$get_Byte_in_for_nic2 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.10.2");
			echo "get byte in for nic 2: ".$get_Byte_in_for_nic2."<br>";
			$get_Byte_out_for_nic2 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.16.2");
			echo "get byte out for nic 2: ".$get_Byte_out_for_nic2."<br>"."<br>";
		
			$interface003 = snmp2_get ("$ipaddress", "$communitystr","1.3.6.1.2.1.2.2.1.2.3");
			echo "interface3 name: ".$interface003."<br>";
			$get_Byte_in_for_nic3 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.10.3");
			echo "get byte in for nic 3: ".$get_Byte_in_for_nic3."<br>";
			$get_Byte_out_for_nic3 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.16.3");
			echo "get byte out for nic 3: ".$get_Byte_out_for_nic3."<br>"."<br>";
			**/
	
	
			/**
			$a = snmpwalkoid("10.254.1.1", "public", "iso.3.6.1.2.1.2.2.1.2");
			$b = array();
			$xi = "";
			for (reset($a); $i = key($a); next($a)) {
				echo "$i: $a[$i]\n";
				echo "<br>";
				$xi = $i;
				$b[$i] = $i;
			}
	
			echo $a."<br>";
			echo count($a)."<br>";
			**/
	
			/**
			$interface003 = snmp2_get ("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.2.3");
			echo "<tr><td>interface3 name</td><td>: </td><td>".$interface003."</td></tr>";
			
			$get_Byte_in_for_nic3 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.10.3");
			echo "<tr><td>get byte in for nic 3</td><td>: </td><td>".$get_Byte_in_for_nic3."</td></tr>";
			
			$get_Byte_out_for_nic3 = snmp2_get("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.16.3");		
			echo "<tr><td>get byte out for nic 3</td><td>: </td><td>".$get_Byte_out_for_nic3."</td></tr>";
			**/
	
			/** interface index value integer **/
			$infindex = snmpwalkoid("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.1");
			
			/** interface description or name value sring[0-255] **/
			$a = snmpwalkoid("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.2");
			
			/** interface speed value integer **/
			$ifspeed = snmpwalkoid("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.5");
			
			/** interface admin status value integer **/
			$ifAdminStatus = snmpwalkoid("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.7");
			
			/** interface operater status value integer **/
			$ifOperStatus = snmpwalkoid("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.8");
			
			/** interface input value integer **/
			$in = snmpwalkoid("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.10");
			
			/** interface output value integer **/
			$out = snmpwalkoid("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.16");
			
			/** interface error status value integer **/
			$ifErrorStatus = snmpwalkoid("$ipaddress", "$communitystr", "1.3.6.1.2.1.2.2.1.20");
			
			/**
			echo "<tr>"."<td>&nbsp;</td>"."<td>&nbsp;</td>"."<td>&nbsp;</td>"."</tr>";
			echo "<tr>"."<td>&nbsp;</td>"."<td>&nbsp;</td>"."<td>&nbsp;</td>"."</tr>";
			**/
	
			
			echo "<table>";
			echo "<tr>";
			echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
			echo "<td width=200><font color=#0000CC>OID</font></td>";
			echo "<td width=100><font color=#0000CC>inf index</font></td>";
			echo "</tr>";
			for (reset($infindex); $i = key($infindex); next($infindex)) {
			echo "<tr><td>".$ipaddress."</td><td>".$i."</td><td>".substr($infindex[$i],8)."\n</td></tr>";
			}
			echo "</table>";
			echo "<br>";
	
			echo "<table>";
			echo "<tr>";
			echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
			echo "<td width=200><font color=#0000CC>OID</font></td>";
			echo "<td width=100><font color=#0000CC>inf index</font></td>";
			echo "<td width=200><font color=#0000CC>inf name</font></td>";
			echo "</tr>";		
			for (reset($a); $i = key($a); next($a)) {
			echo "<tr><td>".$ipaddress."</td><td>".$i."</td><td>".substr($i,22)."</td><td>".trim(substr($a[$i],8),'"')."\n</td></tr>";
			}
			echo "</table>";
			echo "<br>";
	
			echo "<table>";
			echo "<tr>";
			echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
			echo "<td width=200><font color=#0000CC>OID</font></td>";
			echo "<td width=100><font color=#0000CC>inf index</font></td>";
			echo "<td width=200><font color=#0000CC>inf speed</font></td>";
			echo "</tr>";
			for (reset($ifspeed); $i = key($ifspeed); next($ifspeed)) {
			echo "<tr><td>".$ipaddress."</td><td>".$i."</td><td>".substr($i,22)."</td><td>".substr($ifspeed[$i],8)."\n</td></tr>";
			}
			echo "</table>";
			echo "<br>";
			
			echo "<table>";
			echo "<tr>";
			echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
			echo "<td width=200><font color=#0000CC>OID</font></td>";
			echo "<td width=100><font color=#0000CC>inf index</font></td>";
			echo "<td width=200><font color=#0000CC>inf Admin Status</font></td>";
			echo "</tr>";
			for (reset($ifAdminStatus); $i = key($ifAdminStatus); next($ifAdminStatus)) {
			echo "<tr><td>".$ipaddress."</td><td>".$i."</td><td>".substr($i,22)."</td><td>".substr($ifAdminStatus[$i],8)."\n</td></tr>";
			}
			echo "</table>";
			echo "<br>";
	
			echo "<table>";
			echo "<tr>";
			echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
			echo "<td width=200><font color=#0000CC>OID</font></td>";
			echo "<td width=100><font color=#0000CC>inf index</font></td>";
			echo "<td width=200><font color=#0000CC>inf Operator Status</font></td>";
			echo "</tr>";	
			for (reset($ifOperStatus); $i = key($ifOperStatus); next($ifOperStatus)) {
			echo "<tr><td>".$ipaddress."</td><td>".$i."</td><td>".substr($i,22)."</td><td>".substr($ifOperStatus[$i],8)."\n</td></tr>";		
			}
			echo "</table>";
			echo "<br>";
	
			echo "<table>";
			echo "<tr>";
			echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
			echo "<td width=200><font color=#0000CC>OID</font></td>";
			echo "<td width=100><font color=#0000CC>inf index</font></td>";
			echo "<td width=200><font color=#0000CC>inf data input</font></td>";
			echo "<td width=200><font color=#0000CC>date-time</font></td>";
			echo "</tr>";	
			for (reset($in); $i = key($in); next($in)) {
			echo "<tr><td>".$ipaddress."</td><td>".$i."</td><td>".substr($i,23)."</td><td>".substr($in[$i],10)."</td><td>".$cur_date."</td></tr>";
			}
			echo "</table>";
			echo "<br>";
	
			echo "<table>";
			echo "<tr>";
			echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
			echo "<td width=200><font color=#0000CC>OID</font></td>";
			echo "<td width=100><font color=#0000CC>inf index</font></td>";
			echo "<td width=200><font color=#0000CC>inf data output</font></td>";
			echo "<td width=200><font color=#0000CC>date-time</font></td>";
			echo "</tr>";			
			for (reset($out); $i = key($out); next($out)) {
			echo "<tr><td>".$ipaddress."</td><td>".$i."</td><td>".substr($i,23)."</td><td>".substr($out[$i],10)."</td><td>".$cur_date."</td></tr>";		
			}		
			echo "</table>";
			echo "<br>";
			
			echo "<table>";
			echo "<tr>";
			echo "<td width=100><font color=#0000CC>ipaddress</font></td>";
			echo "<td width=200><font color=#0000CC>OID</font></td>";
			echo "<td width=100><font color=#0000CC>inf index</font></td>";
			echo "<td width=200><font color=#0000CC>inf Error</font></td>";
			echo "<td width=200><font color=#0000CC>date-time</font></td>";
			echo "</tr>";		
			for (reset($ifErrorStatus); $i = key($ifErrorStatus); next($ifErrorStatus)) {
			echo "<tr><td>".$ipaddress."</td><td>".$i."</td><td>".substr($i,23)."</td><td>".substr($ifErrorStatus[$i],10)."</td><td>".$cur_date."</td></tr>";
			}		
			echo "</table>";
			echo "<br>";			
		}//if connection check
		
		}//end if
	
	?>
 
	<br><br>
    <div class="footer">
    <?php
      	include 'getClientEnv.php';
    ?>
    </div>
  
  </div>
</div>
</body>
</html>
