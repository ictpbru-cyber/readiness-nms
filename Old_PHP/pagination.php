<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>t</title>
</head>

<body>

<?php
		$ipaddress = '10.254.1.4';
		$infid = '10101';
		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
		$strSQL = "SELECT COUNT(*) as num FROM sysInterfacein WHERE (ipaddress='".$ipaddress."') and (infid='".$infid."');";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		
		echo $objQuery;
		echo "<br>";
		
		$adjacents = 3;
		$total_pages = mysql_fetch_array(mysql_query($strSQL));
		$total_pages = $total_pages[num];
		$limit = 2;
		$stargetpage = "pagination.php";
		
		echo $total_pages;
		
		$page = $_GET['page'];
		if ($page)
			$start = ($page -1 ) * $limit;
		else
			$start = 0;
			
		$strSQL = "SELECT * FROM sysInterfacein WHERE (ipaddress='".$ipaddress."') and (infid='".$infid."') LIMIT $start, $limit";
		$objQuery = mysql_query($strSQL) or die (mysql_error());
		
		if ($page==0) $page =1;
		$prev = $page - 1;
		$next = $page + 1;
		$lastpage = ceil($total_pages/$limit);
		$lmp1 = $lastpage -1 ;
		
		$pagination = "";
		if($lastpage>1){
			$pagination .= "<div class=\"pagination\">";
			
			if ($page > 1)
				$pagination .= "<a href=\"$stargetpage?page=$prev\">previous</a>";
			else
				$pagination .= "<span class=\"disabled\"></span>";
			if ($lastpage < 7 + ($adjacents))
			{
				for ($counter = 1; $counter <=$lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"$stargetpage?page=$counter\">$counter</a>";
				}
			}
			elseif($lastpage > 5 + ($adjacents*2))
			{
				
		}

?>

</body>
</html>