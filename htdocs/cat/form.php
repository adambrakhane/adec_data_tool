<?php
require_once('./lib/template.php');
require_once('./lib/mysql.php');
require_once('./lib/db.php');
require_once('./lib/session.php');
if(!isset($_GET['p']) || empty($_GET['p'])) {
	?>
	<h2>Error</h2>
	Esta página no es accesible directamente. <a href="./">Volver</a>.
	<?php
	/*echo "<pre>";
	print_r($_GET);
	print_r($_POST);
	echo "</pre>";*/
	
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

// Restrict some pages to admins only
$admin_only_pages = array("insert_cr");
$logged_in_circuit_rider = $db->getOne("circuit_riders",intval($_SESSION['circuit_rider_id']));
$is_admin = ($logged_in_circuit_rider["role"] == 10 ? true : false );
if((in_array($_GET['p'],$admin_only_pages) && !$is_admin)) {
	?>
	<h2>Error</h2>
	Usted debe ser un administrador para ver esta página. <a href="./">Volver</a>.
	<?php
	die();
}

if($_GET['p']=='insert_community') {
	echo $tpl->ScreenSmallHead();
	$newcode = ( isset($_GET['newcode']) ? $_GET['newcode'] : '');
	try {
		$circuit_rider = $db->getOne("circuit_riders",intval($_SESSION['circuit_rider_id']));
		
	?>
	
	<div class="container well">
	<form name="insert_community" id="insert_community" action="action.php?p=insert_community&t=screen" method="post" role="form">
		<div class="page-header">
		<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
		<button type="submit" class="btn btn-default" style="float:right;">Guardar</button>
			<h1>Añadir Comunidad</h1>
			
		</div>
		<style>
			.form-group {height:80px;}
			h1 {margin-top:5px;margin-bottom:5px;}
			.page-header{margin:0px;}
		</style>
		
		<div class="row">
			<div class="col-xs-6">
					<label for="department">Departamento:</label>
					<input class="form-control" type="text" name="department" id="department">
					<label for="municipality">Municipio</label>
					<input class="form-control" type="text" name="municipality" id="municipality">
					<label for="community">Comunidad</label>
					<input class="form-control" type="text" name="community" id="community">
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="comments">Comentarios Otros:</label>
					<textarea class="form-control" name="comments" id="comments"></textarea>
				</div>
				<div class="form-group">
					<label for="circuit_rider_id">Circuit Rider:</label>	
					<?php if($is_admin) { ?>
						<?php $tpl->circuit_rider_dropdown($db); ?>
					<?php } else { ?>
						<input type="hidden" id="circuit_rider_id" name="circuit_rider_id" value="<?=$circuit_rider["id"];?>"><br>
						<?=$circuit_rider['first_name'];?> <?=$circuit_rider['last_name'];?> (<?=$circuit_rider['email'];?> )
					<?php } ?>
				</div>
				
			</div>
			
		</div>
		<hr>
		<h3>Información de Sistema</h3>
		<div class="row">
			<div class="col-xs-6">
				<label for="system_type">Tipo de Sistema:</label>
				<input class="form-control" type="text" name="system_type" id="system_type">
				<label for="chlorination_type">Tipo de Cloración</label>
				<input class="form-control" type="text" name="chlorination_type" id="chlorination_type">
				<label for="source_capacity">Aforo Fuente <small>(Gal/Min)</small></label>
				<input class="form-control" type="text" name="source_capacity" id="source_capacity">
				<label for="tank_capacity">Capacidad del Tanque <small>(Gal)</small></label>
				<input class="form-control" type="text" name="tank_capacity" id="tank_capacity">
				<label for="tank_entrance_capacity">Aforo Entrada Tanque <small>(Gal/Min)</small></label>
				<input class="form-control" type="text" name="tank_entrance_capacity" id="tank_entrance_capacity">
			</div>
			<div class="col-xs-6">
				<label for="number_of_houses">Número de Casas:</label>
				<input class="form-control" type="text" name="number_of_houses" id="number_of_houses">
				<label for="number_of_houses_connected">Número de Casas Conectadas al Sistema</label>
				<input class="form-control" type="text" name="number_of_houses_connected" id="number_of_houses_connected">
				<label for="number_of_people">Número de Personas</label>
				<input class="form-control" type="text" name="number_of_people" id="number_of_people">
			</div>
		</div>
		<hr>
		<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
		<button type="submit" class="btn btn-default" style="float:right;">Guardar</button>
		<div style="clear:both"></div>
		<hr>
	</form>
	</div>
	<?=$tpl->scriptIncludes();?>
	<script>
		$(".close_btn").click(function() {window.location.href = "./";});
	</script>
	<script>
		

	$('input#department').autocomplete({
		serviceUrl: './action.php?p=json_communities',
		minChars: 0,
		onSelect: function (suggestion) {
			console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
		}
	});
	$('input#municipality').autocomplete({
		serviceUrl: './action.php?p=json_communities&allmunicip',
		minChars: 0,
		onSelect: function (suggestion) {
			console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
		}
	});
	</script>
	<?php
	}
	catch(Exception $e) {
		echo "Error: ".$e->getMessage()." (".$e->getCode().")";
	}
	echo $tpl->ScreenSmallFoot();
}
if($_GET['p']=='update_community') {
	echo $tpl->ScreenSmallHead();
	try {
		if(!(isset($_GET['community_id']) || isset($_POST['community_id'])))
			throw new Exception("Ninguna comunidad seleccionada. Volver.",0);
		$community_id = (isset($_POST['community_id']) ? intval($_POST['community_id']) : intval($_GET['community_id']));
		$community = $db->getOne("communities",$community_id);
		$circuit_rider = $db->getOne("circuit_riders",intval($community['circuit_rider_id']));
		// @TODO: Is the circuit rider logged in the same as the owner of the community? Am I an admin?
	?>
	
	<div class="container well">
	<form name="update_community" id="update_community" action="action.php?p=update_community&t=screen" method="post" role="form">
		<div class="page-header">
		<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
		<button type="submit" class="btn btn-default" style="float:right;">Guardar</button>
			<h1>Editar Comunidad</h1>
			
		</div>
		<style>
			.form-group {height:80px;}
			h1 {margin-top:5px;margin-bottom:5px;}
			.page-header{margin:0px;}
		</style>
		
		<div class="row">
			<div class="col-xs-6">
					<label for="department">Departamento:</label>
					<input class="form-control" type="text" name="department" id="department" value="<?=$community['department'];?>">
					<label for="municipality">Municipio</label>
					<input class="form-control" type="text" name="municipality" id="municipality" value="<?=$community['municipality'];?>">
					<label for="community">Comunidad</label>
					<input class="form-control" type="text" name="community" id="community" value="<?=$community['community'];?>">
					
					<input type="hidden" id="community_id" name="community_id" value="<?=$community_id;?>">
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="comments">Comentarios Otros:</label>
					<textarea class="form-control" name="comments" id="comments"><?=$community['comments'];?></textarea>
				</div>
				<div class="form-group">
					<label for="circuit_rider_id">Circuit Rider:</label>	
					<input type="hidden" id="circuit_rider_id" name="circuit_rider_id" value="<?=$circuit_rider["id"];?>">
					<?=$circuit_rider["first_name"];?> <?=$circuit_rider["last_name"];?> 
				</div>
				
			</div>
			
		</div>
		<hr>
		<h3>Información de Sistema</h3>
		<div class="row">
			<div class="col-xs-6">
				<label for="system_type">Tipo de Sistema:</label>
				<input class="form-control" type="text" name="system_type" id="system_type" value="<?=$community['system_type'];?>">
				<label for="chlorination_type">Tipo de Cloración</label>
				<input class="form-control" type="text" name="chlorination_type" id="chlorination_type" value="<?=$community['chlorination_type'];?>">
				<label for="source_capacity">Aforo Fuente <small>(Gal/Min)</small></label>
				<input class="form-control" type="text" name="source_capacity" id="source_capacity" value="<?=$community['source_capacity'];?>">
				<label for="tank_capacity">Capacidad del Tanque <small>(Gal)</small></label>
				<input class="form-control" type="text" name="tank_capacity" id="tank_capacity" value="<?=$community['tank_capacity'];?>">
				<label for="tank_entrance_capacity">Aforo Entrada Tanque <small>(Gal/Min)</small></label>
				<input class="form-control" type="text" name="tank_entrance_capacity" id="tank_entrance_capacity" value="<?=$community['tank_entrance_capacity'];?>">
			</div>
			<div class="col-xs-6">
				<label for="number_of_houses">Número de Casas:</label>
				<input class="form-control" type="text" name="number_of_houses" id="number_of_houses" value="<?=$community['number_of_houses'];?>">
				<label for="number_of_houses_connected">Número de Casas Conectadas al Sistema</label>
				<input class="form-control" type="text" name="number_of_houses_connected" id="number_of_houses_connected" value="<?=$community['number_of_houses_connected'];?>">
				<label for="number_of_people">Número de Personas</label>
				<input class="form-control" type="text" name="number_of_people" id="number_of_people" value="<?=$community['number_of_people'];?>">
			</div>
		</div>
		<hr>
		<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
		<button type="submit" class="btn btn-default" style="float:right;">Guardar</button>
		<div style="clear:both"></div>
		<hr>
	</form>
	</div>
	<?=$tpl->scriptIncludes();?>
	<script>
		$(".close_btn").click(function() {window.location.href = "./";});
	</script>
	<script>
		

	$('input#department').autocomplete({
		serviceUrl: './action.php?p=json_communities',
		minChars: 0,
		onSelect: function (suggestion) {
			console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
		}
	});
	$('input#municipality').autocomplete({
		serviceUrl: './action.php?p=json_communities&allmunicip',
		minChars: 0,
		onSelect: function (suggestion) {
			console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
		}
	});
	</script>
	<?php
	}
	catch(Exception $e) {
		echo "Error: ".$e->getMessage()." (".$e->getCode().")";
	}
	echo $tpl->ScreenSmallFoot();
}
else if($_GET['p']=='insert_cr') {
	echo $tpl->ScreenSmallHead();
	$newcode = ( isset($_GET['newcode']) ? $_GET['newcode'] : '');
	?>
	
	<div class="container well">
	<form name="insert_cr" id="insert_cr" action="action.php?p=insert_cr&t=screen" method="post" role="form">
		<div class="page-header">
		
		<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
		<button type="submit" class="btn btn-default" style="float:right;">Guardar</button>
			<h1>Añadir Circuit Rider</h1>
			
		</div>
		<style>
			.form-group {height:80px;}
			h1 {margin-top:5px;margin-bottom:5px;}
			.page-header{margin:0px;}
		</style>
		
		<div class="row">
			
			<div class="col-xs-6">
				<div class="form-group">
					<label for="first_name">Nombre:</label><br>
					<input class="form-control" type="text" name="first_name" id="first_name"><br>
				</div>
				<div class="form-group">
					<label for="last_name">Apellido:</label><br>
					<input class="form-control" type="text" name="last_name" id="last_name"><br>
				</div>
				<div class="form-group">
					<label for="password">Contraseña</label><br>
					<input class="form-control" type="password" name="password" id="password"><br>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="role">Rol:</label><br>
					<input type="text" class="form-control" name="role" id="role"><br>
				</div>
				<div class="form-group">
					<label for="email">Dirección de Correo:</label><br>
					<input type="text" class="form-control" name="email" id="email"><br>
				</div>
				
			</div>
			
		</div>
	</form>
	</div>
	<?=$tpl->scriptIncludes();?>
	<script>
		$(".close_btn").click(function() {window.location.href = "./";});
		function attachRoleAC() {
			$('input#role').autocomplete({
				serviceUrl: './json_drop.php?p=AC_user_roles',
				minChars: 0,
				onSelect: function (suggestion) {
					console.log('You selected: ' + suggestion.value);
					$("input#role").text(suggestion.value);
				}
			});
		}
		attachRoleAC();
	</script>
	
	<?php
	echo $tpl->ScreenSmallFoot();
}

else if($_GET['p']=='profile') {
	echo $tpl->ScreenSmallHead();
	if(!isset($_GET['circuit_rider_id'])) {
		$circuit_rider_id = (isset($_GET['circuit_rider_id']) ? intval($_GET['circuit_rider_id']) : intval($_SESSION['circuit_rider_id']));
		$circuit_rider = $db->getOne("circuit_riders",$circuit_rider_id);
		
		$role = $circuit_rider['role'];
		switch($circuit_rider['role']) {
			case 1: $role="Circuit Rider"; break;
			case 10: $role="Administrador"; break;
		}
	?>
	
	<div class="container well">
	<form name="update_cr" id="update_cr" action="action.php?p=update_cr&t=screen" method="post" role="form">
		<input type="hidden" name="circuit_rider_id" value="<?=$circuit_rider_id;?>">
		<div class="page-header">
		
		<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
		<button type="submit" class="btn btn-default" style="float:right;">Guardar</button>
			<h1>Mi Perfil</h1>
			
		</div>
		<style>
			.form-group {height:80px;}
			h1 {margin-top:5px;margin-bottom:5px;}
			.page-header{margin:0px;}
		</style>
		
		<div class="row">
			
			<div class="col-xs-6">
				<div class="form-group">
					<label for="first_name">Nombre:</label><br>
					<input class="form-control" type="text" name="first_name" id="first_name" disabled="disabled" value="<?=$circuit_rider['first_name'];?>"><br>
				</div>
				<div class="form-group">
					<label for="last_name">Apellido:</label><br>
					<input class="form-control" type="text" name="last_name" id="last_name" disabled="disabled" value="<?=$circuit_rider['last_name'];?>"><br>
				</div>
				<div class="form-group">
					<label for="role">Rol:</label><br>
					<input type="text" class="form-control" name="role" id="role" disabled="disabled" value="<?=$role;?>"><br>
				</div>
				<div class="form-group">
					<label for="email">Dirección de Correo:</label><br>
					<input type="text" class="form-control" name="email" id="email" value="<?=$circuit_rider['email'];?>"><br>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="password_old">Contraseña Anterior</label><br>
					<input class="form-control" type="password" name="password_old" id="password_old"><br>
					
				</div>
				<div class="form-group">
					<div class="alert alert-warning validate" id="validate_status_old" style="display:none;"></div>
				</div>
				<div class="form-group">
					<label for="password_new">Contraseña Nueva</label><br>
					<input class="form-control" type="password" name="password_new" id="password_new"><br>
				</div>
				<div class="form-group">
					<label for="password_new_2">Contraseña Nueva (Confirmar)</label><br>
					<input class="form-control" type="password" name="password_new_2" id="password_new_2"><br>
				</div>
				<div class="form-group">
					<div class="alert alert-warning validate" id="validate_status" style="display:none;"></div>
				</div>
				
			</div>
		</div>
	</form>
	</div>
	<?=$tpl->scriptIncludes();?>
	<script>
		$(".close_btn").click(function() {window.location.href = "./";});
		$(document).ready(function() {
			$("#password_old").keyup(validate_old);
			$("#password_new").keyup(validate);
			$("#password_new_2").keyup(validate);
		});
		var form_ready = true;
		function validate_old() {
			if($("#password_old").val().length<=1) {
				$('#update_cr button[type="submit"]').prop('disabled',true);
				$("#validate_status_old").text("Usted debe dar su antigua contraseña para realizar cualquier cambio.").removeClass("alert-success").addClass("alert-warning").slideDown();
				$("#validate_status_old").parent().slideDown();
			}
			else {
				$('#update_cr button[type="submit"]').prop('disabled',false);
				$("#validate_status_old").text("Listo!").removeClass("alert-warning").addClass("alert-success").delay(1000).slideUp();      
				$("#validate_status_old").parent().slideUp();
			}
		}
		function validate() {
			var password1 = $("#password_new").val();
			var password2 = $("#password_new_2").val();
			if(password1 == password2) {
				$("#validate_status").text("Listo!").removeClass("alert-warning").addClass("alert-success").delay(1000).slideUp();      
				$("#validate_status").parent().slideUp();
				form_ready = true;
			}
			else {
				$("#validate_status").text("Las contraseñas deben coincidir.").removeClass("alert-success").addClass("alert-warning").slideDown();
				$("#validate_status").parent().slideDown();
				form_ready = false;
			}
		}
		validate(); validate_old();
		$("#update_cr").submit(function(e) {
			if(!form_ready) e.preventDefault();
		});
	</script>
	
	<?php
	} // End if isset(circuit_rider_id)
	echo $tpl->ScreenSmallFoot();
}
else if($_GET['p']=='update_junta') {
	echo $tpl->ScreenSmallHead();
	if(!isset($_GET['community_id'])) {
		echo "Community not selected";
	}
	try {
		$community_id = intval($_GET['community_id']);
		$community = $db->getOne("communities",$community_id);
	}
	catch(Exception $e) {
		echo "<h2>Error connecting to database</h2>";
		echo $e->getMessage();
		return;
	}
	?>
<form id="junta_form" action="action.php?p=update_junta" method="post">
<input type="hidden" name="community_id" value="<?=$community_id;?>">
	<div class="container well">
		<div class="page-header">
		<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
		<button type="submit" class="btn btn-default" style="float:right;">Guardar</button>
			<h1>Editar Junta <small><?=$community['community'];?>, <?=$community['municipality'];?></small></h1>
			
		</div>
		<style>
			.form-group {height:80px;}
			h1 {margin-top:5px;margin-bottom:5px;}
			.page-header{margin:0px;}
		</style>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-info" id="response_message" style="margin-top:10px;display:none;">
				<button type="button" class="btn btn-default close_btn" style="float:right;">Volver</button>
					<div class="panel-heading">
						
						<h3 class="panel-title" id="uresponse_message_title">Resultado</h3>
					</div>
					<div class="panel-body" id="response_message_data"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				
				<table class="table table-bordered table-striped table-hover" id="junta_table">
					<thead>
						<th>Rol</th>
						<th>Nombre</th>
						<th>Celular</th>
						<th>Comentario</th>
						<th></th>
					</thead>
					<tbody>
					<?php
$data_for_community = $db->listAllJunta($community_id,"community_junta","all","","role");
foreach($data_for_community as $d) {
	echo '<tr>';
		echo '<td><input type="text" name="role[]" class="form-control role" placeholder="Rol" value="'.$d['role'].'" disabled=\"disabled\"></td>';
		echo '<td><input type="text" name="name[]" class="form-control" placeholder="Nombre" value="'.$d['name'].'"></td>';
		echo '<td><input type="text" name="phone[]" class="form-control" placeholder="Numero" value="'.$d['phone'].'"></td>';
		echo '<td><textarea name="comments[]" class="commentbox form-control">'.$d['comments'].'</textarea></td>';
		echo '<td><button type="button" class="btn btn-danger deleterow">Borrar</button></td>';
	echo '</tr>';
}
					?>
						<tr>
							<td><input type="text" name="role[]" class="form-control role" placeholder="Rol"></td>
							<td><input type="text" name="name[]" class="form-control" placeholder="Nombre"></td>
							<td><input type="text" name="phone[]" class="form-control" placeholder="Numero"></td>
							<td><textarea name="comments[]" class="commentbox form-control"></textarea></td>
							<td><button type="button" class="btn btn-danger deleterow">Borrar</button></td>
						</tr>
					</tbody>
				</table>
				<button type="button" id="new_row_btn" class="btn btn-large btn-success" style="float:right;">Añadir Rol</button>
			</div>
		</div>
	</div>
</form>
	<?=$tpl->scriptIncludes();?>
	<script>
		$(".close_btn").click(function() {window.location.href = "./";});
		$("#new_row_btn").click(function() {
			$("#junta_table").append('<tr><td><input type="text" name="role[]" class="form-control role" placeholder="Rol"></td><td><input type="text" name="name[]" class="form-control" placeholder="Nombre"></td><td><input type="text" name="phone[]" class="form-control" placeholder="Numero"></td><td><textarea name="comments[]" class="commentbox form-control"></textarea></td><td><button type="button" class="btn btn-danger deleterow">Borrar</button></td></tr>');
			attachRoleAC();
			attachDelete();
		});
		function attachDelete() {
			$(".deleterow").click(function(e) {
				var name = $(this).parent().parent().children("td").children("input").val();
				$("#junta_form").append('<input type="hidden" name="delete[]" value="'+name+'">');
				$(this).parent().parent().remove();
			});
		}
		attachDelete();
		$("#junta_form").submit(function(e) {
		
			e.preventDefault();
			if(no_duplicate_names()) {
				var $json_data = {};
				$json_data.role = [];
				$json_data.name = [];
				$json_data.phone = [];
				$json_data.comments = [];
				$json_data.delete = [];
				

				$("#junta_form").children('input[name="delete[]"]').each(function(k,v) {
					var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
					$json_data[row_name].push(($(v).val()));
				});
				$("#junta_table tbody").children("tr").each(function(rownum,row) {
					$(row).children("td").children("input").each(function(k,v) {
						var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
						$json_data[row_name].push(($(v).val()));
					});
					$(row).children("td").children("textarea").each(function(k,v) {
						var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
						$json_data[row_name].push(($(v).val()));
					});
				});
				$json_data.community_id = $('input[name="community_id"]').val();
				
				console.log("Submitting form");
				$.ajax({
					type: "POST",
					url: "action.php?p=update_junta",
					data: $json_data,
					success: function(msg) {on_submit_success(msg);},
					dataType: "json",
				});
			}
			else {
				console.log("First clear up errors");
				alert("May not have duplicate location names");
			}
			
			return false;
		});
		function no_duplicate_names() {
			var roles = [];
			var bad = true;
			$("#junta_form").find("input").each(function(value,key){
				if($(key).attr("name")=="role[]") {
					if($.inArray($(key).val(),roles)!=-1) {
						bad = false;
					}
					roles.push($(key).val());
				}
				
			});
			return bad;
		}
		function on_submit_success(msg) {
			//$("#eval_section_holder").slideUp(2000).fadeOut();
			$("#response_message").fadeIn();
			$("#response_message .panel-body").text(msg.message);
			if(msg.response==200)
				$("#response_message").removeClass("panel-info").addClass("panel-success");
			
			$('html,body').animate({
				scrollTop: $("#response_message").offset().top - 10
			});
			
			//disable_save_button
			
			$("input").attr("disabled", "disabled");
			$("textarea").attr("disabled", "disabled");
			$("button").attr("disabled", "disabled");
			$("button.close_btn").prop("disabled",false);
		}
		function attachRoleAC() {
			$('input.role').autocomplete({
				serviceUrl: './json_drop.php?p=AC_junta_roles',
				minChars: 0,
				onSelect: function (suggestion) {
					console.log('You selected: ' + suggestion.value);
					$(this).text(suggestion.value);
				}
			});
		}
		attachRoleAC();
	</script>
	<?php
	echo $tpl->ScreenSmallFoot();
}
else if($_GET['p']=='basic_history') {
	echo $tpl->ScreenSmallHead();
	$circuit_rider_id = (isset($_GET['circuit_rider_id']) ? intval($_GET['circuit_rider_id']) : intval($_SESSION['circuit_rider_id']));
	try {
		$circuit_rider = $db->getOne("circuit_riders",$circuit_rider_id);
		$community_list = $db->getCommunities(null,null,$circuit_rider["id"]);
	}
	catch(Exception $e) {
		echo "<h2>Error connecting to database</h2>";
		echo $e->getMessage();
		return;
	}
	?>
<form id="basic_history_form" action="action.php?p=basic_history" method="post">
<input type="hidden" name="circuit_rider_id" value="<?=$circuit_rider_id;?>">
	<div class="container well">
		<div class="page-header">
		<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
		<button type="submit" class="btn btn-default" style="float:right;">Guardar</button>
			<h1>Editar Junta <small></small></h1>
			
		</div>
		<style>
			.form-group {height:80px;}
			h1 {margin-top:5px;margin-bottom:5px;}
			.page-header{margin:0px;}
		</style>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-info" id="response_message" style="margin-top:10px;display:none;">
				<button type="button" class="btn btn-default close_btn" style="float:right;">Volver</button>
					<div class="panel-heading">
						
						<h3 class="panel-title" id="uresponse_message_title">Resultado</h3>
					</div>
					<div class="panel-body" id="response_message_data"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Comunidades</h3>
					</div>
					<div class="panel-body" id="communities">
	<?php

	$community_list = $db->getCommunities(null,null,$circuit_rider["id"]);
	echo "<table class=\"table table-bordered table-striped table-hover\">";
	echo "<thead><th>Comunidad</th><th>Municipio</th><th>Opciones</th><th>Ultima Fecha</th></thead><tbody>";
	foreach($community_list as $c) {
		$dates = $db->date_of_data($c["id"]);
		$last_date = (isset($dates['recorded_date']) ? $dates['recorded_date'] : "nunca");
		echo "<tr>";
		echo "<td>".$c["community"]."</td>";
		echo "<td>".$c["municipality"]."</td>";
		echo "<td>";
		echo '<div class="dropdown">
	  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
		  <span class="glyphicon glyphicon-align-left" aria-hidden="true"></span>
		<span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
		<li role="presentation"><a role="menuitem" tabindex="-1" href="./form.php?p=eval_form_update&community_id='.$c['id'].'">Editar Evaluación Ultima</a></li>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="./form.php?p=eval_form_new&community_id='.$c['id'].'">Evaluación Nueva</a></li>
		<li role="presentation" class="divider"></li>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="./form.php?p=update_community&community_id='.$c['id'].'">Editar Información</a></li>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="./form.php?p=update_gps&community_id='.$c['id'].'">GPS</a></li>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="./form.php?p=update_junta&community_id='.$c['id'].'">Junta</a></li>
		<li role="presentation" class="divider"></li>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="./basic_report.php?p=history&community_id='.$c['id'].'">Basic History</a></li>
	  </ul>
	</div>';
		echo " </td>";
		echo "<td>".$last_date."</td>";
		echo "</tr>";
	}
	echo "</tbody></table>\n";
	?>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
	<?=$tpl->scriptIncludes();?>
	<script>
		$(".close_btn").click(function() {window.location.href = "./";});
		$("#new_row_btn").click(function() {
			$("#junta_table").append('<tr><td><input type="text" name="role[]" class="form-control role" placeholder="Rol"></td><td><input type="text" name="name[]" class="form-control" placeholder="Nombre"></td><td><input type="text" name="phone[]" class="form-control" placeholder="Numero"></td><td><textarea name="comments[]" class="commentbox form-control"></textarea></td><td><button type="button" class="btn btn-danger deleterow">Borrar</button></td></tr>');
			attachRoleAC();
			attachDelete();
		});
		function attachDelete() {
			$(".deleterow").click(function(e) {
				var name = $(this).parent().parent().children("td").children("input").val();
				$("#junta_form").append('<input type="hidden" name="delete[]" value="'+name+'">');
				$(this).parent().parent().remove();
			});
		}
		attachDelete();
		$("#junta_form").submit(function(e) {
		
			e.preventDefault();
			if(no_duplicate_names()) {
				var $json_data = {};
				$json_data.role = [];
				$json_data.name = [];
				$json_data.phone = [];
				$json_data.comments = [];
				$json_data.delete = [];
				

				$("#junta_form").children('input[name="delete[]"]').each(function(k,v) {
					var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
					$json_data[row_name].push(($(v).val()));
				});
				$("#junta_table tbody").children("tr").each(function(rownum,row) {
					$(row).children("td").children("input").each(function(k,v) {
						var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
						$json_data[row_name].push(($(v).val()));
					});
					$(row).children("td").children("textarea").each(function(k,v) {
						var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
						$json_data[row_name].push(($(v).val()));
					});
				});
				$json_data.community_id = $('input[name="community_id"]').val();
				
				console.log("Submitting form");
				$.ajax({
					type: "POST",
					url: "action.php?p=update_junta",
					data: $json_data,
					success: function(msg) {on_submit_success(msg);},
					dataType: "json",
				});
			}
			else {
				console.log("First clear up errors");
				alert("May not have duplicate location names");
			}
			
			return false;
		});
		function no_duplicate_names() {
			var roles = [];
			var bad = true;
			$("#junta_form").find("input").each(function(value,key){
				if($(key).attr("name")=="role[]") {
					if($.inArray($(key).val(),roles)!=-1) {
						bad = false;
					}
					roles.push($(key).val());
				}
				
			});
			return bad;
		}
		function on_submit_success(msg) {
			//$("#eval_section_holder").slideUp(2000).fadeOut();
			$("#response_message").fadeIn();
			$("#response_message .panel-body").text(msg.message);
			if(msg.response==200)
				$("#response_message").removeClass("panel-info").addClass("panel-success");
			
			$('html,body').animate({
				scrollTop: $("#response_message").offset().top - 10
			});
			
			//disable_save_button
			
			$("input").attr("disabled", "disabled");
			$("textarea").attr("disabled", "disabled");
			$("button").attr("disabled", "disabled");
			$("button.close_btn").prop("disabled",false);
		}
		function attachRoleAC() {
			$('input.role').autocomplete({
				serviceUrl: './json_drop.php?p=AC_junta_roles',
				minChars: 0,
				onSelect: function (suggestion) {
					console.log('You selected: ' + suggestion.value);
					$(this).text(suggestion.value);
				}
			});
		}
		attachRoleAC();
	</script>
	<?php
	echo $tpl->ScreenSmallFoot();
}
else if($_GET['p']=='update_gps') {
	echo $tpl->ScreenSmallHead();
	if(!isset($_GET['community_id'])) {
		echo "Community not selected";
	}
	try {
		$community_id = intval($_GET['community_id']);
		$community = $db->getOne("communities",$community_id);
	}
	catch(Exception $e) {
		echo "<h2>Error connecting to database</h2>";
		echo $e->getMessage();
		return;
	}
	?>
<form id="gps_form" action="action.php?p=update_gps" method="post">
<input type="hidden" name="community_id" value="<?=$community_id;?>">
	<div class="container well">
		<div class="page-header">
		<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
		<button type="submit" class="btn btn-default" style="float:right;">Guardar</button>
			<h1>Editar GPS <small><?=$community['community'];?>, <?=$community['municipality'];?></small></h1>
			
		</div>
		<style>
			.form-group {height:80px;}
			h1 {margin-top:5px;margin-bottom:5px;}
			.page-header{margin:0px;}
			#gps_table th span {
				font-weight:300;
				font-style:italic;
			}
		</style>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-info" id="response_message" style="margin-top:10px;display:none;">
				<button type="button" class="btn btn-default close_btn" style="float:right;">Volver</button>
					<div class="panel-heading">
						
						<h3 class="panel-title" id="uresponse_message_title">Resultado</h3>
					</div>
					<div class="panel-body" id="response_message_data"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				
				<table class="table table-bordered table-striped table-hover" id="gps_table">
					<thead>
						<th>Ubicacion</th>
						<th>Latitud <span>(Ex: 14.16838)</span></th>
						<th>Longitud <span>(Ex: -87.98188)</span></th>
						<th>Elevación (m) <span>(Ex: 1524)</span></th>
						<th>Comentario</th>
						<th></th>
					</thead>
					<tbody>
					<?php
$data_for_community = $db->listAllGPS($community_id,"community_gps","all","","location_name");
foreach($data_for_community as $d) {
	echo '<tr>';
		echo '<td><input type="text" name="location_name[]" class="form-control location_name" placeholder="Ubicacion" value="'.$d['location_name'].'" disabled=\"disabled\"></td>';
		echo '<td><input type="text" name="gps_lat[]" class="form-control numeric_only" placeholder="lat" pattern="[-+]?[0-9]*[.,]?[0-9]+" value="'.$d['gps_lat'].'"></td>';
		echo '<td><input type="text" name="gps_lon[]" class="form-control numeric_only" placeholder="lon" pattern="[-+]?[0-9]*[.,]?[0-9]+" value="'.$d['gps_lon'].'"></td>';
		echo '<td><input type="text" name="gps_ele[]" class="form-control numeric_only" placeholder="ele" pattern="[-+]?[0-9]*[.,]?[0-9]+" value="'.$d['gps_ele'].'"></td>';
		echo '<td><textarea name="comments[]" class="commentbox form-control">'.$d['comments'].'</textarea></td>';
		echo '<td><button type="button" class="btn btn-danger deleterow">Borrar</button></td>';
	echo '</tr>';
}
					?>
						<tr>
							<td><input type="text" name="location_name[]" class="form-control location_name" placeholder="Ubicacion"></td>
							<td><input type="text" name="gps_lat[]" class="form-control numeric_only" placeholder="lat" pattern="[-+]?[0-9]*[.,]?[0-9]+"></td>
							<td><input type="text" name="gps_lon[]" class="form-control numeric_only" placeholder="lon" pattern="[-+]?[0-9]*[.,]?[0-9]+"></td>
							<td><input type="text" name="gps_ele[]" class="form-control numeric_only" placeholder="ele" pattern="[-+]?[0-9]*[.,]?[0-9]+"></td>
							<td><textarea name="comments[]" class="commentbox form-control"></textarea></td>
							<td><button type="button" class="btn btn-danger deleterow">Borrar</button></td>
						</tr>
					</tbody>
				</table>
				<button type="button" id="new_row_btn" class="btn btn-large btn-success" style="float:right;">Añadir Ubicacion</button>
			</div>
		</div>
	</div>
</form>
<?=$tpl->scriptIncludes();?>
	<script>
		function attachNumericValidator() {
			$('.numeric_only').keyup(function () {
				if (this.value != this.value.replace(/[^0-9\.\-]/g, '')) {
				   this.value = this.value.replace(/[^0-9\.\-]/g, '');
				}
			});
		}
		attachNumericValidator();
		$(".close_btn").click(function() {window.location.href = "./";});
		$("#new_row_btn").click(function() {
			$("#gps_table").append('<tr><td><input type="text" name="location_name[]" class="form-control location_name" placeholder="Ubicacion"></td><td><input type="text" name="gps_lat[]" class="form-control numeric_only" placeholder="lat" pattern="[-+]?[0-9]*[.,]?[0-9]+"></td><td><input type="text" name="gps_lon[]" class="form-control numeric_only" placeholder="lon" pattern="[-+]?[0-9]*[.,]?[0-9]+"></td><td><input type="text" name="gps_ele[] numeric_only" class="form-control" placeholder="ele" pattern="[-+]?[0-9]*[.,]?[0-9]+"></td><td><textarea name="comments[]" class="commentbox form-control"></textarea></td><td><button type="button" class="btn btn-danger deleterow">Borrar</button></td></tr>');
			attachLocationAC();
			attachDelete();
			attachNumericValidator();
		});
		function attachDelete() {
			$(".deleterow").click(function(e) {
				var location_name = $(this).parent().parent().children("td").children("input").val();
				$("#gps_form").append('<input type="hidden" name="delete[]" value="'+location_name+'">');
				$(this).parent().parent().remove();
			});
		}
		attachDelete();
		$("#gps_form").submit(function(e) {
		
			e.preventDefault();
			if(no_duplicate_names()) {
				var $json_data = {};
				$json_data.location_name = [];
				$json_data.gps_lat = [];
				$json_data.gps_lon = [];
				$json_data.gps_ele = [];
				$json_data.comments = [];
				$json_data.delete = [];
				

				$("#gps_form").children('input[name="delete[]"]').each(function(k,v) {
					var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
					$json_data[row_name].push(($(v).val()));
				});
				$("#gps_table tbody").children("tr").each(function(rownum,row) {
					$(row).children("td").children("input").each(function(k,v) {
						var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
						$json_data[row_name].push(($(v).val()));
					});
					$(row).children("td").children("textarea").each(function(k,v) {
						var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
						$json_data[row_name].push(($(v).val()));
					});
				});
				$json_data.community_id = $('input[name="community_id"]').val();
				
				console.log("Submitting form");

				$.ajax({
					type: "POST",
					url: "action.php?p=update_gps",
					data: $json_data,
					success: function(msg) {on_submit_success(msg);},
					dataType: "json",
				});
			}
			else {
				console.log("First clear up errors");
				alert("May not have duplicate location names");
			}
			
			return false;
		});
		function no_duplicate_names() {
			var location_names = [];
			var bad = true;
			$("#gps_form").find("input").each(function(value,key){
				if($(key).attr("name")=="location_name[]") {
					if($.inArray($(key).val(),location_names)!=-1) {
						bad = false;
					}
					location_names.push($(key).val());
				}
				
			});
			return bad;
		}
		function on_submit_success(msg) {
			//$("#eval_section_holder").slideUp(2000).fadeOut();
			$("#response_message").fadeIn();
			$("#response_message .panel-body").text(msg.message);
			if(msg.response==200)
				$("#response_message").removeClass("panel-info").addClass("panel-success");
			
			$('html,body').animate({
				scrollTop: $("#response_message").offset().top - 10
			});
			
			//disable_save_button
			
			$("input").attr("disabled", "disabled");
			$("textarea").attr("disabled", "disabled");
			$("button").attr("disabled", "disabled");
			$("button.close_btn").prop("disabled",false);
		}
		function attachLocationAC() {
			$('.location_name').autocomplete({
				serviceUrl: './json_drop.php?p=AC_GPS_locations',
				minChars: 0,
				onSelect: function (suggestion) {
					console.log('You selected: ' + suggestion.value);
					$(this).text(suggestion.value);
				}
			});
		}
		attachLocationAC();
	</script>
	<?php
	echo $tpl->ScreenSmallFoot();
}
else if($_GET['p']=='eval_form_new') {
	echo $tpl->ScreenSmallHead();
	$error = false;
	if(!(
			(
				isset($_POST['community']) &&
				isset($_POST['municipality']) &&
				isset($_POST['department']) &&
				isset($_POST['community_id'])
			) || 
			(
				isset($_GET['community_id'])
			)
		)) {
		// Error. Set the variables
		echo "not set";
		
		$error=true;
	}
	try {
		$community_id = (isset($_POST['community_id']) ? intval($_POST['community_id']) : intval($_GET['community_id']));
		$circuit_rider_id = (isset($_POST['circuit_rider_id']) ? intval($_POST['circuit_rider_id']) : intval($_SESSION['circuit_rider_id']));
		
		$circuit_rider = $db->getOne("circuit_riders",$circuit_rider_id);
		$community = $db->getOne("communities",$community_id);
	}
	catch (Exception $e) {
		echo "couldn't get CR";
		$error=true;
	}
	
	if($error==false) {
		$form_questions = $db->listAll("questions");
		
		// Add a higher dimension to group by "section"
		$sections = array();
		foreach($form_questions as $q) {
			if($q["current"]) { // db flag to hide questions
				if(!isset($sections[$q["section"]]) || !is_array($sections[$q["section"]])) {
					$sections[$q["section"]] = array();
				}
				$sections[$q["section"]][] = $q;
			}
		}
		$sectionNum = 0;
		?>
		
		<div class="container" id="eval_form_container">
			<form name="eval_form" id="eval_form" action="action?p=eval_form_new" method="post" role="form">
				<div class="page-header well" style="margin-bottom:20px;">
				<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
				<button type="submit" class="btn btn-default savebtn" style="float:right;">Guardar</button>
					<h1>Community Evaluation Form <small>(Circuit Rider: <?=$circuit_rider['first_name'];?> <?=$circuit_rider['last_name'];?>)</h1>
					<h2><?php
						echo $community['community'].", ".$community['municipality'].", ".$community['department'];
					?>
					<small></small>
					</h2>
					<div class="row" style="text-align:center;">
						<label for="date_recorded">Fecha <em>(año-mes-día)</em></label> <input type="text" name="date_recorded" value="" id="date_recorded" placeholder="año-mes-día">
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-info" id="response_message" style="margin-top:10px;display:none;">
						<button type="button" class="btn btn-default close_btn" style="float:right;">Volver</button>
							<div class="panel-heading">
								
								<h3 class="panel-title" id="uresponse_message_title">Resultado</h3>
							</div>
							<div class="panel-body" id="response_message_data"></div>
						</div>
					</div>
					<input type="hidden" name="date_created" value="">
					<input type="hidden" name="date_updated" value="">
					
					<div style="clear:both;"></div>
					<div id="msg_before_form" class="alert alert-warning alert-dismissible" role="alert"></div>
					<div id="eval_section_holder">
		<?php
		
		foreach($sections as $sectionName => $section) { // For each section
			echo "<div class=\"section well\"><h2 class=\"section_name\">".$sectionName."</h2>";
			echo "<table id=\"eval_form-row".$sectionNum."\" class=\"table table-bordered eval_form_section\"><tbody>";
			foreach($section as $qNum => $question) {
				$qNum = $question['id'];
				echo "<tr id=\"".$sectionNum."_".$qNum."\" class=\"table-striped\">";
				echo "<td class=\"question\">".$question["text"]."</td>";
				$qvals = json_decode($question['options']);
				foreach($qvals as $val => $option) {
					echo "<td id=\"".$sectionNum."_".$qNum."_".$val."\" class=\"option\"><div class=\"val\">".$val."</div>".$option."</td>";
				}
				echo "<td id=\"".$sectionNum."_".$qNum."_—\" class=\"option ni\"><div class=\"val\">&mdash;</div>No Informar</td>"; // Add a "do not report" option
				//<label for="comments">Comentario</label><textarea id="comments" name="comments"></textarea>
				echo "<td id=\"".$sectionNum."_".$qNum."_comments\" class=\"comments\"><div class=\"val\"><label for=\"".$sectionNum."_".$qNum."_commentbox\">Comentario</label></div><textarea id=\"".$sectionNum."_".$qNum."_commentbox\" name=\"comments[]\" class=\"form-control\"></textarea></td>"; // Add a comment option
				echo "</tr>";
			}
			echo "</tbody></table></div>\n";
			$sectionNum++;
		}
		?>
					</div>
					<div id="msg_after_form" class="alert alert-warning alert-dismissible" role="alert"></div>
					<div class="row well">
						<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
						<button type="submit" class="btn btn-default savebtn" style="float:right;">Guardar</button>
					</div>
				</div>
			</form>
		</div>
</div> <!--//close container-->
<?=$tpl->scriptIncludes();?>
<script>
	// Some information given by php
	<?php
		echo "var circuit_rider = ".json_encode($circuit_rider).";";
		echo "var community = ".json_encode($community).";";
	?>
	
	var form_target = "eval_form_new";
</script>
<script type="text/javascript">
	$(function(){
		$('input#date_recorded').appendDtpicker({
			"locale": "es",
			"dateFormat": "YYYY-MM-DD"
		});
	});
	
	$(".close_btn").click(function() {window.location.href = "./";});
</script>
	<?php
	}
	
	echo $tpl->ScreenSmallFoot();
}
else if($_GET['p']=='eval_form_update') {
	echo $tpl->ScreenSmallHead();
	$error = false;
	if(!(
			(
				isset($_POST['community']) &&
				isset($_POST['municipality']) &&
				isset($_POST['department']) &&
				isset($_POST['community_id'])
			) || 
			(
				isset($_GET['community_id'])
			)
		)) {
		// Error. Set the variables
		echo "not set";
		
		$error=true;
	}
	try {
		$community_id = (isset($_POST['community_id']) ? intval($_POST['community_id']) : intval($_GET['community_id']));
		$circuit_rider_id = (isset($_POST['circuit_rider_id']) ? intval($_POST['circuit_rider_id']) : intval($_SESSION['circuit_rider_id']));
		
		$circuit_rider = $db->getOne("circuit_riders",$circuit_rider_id);
		unset($circuit_rider["password"]);
		$community = $db->getOne("communities",$community_id);
		$latest_data_unordered = $db->latestData($community_id);
		// Instead of just a sequential array, make it associative by question_id
		$latest_data = array();
		foreach($latest_data_unordered as $k => $v) {
			$latest_data[$v["question_id"]] = $v;
		}
		unset($latest_data_unordered);
		
	}
	catch (Exception $e) {
		echo "couldn't get CR";
		$error=true;
	}
	
	if($error==false) {
		$form_questions = $db->listAll("questions");
		
		// Add a higher dimension to group by "section"
		$sections = array();
		foreach($form_questions as $q) {
			if($q["current"]) { // db flag to hide questions
				if(!isset($sections[$q["section"]]) || !is_array($sections[$q["section"]])) {
					$sections[$q["section"]] = array();
				}
				$sections[$q["section"]][] = $q;
			}
		}
		$sectionNum = 0;
		?>
		
		<div class="container" id="eval_form_container">
			<form name="eval_form" id="eval_form" action="action?p=eval_form_update" method="post" role="form">
				<div class="page-header well" style="margin-bottom:20px;">
				<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
				<button type="submit" class="btn btn-default savebtn" style="float:right;">Guardar</button>
					<h1>Community Evaluation Form <small>(<?=$circuit_rider['first_name'];?> <?=$circuit_rider['last_name'];?>)</h1>
					<h2><?php
						echo $community['community'].", ".$community['municipality'].", ".$community['department'];
					?></h2><hr>
					<div class="row" style="text-align:center;">
						<label for="date_recorded">Fecha <em>(año-mes-día)</em></label> <input disabled="disabled" type="text" name="date_recorded" value="" id="date_recorded" placeholder="año-mes-día"> <span>Esta fecha no podrá ser cambiado</span>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-info" id="response_message" style="margin-top:10px;display:none;">
						<button type="button" class="btn btn-default close_btn" style="float:right;">Volver</button>
							<div class="panel-heading">
								
								<h3 class="panel-title" id="uresponse_message_title">Resultado</h3>
							</div>
							<div class="panel-body" id="response_message_data"></div>
						</div>
					</div>
					<input type="hidden" name="date_created" value="">
					<input type="hidden" name="date_updated" value="">
					
					<div style="clear:both;"></div>
					<div id="msg_before_form" class="alert alert-warning alert-dismissible" role="alert"></div>
					<div id="eval_section_holder">
		<?php
		
		$js_click_ids = array();
		foreach($sections as $sectionName => $section) { // For each section
			echo "<div class=\"section well\"><h2 class=\"section_name\">".$sectionName."</h2>";
			echo "<table id=\"eval_form-row".$sectionNum."\" class=\"table table-bordered eval_form_section\"><tbody>";
			foreach($section as $qNum => $question) {
				$qNum = $question['id'];
				echo "<tr id=\"".$sectionNum."_".$qNum."\" class=\"table-striped\">";
				echo "<td class=\"question\">".$question["text"]."</td>";
				$qvals = json_decode($question['options']);
				$id_added = false;
				foreach($qvals as $val => $option) {
					echo "<td id=\"".$sectionNum."_".$qNum."_".$val."\" class=\"option\"><div class=\"val\">".$val."</div>".$option."</td>";
					// Should we select this?
					if(array_key_exists($qNum,$latest_data) && $latest_data[$qNum]["response"]==$val) {
						$js_click_ids[]=$sectionNum."_".$qNum."_".$val;
						$id_added=true;
					}
				}
				if(!$id_added) {
					// put in the dash
					$js_click_ids[]=$sectionNum."_".$qNum."_—";
				}
				echo "<td id=\"".$sectionNum."_".$qNum."_—\" class=\"option ni\"><div class=\"val\">&mdash;</div>No Informar</td>"; // Add a "do not report" option
				//<label for="comments">Comentario</label><textarea id="comments" name="comments"></textarea>
				echo "<td id=\"".$sectionNum."_".$qNum."_comments\" class=\"comments\"><div class=\"val\"><label for=\"".$sectionNum."_".$qNum."_commentbox\">Comentario</label></div><textarea id=\"".$sectionNum."_".$qNum."_commentbox\" name=\"comments[]\" class=\"form-control\"></textarea></td>"; // Add a comment option
				echo "</tr>";
			}
			echo "</tbody></table></div>\n";
			$sectionNum++;
		}
		?>
					</div>
					<div id="msg_after_form" class="alert alert-warning alert-dismissible" role="alert"></div>
					<div class="row well">
						<button type="button" class="btn btn-default close_btn" style="float:right;">Cancelar</button>
						<button type="submit" class="btn btn-default savebtn" style="float:right;">Guardar</button>
					</div>
				</div>
			</form>
		</div>
</div> <!--//close container-->
<?=$tpl->scriptIncludes();?>
<script>
	// Some information given by php
	<?php
		echo "var circuit_rider = ".json_encode($circuit_rider).";";
		echo "var community = ".json_encode($community).";";
		echo "var click_boxes = ".json_encode($js_click_ids).";";
	?>
	var form_target = "eval_form_update";
</script>
<script>
	// Go through and select the options
</script>
<script type="text/javascript">
	$(function(){
		$('input#date_recorded').appendDtpicker({
			"locale": "es",
			"dateFormat": "YYYY-MM-DD"
		});
	});
	
	$(".close_btn").click(function() {window.location.href = "./";});
</script>
	<?php
	}
	
	echo $tpl->ScreenSmallFoot();
}