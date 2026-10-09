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


</head>
<body>

<div class="gridContainer clearfix">
  <div id="LayoutDiv1">
	<?php
	echo "<br>";
	echo "| <a href=index.php>Home</a> | <a href=#>Server Monitor</a> | <a href=#>Service Monitor</a> |";
	echo "<br>";
	

	if($_REQUEST['ipaddress_fw'] != "")
	{

		$ipaddress=$_REQUEST['ipaddress_fw'];
		echo "<br>";
		/**	
		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
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
		**/
			$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
			$objDB = mysql_select_db("readiness");
			$strSQL = "SELECT * FROM cpmCPULoad WHERE ipaddress='".$ipaddress."';";
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
	
			$strSQL = "SELECT * FROM cpmCPULoad WHERE ipaddress='".$ipaddress."'";
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
		mysql_close($objConnect);

		
	}//end if
	

    ?> 
  </div>
</div>
</body>
</html>
