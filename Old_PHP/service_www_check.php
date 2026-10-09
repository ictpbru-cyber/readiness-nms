<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<?php

  $addr = my_ip();
  echo "my ip address is $addr\n";

  function my_ip($dest='www.pbru.ac.th', $port=80)
  {
    $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    socket_connect($socket, $dest, $port);
    socket_getsockname($socket, $addr, $port);
    socket_close($socket);
    return $addr;
  }
?>
</body>
</html>