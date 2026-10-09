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
<link rel="stylesheet" type="text/css" href="css/menu.css"/>
<link rel="stylesheet" type="text/css" href="css/raduis.css"/>
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
	
    <div id="box_css3_raduis">

	<?php

		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
	
		$strSQL = "SELECT * FROM sysTCPUDP ORDER BY Server";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<br>";
		echo "<table border=0>";
			echo "<tr>";
			echo "<td width=20><b>No</b></td>";
			echo "<td width=170><b>Server</b></td>";
			//echo "<td><b>:</b></td>";
			echo "<td width=50><b>Port</b></td>";
			echo "<td width=100><b>Time Respond</b></td>";
			echo "<td witdh=100><b>Connect</b></td>";
			//echo "<td width=50>ICMP6</td>";
			echo "<td width=150><b>Respond Status</b></td>";
			echo "</tr>";
		$i=0;
		while($objResult = mysql_fetch_array($objQuery)){
			$i++;
        	echo "<tr>";
			echo "<td>".$i."</td>";

			/**<td><div align="center"><a href="edit.php?edit_id=<?=$row_show[id]?>">แก้ไข</a> **/
            //echo "<td><a href='status.php?ip_add=$objResult[ipaddress]'>".$objResult["sysName"]."</a></td>";			
            echo "<td width=170><a href='status-service.php?server_add=$objResult[Server]'>".trim($objResult["Server"])."</a></td>";
			//echo "<td>:</td>";
            echo "<td>".$objResult["Port"]."</td>";
            echo "<td width=100>".$objResult["Responded"]."</td>";
			//$connected = 'IPv4/IPv6';
			$icmp4 = intval(strval($objResult["ICMP"]));
			$icmp6 = intval(strval($objResult["ICMP6"]));
			if ($icmp4 == 0 && $icmp6 == 0){
				$aaaaa = "IPv4/IPv6";
				}
			if ($icmp4 == 0 && $icmp6 != 0){
				$aaaaa = 'IPv4'; 
				}
			if ($icmp4 != 0 && $icmp6 == 0){
				$aaaaa = 'IPv6'; 
				}
			if ($icmp4 != 0 && $icmp6 != 0){
				$aaaaa = 'ICMP Error'; 										
				}
			echo "<td width=100>".$aaaaa."</td>";			
			//echo "<td width=50>".$objResult["ICMP"]."</td>"; 
			//echo "<td width=50>".$objResult["ICMP6"]."</td>";
			echo "<td width=100>".$objResult["Status"]."</td>";
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
</div>
</div>

</body>
</html>
