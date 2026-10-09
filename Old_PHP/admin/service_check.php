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
	
	//$ipaddress=$_REQUEST['ipaddress_fw'];
	//$infid=$_REQUEST['infid_fw'];

	//echo "Data request ipaddress : ".$ipaddress."<br>";
	//echo "Data request infid: ".$infid."<br>";	

        function ping($host, $timeout = 1) {
                /* ICMP ping packet with a pre-calculated checksum */
                $package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
                $socket  = socket_create(AF_INET, SOCK_RAW, 1);
                socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
                socket_connect($socket, $host, null);

                $ts = microtime(true);
                socket_send($socket, $package, strLen($package), 0);
                if (socket_read($socket, 255))
                        $result = microtime(true) - $ts;
                else    $result = false;
                socket_close($socket);

                return $result;
        }
		


		function ping($host) {
    		$package = "\x08\x00\x19\x2f\x00\x00\x00\x00\x70\x69\x6e\x67";

    		/* create the socket, the last '1' denotes ICMP */    
			$socket = socket_create(AF_INET, SOCK_RAW, 1);
    
    		/* set socket receive timeout to 1 second */
    		socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 1, "usec" => 0));
    
    		/* connect to socket */
    		socket_connect($socket, $host, null);
    
    		/* record start time */
    		list($start_usec, $start_sec) = explode(" ", microtime());
    		$start_time = ((float) $start_usec + (float) $start_sec);
    
    		socket_send($socket, $package, strlen($package), 0);
    
    		if(@socket_read($socket, 255)) {
        		list($end_usec, $end_sec) = explode(" ", microtime());
        		$end_time = ((float) $end_usec + (float) $end_sec);
    
        		$total_time = $end_time - $start_time;
        
        		return $total_time;
    		} else {
        		return false;
    		}
    
    		socket_close($socket);
			}

		
		
	?>    
    
  </div>
</div>
</body>
</html>
