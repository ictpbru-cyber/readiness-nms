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
              <a href="../index.php">Home</a>
            </li>
            <li class="menu-item">
              <a href="../network.php">Network</a>
            </li>
            <li class="menu-item">
              <a href="../server.php">Server</a>
            </li>
            <li class="menu-item">
              <a href="../service.php">Service</a>
            </li>
        </ul>

        <form name="form1" method="post" action="check_login.php">
          <br><b>Login</b><br>
          <table border="1" style="width: 300px">
            <tbody>
              <tr>
                <td> &nbsp;Username</td>
                <td>
                  <input name="txtUsername" type="text" id="txtUsername">
                </td>
              </tr>
              <tr>
                <td> &nbsp;Password</td>
                <td><input name="txtPassword" type="password" id="txtPassword">
                </td>
              </tr>
            </tbody>
          </table>
          <br>
          <input type="submit" name="Submit" value="Login">
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



