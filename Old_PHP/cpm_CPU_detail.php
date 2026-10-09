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


	<?php

	if($_REQUEST['ipaddress_fw'] != "")
	{

		$ipaddress=$_REQUEST['ipaddress_fw'];
		echo "<br>";
			
		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
		/**
		//select sysCPUload, timestamp from cpmCPULoad where ipaddress='10.254.1.1';
		$strSQL = "SELECT ipaddress,sysCPUload,timestamp FROM cpmCPULoad WHERE ipaddress='".$ipaddress."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<table border=1>";
		echo "<tr>";
		echo "<td width='150'> ip address </td>";
		echo "<td width='150'> CPU Load </td>";
		echo "<td witdh='100'> Date-Time </td>";
		echo "</tr>";
		while($objResult = mysql_fetch_array($objQuery)){
        	echo "<tr>";
			echo "<td>".$objResult["ipaddress"]."</td>";
			echo "<td>".$objResult["sysCPUload"]."</td>";  
			echo "<td>".$objResult["timestamp"]."</td>";  
        	echo "</tr>";
        }
        echo "</table>";
		echo "<br>";
		
			$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
			$objDB = mysql_select_db("readiness");
			//$strSQL = "SELECT * FROM cpmCPULoad WHERE ipaddress='".$ipaddress."';";
			$strSQL = "SELECT * FROM cpmCPULoad WHERE (date(timestamp)=curdate()) and (ipaddress='".$ipaddress."');";
			$objQuery = mysql_query($strSQL) or die (mysql_error());
	
			$Num_Rows = mysql_num_rows($objQuery);
	
			$Per_Page = 25;   // Per Page
			$Page = $_GET["Page"];
	
			if(!$_GET["Page"])
			{		
				$Page=1;		
			}
	
			$Prev_Page = $Page-1;
			$Next_Page = $Page+1;
			$Page_Start = (($Per_Page*$Page)-$Per_Page);
	
			if($Num_Rows<=$Per_Page)
			{
			$Num_Pages =1;
			}
			else if(($Num_Rows % $Per_Page)==0)
			{
			$Num_Pages =($Num_Rows/$Per_Page) ;
			}
			else
			{
			$Num_Pages =($Num_Rows/$Per_Page)+1;
			$Num_Pages = (int)$Num_Pages;
			}
			
			$strSQL = "SELECT * FROM cpmCPULoad WHERE (date(timestamp)=curdate()) and (ipaddress='".$ipaddress."')";
			//$strSQL = "SELECT * FROM cpmCPULoad WHERE ipaddress='".$ipaddress."'";
			$strSQL.= "LIMIT $Page_Start , $Per_Page;";
			$objQuery  = mysql_query($strSQL);
	
			echo "<table border=1>";
			echo "<tr>";
			echo "<td width='50'> NO </td>";
			echo "<td width='150'> ip address </td>";
			echo "<td width='150'> CPU Load </td>";
			echo "<td witdh='100'> Date-Time </td>";
			echo "</tr>";
			$i=1;
			while($objResult = mysql_fetch_array($objQuery)){
				echo "<tr>";
				echo "<td >".$i."</td>";
				echo "<td>".$objResult["ipaddress"]."</td>";
				echo "<td>".$objResult["sysCPUload"]."</td>";  
				echo "<td>".$objResult["timestamp"]."</td>";  
				echo "</tr>";
				$i++;
			}
			echo "</table>";
	
			echo "<br>";
	
			echo "<table border=\"0\">";
			echo "<tr>";
			echo "<td width=200>Total : ".$Num_Rows." Record : ".$Num_Pages." Page :</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>";
			if($Prev_Page)
			{
				echo " <a href='$_SERVER[SCRIPT_NAME]?Page=$Prev_Page&ipaddress_fw=$ipaddress'>< Back</a> ";
			}
			for($i=1; $i<=$Num_Pages; $i++)
			{
				if($i != $Page)
				{
				echo "[ <a href='$_SERVER[SCRIPT_NAME]?Page=$i&ipaddress_fw=$ipaddress'>$i</a> ]";
				}
				else
				{
					echo "<b> $i </b>";
				}
			}
			if($Page!=$Num_Pages)
			{
				echo " <a href ='$_SERVER[SCRIPT_NAME]?Page=$Next_Page&ipaddress_fw=$ipaddress'>Next></a> ";
			}
			echo "</td>";
			echo "</tr>";
			echo "</table>";	
			**/

			$strSQL1 = "select ipaddress, avg(sysCPUload) as sum_avg, timestamp from cpmCPULoad where date(timestamp)=curdate() and ipaddress='".$ipaddress."' group by hour(timestamp);";
			$objQuery1 = mysql_query($strSQL1) or die (mysql_error());

			$strSQL2 = "select ipaddress, avg(sysCPUload) as sum_avg, timestamp from cpmCPULoad where date(timestamp)>=date_add(curdate(),interval -7 day) and date(timestamp)<curdate() and ipaddress='".$ipaddress."' group by day(timestamp);";
			$objQuery2 = mysql_query($strSQL2) or die (mysql_error());

			$strSQL3 = "select ipaddress, avg(sysCPUload) as sum_avg, timestamp from cpmCPULoad where date(timestamp)>=date_add(curdate(),interval -1 month) and date(timestamp)<=curdate() and ipaddress='".$ipaddress."' group by month(timestamp);";
			$objQuery3 = mysql_query($strSQL3) or die (mysql_error());
			/**
			$strSQL4 = "select ipaddress, avg(sysCPUload) as sum_avg, timestamp from cpmCPULoad where date(timestamp)>=date_add(curdate(),interval -1 year) and date(timestamp)<=curdate() and ipaddress='".$ipaddress."' group by month(timestamp);";			
			$objQuery4 = mysql_query($strSQL4) or die (mysql_error());
			**/

			echo "<b>All Day(Hour) CPU Load Detail</b>";
			echo "<table border=1>";
			echo "<tr>";
			echo "<td width='50'>NO</td>";
			echo "<td width='50'>ipaddress</td>";
			echo "<td width='100'>CPU avg</td>";
			echo "<td witdh='100'>date-time</td>";
			echo "</tr>";
			$i=1;
			while($objResult = mysql_fetch_array($objQuery1)){
				echo "<tr>";
				echo "<td >".$i."</td>";
				echo "<td>".$objResult["ipaddress"]."</td>";
				echo "<td>".number_format($objResult["sum_avg"],2)."</td>";
				echo "<td>".$objResult["timestamp"]."</td>";  
				echo "</tr>";
				$i++;
			}
			echo "</table>";
			echo "<br>";

			echo "<b>Last 7 Day</b>";
			echo "<table border=1>";
			echo "<tr>";
			echo "<td width='50'>NO</td>";
			echo "<td width='50'>ipaddress</td>";
			echo "<td width='100'>CPU avg</td>";
			echo "<td witdh='100'>date-time</td>";
			echo "</tr>";
			$i=1;
			while($objResult = mysql_fetch_array($objQuery2)){
				echo "<tr>";
				echo "<td>".$i."</td>";  
				echo "<td>".$objResult["ipaddress"]."</td>";
				echo "<td>".number_format($objResult["sum_avg"],2)."</td>";
				echo "<td>".$objResult["timestamp"]."</td>";  
				echo "</tr>";
				$i++;
			}
			echo "</table>";
			echo "<br>";

			echo "<b>All Month</b>";
			echo "<table border=1>";
			echo "<tr>";
			echo "<td width='50'>NO</td>";
			echo "<td width='50'>ipaddress</td>";
			echo "<td width='100'>CPU avg</td>";
			echo "<td witdh='100'>date-time</td>";
			echo "</tr>";
			$i=1;
			while($objResult = mysql_fetch_array($objQuery3)){
				echo "<tr>";
				echo "<td>".$i."</td>";  
				echo "<td>".$objResult["ipaddress"]."</td>";
				echo "<td>".number_format($objResult["sum_avg"],2)."</td>";
				echo "<td>".$objResult["timestamp"]."</td>";  
				echo "</tr>";
				$i++;
			}
			echo "</table>";
			echo "<br>";

			/**
			echo "<b>Year</b>";
			echo "<table border=1>";
			echo "<tr>";
			echo "<td width='50'>NO</td>";
			echo "<td width='50'>ipaddress</td>";
			echo "<td width='100'>CPU avg</td>";
			echo "<td witdh='100'>date-time</td>";
			echo "</tr>";
			$i=1;
			while($objResult = mysql_fetch_array($objQuery4)){
				echo "<tr>";
				echo "<td>".$i."</td>";  
				echo "<td>".$objResult["ipaddress"]."</td>";
				echo "<td>".number_format($objResult["sum_avg"],2)."</td>";
				echo "<td>".$objResult["timestamp"]."</td>";  
				echo "</tr>";
				$i++;
			}
			echo "</table>";
			**/
			echo "<br>";			
				
		mysql_close($objConnect);
		
	}//end if
    ?> 
    
	<br>
    <div class="footer">
    <?php
      	include 'getClientEnv.php';
    ?>
    </div>
    
  </div>
</div>
</body>
</html>
