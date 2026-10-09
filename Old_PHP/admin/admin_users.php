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
		  <a href="admin_system.php">Manufacturers</a>
		</li>
		<li class="menu-item">
		  <a href="admin_users.php">User Management</a>
		</li>
		<li class="menu-item">
		  <a href="logout.php">logout</a>
		</li>
	</ul>


	<?php	

		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
				
		$strSQL = "SELECT * FROM member";		
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		echo "<br>";
		echo "<table border=0>";
		while($objResult = mysql_fetch_array($objQuery)){
        	echo "<tr>";
			echo "<td width='40'>".$objResult["UserID"]."</td>";
			echo "<td width='40'>".$objResult["Username"]."</td>";
			echo "<td width='80'>".$objResult["Password"]."</td>";
			echo "<td width='150'>".$objResult["Name"]."</td>";
			//echo "<td>".$objResult["Status"]."</td>";		
			echo "<td><div align='center'>|<a href='add_admin_users.php'>Add</a></td>";
			echo "<td><div align='center'>|<a href='edit_admin_users.php?ServerID=$objResult[UserID]'>Edit</a></td>";
			?>
			<td align="center">|<a href="JavaScript:if(confirm('Confirm Delete?')==true){window.location='delete_admin_users.php?ServerID=<?php echo $objResult["UserID"];?>';}">Delete</a>|</td>
			<?php
        	echo "</tr>";
        }
        echo "</table>";

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
