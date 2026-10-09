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
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="RGraph.common.core.js"></script>
<script src="RGraph.common.dynamic.js"></script>
<script src="RGraph.meter.js"></script>
<script>
    var gauge = new RGraph.Gauge({
        id: 'cvs',
        min: 0,
        max: 100,
        value: 84,
        options: {
            scale: {
                decimals: 0
            },
            tickmarks: {
                small: 50,
                big: 5
            },
            title: {
                top: {
                    self: 'Volume', 
                    size: 24
                },
                bottom: {
                    self: 'm³',
                    color: '#aaa'
                },
            },
            border: {
                outer: 'Gradient(white:white:white:white:white:white:white:white:white:white:#aaa)'
            }
        }
    }).draw()
</script>


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
				
		$strSQL = "SELECT * FROM hrDetail WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());

		echo "<br>";
		echo "<table border=0>";
		while($objResult = mysql_fetch_array($objQuery)){
            echo "<tr><td width=150>Device address</td>"."<td>".$objResult["ipaddress"]."</td></tr>";
			echo "<tr><td>Description</td>"."<td>".$objResult["sysDescription"]."</td></tr>";
			echo "<tr><td>System OID</td>"."<td>".$objResult["sysOID"]."</td></tr>";
            echo "<tr><td>System Name</td>"."<td>".$objResult["sysName"]."</td></tr>";
            echo "<tr><td>System Up Time</td>"."<td>".$objResult["sysUpTime"]."</td></tr>";
            echo "<tr><td>System Contact</td>"."<td>".$objResult["sysContact"]."</td></tr>";
            echo "<tr><td>System Location</td>"."<td>".$objResult["sysLocation"]."</td></tr>";
        }
		//draw();
		$strSQL = "SELECT ipaddress,cpmCPUIndex,cpmCPUUtils,cpmCPULoad";
		$strSQL .= " FROM hrCPUDetail WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		while($objResult = mysql_fetch_array($objQuery)){
			$n_core++;
			$n_coreLoad=$n_coreLoad+$objResult["cpmCPULoad"];
        }
            echo "<tr><td>CPU Utilization</td>"."<td>".$n_coreLoad/$n_core." % </td></tr>";
		echo "</table>";
		echo "<br>";
		
		echo "<b>CPU Load Detail</b>";
		$strSQL = "SELECT ipaddress,cpmCPUIndex,cpmCPUUtils,cpmCPULoad";
		$strSQL .= " FROM hrCPUDetail WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<table border=1>";
		echo "<tr>";
			echo "<td width='50'> CPU</td>";
			echo "<td width='100'> Core id </td>";
			echo "<td width='100'> Core name </td>";
			echo "<td witdh='150'> Core load (%) </td>";
		echo "</tr>";
		$line=0;
		while($objResult = mysql_fetch_array($objQuery)){
			$line++;
        	echo "<tr>";
			echo "<td>".$line."</td>";
			echo "<td>".$objResult["cpmCPUIndex"]."</td>";
			$cpmIndex = $objResult["cpmCPUIndex"];
 			echo "<td>".$objResult["cpmCPUUtils"]."</td>";
			echo "<td><a href='cpmCPULoad.php?ipaddress_fw=$ipaddress&infid_fw=$cpmIndex'>".$objResult["cpmCPULoad"]."</a></td>";
        	echo "</tr>";
        }		
		echo "</table>";
		echo "<br>";

		$strSQL = "SELECT ipaddress,hrStorageindex,hrStorageDescr,hrStorageUnits,hrStorageSize,hrStorageUsed";
		$strSQL .= " FROM hrStorage WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<b>Storage Used Detail</b>";
		echo "<table border=1>";
		echo "<tr>";
			echo "<td width='50'>Storage</td>";
			echo "<td width='200'>Description </td>";
			//echo "<td witdh='100'>Storage Units </td>";
			echo "<td width='100'>Storage Size </td>";
			echo "<td width='100'>Storage Used </td>";
			echo "<td width='100'>Storage Used %</td>";
			echo "<td width='100'>Storage Free %</td>";		
		echo "</tr>";
		while($objResult = mysql_fetch_array($objQuery)){
        	echo "<tr>";
			echo "<td>".$objResult["hrStorageindex"]."</td>";
			$hrid = $objResult["hrStorageindex"];
 			echo "<td>".$objResult["hrStorageDescr"]."</td>";
			//echo "<td>".$objResult["hrStorageUnits"]."</td>";
			echo "<td>".$objResult["hrStorageSize"]."</td>";
			echo "<td><a href='storageUsed.php?ipaddress_fw=$ipaddress&infid_fw=$hrid'>".$objResult["hrStorageUsed"]."</a></td>";			
			$hrStorageFree = $objResult["hrStorageSize"]-$objResult["hrStorageUsed"];
			$hrStorageFreepercen = ($hrStorageFree/$objResult["hrStorageSize"])*100;
			$hrStorageUsedpercen = ($objResult["hrStorageUsed"]/$objResult["hrStorageSize"])*100;
			if ($hrStorageUsedpercen>=80){
				echo "<td><font color='red'>".number_format($hrStorageUsedpercen,1)."</font></td>";
				}else {echo "<td>".number_format($hrStorageUsedpercen,1)."</td>";}
			echo "<td>".number_format($hrStorageFreepercen,1)."</td>";
        	echo "</tr>";
        }		
		echo "</table>";
		echo "<br>";

		$strSQL = "SELECT infid,interfacename,interfacespeed,interfacestatus,interfaceinput,interfaceoutput,";
		$strSQL .= "interfaceerror FROM sysInterface WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<b>Intrface Detail</b>";
		echo "<table border=1>";
		echo "<tr>";
			//echo "<td> Interface </td>";
			echo "<td width='150'> Interface Name </td>";
			echo "<td width='100'> Speed (Mbps)</td>";
			echo "<td witdh='100'> Status </td>";
			echo "<td width='150'> Input (Mbps) </td>";
			echo "<td width='150'> Output (Mbps) </td>";
			echo "<td width='100'> Error (Mbps) </td>";		
			echo "</tr>";
			while($objResult = mysql_fetch_array($objQuery)){
        	echo "<tr>";

			$infid = $objResult["infid"];
			$speed = $objResult["interfacespeed"]/1000000;
			$status = $objResult["interfacestatus"];
			$input = (($objResult["interfaceinput"])/(60*5))/(1024*1024);
			$output = (($objResult["interfaceoutput"])/(60*5))/(1024*1024);
			$error = (($objResult["interfaceerror"])/(60*5))/(1024*1024);	
			
			//echo "<td>".$objResult["infid"]."</td>";
			$infid=$objResult["infid"];
			echo "<td>".$objResult["interfacename"]."</td>";
			echo "<td>".number_format($speed,0)."</td>";
			
			if ($status == '1'){
				echo "<td><a href='inf_status_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>"."UP"."</a></td>";
			}else if ($status == '2'){
				echo "<td><a href='inf_status_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>"."Down"."</a></td>";
			}
			
			echo "<td><a href='inf_input_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".number_format($input,4)."</a></td>";
			echo "<td><a href='inf_output_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".number_format($output,4)."</a></td>";
			echo "<td><a href='inf_error_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".number_format($error,4)."</a></td>";
				
			/**
			echo "<td><a href='infdetail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".$objResult["interfacename"]."</a></td>";
			echo "<td><a href='inf_speed_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".$objResult["interfacespeed"]."</a></td>";
			echo "<td><a href='inf_status_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".$objResult["interfacestatus"]."</a></td>";
			echo "<td><a href='inf_input_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".$objResult["interfaceinput"]."</a></td>";
			echo "<td><a href='inf_output_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".$objResult["interfaceoutput"]."</a></td>";			
			echo "<td><a href='inf_error_detail.php?ipaddress_fw=$ipaddress&infid_fw=$infid'>".$objResult["interfaceerror"]."</a></td>";
			**/
			
        	echo "</tr>";
        	}		
		echo "</tr>";
		echo "</table>";
		echo "<br>";

		echo "<br>";

		mysql_close($objConnect);
		
	}//end if
	
    ?> 
    
  </div>
</div>
</body>
</html>
