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
    
        <form name="form1" method="post" action="save_system_users.php">
          <br><b>Add System Manufacturers</b><br>
          <table border="1" style="width: 250px">
            <tbody>
              <tr>
                <td> &nbsp;User Name</td>
                <td>
                  <input name="txtUsername" type="text" id="txtUsername">
                </td>
              </tr>
              <tr>
                <td> &nbsp;Password</td>
                <td><input name="txtPassword" type="text" id="txtPassword">
                </td>
              </tr>
              <tr>
                <td> &nbsp;Full Name</td>
                <td><input name="txtName" type="text" id="txtName">
                </td>
              </tr>
              <tr>
                <td> &nbsp;Status</td>
                <td><input name="txtStatus" type="text" id="txtStatus" value="ADMIN" disabled>
                </td>
              </tr>
            </tbody>
          </table>
          <br>
          <input type="submit" name="Submit" value="Save">
          &nbsp;<input type="reset" value="Reset" name="reset">
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



