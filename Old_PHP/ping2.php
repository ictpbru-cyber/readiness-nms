<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ping 2tools</title>
</head>

<body>

<?php


function ping_www($host,$port,$timeout)
{
        $fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
        if ( ! $fsock )
        {
                return FALSE;
        }
        else
        {
                return TRUE;
        }
}


$host = 'ct.pbru.ac.th';
$port = '80';
$timeout = '6';
$up = ping_www($host,$port,$timeout);

// if site is up, send them to the site.
if( $up ) {
        //header('Location: http://'.$host); 
		echo $host." "."Up<br>";
}
// otherwise, take them to another one of our sites and show them a descriptive message
else {
        //header('Location: http://www.anothersite.com/some_message');
		echo $host." "."Down<br>";
}

function ping_dns($host,$port=53,$timeout=6)
{
        $fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
        if ( ! $fsock )
        {
                return FALSE;
        }
        else
        {
                return TRUE;
        }
}


$host = 'nsl1.pbru.ac.th';
$up_dns = ping_dns($host);
// if site is up, send them to the site.
if( $up_dns ) {
        //header('Location: http://'.$host); 
		echo $host." "."Up<br>";
}
// otherwise, take them to another one of our sites and show them a descriptive message
else {
        //header('Location: http://www.anothersite.com/some_message');
		echo $host." "."Down<br>";
}

?>

</body>
</html>