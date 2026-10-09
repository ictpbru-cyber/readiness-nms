<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>test-snmp-1</title>
</head>

<body>

<?php
	$time_start = microtime(true);
	$reponse = snmpget('10.254.1.121', 'public', "1.3.6.1.2.1.1.3.0",1000000,5);
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	$time = number_format($time,0);
	echo "Delay in $time secondes\n";
	echo "<br>";
	
	if ($time <> 0) {
		echo $time." Connection Error";
	}
	if ($time == 0) {
		echo $time." Connection Succes";

	}
?>
<?php
	$time_start = microtime(true);
	$reponse = snmpget('192.168.18.2', 'public', "1.3.6.1.2.1.1.1.0",1000000,5);
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	$time = number_format($time,0);
	echo "Delay in $time secondes\n";
	echo "<br>";
	
	if ($time <> 0) {
		echo $time." Connection Error";
	}
	if ($time == 0) {
		echo $time." Connection Succes";

	}
?>

</body>
</html>