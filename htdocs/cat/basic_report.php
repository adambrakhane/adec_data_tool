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



if($_GET['p']=='basic_history') {
	echo $tpl->ScreenSmallHead();
	$circuit_rider_id = (isset($_GET['circuit_rider_id']) ? intval($_GET['circuit_rider_id']) : intval($_SESSION['circuit_rider_id']));
	
	try {
		if(!(isset($_GET['community_id']) || isset($_POST['community_id']))) {
			throw new Exception("Tiene que volver.",0);
		}
		$community_id = (isset($_GET['community_id']) ? intval($_GET['community_id']) : intval($_GET['community_id']));
		$circuit_rider = $db->getOne("circuit_riders",$circuit_rider_id);
		//$community_list = $db->getCommunities(null,null,$circuit_rider["id"]);
		$date_list = $db->dates_of_data($community_id);
		$recordset_list = array();
		foreach($date_list as $date) {
			// Get data for date
			$recordset_list[] = $db->dataByDate($community_id,$date["recorded_date"]);
/*			echo '<form id="'.$date['recorded_date'].'" action="" method="post">';
			foreach($recordset_list as $questions) {
				echo '<input name="'.$questions[0]['community_id'].' class="community_id" value="'.$questions[0]['community_id'].'">';
				echo '<input name="'.$questions[0]['recorded_date'].' class="recorded_date" value="'.$questions[0]['recorded_date'].'">';
				foreach ($questions as $response) {
					echo '<input name="'.$response['question_id'].' class="question_id" value="'.$response['question_id'].'">';
					echo '<input name="'.$response['recorded_by_id'].' class="recorded_by_id" value="'.$response['recorded_by_id'].'">';
					echo '<input name="'.$response['response'].' class="response" value="'.$response['response'].'">';
					echo '<input name="'.$response['comments'].' class="comments" value="'.$response['comments'].'">';
				}
			}
			echo '</form>';
*/
		}
	?>
<form id="basic_history_form" action="action.php?p=basic_history" method="post">
<input type="hidden" name="circuit_rider_id" value="<?=$circuit_rider_id;?>">
	<div class="container well">
		<div class="page-header">
		<button type="button" class="btn btn-default close_btn" style="float:right;">Volver</button>
		<?php // <button type="submit" class="btn btn-default" style="float:right;">Guardar</button> ?>
			<h1>Lista de Registros <small></small></h1>
			
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

	echo "<table class=\"table table-bordered table-striped table-hover\">";
	echo "<thead><th>Ultima Fecha</th><th>Numero de Registros</th><th></th><th></th></thead><tbody>";
	foreach($recordset_list as $k => $responses) {
		$num_of_responses = 0;
		foreach($responses as $question_id => $v) {
			$num_of_responses++;
		}
		//$last_date = (isset($dates['recorded_date']) ? $dates['recorded_date'] : "nunca");
		echo "<tr>";
		echo "<td class=\"date\">".$date_list[$k]["recorded_date"]."</td>";
		echo "<td>".$num_of_responses."</td>";
		echo "<td><a href=\"./basic_report.php?p=see_date&recorded_date=".$date_list[$k]["recorded_date"]."&community_id=".$v["community_id"]."\" class=\"btn btn-info see_data btn-sm\">Ver Datos</a></td>";
		if($is_admin) {
			echo "<td><button type=\"button\" class=\"btn btn-danger delete_data btn-sm\">Borrar Datos</button></td>";
		}
		else {
			echo "<td></td>";
		}
		/*echo "<td>";
		print_r($v);
		echo " </td>";*/
		//echo "<td>".$last_date." (<button>Ver</button>)</td>";
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
		function attachDelete() {
			$(".delete_data").click(function(e) {
				var date = $(this).parent().parent().children("td.date").text();
				var this_row = $(this).parent().parent();
				var community_id = <?=$community_id;?>;
				if(confirm("¿Está usted muy seguro de que quieres eliminar registros de "+date))
				{
					console.log("date: "+date+"\tCR: "+community_id);$.ajax({
						type: "GET",
						url: "action.php?p=delete_data&community_id="+community_id+"&date_recorded="+date,
						success: function(msg) {
							if(msg.response==200) {
								this_row.addClass("alert-danger");
								this_row.children("td").children("button").hide().prop('disabled',true).removeClass("btn-danger").addClass("btn-success").text('Borrado!').slideDown();
								this_row.children("td").children("a").slideUp();
							}
							else {
								alert("ERROR: "+msg.message);
							}
							console.log(msg);
						},
						dataType: "json",
					});
				}
				else
				{
					
				}
			});
		}
		attachDelete();
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
	</script>
	<?php
		} // End try
	catch(Exception $e) {
			echo "<h2>Error connecting to database</h2>";
		echo $e->getMessage();
		return;
	}
	echo $tpl->ScreenSmallFoot();
}
else if($_GET['p']=='see_date') {
	echo $tpl->ScreenSmallHead();
	$circuit_rider_id = (isset($_GET['circuit_rider_id']) ? intval($_GET['circuit_rider_id']) : intval($_SESSION['circuit_rider_id']));
	
	try {
		if(!(isset($_GET['community_id']) || isset($_POST['community_id']))) {
			throw new Exception("Tiene que volver.",0);
		}
		if(!(isset($_GET['recorded_date']) || isset($_POST['recorded_date']))) {
			throw new Exception("Tiene que volver.",0);
		}
		$community_id = (isset($_GET['community_id']) ? intval($_GET['community_id']) : intval($_GET['community_id']));
		$circuit_rider = $db->getOne("circuit_riders",$circuit_rider_id);
		$community = $db->getOne("communities",$community_id);
		//$community_list = $db->getCommunities(null,null,$circuit_rider["id"]);

		$recordset_list = array();

		$recordset_list[] = $db->dataByDate($community_id,$_GET["recorded_date"]);
		
		$form_questions = $db->listAll("questions");
		$form_questions_assoc = array();
		// Add a higher dimension to group by "section"
		$sections = array();
		foreach($form_questions as $q) {
			if($q["current"]) { // db flag to hide questions
				if(!isset($sections[$q["section"]]) || !is_array($sections[$q["section"]])) {
					$sections[$q["section"]] = array();
				}
				$sections[$q["section"]][$q['id']] = $q;
				$form_questions_assoc[$q['id']] = $q;
			}
		}
		
/*			echo '<form id="'.$date['recorded_date'].'" action="" method="post">';
			foreach($recordset_list as $questions) {
				echo '<input name="'.$questions[0]['community_id'].' class="community_id" value="'.$questions[0]['community_id'].'">';
				echo '<input name="'.$questions[0]['recorded_date'].' class="recorded_date" value="'.$questions[0]['recorded_date'].'">';
				foreach ($questions as $response) {
					echo '<input name="'.$response['question_id'].' class="question_id" value="'.$response['question_id'].'">';
					echo '<input name="'.$response['recorded_by_id'].' class="recorded_by_id" value="'.$response['recorded_by_id'].'">';
					echo '<input name="'.$response['response'].' class="response" value="'.$response['response'].'">';
					echo '<input name="'.$response['comments'].' class="comments" value="'.$response['comments'].'">';
				}
			}
			echo '</form>';
*/
		
	?>
<form id="basic_history_form" action="action.php?p=basic_history" method="post">
<input type="hidden" name="circuit_rider_id" value="<?=$circuit_rider_id;?>">
	<div class="container well">
		<div class="page-header">
		<button type="button" class="btn btn-default close_btn" style="float:right;">Volver a Lista</button>
		<?php // <button type="submit" class="btn btn-default" style="float:right;">Guardar</button> ?>
			<h1>Resumen de <small><?=$community['community'];?>, <?=$community['municipality'];?>, <?=$community['department'];?> </small></h1>
			
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

						<p>Datos de <?=$_GET['recorded_date'];?> - <em><?=$circuit_rider['first_name'];?> <?=$circuit_rider['last_name'];?></em></p>

					<style>
						#basic_report_summary_table {table-layout:fixed;}
						#basic_report_summary_table tr th:nth-child(1) {width:100px;}
						#basic_report_summary_table tr th:nth-child(2) {}
						#basic_report_summary_table tr th:nth-child(3) {width:160px;}
						#basic_report_summary_table tr th:nth-child(4) {width:60px; text-align:center;}
						#basic_report_summary_table tr th:nth-child(5) {width:30px;}
						
						
						#basic_report_summary_table tr td:nth-child(4) {text-align:center;}
					</style>
	<?php

	
	foreach($recordset_list as $k => $responses) {
		$num_of_responses = 0;
		
		foreach($sections as $section_name => $section_questions) {
			echo "<h3>".$section_name."</h3>";
			echo "<table class=\"table table-bordered table-striped table-hover\" id=\"basic_report_summary_table\">";
			echo "<thead><th>Pregunta</th><th>Respuesta</th><th>Comentario</th><th>(valor)</th><th></th></thead><tbody>";
			foreach($responses as $v) {
				$num_of_responses++;
				echo "<tr>";
				if(array_key_exists($v['question_id'],$section_questions)) {
					if(strlen($v['comments'])==0) $v['comments'] = "&mdash;";
					$qvals = json_decode($form_questions_assoc[$v['question_id']]['options'],true);
					//$last_date = (isset($dates['recorded_date']) ? $dates['recorded_date'] : "nunca");
					echo "<td>".$form_questions_assoc[$v['question_id']]['text']."</td>";
					echo "<td>".$qvals[$v['response']]."</td>";
					echo "<td>".$v['comments']."</td>";
					echo "<td>".$v['response']."</td>";
					echo "<td>";
					echo " </td>";
					//echo "<td>".$last_date." (<button>Ver</button>)</td>";

				}
				else {
					/*echo "<td>";
					print_r($section_questions);
					echo " </td>";*/
				}
				echo "</tr>";
			}
			echo "</tbody></table>\n";
		}
		
	}
	
	?>
		</div>
	</div>
</form>
	<?=$tpl->scriptIncludes();?>
	<script>
		$(".close_btn").click(function() {window.location.href = "./basic_report.php?p=basic_history&community_id=<?=$community_id;?>";});
	</script>
	<?php
		} // End try
	catch(Exception $e) {
		echo "<h2>Error connecting to database</h2>";
		echo $e->getMessage();
		return;
	}
	echo $tpl->ScreenSmallFoot();
}
else if($_GET['p']=='by_circuit_rider') {
	echo $tpl->ScreenSmallHead();
	if(!isset($_GET['circuit_rider_id'])) {
		// Show the form 
		
		?>
		<style>
			#community_select_table_filter, #community_select_table_paginate {float:right;}
			#community_select_table_paginate .pagination {margin-top:0px;}
			#community_select_table_info {margin-top:10px;}
			#community_select_table_length {float:left;margin-top:20px;}
		</style>
		<div class="container well">
			<form name="by_circuit_rider_select" id="by_circuit_rider_select" action="basic_report.php" method="get" role="form">
				<input type="hidden" name="p" value="by_circuit_rider">
				<div class="row">
					<div class="col-lg-6">
						<div class="panel panel-info" id="date_panel">
							<div class="panel-heading"><h2 class="panel-title">Cual Fecha</h2></div>
							<div class="panel-body">
								<div style="margin:10px 0px;border-bottom:1px solid black;text-align:center;">
									<input type="checkbox" name="all_time" id="all_time_cb"> <label for="all_time_cb">Siempre</label>
								</div>
								<label for="date_from">Desde <em>(año-mes-día)</em></label> <input type="text" name="date_from" value="" id="date_from" placeholder="año-mes-día" class="form-control datebox"><br>
								<label for="date_to">Hasta <em>(año-mes-día)</em></label> <input type="text" name="date_to" value="" id="date_to" placeholder="año-mes-día" class="form-control datebox">
								
							</div>
						</div>
						<div class="panel panel-info">
							<div class="panel-heading"><h2 class="panel-title">Cual Circuit Rider</h2></div>
							<div class="panel-body">
								<label for="circuit_rider_id">Circuit Rider</label>
								<?=$tpl->circuit_rider_dropdown($db);?>
								<div style="margin-top:10px;">
								<button type="submit" class="btn btn-success">Todas sus Comunidades</button>
								<button type="button" class="btn btn-success pull-right" id="pick_communities">Elegir Comunidades</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="panel panel-info" id="circuit_rider_communities" style="display:none;">
							<div class="panel-heading"><h2 class="panel-title">Hi</h2></div>
							<div class="panel-body"></div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
		echo $tpl->scriptIncludes();
		?>
		<script>
		$(function(){
			$('input.datebox').appendDtpicker({
				"locale": "es",
				"dateFormat": "YYYY-MM-DD"
			});
			$("#all_time_cb").click(function() {
				if($("#all_time_cb").prop('checked')==true) {
					$("#date_panel").find(".datebox").prop('disabled',true);

				}
				else {
					$("#date_panel").find(".datebox").prop('disabled',false);
				}
			}).click();
			
			$("#by_circuit_rider_select").submit(function(e) {
				$("#community_select_table_length").remove();
			});
			function show_cr_communities(msg) {
				var $panel = $("#circuit_rider_communities");
				var $body = $("#circuit_rider_communities .panel-body"); // take copy
				var $title = $("#circuit_rider_communities .panel-title");
				if(msg.response==200) {
					$body.append("<span>Este Circuit Rider tiene "+msg.data.length+" comunidades.</span><table id=\"community_select_table\" class='table-fancy'><thead><th></th><th>Comunidad</th></thead><tbody></tbody></table>");
					$.each(msg.data,function(key,value) {
						console.log(value);
						$body.find("tbody").append('<tr><td><input id="cr_cb_'+value.community_id+'" type="checkbox" name="communities[]" value="'+value.community_id+'"></td><td><label for="cr_cb_'+value.community_id+'">'+value.community+', '+value.municipality+', '+value.department+'</label></td></tr>');
					});
					$body.append('<button type="submit" class="btn btn-success btn-block">Crear Resumen</button>');
				}
				else {
					$panel.removeClass("panel-info").addClass("panel-danger");
					$body.append(msg.message);
				}
				$panel.slideDown(1000);
				attachTablehandler();
			}
			$("#pick_communities").click(function(e) {
				var cr_id = $("select#circuit_rider_id").val();
				var $panel = $("#circuit_rider_communities");
				var $body = $("#circuit_rider_communities .panel-body"); // take copy
				var $title = $("#circuit_rider_communities .panel-title");
				$body.html("");
				$title.html("");
				$panel.slideUp();
				$.ajax({
					type: "GET",
					url: "action.php?p=json_communities&circuit_rider_id="+cr_id,
					success: function(msg) {show_cr_communities(msg);},
					dataType: "json",
				});
			});
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
			
			function attachTablehandler() {
				$('.table-fancy').DataTable({
					"language": lang_data,
					"columns": [
						{ "orderable": false },
						null
					]
				});
			}
		});
		</script>
		<?php
		// We're done with the page.
		echo $tpl->ScreenSmallFoot();
		die();
	}
	print_r($_GET);
	die();
	$circuit_rider_id = (isset($_GET['circuit_rider_id']) ? intval($_GET['circuit_rider_id']) : intval($_SESSION['circuit_rider_id']));
	
	try {
		if(!(isset($_GET['community_id']) || isset($_POST['community_id']))) {
			throw new Exception("Tiene que volver.",0);
		}
		if(!(isset($_GET['recorded_date']) || isset($_POST['recorded_date']))) {
			throw new Exception("Tiene que volver.",0);
		}
		$community_id = (isset($_GET['community_id']) ? intval($_GET['community_id']) : intval($_GET['community_id']));
		$circuit_rider = $db->getOne("circuit_riders",$circuit_rider_id);
		$community = $db->getOne("communities",$community_id);
		//$community_list = $db->getCommunities(null,null,$circuit_rider["id"]);

		$recordset_list = array();

		$recordset_list[] = $db->dataByDate($community_id,$_GET["recorded_date"]);
		
		$form_questions = $db->listAll("questions");
		$form_questions_assoc = array();
	}
	
	catch (Exception $e) {
	
	}
}