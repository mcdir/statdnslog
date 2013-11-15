<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="images/favicon.ico">
<title>Dns (Bind) Log Analyzer</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/starter-template.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<link href="css/table_jui.css" rel="stylesheet">
<link href="css/table.css" rel="stylesheet">
</head>
<body>
	<!-- nav -->
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">StatDNS</a>
			</div>
			<div class="collapse navbar-collapse" id="nav_menu">
				<ul class="nav navbar-nav" id='navbar-nav'>
					<li class="active"><a href="#home" id="home">Last query</a></li>
					<li><a href="#GroupByDNS" id="#GroupByDNS">Count query, group by DNS</a></li>
				</ul>
			</div>
			<!--/.nav-collapse -->
		</div>
	</div>
	<!-- /nav -->
	<div class="container">
		<div class="starter-template">
			<h1>StatDNS</h1>
			<div id="part_home">
			<label>Show banned only </label>
			<input type="checkbox" name="bannedOnly" id="bannedOnly" value="">
			<div id="dt_example">
				<div id="container">
					<div id="dynamic">
						<table cellpadding="0" cellspacing="0" border="0" class="display" id="dnslist">
							<thead>
								<tr>
									<th width="15%">Date</th>
									<th width="20%">Time</th>
									<th width="20%">DNS</th>
									<th width="15%">Client ip</th>
									<th width="15%">Client port</th>
									<th width="15%">Querys</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="5" class="dataTables_empty">Loading data from server</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th><input type="text" name="search_date" value="date" placeholder="Search date" class="search_init" /></th>
									<th><input type="text" name="search_date_time" value="datetime" placeholder="Search datetime" class="search_init" /></th>
									<th><input type="text" name="search_dns" value="Search dns" placeholder="Search dns" class="search_init" /></th>
									<th><input type="text" name="search_client_ip" value="Search client ip" placeholder="Search client ip" class="search_init" /></th>
									<th><input type="text" name="search_client_port" value="Search client port" placeholder="Search client port" class="search_init" /></th>
									<th><input type="text" name="search_querys" value="Search querys" placeholder="Search querys" class="search_init" /></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			</div>
			<!-- /part_home -->
			<div id="part_GroupByDNS">
			<label>Show banned only </label>
			<input type="checkbox" name="bannedOnly" id="bannedOnlyGroup" value="">
			<div id="dt_example">
				<div id="container">
					<div id="dynamic">
						<table cellpadding="0" cellspacing="0" border="0" class="display" id="GroupByDNS">
							<thead>
								<tr>
									<th width="15%">Date</th>
									<th width="20%">DNS</th>
									<th width="15%">Client ip</th>
									<th width="15%">Count DNS</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="5" class="dataTables_empty">Loading data from server</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th><input type="text" name="search_date" value="date" placeholder="Search date" class="search_init" /></th>
									<th><input type="text" name="search_dns" value="Search dns" placeholder="Search dns" class="search_init" /></th>
									<th><input type="text" name="search_client_ip" value="Search client ip" placeholder="Search client ip" class="search_init" /></th>
									<th>&nbsp;</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			</div>
			<!-- /part_home -->

		</div>
	</div>
	<!-- /.container -->
	<!-- Bootstrap core JavaScript 	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="js/jquery-1.10.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/fnReloadAjax.js"></script>
	<script type="text/javascript" language="javascript" src="js/statdns.js"></script>
</body>
</html>