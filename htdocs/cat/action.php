<?php

if(!isset($_GET['p'])) {
	echo "This page is not directly accessable.";
	die();
}
require_once('./lib/template.php');
require_once('./lib/mysql.php');
require_once('./lib/db.php');

$response_type = (isset($_GET['t']) ? $_GET['t'] : "json" );

// Connect to DB
try {
	$db = new Db;
}
catch (Exception $e) {
	echo "<h2>Error (".$e->getCode().")</h2>";
	echo "<p>Action Set: ".$_GET['p']."<br>".$e->getMessage()."</p>";
}
$tpl = new Template;


function removeInvalidFields(&$data,$table) {
	global $db; // Gain access to database object
	$schema = $db->getTableSchema($table);
	foreach($data as $k => $d) {
		if(!in_array($k,$schema))
			unset($data[$k]);
	}
}

if($_GET['p']=='insert_cr') {
	$data = $_POST;
	if($response_type=="screen") {	
		echo $tpl->ScreenSmallHead();
		?>
		<div class="row">
		<div class="col-lg-6 col-lg-offset-3">
		<div id="small_content_wrapper" class="panel panel-info">
		<div class="panel panel-heading"><h1>Circuit Rider Nuevo</h1></div>
		<div class="panel-body">
		<?php
	}
	try {
		removeInvalidFields($data,'circuit_riders');
		$data['password'] = hash('md5',$data['password']);
		switch(strtolower($data['role'])) {
			case "administrador":
				$data['role']=10;
			break;
			case "circuit rider":
				$data['role']=1;
			break;
			default:
				$data['role']=0;
		}
		$newid = $db->insert($data,'circuit_riders');
		?>
		
		<div class="alert alert-success">
		<button type="button" class="btn btn-default close_btn btn-lg" style="float:right;margin-top:30px">Volver</button>
		<h2>Éxito!</h2>
		
		<?php
		echo "<p>Nombre: ".$data['first_name']." ".$data['last_name']."</p>";
		echo "<p>ID: ".$newid."</p>";
		//echo "<p><a href=\"./form.php?p=insert_asset\">Add another</a><br><a href=\"./form.php?p=update_asset&id=".$newid."\">Edit this asset</a></p>";
		echo "</div>";
	}
	catch (Exception $e) {
		echo "<div class=\"alert alert-danger\"><h2>Error ".$e->getCode()."</h2>";
		echo "<p>".$e->getMessage()."</p></div>";
	}
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo $tpl->scriptIncludes();
	?>
	<script>
		$(".close_btn").click(function() {window.location.href = "./";});
	</script>
	<?php
	echo $tpl->ScreenSmallFoot();
}

else if($_GET['p']=='insert_community') {
	$data = $_POST;
	if($response_type=="screen") {
		echo $tpl->ScreenSmallHead();
		?>
		<div class="row">
		<div class="col-lg-6 col-lg-offset-3">
		<div id="small_content_wrapper" class="panel panel-info">
		<div class="panel panel-heading"><h1>Comunidad Nuevo</h1></div>
		<div class="panel-body">
		<?php
	}
	try {
		removeInvalidFields($data,'communities');
		$newid = $db->insert($data,'communities');
		if($response_type=="screen") {
			?>
			<div class="alert alert-success">
			<button type="button" class="btn btn-default close_btn btn-lg" style="float:right;margin-top:30px">Volver</button>
			<h2>Éxito!</h2>
			<?php
			echo "<p>Nombre: ".$data['community']."</p>";
			echo "<p><a href=\"./form.php?p=update_gps&community_id=".$newid."\">Añadir GPS</a><br><a href=\"./form.php?p=update_junta&community_id=".$newid."\">Añadir Junta</a></p>";
			echo "</div>";
		}
	}
	catch (Exception $e) {
		if($response_type=="screen") {
			echo "<div class=\"alert alert-danger\"><h2>Error ".$e->getCode()."</h2>";
			echo "<p>".$e->getMessage()."</p></div>";
		}
	}
	if($response_type=="screen") {
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo $tpl->scriptIncludes();
		?>
		<script>
			$(".close_btn").click(function() {window.location.href = "./";});
		</script>
		<?php
		echo $tpl->ScreenSmallFoot();
	}
}
else if($_GET['p']=='update_community') {
	$data = $_POST;
	if($response_type=="screen") {
		echo $tpl->ScreenSmallHead();
		?>
		<div class="row">
		<div class="col-lg-6 col-lg-offset-3">
		<div id="small_content_wrapper" class="panel panel-info">
		<div class="panel panel-heading"><h1>Editar Comunidad</h1></div>
		<div class="panel-body">
		<?php
	}
	try {
		$community_id = intval($_POST['community_id']);
		removeInvalidFields($data,'communities');
		$status = $db->updateOneById($data,$community_id,"communities");
		if($response_type=="screen") {
			?>
			<div class="alert alert-success">
			<button type="button" class="btn btn-default close_btn btn-lg" style="float:right;margin-top:30px">Volver</button>
			<h2>Éxito!</h2>
			<?php
			echo "<p>Nombre: ".$data['community'].", ".$data['municipality']."</p>";
			echo "<p><a href=\"./form.php?p=update_community&community_id=".$community_id."\">Editar Mas</a><br><a href=\"./form.php?p=update_gps&community_id=".$community_id."\">Añadir GPS</a><br><a href=\"./form.php?p=update_junta&community_id=".$community_id."\">Añadir Junta</a></p>";
			echo "</div>";
		}
	}
	catch (Exception $e) {
		if($response_type=="screen") {
			echo "<div class=\"alert alert-danger\">";
			echo '<button type="button" class="btn btn-default close_btn btn-lg" style="float:right;margin-top:30px">Volver</button>';
			echo "<h2>Error ".$e->getCode()."</h2>";
			echo "<p>".$e->getMessage()."</p></div>";
		}
	}
	if($response_type=="screen") {
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo $tpl->scriptIncludes();
		?>
		<script>
			$(".close_btn").click(function() {window.location.href = "./";});
		</script>
		<?php
		echo $tpl->ScreenSmallFoot();
	}
}
else if($_GET['p']=='update_cr') {
	$data = $_POST;
	if($response_type=="screen") {
		echo $tpl->ScreenSmallHead();
		?>
		<div class="row">
		<div class="col-lg-6 col-lg-offset-3">
		<div id="small_content_wrapper" class="panel panel-info">
		<div class="panel panel-heading"><h1>Actualizar Perfil</h1></div>
		<div class="panel-body">
		<?php
	}
	try {
		$circuit_rider_id = intval($_POST['circuit_rider_id']);
		if(isset($_POST['password_old']) && strlen($_POST['password_old'])>0 && $db->verifyUserPassword($circuit_rider_id,$_POST['password_old'])) {
			$data['password'] = hash('md5',$_POST['password_new']);
			removeInvalidFields($data,'circuit_riders');
			
			$status = $db->updateOneById($data,$circuit_rider_id,"circuit_riders");
			if($response_type=="screen") {
				?>
				<div class="alert alert-success">
				<button type="button" class="btn btn-default close_btn btn-lg" style="float:right;margin-top:20px">Volver</button>
				<h2>Éxito!</h2>
				<?php
				echo "</div>";
			}
		}
		else {
			throw new Exception("Las contraseñas deben coincidir, y debe dar su contraseña anterior",0);
		}
	}
	catch (Exception $e) {
		if($response_type=="screen") {
			echo "<div class=\"alert alert-danger\">";
			echo '<button type="button" class="btn btn-default close_btn btn-lg" style="float:right;margin-top:20px">Volver</button>';
			echo "<h2>Error <small>".$e->getCode()."</small></h2>";
			echo "<p>".$e->getMessage()."</p></div>";
		}
	}
	if($response_type=="screen") {
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo $tpl->scriptIncludes();
		?>
		<script>
			$(".close_btn").click(function() {window.location.href = "./";});
		</script>
		<?php
		echo $tpl->ScreenSmallFoot();
	}
}
else if($_GET['p']=='update_gps') {
	//echo "<pre>"; print_r($_POST); return;
	// Default response
	$msg=array(
		'response' => 500,
		'message' => 'No puedo gravar respuestas',
		'time'		=> date('Y-m-d H:i:s'),
		'data' => $_POST
	);
		
	if(!(isset($_POST["community_id"]) && isset($_POST["location_name"]) && is_array($_POST['location_name']))) {
		echo json_encode($msg);
		return;
	}
	try {
		$community = $db->getOne("communities",intval($_POST['community_id']));
	}
	catch (Exception $e) {
		$msg["message"] = $msg["message"]."(".$e->getMessage().")";
		echo json_encode($msg);
		return;
	}
	
	$data = array();
	$responses_given = count($_POST['location_name']);
	if($responses_given<=0) {
		$msg=array(
				'response' => 200,
				'message' => $responses_given.' respuestas registradas',
				'time'		=> date('Y-m-d H:i:s'),
				'data' => $data
		);
		echo json_encode($msg);
		return;
	}
	$i=0;
	// location_name, gps_lat, gps_lon, gps_ele, comments
	
	try {
		// First delete all removed entries
		if(isset($_POST['delete']) && is_array($_POST['delete'])) {
			$db->deleteGPS(intval($_POST['community_id']),$_POST['delete']);
		}
		
		$something_to_do = false;
		// Then add or update
		foreach($_POST['location_name'] as $num => $d) {
			if(!empty($d)) {
				$something_to_do = true;
				$data[$i]["community_id"] = intval($_POST['community_id']);
				$data[$i]["location_name"] = $d;
				$data[$i]["gps_lat"] = $_POST["gps_lat"][$num];
				$data[$i]["gps_lon"] = $_POST["gps_lon"][$num];
				$data[$i]["gps_ele"] = $_POST["gps_ele"][$num];
				$data[$i]["comments"] = $_POST["comments"][$num];
				$i++;
			}
		}
		if($something_to_do)
			$db->updateGPS($data,intval($_POST['community_id']));
		$msg=array(
				'response' => 200,
				'message' => $i.' respuestas registradas',
				'time'		=> date('Y-m-d H:i:s'),
				'data' => $data
		);
	}
	catch(Exception $e) {
		$msg["message"] = $msg["message"]." (".$e->getMessage().")";
	}
	echo json_encode($msg);
}
else if($_GET['p']=='update_junta') {
	//echo "<pre>"; print_r($_POST); return;
	// Default response
	$msg=array(
		'response' => 500,
		'message' => 'No puedo gravar respuestas',
		'time'		=> date('Y-m-d H:i:s'),
		'data' => $_POST
	);
		
	if(!(isset($_POST["community_id"]) && isset($_POST["role"]) && is_array($_POST['role']))) {
		echo json_encode($msg);
		return;
	}
	try {
		$community = $db->getOne("communities",intval($_POST['community_id']));
	}
	catch (Exception $e) {
		$msg["message"] = $msg["message"]."(".$e->getMessage().")";
		echo json_encode($msg);
		return;
	}
	
	$data = array();
	$responses_given = count($_POST['role']);
	if($responses_given<=0) {
		$msg=array(
				'response' => 200,
				'message' => $responses_given.' respuestas registradas',
				'time'		=> date('Y-m-d H:i:s'),
				'data' => $data
		);
		echo json_encode($msg);
		return;
	}
	$i=0;
	// location_name, gps_lat, gps_lon, gps_ele, comments
	
	try {
		// First delete all removed entries
		if(isset($_POST['delete']) && is_array($_POST['delete'])) {
			$db->deleteJunta(intval($_POST['community_id']),$_POST['delete']);
		}
		
		$something_to_do = false;
		// Then add or update
		foreach($_POST['role'] as $num => $d) {
			if(!empty($d)) {
				$something_to_do = true;
				$data[$i]["community_id"] = intval($_POST['community_id']);
				$data[$i]["role"] = $d;
				$data[$i]["name"] = $_POST["name"][$num];
				$data[$i]["phone"] = $_POST["phone"][$num];
				$data[$i]["comments"] = $_POST["comments"][$num];
				$i++;
			}
		}
		if($something_to_do)
			$db->updateJunta($data,intval($_POST['community_id']));
		$msg=array(
				'response' => 200,
				'message' => $i.' respuestas registradas',
				'time'		=> date('Y-m-d H:i:s'),
				'data' => $data
		);
	}
	catch(Exception $e) {
		$msg["message"] = $msg["message"]." (".$e->getMessage().")";
	}
	echo json_encode($msg);
}
else if($_GET['p']=='delete_data') {
	// Submit data: {"responses":{"1":1,"6":2},"circuit_rider_id":"2","date_recorded":"2015-03-02"}"

	// Default response
	$msg=array(
		'response' => 500,
		'message' => 'No puedo gravar respuestas',
		'time'		=> date('Y-m-d H:i:s'),
		'data' => $_POST
	);
	
	if(!(isset($_POST["community_id"]) || isset($_GET['community_id']))) {
		echo json_encode($msg);
		return;
	}
	try {
		$community_id = (isset($_POST['community_id']) ? intval($_POST['community_id']) : intval($_GET['community_id']));
		if(isset($_GET['date_recorded'])) {
			$response = $db->deleteByKeys("data",array("community_id"=>$community_id,"recorded_date"=>$_GET['date_recorded']));
			$msg=array(
					'response' => 200,
					'message' => 'Respuestas borradas!'.$response,
					'time'		=> date('Y-m-d H:i:s'),
					'data' => array()
			);
		}
		else {
			$msg["message"] = "No puedo hacer este accion";
		}
	}
	catch(Exception $e) {
		if($e->getCode()==23000) {
			// Foreign key constraint
			$msg["message"] = "<h2>".$msg["message"]."</h2> Los datos ya registrados para esta fecha. Por favor, seleccione una nueva fecha o volver atrás y editar los datos existentes.<br>(Error: ".$e->getCode().")";
		}
		else {
			$msg["message"] = $msg["message"]." (".$e->getMessage().")(".$e->getCode().")";
		}
	}
	echo json_encode($msg);
}
else if($_GET['p']=='eval_form_new') {
	// Submit data: {"responses":{"1":1,"6":2},"circuit_rider_id":"2","date_recorded":"2015-03-02"}"

	// Default response
	$msg=array(
		'response' => 500,
		'message' => 'No puedo gravar respuestas',
		'time'		=> date('Y-m-d H:i:s'),
		'data' => $_POST
	);
		
	if(!(isset($_POST["responses"]) && isset($_POST["circuit_rider_id"]) && isset($_POST["date_recorded"]) && is_array($_POST['responses']))) {
		echo json_encode($msg);
		return;
	}
	try {
		$circuit_rider = $db->getOne("circuit_riders",intval($_POST['circuit_rider_id']));
	}
	catch (Exception $e) {
		echo json_encode($msg);
		return;
	}
	
	if(intval($_POST['circuit_rider_id'])!=intval($circuit_rider['id'])) {
		$msg["message"] = "Trying to submit records under another name";
		echo json_encode($msg);
		return;
	}
	$data = array();
	$responses_given = count($_POST['responses']);
	if($responses_given<=0) {
		$msg=array(
				'response' => 200,
				'message' => $responses_given.' respuestas registradas',
				'time'		=> date('Y-m-d H:i:s'),
				'data' => $data
		);
		echo json_encode($msg);
		return;
	}
	$i=0;
	// community_id, question_id, recorded_by_id, recorded_date, response, comments
	
	try {
		foreach($_POST['responses'] as $question_id => $response) {
			$data[$i]["community_id"] = intval($_POST["community_id"]);
			$data[$i]["question_id"] = intval($question_id);
			$data[$i]["recorded_by_id"] = intval($circuit_rider["id"]);
			$data[$i]["recorded_date"] = $_POST["date_recorded"];
			$data[$i]["response"] = $response;
			$data[$i]["comments"] = $_POST["comments"][$question_id];
			$i++;
		}
		$db->insert($data,"data");
		$msg=array(
				'response' => 200,
				'message' => $responses_given.' respuestas registradas'.$data[0]["comments"],
				'time'		=> date('Y-m-d H:i:s'),
				'data' => $data
		);
	}
	catch(Exception $e) {
		if($e->getCode()==23000) {
			// Foreign key constraint
			$msg["message"] = "<h2>".$msg["message"]."</h2> Los datos ya registrados para esta fecha. Por favor, seleccione una nueva fecha o volver atrás y editar los datos existentes.<br>(Error: ".$e->getCode().")";
		}
		else {
			$msg["message"] = $msg["message"]." (".$e->getMessage().")(".$e->getCode().")";
		}
	}
	echo json_encode($msg);
}
else if($_GET['p']=='eval_form_update') {
	// Submit data: {"responses":{"1":1,"6":2},"circuit_rider_id":"2","date_recorded":"2015-03-02"}"

	// Default response
	$msg=array(
		'response' => 500,
		'message' => 'No puedo gravar respuestas',
		'time'		=> date('Y-m-d H:i:s'),
		'data' => $_POST
	);

	try {
		// First delete all removed entries
		if(isset($_POST['deletes']) && is_array($_POST['deletes'])) {
			$deleted_count = count($_POST['deletes']);
			$db->deleteData(intval($_POST['community_id']),$_POST['date_recorded'],$_POST['deletes']);
		}
	}
	catch(Exception $e) {
		$msg["message"] = $msg["message"]." (".$e->getMessage().")";
	}
	
	if(!(isset($_POST["responses"]) && isset($_POST["circuit_rider_id"]) && isset($_POST["date_recorded"]) && is_array($_POST['responses']))) {
		$msg["message"] = "<h2>".$msg["message"]."</h2> No hay cambios solicitados";
		if(isset($deleted_count)) {
			$msg["message"] .= "<br>".$deleted_count." registros borrados";
			$msg["response"] = 200;
		}
		echo json_encode($msg);
		return;
	}
	try {
		$circuit_rider = $db->getOne("circuit_riders",intval($_POST['circuit_rider_id']));
	}
	catch (Exception $e) {
		echo json_encode($msg);
		return;
	}
	
	if(intval($_POST['circuit_rider_id'])!=intval($circuit_rider['id'])) {
		$msg["message"] = "Trying to submit records under another name";
		echo json_encode($msg);
		return;
	}
	$data = array();
	$responses_given = count($_POST['responses']);
	if($responses_given<=0) {
		$msg=array(
				'response' => 200,
				'message' => $responses_given.' respuestas registradas',
				'time'		=> date('Y-m-d H:i:s'),
				'data' => $data
		);
		echo json_encode($msg);
		return;
	}
	$i=0;
	// community_id, question_id, recorded_by_id, recorded_date, response, comments
	
	try {
		
		foreach($_POST['responses'] as $question_id => $response) {
			$data[$i]["community_id"] = intval($_POST["community_id"]);
			$data[$i]["question_id"] = intval($question_id);
			$data[$i]["recorded_by_id"] = intval($circuit_rider["id"]);
			$data[$i]["recorded_date"] = $_POST["date_recorded"];
			$data[$i]["response"] = $response;
			$data[$i]["comments"] = $_POST["comments"][$question_id];
			$i++;
		}
		$response = $db->updateEval($data,intval($_POST['community_id']));
		$msg=array(
				'response' => 200,
				'message' => $responses_given.' respuestas registradas <br>',
				'time'		=> date('Y-m-d H:i:s'),
				'data' => $data
		);
	}
	catch(Exception $e) {
		$msg["message"] = $msg["message"]." (".$e->getMessage().")";
	}
	echo json_encode($msg);
}
else if($_GET['p']=='json_communities'){
	
	$out = array();
	$out["query"] = "Department";
	
	$cr_id = (isset($_GET['circuit_rider_id']) && intval($_GET['circuit_rider_id'])>0  ? intval($_GET['circuit_rider_id']) : null );
	if((!isset($_GET['department']) && !isset($_GET['municipality'])) && $cr_id!==null ) {
		// ONLY circuit rider is set;
		$data_raw = $db->getCommunities(null,null,$cr_id);
		unset($out);
		$out=array(
			'response' => 200,
			'message' => "",
			'time'		=> date('Y-m-d H:i:s'),
			'data' => array()
		);
		
		$ex = 0;
	}
	else if((isset($_GET['department']) && isset($_GET['municipality'])) && (strlen($_GET['department'])>0 && strlen($_GET['municipality'])>0)) {
		$data_raw = $db->getCommunities($_GET['department'], $_GET['municipality'],$cr_id);
		$ex = 1;
	}
	else if(isset($_GET['department']) && strlen($_GET['department'])>0) {
		$data_raw = $db->getMunicipalities($_GET['department'], $cr_id);
		$ex = 2;
	}
	else if(isset($_GET['allmunicip'])) {
		$data_raw = $db->getMunicipalities(null, null);
		$ex = 2;
	}
	else {
		$data_raw = $db->getDepartments($cr_id);
		$ex = 3;
	}
	
	$i=0;
	foreach($data_raw as $comm) {
		switch($ex) {
			case 0:
				$out["data"][$i]["community_id"] = $comm["id"];
				$out["data"][$i]["community"] = $comm["community"];
				$out["data"][$i]["department"] = $comm["department"];
				$out["data"][$i]["municipality"] = $comm["municipality"];
				$dates = $db->date_of_data($comm["id"]);
				$out["data"][$i]["last_date"] = (isset($dates['recorded_date']) ? $dates['recorded_date'] : "nunca");
			break;
			case 1:
				$out["suggestions"][$i]["value"] = $comm["community"];
				$out["suggestions"][$i]["data"] = $comm["id"];
				$out["suggestions"][$i]["community_id"] = $comm["id"];
				$out["suggestions"][$i]["department"] = $comm["department"];
				$out["suggestions"][$i]["municipality"] = $comm["municipality"];
				$circuit_rider = $db->getOne("circuit_riders",$comm['circuit_rider_id']);
				$out["suggestions"][$i]["circuit_rider_name"] = $circuit_rider['first_name']." ".$circuit_rider['last_name'];
				$out["suggestions"][$i]["circuit_rider_id"] = $comm['circuit_rider_id'];
			break;
			case 2:
				$out["suggestions"][$i]["value"] = $comm["municipality"];
				$out["suggestions"][$i]["data"] = $comm["department"];
			break;
			case 3:
				$out["suggestions"][$i]["value"] = $comm["department"];
			break;
		}
		$i++;
	}
	echo(json_encode($out));
	
	/*
	$msg=array(
			'response' => 200,
			'message' => $ex,
			'time'		=> date('Y-m-d H:i:s'),
			'data' => $out
	);
	echo json_encode($msg);*/
}
else if($_GET['p']=='json_circuit_riders'){
	
	$out = array();
	$out["query"] = "circuit_riders";
	$out["suggestions"] = array();
	
	$data_raw = $db->getCircuitRiders();

	$i=0;
	foreach($data_raw as $comm) {

		$out["suggestions"][$i]["value"] = $comm["first_name"]." ".$comm["last_name"];
		$out["suggestions"][$i]["data"] = $comm["id"];
		$i++;
	}
	echo(json_encode($out));
	
	/*
	$msg=array(
			'response' => 200,
			'message' => $ex,
			'time'		=> date('Y-m-d H:i:s'),
			'data' => $out
	);
	echo json_encode($msg);*/
}
else if($_GET['p']=="tj") {
	$out = array();
	$out["query"] = "Department";
	$out["suggestions"] = array();
	$data_raw = $db->getDepartments();
	$i=0;
	foreach($data_raw as $comm) {
		$out["suggestions"][$i]["value"] = $comm["department"];
		//$out["suggestions"][$i]["data"] = $comm["department"];
		if(isset($_GET["dept"])) {
			$out["suggestions"][$i]["data"] = $_GET["dept"];
		}
		$i++;
	}
	echo json_encode($out);
}
else {
	$msg=array(
			'response' => 400,
			'message' => 'Action Set: "'.$_GET['p'].'" does not exist.',
			'time'		=> date('Y-m-d H:i:s'),
			'data' => ''
	);
	echo json_encode($msg);
}