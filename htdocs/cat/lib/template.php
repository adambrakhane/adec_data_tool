<?php
class Template {
	function ScreenSmallHead() {
		$out = '';
		$out .= '<!DOCTYPE html><html><head>';
		$out .= '<meta charset="utf-8">';
		$out .= '<link rel="stylesheet" type="text/css" href="./lib/style.css">';
		$out .= '<link rel="stylesheet" href="lib/bootstrap.min.css">';
		//$out .= '<link rel="stylesheet" href="lib/bootstrap-theme.min.css">';
		$out .= '<script src="lib/jquery.min.js"></script>';
		$out .= '<script src="lib/jquery.animate-colors-min.js"></script>';
		$out .= '<script type="text/javascript" src="lib/jquery.simple-dtpicker.js"></script>';
		$out .= '<link type="text/css" href="lib/jquery.simple-dtpicker.css" rel="stylesheet" />';
		$out .= '<link type="text/css" href="//cdn.datatables.net/1.10.5/css/jquery.dataTables.min.css" rel="stylesheet" />';
		$out .= '</head><body class="screenSmall">';
		
		return $out;
	}
	function ScreenSmallFoot() {
		$out = '<footer class="footer"><div class="container well"><p class="text-muted">Si necesita ayuda para utilizar este programa, usted puede enviar un email <a href="mailto:adam.brakhane@gmail.com">adam.brakhane@gmail.com</a></p></div></footer>';
		$out .= '</body>';
		$out .= '</html>';
		
		return $out;
	}
	function ScreenLargeHead($is_admin=false) {
$out = <<<'EOD'
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="./lib/style.css" />
<link rel="stylesheet" href="./lib/colorbox.css" />
<link rel="stylesheet" href="./lib/bootstrap.min.css">
<link rel="stylesheet" href="./lib/bootstrap-theme.min.css">
<link type="text/css" href="lib/jquery.simple-dtpicker.css" rel="stylesheet" />

</head>
<body>
	<!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">comEval</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
			<li class="active"><a href="./">Página Principal</a></li>
            <li><a class="" href="./form.php?p=insert_community">Añadir Comunidad</a></li>
EOD;
if($is_admin) {
$out .= <<<'EOD'
            
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Crear Resumenes <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a class="" href="./basic_report.php?p=by_circuit_rider">Por Circuit Rider</a></li>
				<li><a  href="./basic_report.php?p=by_location">Por Ubicación</a></li>
				<li class="divider"></li>
				<li class="dropdown-header">Nav header</li>
				<li><a href="#">Separated link</a></li>
				<li><a href="#">One more separated link</a></li>
			</ul>
		</li>
EOD;
	  }
$out .= <<<'EOD'
          </ul>
	<ul class="nav navbar-nav navbar-right">
		<li><a class="" href="./logout.php">Cerrar Sesion</a></li>
		<li><a class="" href="./form.php?p=profile">Mi Perfil</a></li>
EOD;
if($is_admin) {
$out .= <<<'EOD'
            
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Administración <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a class="" href="./form.php?p=insert_cr">Añadir Usuario</a></li>
				<!--<li><a href="#">Another action</a></li>
				<li><a href="#">Something else here</a></li>
				<li class="divider"></li>
				<li class="dropdown-header">Nav header</li>
				<li><a href="#">Separated link</a></li>
				<li><a href="#">One more separated link</a></li>-->
			</ul>
		</li>
EOD;
	  }
$out .= <<<'EOD'
	</ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
EOD;

	return $out;
	}
	
	function circuit_rider_dropdown($db) {
		echo '<select id="circuit_rider_id" name="circuit_rider_id" class="form-control">';
		$cr_list = $db->listAll('circuit_riders');
		foreach($cr_list as $cr) {
			echo '<option value="'.$cr['id'].'">'.$cr['first_name'].' '.$cr['last_name'].'</option>';
		}
		echo '</select>';
	}
	function scriptIncludes() {
		$out = <<<'EOD'
<script src="./lib/jquery.min.js"></script>
<script src="./lib/bootstrap.min.js"></script>
<script src="./lib/jquery.autocomplete.js"></script>
<script src="./lib/jquery.colorbox.js"></script>
<script src="./lib/jquery.animate-colors-min.js"></script>
<script src="./lib/jquery.simple-dtpicker.js"></script>
<script src="./lib/eval_form.js"></script>
<script src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.js"></script>
EOD;
	return $out;
	}
}
?>