<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="refresh" content="80">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="boilerplate.css" rel="stylesheet" type="text/css">
<link href="css/fluidGird.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Computer Center</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="description" content="Demo of Drop-Down Navigation: Touch-Friendly and Responsive" />
<meta name="robots" content="all">
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="canonical" href="test-menu3">
<link rel="icon" href="images/favicon.ico" />
<link rel="stylesheet" href="css/main.css" />
<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400">
<script src="respond.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/menu.css"/>	

	<style>

		body
		{
			background-color: #f7efeb;
			padding: 1.25em; /* 20 */
		}

		#nav
		{
			width: 60em; /* 1000 */
			font-family: 'Open Sans', sans-serif;
			font-weight: 400;
			position: absolute;
			top: 0%;
			left: 	30%;
			margin-left: -30em; /* 30 480 */
		}

			#nav > a
			{
				display: none;
			}

			#nav li
			{
				position: relative;
			}
				#nav li a
				{
					color: #fff;
					display: block;
				}
				#nav li a:active
				{
					background-color: #c00 !important;
				}

			#nav span:after
			{
				width: 0;
				height: 0;
				border: 0.313em solid transparent; /* 5 */
				border-bottom: none;
				border-top-color: #efa585;
				content: '';
				vertical-align: middle;
				display: inline-block;
				position: relative;
				right: -0.313em; /* 5 */
			}

			/* first level */

			#nav > ul
			{
				height: 3.75em; /* 60 */
				background-color: #e15a1f;
			}
				#nav > ul > li
				{
					width: 25%;
					height: 100%;
					float: left;
				}
					#nav > ul > li > a
					{
						height: 100%;
						font-size: 1.5em; /* 24 */
						line-height: 2.5em; /* 60 (24) */
						text-align: center;
					}
						#nav > ul > li:not( :last-child ) > a
						{
							border-right: 1px solid #cc470d;
						}
						#nav > ul > li:hover > a,
						#nav > ul:not( :hover ) > li.active > a
						{
							background-color: #cc470d;
						}

				/* second level */

				#nav li ul
				{
					background-color: #cc470d;
					display: none;
					position: absolute;
					top: 100%;
				}
					#nav li:hover ul
					{
						display: block;
						left: 0;
						right: 0;
					}
						#nav li:not( :first-child ):hover ul
						{
							left: -1px;
						}
						#nav li ul a
						{
							font-size: 1.25em; /* 20 */
							border-top: 1px solid #e15a1f;
							padding: 0.75em; /* 15 (20) */
						}
							#nav li ul li a:hover,
							#nav li ul:not( :hover ) li.active a
							{
								background-color: #e15a1f;
							}


		@media only screen and ( max-width: 62.5em ) /* 1000 */
		{
			#nav
			{
				width: 100%;
				position: static;
				margin: 0;
			}
		}

		@media only screen and ( max-width: 40em ) /* 640 */
		{
			html
			{
				font-size: 75%; /* 12 */
			}

			#nav
			{
				position: relative;
				top: auto;
				left: auto;
			}
				#nav > a
				{
					width: 3.125em; /* 50 */
					height: 3.125em; /* 50 */
					text-align: left;
					text-indent: -9999px;
					background-color: #e15a1f;
					position: relative;
				}
					#nav > a:before,
					#nav > a:after
					{
						position: absolute;
						border: 2px solid #fff;
						top: 35%;
						left: 25%;
						right: 25%;
						content: '';
					}
					#nav > a:after
					{
						top: 60%;
					}

				#nav:not( :target ) > a:first-of-type,
				#nav:target > a:last-of-type
				{
					display: block;
				}


			/* first level */

			#nav > ul
			{
				height: auto;
				display: none;
				position: absolute;
				left: 0;
				right: 0;
			}
				#nav:target > ul
				{
					display: block;
				}
				#nav > ul > li
				{
					width: 100%;
					float: none;
				}
					#nav > ul > li > a
					{
						height: auto;
						text-align: left;
						padding: 0 0.833em; /* 20 (24) */
					}
						#nav > ul > li:not( :last-child ) > a
						{
							border-right: none;
							border-bottom: 1px solid #cc470d;
						}


				/* second level */

				#nav li ul
				{
					position: static;
					padding: 1.25em; /* 20 */
					padding-top: 0;
				}
		}

	</style>
</head>
<body>
<div class="gridContainer clearfix">
  <div id="LayoutDiv1">
  	<!--
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
	-->
    <nav id="nav" role="navigation">
        <a href="#nav" title="Show navigation">Show navigation</a>
        <a href="#" title="Hide navigation">Hide navigation</a>
        <ul class="clearfix">
            <li><a href="">Home</a></li>
            <li>
                <a href="" aria-haspopup="true"><span>Network</span></a>
                <ul>
                    <li><a href="">Add New Device</a></li>
                    <li><a href="">Edit</a></li>
                    <li><a href="">Test</a></li>
                    <li><a href="">JavaScript</a></li>
                </ul>
            </li>
            <li>
                <a href="" aria-haspopup="true"><span>Server</span></a>
                <ul>
                    <li><a href="">Web Design</a></li>
                    <li><a href="">Typography</a></li>
                    <li><a href="">Front-End</a></li>
                </ul>
            </li>
            <li>
                <a href="" aria-haspopup="true"><span>Service</span></a>
                <ul>
                    <li><a href="">Web Design</a></li>
                    <li><a href="">Typography</a></li>
                    <li><a href="">Front-End</a></li>
                </ul>
            </li>
            <!-- li><a href="">About</a></li -->
        </ul>
    </nav>
   </div>
   </div>
  </body>
</html>
