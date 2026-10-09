<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Ping</title>
</head>

<body>

<?php
/**
function ping($host, $port, $timeout) 
{ 
  $tB = microtime(true); 
  $fP = fSockOpen($host, $port, $errno, $errstr, $timeout); 
  if (!$fP) { return "down"; } 
  $tA = microtime(true); 
  return round((($tA - $tB) * 1000), 0)." ms"; 
}

//Echoing it will display the ping if the host is up, if not it'll say "down".


echo ping("www.google.com", 80, 10);
echo "<br>";


function pingAddress($ip) {
    $pingresult = exec("/bin/ping -n 3 $ip", $outcome, $status);
    if (0 == $status) {
        $status = "alive";
    } else {
        $status = "dead";
    }
    echo "The IP address, $ip, is  ".$status;
}

pingAddress("127.0.0.1");
echo "<br>";
pingAddress("10.254.1.1");


    $ipAddress = array("192.168.18.2","192.168.18.3");
    $kiosk = array("Kuantan (35)","UTC Kuantan (36)");

    $checkCount = 0;
    foreach(array_combine($ipAddress,$kiosk) as $items => $kiosk){
        $fp = @fSockOpen($items,80,$errno,$errstr,1);

        if(is_resource($fp)){
            if($fp) { 
                $status=0; 
                fclose($fp); 
                echo 'Success<br/>'; 
            }
        }
        else{
            echo 'Failed<br/>'; 

        }

    }
**/
/**
$retval=-1;
$output=array();
exec("ping 127.0.0.1 -c2 -w2 2>&1",$output,$retval);
echo "Return code: ".$retval."<br>\n";
//echo implode("<br>\n",$output);
echo "<hr>";

$retval=-1;
$output=array();
exec("ping www.pbru.ac.th -c2 -w2 2>&1",$output,$retval);
echo "Return code: ".$retval."<br>\n";
//echo implode("<br>\n",$output);
echo "<hr>";

$retval=-1;
$output=array();
exec("ping6 www.pbru.ac.th -c2 -w2 2>&1",$output,$retval);
echo "Return code: ".$retval."<br>\n";
//echo implode("<br>\n",$output);
echo "<hr>";
**/

$host="www.pbru.ac.th";

$retval=-1;
$output=array();
exec("ping $host -c2 -w2 2>&1",$output,$retval);
echo "Return code: ".$retval."<br>\n";
echo implode("<br>\n",$output);
echo "<hr>";

$retval=-1;
$output=array();
exec("ping6 $host -c2 -w2 2>&1",$output,$retval);
echo "Return code: ".$retval."<br>\n";
echo implode("<br>\n",$output);
echo "<hr>";
?>

</body>
</html>