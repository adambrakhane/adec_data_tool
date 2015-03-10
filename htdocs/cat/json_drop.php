<?php
require_once('./lib/template.php');
require_once('./lib/mysql.php');
require_once('./lib/db.php');
require_once('./lib/session.php');
if(!isset($_GET['p'])) {
	echo "This page is not directly accessable.";
	echo "<pre>";
	print_r($_GET);
	print_r($_POST);
	echo "</pre>";
	
	die();
}

$tpl = new Template;


try {
	$db = new Db;
}
catch (Exception $e) {
	echo "<h2>Error (".$e->getCode().")</h2>";
	echo "<p>Action Set: ".$_GET['p']."<br>".$e->getMessage()."</p>";
}

if($_GET['p']=='AC_user_roles') {
	$out = array();
	$out["query"] = "circuit_rider_roles";
	$out["suggestions"][]["value"] = "Administrador";
	$out["suggestions"][]["value"] = "Circuit Rider";
	echo json_encode($out);
}
if($_GET['p']=='AC_junta_roles') {
	$out = array();
	$out["query"] = "junta_roles";
	$out["suggestions"][]["value"] = "Presidente";
	$out["suggestions"][]["value"] = "Vice-Presidente";
	$out["suggestions"][]["value"] = "Secretario";
	$out["suggestions"][]["value"] = "Tesorero";
	$out["suggestions"][]["value"] = "Fiscal";
	$out["suggestions"][]["value"] = "Vocal 1";
	$out["suggestions"][]["value"] = "Vocal 2";
	$out["suggestions"][]["value"] = "Vocal 3";
	$out["suggestions"][]["value"] = "Otro";
	echo json_encode($out);
}
else if($_GET['p']=='AC_GPS_locations') {
	$out = array();
	$out["query"] = "junta_roles";
	$out["suggestions"][]["value"] = "Fuente de Agua";
	$out["suggestions"][]["value"] = "Fuente de Agua (#2)";
	$out["suggestions"][]["value"] = "Tanque";
	$out["suggestions"][]["value"] = "Tanque (#2)";
	$out["suggestions"][]["value"] = "Primera Casa";
	$out["suggestions"][]["value"] = "Casa Media";
	$out["suggestions"][]["value"] = "Ultima Casa";
	$out["suggestions"][]["value"] = "Casa mas Alta";
	$out["suggestions"][]["value"] = "Casa mas Baja";
	echo json_encode($out);
}