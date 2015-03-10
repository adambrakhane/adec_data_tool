<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'/>
<style>
table#all_assets {
	border-collapse:collapse;
	border:1px solid black;
}
table#all_assets td {
	border:1px solid black;
}
.deleterow {
	border-radius:4px;
}

#community_list {
	table-layout:fixed;
}
#community_list tr th:nth-child(1) {width:2%; min-width:20px;}
#community_list tr th:nth-child(2) {width:11%; min-width:60px;}
#community_list tr th:nth-child(3) {width:10%; min-width:200px;}
#community_list tr th:nth-child(4) {width:10%; min-width:100px;}
#community_list tr th:nth-child(5) {width:12%; min-width:20px;}
#community_list tr th:nth-child(6) {width:18%; min-width:20px;}
#community_list tr th:nth-child(7) {width:15%; min-width:20px;}

#community_list tr td {
	vertical-align:middle;
}
</style>
<link rel="stylesheet" href="./lib/style.css" />
<link rel="stylesheet" href="./lib/colorbox.css" />
<link rel="stylesheet" href="./lib/bootstrap.min.css">
<link rel="stylesheet" href="./lib/bootstrap-sandstone.css">
<script src="./lib/jquery.min.js"></script>

<script src="./lib/jquery.colorbox.js"></script>

</head>
<body>
<div class="navbar navbar-default navbar-fixed-top">
  <div class="container">
	<div class="navbar-header">
	  <a href="../" class="navbar-brand">ADECdatos</a>
	  <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	</div>
	<div class="navbar-collapse collapse" id="navbar-main">
	  <ul class="nav navbar-nav">
		<li>
		  <a href="/">Página Principal</a>
		</li>
		<li>
		  <a href="/informe.php">Informe</a>
		</li>
		<li>
		  <a href="/mes.php">Este Mes</a>
		</li>
		<li class="dropdown">
		  <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="ajustes">Ajustes <span class="caret"></span></a>
		  <ul class="dropdown-menu" aria-labelledby="ajustes">
			<li><a href="#"><?=$cr_name;?>'s perfil</a></li>
			<li class="divider"></li>
			<li><a href="#">Test</a></li>
		  </ul>
		</li>
	  </ul>

	  <ul class="nav navbar-nav navbar-right">
		<li><a href="#" target="_blank">Salir</a></li>
		<li><a href="#" target="_blank">Ayuda</a></li>
	  </ul>

	</div>
  </div>
</div>
	
<div class="container" style="margin-top:20px;">