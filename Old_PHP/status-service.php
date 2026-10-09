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
<link rel="stylesheet" type="text/css" href="css/menu.css"/>
<style type="text/css"> 
<!--
	.paginate {
	font-family: Arial, Helvetica, sans-serif;
	font-size: .7em;
	}
	a.paginate {
	border: 1px solid #000080;
	padding: 2px 6px 2px 6px;
	text-decoration: none;
	color: #000080;
	}
	h2 {
		font-size: 12pt;
		color: #003366;
		}
		
		 h2 {
		line-height: 1.2em;
		letter-spacing:-1px;
		margin: 0;
		padding: 0;
		text-align: left;
		}
	a.paginate:hover {
	background-color: #000080;
	color: #FFF;
	text-decoration: underline;
	}
	a.current {
	border: 1px solid #000080;
	font: bold .7em Arial,Helvetica,sans-serif;
	padding: 2px 6px 2px 6px;
	cursor: default;
	background:#000080;
	color: #FFF;
	text-decoration: none;
	}
	span.inactive {
	border: 1px solid #999;
	font-family: Arial, Helvetica, sans-serif;
	font-size: .7em;
	padding: 2px 6px 2px 6px;
	color: #999;
	cursor: default;
	}
-->
</style>
</head>
<body>

<div class="gridContainer clearfix">
  <div id="LayoutDiv1">
 
 	<ul class="menu">
		<li class="menu-item">
		  <a href="index.php">Home</a>
		</li>
		<li class="menu-item">
		  <a href="network.php">Network</a>
		</li>
		<li class="menu-item">
		  <a href="server.php">Server</a>
		</li>
		<li class="menu-item">
		  <a href="service.php">Service</a>
		</li>
	</ul> 
  
	<?php
	
	/**

	if($_REQUEST['server_add'] != "")
	{
		$rsServer = $_REQUEST['server_add'];
		$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		$objDB = mysql_select_db("readiness");
		//echo $rsServer;		
		$strSQL = "SELECT * FROM service_tcp_udp_log WHERE trim(Server)='".trim($rsServer)."';";
		$objQuery = mysql_query($strSQL) or die (mysql_error());

		echo "<br>";
		echo "<table border=1>";
		echo "<tr>";
		echo "<td>Server</td>";
		echo "<td>Port</td>";
		echo "<td>Responded</td>";
		echo "<td>ICMP</td>";
		echo "<td>ICMP6</td>";
		echo "<td>Status</td>";
		echo "<td>Date-Time</td>";
		echo "</tr>";
		while($objResult = mysql_fetch_array($objQuery)){
        	echo "<tr>";
            //echo "<td>".$objResult["host_log_id"]."</td>";
            echo "<td>".$objResult["Server"]."</td>";
            echo "<td>".$objResult["Port"]."</td>";
            echo "<td>".$objResult["Responded"]."</td>";
			echo "<td>".$objResult["ICMP"]."</td>"; 
			echo "<td>".$objResult["ICMP6"]."</td>";
			echo "<td>".$objResult["Status"]."</td>";
 			echo "<td>".$objResult["Date_in"]."</td>";
        	echo "</tr>";        
		}

		
		echo "</table>";
		echo "<br>";


		echo "<br>";

		mysql_close($objConnect);
		
	}//end if
	**/
    ?> 


<?php
	if($_REQUEST['server_add'] != "")
	{
?>

<?php
		$rsServer = $_REQUEST['server_add'];
?>

<?php
class Paginator{
	var $items_per_page;
	var $items_total;
	var $current_page;
	var $num_pages;
	var $mid_range;
	var $low;
	var $high;
	var $limit;
	var $return;
	var $default_ipp;
	var $querystring;
	var $url_next;

	function Paginator()
	{
		$this->current_page = 1;
		$this->mid_range = 7;
		$this->items_per_page = $this->default_ipp;
		$this->url_next = $this->url_next;
	}
	function paginate()
	{

		if(!is_numeric($this->items_per_page) OR $this->items_per_page <= 0) $this->items_per_page = $this->default_ipp;
		$this->num_pages = ceil($this->items_total/$this->items_per_page);

		if($this->current_page < 1 Or !is_numeric($this->current_page)) $this->current_page = 1;
		if($this->current_page > $this->num_pages) $this->current_page = $this->num_pages;
		$prev_page = $this->current_page-1;
		$next_page = $this->current_page+1;


		if($this->num_pages > 10)
		{
			$this->return = ($this->current_page != 1 And $this->items_total >= 10) ? "<a class=\"paginate\" href=\"".$this->url_next.$this->$prev_page."\">&laquo; Previous</a> ":"<span class=\"inactive\" href=\"#\">&laquo; Previous</span> ";

			$this->start_range = $this->current_page - floor($this->mid_range/2);
			$this->end_range = $this->current_page + floor($this->mid_range/2);

			if($this->start_range <= 0)
			{
				$this->end_range += abs($this->start_range)+1;
				$this->start_range = 1;
			}
			if($this->end_range > $this->num_pages)
			{
				$this->start_range -= $this->end_range-$this->num_pages;
				$this->end_range = $this->num_pages;
			}
			$this->range = range($this->start_range,$this->end_range);

			for($i=1;$i<=$this->num_pages;$i++)
			{
				if($this->range[0] > 2 And $i == $this->range[0]) $this->return .= " ... ";
				if($i==1 Or $i==$this->num_pages Or in_array($i,$this->range))
				{
					$this->return .= ($i == $this->current_page And $_GET['Page'] != 'All') ? "<a title=\"Go to page $i of $this->num_pages\" class=\"current\" href=\"#\">$i</a> ":"<a class=\"paginate\" title=\"Go to page $i of $this->num_pages\" href=\"".$this->url_next.$i."\">$i</a> ";
				}
				if($this->range[$this->mid_range-1] < $this->num_pages-1 And $i == $this->range[$this->mid_range-1]) $this->return .= " ... ";
			}
			$this->return .= (($this->current_page != $this->num_pages And $this->items_total >= 10) And ($_GET['Page'] != 'All')) ? "<a class=\"paginate\" href=\"".$this->url_next.$next_page."\">Next &raquo;</a>\n":"<span class=\"inactive\" href=\"#\">&raquo; Next</span>\n";
		}
		else
		{
			for($i=1;$i<=$this->num_pages;$i++)
			{
				$this->return .= ($i == $this->current_page) ? "<a class=\"current\" href=\"#\">$i</a> ":"<a class=\"paginate\" href=\"".$this->url_next.$i."\">$i</a> ";
			}
		}
		$this->low = ($this->current_page-1) * $this->items_per_page;
		$this->high = ($_GET['ipp'] == 'All') ? $this->items_total:($this->current_page * $this->items_per_page)-1;
		$this->limit = ($_GET['ipp'] == 'All') ? "":" LIMIT $this->low,$this->items_per_page";
	}

	function display_pages()
	{
		return $this->return;
	}
}
?>

<?php

$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
$objDB = mysql_select_db("readiness");
$strSQL = "SELECT * FROM service_tcp_udp_log WHERE date(Date_in)=curdate() and Server='".$rsServer."'";


$objQuery = mysql_query($strSQL) or die ("Error Query [".$strSQL."]");
$Num_Rows = mysql_num_rows($objQuery);
		
		//$objConnect = mysql_connect("localhost","nms","permissions") or die("Error Connect to Database");
		//$objDB = mysql_select_db("readiness");
		//$strSQL = "SELECT * FROM service_tcp_udp_log WHERE Server = $rsServer;";
		//$objQuery = mysql_query($strSQL) or die (mysql_error());
		//$objQuery = mysql_query($strSQL) or die ("Error Query [".$strSQL."]");
		//$Num_Rows = mysql_num_rows($objQuery);

$Per_Page = 20;   // Per Page

$Page = $_GET["Page"];
if(!$_GET["Page"])
{
	$Page=1;
}

$Prev_Page = $Page-1;
$Next_Page = $Page+1;

$Page_Start = (($Per_Page*$Page)-$Per_Page);
if($Num_Rows<=$Per_Page)
{
	$Num_Pages =1;
}
else if(($Num_Rows % $Per_Page)==0)
{
	$Num_Pages =($Num_Rows/$Per_Page) ;
}
else
{
	$Num_Pages =($Num_Rows/$Per_Page)+1;
	$Num_Pages = (int)$Num_Pages;
}

$strSQL .=" order  by Server ASC LIMIT $Page_Start , $Per_Page";
$objQuery  = mysql_query($strSQL);
?>
<br>
<table border="1">
  <tr>
    <th> NO </th>
    <th> Server </th>
    <th> Port </th>
    <th> Responded </th>
    <th> ICMP </th>
    <th> ICMP6 </th>
    <th> Status </th>
    <th> Date-Time </th>
  </tr>
<?php
$i=0;
while($objResult = mysql_fetch_array($objQuery))
{ $i++;
?>
  <tr>
    <td><?php echo $i;?></td>
    <td><?php echo $objResult["Server"];?></td>
    <td><?php echo $objResult["Port"];?></td>
    <td><?php echo $objResult["Responded"];?></td>
    <td><?php echo $objResult["ICMP"];?></td>
    <td><?php echo $objResult["ICMP6"];?></td>
    <td><?php echo $objResult["Status"];?></td>    
    <td align="right"><?php echo $objResult["Date_in"];?></td>
  </tr>
<?php
}
?>
</table>

<!--
<br>
Total <?php echo $Num_Rows;?> Record 
-->
<br>

<?php
		
$pages = new Paginator;
$pages->items_total = $Num_Rows;
$pages->mid_range = 5;
$pages->current_page = $Page;
$pages->default_ipp = $Per_Page;
$pages->url_next = $_SERVER["PHP_SELF"]."?server_add=$rsServer&querystring=value&Page=";

$pages->paginate();

echo $pages->display_pages();
?>		

<?php
mysql_close($objConnect);
?>

<?php
	}//end if
?>
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
