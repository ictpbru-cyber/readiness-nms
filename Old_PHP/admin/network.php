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
		  <a href="admin.php">Home</a>
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
				
		$strSQL = "SELECT sysDetail.ipaddress, sysDetail.sysName, hrmanFac.hrname, sysDetail.sysDescription FROM sysDetail, hrmanFac WHERE sysDetail.sysOID=hrmanFac.hrOID";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<br>";
		echo "<table border=0>";
		echo "<tr>";
		echo "<td width=20>NO</td>";
		echo "<td>ip address</td>";
		echo "<td>System name</td>";
		echo "<td>Manufacture</td>";
		echo "<td>Description</td>";
		//echo "<td>os version</td>";
		//echo "<td>Location</td>";
		//echo "<td>Contact</td>";
		//echo "<td>CPU Load</td>";
		//echo "<td>Total Memory</td>";
		//echo "<td>Memory Used</td>";
		//echo "<td>Memory Free</td>";
		//echo "<td>System up time</td>";
		//echo "<td>Status</td>";
		echo "<td width=50 align='center'>Edit</td>";
		echo "<td width=50 align='center'>Delete</td>";
		echo "</tr>";
		//$i=0;
		while($objResult = mysql_fetch_array($objQuery)){
			$i++;
        	echo "<tr>";
			echo "<td>".$i."</td>";
			/**<td><div align="center"><a href="edit.php?edit_id=<?=$row_show[id]?>">แก้ไข</a> **/
            //echo "<td><a href='status.php?ip_add=$objResult[ipaddress]'>".trim($objResult["sysName"], '"')."</a></td>";			
            //echo "<td><a href='status.php?ip_add=$objResult[ipaddress]'>".$objResult["ipaddress"]."</a></td>";
            echo "<td>".trim($objResult["ipaddress"])."</td>";
            echo "<td>".trim($objResult["sysName"], '"')."</td>";			
			//echo "<td>".$objResult["sysOID"]."</td>";
			echo "<td>".$objResult["hrname"]."</td>";
			echo "<td>".trim($objResult["sysDescription"], '"')."</td>";
            //echo "<td>".$objResult["sysName"]."</td>";
            //echo "<td>".$objResult["sysIOS"]."</td>";
            //echo "<td>".$objResult["sysLocation"]."</td>";
            //echo "<td>".$objResult["sysContact"]."</td>";
			//echo "<td>".$objResult["sysCPULoad"]."</td>";
			//$totalMemory = $objResult["sysMemoryFree"]+$objResult["sysMemoryUse"];
			//echo "<td>".$totalMemory."</td>";
			//echo "<td>".$objResult["sysMemoryFree"]."</td>";
			//echo "<td>".$objResult["sysMemoryUse"]."</td>";  
			//echo "<td>".substr($objResult["sysUpTime"],10)."</td>";
			//echo "<td>UP</td>";
			echo "<td><div align='center'><a href='edit.php?edit_id=<?=$row_show[id]?>'>Edit</a>";
			echo "<td><div align='center'><a href='delete.php?edit_id=<?=$row_show[id]?>'>Delete</a>";
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
