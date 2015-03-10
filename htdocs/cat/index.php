<?php

require_once('./lib/mysql.php');
require_once('./lib/db.php');
require_once('./lib/template.php');
require_once('./lib/session.php');
try {
	$db = new Db;
	$tpl = new Template;
	$circuit_rider = $db->getOne("circuit_riders",intval($_SESSION['circuit_rider_id']));
	$is_admin = ($circuit_rider["role"] == 10 ? true : false );
	$is_circuit_rider = ($circuit_rider["role"] == 1 ? true : false );
}
catch (Exception $e) {
	echo "<h2>Error (".$e->getCode().")</h2>";
	echo "<p>".$e->getMessage()."</p>";
}

?>
<?=$tpl->screenLargeHead($is_admin); ?>
<div class="container" style="padding-top:40px;">
	<div class="page-header well">
		<?php if($is_admin) { ?>
			<h2>Evaluación Comunitaria <small>(<?=$circuit_rider['first_name'];?> <?=$circuit_rider['last_name'];?>)</small> <span style="float:right;">Administrador</span></h2>
		<?php } else { ?>
			<h2>Evaluación Comunitaria <small>(<?=$circuit_rider['first_name'];?> <?=$circuit_rider['last_name'];?>)</small></h2>
		<?php } ?>
	</div>
	<div class="row well">
		<!--// <div class="col-lg-3">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Informe Rápido</h3>
				</div>
				<div class="panel-body" id="quick_report">
				<button style="font-size:25px;height:50px;font-weight:800;" class="btn btn-info form-control">Crear</button></div>
				
			</div>
		</div> -->
		<div class="col-lg-8">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Comunidades</h3>
				</div>
				<div class="panel-body" id="communities">
				<style>
					#community_select_table {table-layout:fixed;overflow:none;}
					#community_select_table tr td:nth-child(4), #community_select_table tr th:nth-child(4) {text-align:center;width:85px}
					#community_select_table tr td:nth-child(4) .dropdown li {text-align:left;}
					#community_select_table_filter, #community_select_table_paginate {float:right;}
					#community_select_table_paginate .pagination {margin-top:0px;}
					#community_select_table_info {margin-top:10px;}
					#community_select_table_length {float:left;}
				</style>
<?php
if($is_admin) {
	$community_list = $db->getCommunities();
	$circuit_rider_list = $db->listAll("circuit_riders");
	$circuit_rider_ordered_list = array();
	foreach($circuit_rider_list as $q) {
		$circuit_rider_ordered_list[$q['id']] = $q;
	}
	echo "<table class=\"table table-bordered table-striped table-hover table-fancy\" id=\"community_select_table\">";
	echo "<thead><th>Comunidad</th><th>Municipio</th><th>Departamento</th><th>Opciones</th><th>Ultima Fecha</th><th>Circuit Rider</th></thead><tbody>";
	foreach($community_list as $c) {
		$dates = $db->date_of_data($c["id"]);
		$last_date = (isset($dates['recorded_date']) ? $dates['recorded_date'] : "nunca");
		echo "<tr>";
		echo "<td>".$c["community"]."</td>";
		echo "<td>".$c["municipality"]."</td>";
		echo "<td>".$c["department"]."</td>";
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
		<li role="presentation"><a role="menuitem" tabindex="-1" href="./basic_report.php?p=basic_history&community_id='.$c['id'].'">Todas las Evaluaciones</a></li>
	  </ul>
	</div>';
		echo " </td>";
		echo "<td>".$last_date."</td>";
		echo "<td>".$circuit_rider_ordered_list[$c['circuit_rider_id']]['first_name']." ".$circuit_rider_ordered_list[$c['circuit_rider_id']]['last_name']."</td>";
		echo "</tr>";
	}
	echo "</tbody></table>\n";
}
else {
	$community_list = $db->getCommunities(null,null,$circuit_rider["id"]);
	echo "<table class=\"table table-bordered table-striped table-hover table-fancy\" id=\"community_select_table\">";
	echo "<thead><th>Comunidad</th><th>Municipio</th><th>Departamento</th><th>Opciones</th><th>Ultima Fecha</th></thead><tbody>";
	foreach($community_list as $c) {
		$dates = $db->date_of_data($c["id"]);
		$last_date = (isset($dates['recorded_date']) ? $dates['recorded_date'] : "nunca");
		echo "<tr>";
		echo "<td>".$c["community"]."</td>";
		echo "<td>".$c["municipality"]."</td>";
		echo "<td>".$c["department"]."</td>";
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
		<li role="presentation"><a role="menuitem" tabindex="-1" href="./basic_report.php?p=basic_history&community_id='.$c['id'].'">Lista de Registros</a></li>
	  </ul>
	</div>';
		echo " </td>";
		echo "<td>".$last_date."</td>";
		echo "</tr>";
	}
	echo "</tbody></table>\n";
}
?>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Crear Evaluación</h3>
				</div>
				<div class="panel-body" id="community_selector">
				
				<form action="form.php?p=eval_form_new" method="POST" id="city_select_form">
					<label for="department">Departamento</label><input type="text" name="department" id="department" class="autocomplete form-control" placeholder="Departamento">
					<label for="municipality">Municipio</label><input type="text" name="municipality" id="municipality" class="autocomplete form-control" placeholder="Municipio">
					<label for="community">Comunidad</label><input type="text" name="community" id="community" class="autocomplete form-control" placeholder="Comunidad"><br>
					<input type="hidden" name="community_id" id="community_id">
					
					
					<?php if($is_admin) { ?>
						<?php $tpl->circuit_rider_dropdown($db); ?><br>
					<?php } else { ?>
						<input type="hidden" name="circuit_rider_id" id="circuit_rider_id" value="<?=$circuit_rider['id'];?>"><br>
					<?php } ?>
					
					<button type="submit" id="community_select_button" class="btn" style="float:right;">Crear Nueva</button>
				</form>
				<div id="current_selection">Seleccionado: <span id="department"></span> <span id="municipality"></span> <span id="community"></span> </div>
				<?php if($is_admin) {echo '<div id="current_selection_cr_name">Circuit Rider: <span id="name"></span> </div>'; } ?>
				</div>
				
			</div>
		</div>
	</div>
</div> <!--//END CONTAINER-->
<?=$tpl->scriptIncludes();?>
<?php
	$cr_restrict = ($is_admin ? "" : "&circuit_rider_id=".$circuit_rider['id'] );
	$cr_name_show = ($is_admin ? "true" : "false" );
?>
<script>
/* 
##
##	BEGIN AutoComplete
##
*/
function disableMunicipalityAC() {
	$("input#municipality").prop('disabled', true).val("");
	$("#current_selection #department").text("");
	disableCommunityAC();
}
function disableCommunityAC() {
	$("input#community").prop('disabled', true).val("");
	$("#current_selection #municipality").text("");
	$("input#community_id").val("");
	$("#community_select_button").prop('disabled', true).removeClass("btn-success");
}
$('input#department').autocomplete({
    serviceUrl: './action.php?p=json_communities<?=$cr_restrict;?>',
	minChars: 0,
    onSelect: function (suggestion) {
        console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
		disableMunicipalityAC();
		if(suggestion.value.length>0)
			attachMunicipalityAC(suggestion.value);
		$("#current_selection #department").text(suggestion.value);
    },
	onInvalidateSelection: disableMunicipalityAC
});
function attachMunicipalityAC(department) {
	$("input#municipality").prop('disabled', false)
	$('input#municipality').autocomplete({
		serviceUrl: './action.php?p=json_communities&department='+department+'<?=$cr_restrict;?>',
		minChars: 0,
		onSelect: function (suggestion) {
			console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
			disableCommunityAC();
			if(department.length>0)
				attachCommunityAC(department, suggestion.value);
		$("#current_selection #municipality").text(suggestion.value);
		},
		onInvalidateSelection: disableCommunityAC
	});
}
function attachCommunityAC(department, municipality) {
	$("input#community").prop('disabled', false);
	$('input#community').autocomplete({
		serviceUrl: './action.php?p=json_communities&department='+department+'&municipality='+municipality+'<?=$cr_restrict;?>',
		minChars: 0,
		onSelect: function (suggestion) {
			console.log('You selected: ' + suggestion.value + ', ' + suggestion.data + ', ' + suggestion.circuit_rider_name);
			$("input#community_id").val(suggestion.data);
			$("#current_selection #community").text(suggestion.value);
			$("#community_select_button").prop('disabled', false).addClass("btn-success");
			if(<?=$cr_name_show;?>) {
				$("#current_selection_cr_name #name").text(suggestion.circuit_rider_name);
				$("input#circuit_rider_id").val(suggestion.circuit_rider_id);
			}
		},
		onInvalidateSelection: function() {	$("input#community_id").val(""); $("#current_selection #community").text("");$("#community_select_button").prop('disabled', true).removeClass("btn-success");}
	});
}
disableMunicipalityAC();
/* 
##
##	END AutoComplete
##
*/

/* 
##
##	BEGIN city_select_form controller AutoComplete
##
*/

$("form#city_select_form").submit(function(e) {
	var $community = $("input#community");
	var $municipality = $("input#municipality");
	var $department = $("input#department");
	
	if($municipality.prop('disabled') || $community.prop('disabled') || $community.val().length<=1) {
		e.preventDefault();
		alert("Form not complete");
		return false;
	}

});

/* 
##
##	END city_select_form controller AutoComplete
##
*/
/* 
##
##	BEGIN colorbox controller
##
*/
function attachColorbox() {
	$(".small_cb").colorbox({
		iframe:true,
		width:"800px",
		height:"400px",
		onClosed:function() {
			//refreshPageInfo();
		}
	});
}

attachColorbox();
/* 
##
##	END colorbox controller
##
*/
var lang_data = {
		"emptyTable":     "No hay datos disponibles de la tabla",
		"info":           "Mostrando _START_ a _END_ de comunidades _TOTAL_",
		"infoEmpty":      "Mostrando 0 a 0 de comunidades 0",
		"infoFiltered":   "(filtrado de comunidades totales _MAX_)",
		"infoPostFix":    "",
		"thousands":      ",",
		"lengthMenu":     "Mostrar comunidades _MENU_",
		"loadingRecords": "Cargando...",
		"processing":     "Trabajando...",
		"search":         "Buscar:",
		"zeroRecords":    "No hay registros coincidentes encontrados",
		"paginate": {
			"first":      "Primero",
			"last":       "Último",
			"next":       "Siguiente",
			"previous":   "Anterior"
		},
		"aria": {
			"sortAscending":  ": Orden ascendente columna",
			"sortDescending": ": Orden descendente columna"
		}
	}
$('.table-fancy').DataTable({
	"language": lang_data,
	<?php
	if($is_admin) {
	?>
	"columns": [
		null,
		null,
		null,
		{ "orderable": false },
		null,
		null
	]
	<?php
	} else {
	?>
	"columns": [
		null,
		null,
		null,
		{ "orderable": false },
		null
	]
	<?php } ?>
	 
});
</script>
</body>
</html>