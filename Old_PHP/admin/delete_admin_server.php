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

<?php
	ini_set('display_errors', 1);
	error_reporting(~0);

	$serverName = "localhost";
	$userName = "nms";
	$userPassword = "permissions";
	$dbName = "readiness";
	$conn = mysqli_connect($serverName,$userName,$userPassword,$dbName);

	$strServerID = $_GET["ServerID"];

	$sql = "DELETE FROM hrDevice WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrDetail WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrCPULoad WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrCPUDetail WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrStorage WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrStorageIndex WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrStorageGOID WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrStorageDescr WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrStorageUnits WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrStorageSize WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM hrStorageUsed WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);
	
	$sql = "DELETE FROM sysInterface WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM sysInterfaceIndex WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM sysInterfaceSpeed WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM sysInterfacein WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM sysInterfaceout WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM sysInterfaceerror WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM sysinfAdminstatus WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	$sql = "DELETE FROM sysinfOperstatus WHERE ipaddress = '".$strServerID."' ";
	$query = mysqli_query($conn,$sql);

	if(mysqli_affected_rows($conn)) {
		 echo "Record delete successfully";
	}

	mysqli_close($conn);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="0; url=http://readiness.pbru.ac.th/admin/admin_server.php">
<title>Readiness Project</title>
</head>

<body>
</body>
</html>