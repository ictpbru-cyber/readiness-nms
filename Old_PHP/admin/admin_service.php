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
		  <a href="admin_service.php">Status </a>
		</li>
		<li class="menu-item">
		  <a href="get-service.php">Add</a>
		</li>
		<li class="menu-item">
		  <a href="#">Test</a>
		</li>
		<li class="menu-item">
		  <a href="logout.php">logout</a>
		</li>
	</ul>


	<?php

		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
	
		$strSQL = "SELECT * FROM sysTCPUDP";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<br>";
		echo "<table border=0>";
		echo "<tr>";
		echo "<td width=20><b>No</b></td>";
		//echo "<td width=20><b>Rec_ID</b></td>";
		echo "<td width=170><b>Server</b></td>";
		echo "<td width=50 align='right'><b>Port</b></td>";
		echo "<td width=100 align='right'><b>Respond Time</b></td>";
		echo "<td witdh=50 align='center'><b>ICMP</b></td>";
		echo "<td width=50 align='center'><b>ICMP6</b></td>";
		echo "<td width=80><b>Status</b></td>";
		echo "<td width=50 align='center'><b>Edit</b></td>";
		echo "<td width=50 align='center'><b>Delete</b></td>";
		echo "</tr>";
		$i=0;
		while($objResult = mysql_fetch_array($objQuery)){
			$i++;
        	echo "<tr>";
			echo "<td>".$i."</td>";
			//echo "<td>".$objResult["hot_Monitor_ID"]."</td>";						
			echo "<td>".$objResult["Server"]."</td>";			
            echo "<td align='right'>".$objResult["Port"]."</td>";
            echo "<td width=100 align='right'>".$objResult["Responded"]."</td>";
			echo "<td width=50 align='center'>".$objResult["ICMP"]."</td>"; 
			echo "<td width=50 align='center'>".$objResult["ICMP6"]."</td>";
			echo "<td width=100 align='left'>".$objResult["Status"]."</td>";
			
            //echo "<td><a href='edit_admin_servic.php?ip_add=$objResult[ipaddress]'>".$objResult["ipaddress"]."</a></td>";
			
            echo "<td align='center'><a href='edit_admin_servic.php?ServerID=$objResult[Server]'>"."Edit"."</a></td>";

			?>
			<td align="center"><a href="JavaScript:if(confirm('Confirm Delete?')==true){window.location='delete_admin_service.php?ServerID=<?php echo $objResult["Server"];?>';}">Delete</a></td>
			<?php
        	echo "</tr>";
        }

        echo "</table>";
		echo "<br>";
		
		mysql_close($objConnect);

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
