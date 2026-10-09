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
	


	if($_REQUEST['server_add'] != "")
	{
		$rsServer = $_REQUEST['server_add'];
		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
		//echo $rsServer;		
		$strSQL = "SELECT * FROM service_tcp_udp_log WHERE trim(Server)='".trim($rsServer)."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());

		echo "<br>";
		echo "<table border=1>";
		echo "<tr>";
		echo "<td>Server</td>";
		echo "<td>Port</td>";
		echo "<td>Responded</td>";
		echo "<td>ICMP</td>";
		echo "<td>ICMP6</td>";
		echo "<td>Status</td>";
		echo "<td>Date-Time</td>";
		echo "</tr>";
		while($objResult = mysql_fetch_array($objQuery)){
        	echo "<tr>";
            //echo "<td>".$objResult["host_log_id"]."</td>";
            echo "<td>".$objResult["Server"]."</td>";
            echo "<td>".$objResult["Port"]."</td>";
            echo "<td>".$objResult["Responded"]."</td>";
			echo "<td>".$objResult["ICMP"]."</td>"; 
			echo "<td>".$objResult["ICMP6"]."</td>";
			echo "<td>".$objResult["Status"]."</td>";
 			echo "<td>".$objResult["Date_in"]."</td>";
        	echo "</tr>";        
		}

		
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
