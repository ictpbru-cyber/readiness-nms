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
		  <a href="admin_system.php">Manufacturers</a>
		</li>
		<li class="menu-item">
		  <a href="admin_users.php">User Management</a>
		</li>
		<li class="menu-item">
		  <a href="logout.php">logout</a>
		</li>
	</ul>

    <br>   
    <form action="admin_save_users.php?ServerID=<?php echo $_GET["ServerID"];?>" name="frmEdit" method="post">
    <?php
    $objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
    $objDB = mysql_select_db("readiness");
    $strSQL = "SELECT * FROM member WHERE UserID = '".$_GET["ServerID"]."' ";
    $objQuery = mysql_query($strSQL);
    $objResult = mysql_fetch_array($objQuery);
    if(!$objResult)
    {
        echo "Not found hrid=".$_GET["ServerID"];
    }
    else
    {
    ?>
    <table width="600" border="1">
      <tr>
        <th>UserID</th>
        <th>User Name</th>
        <th>Password</th>
        <th>Full Name</th>
      </tr>
      <tr>
        <td><input type="text" name="txtUserID" size="5" disabled="disabled" value="<?php echo $objResult["UserID"];?>"></td>
        <td><input type="text" name="txtUsername" size="20" value="<?php echo $objResult["Username"];?>"></td>
        <td><input type="text" name="txtPassword" size="25" value="<?php echo $objResult["Password"];?>"></td>
        <td><input type="text" name="txtName" size="25" value="<?php echo $objResult["Name"];?>"></td>
      </tr>
      </table>
      <input type="submit" name="submit" value="submit">

      <?php
      }
      mysql_close($objConnect);
      ?>
      </form>
  
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
