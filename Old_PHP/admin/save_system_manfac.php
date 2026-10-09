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
		$data1 = $_POST['txthrOID'];
		$data2 = $_POST['txthrManfac'];
	
		if (($data1!="") and ($data2!=""))
		{

			$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
			$objDB = mysql_select_db("readiness");

			$strSQL = "INSERT INTO hrmanFac ";
			$strSQL .="(hrOID,hrName) ";
			$strSQL .="VALUES ";
			$strSQL .="('".$data1."','".$data2."') ";
			$objQuery = mysql_query($strSQL);						

		mysql_close($objConnect);

		}//end if

	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="0; url=http://readiness.pbru.ac.th/admin/add_admin_hrfac.php">
<title>Readiness Project</title>
</head>

<body>
</body>
</html>