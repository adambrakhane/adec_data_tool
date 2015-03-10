<?php

function pr($d) {echo "<pre>"; print_r($d); echo "<pre>";}

require_once('./lib/template.php');
require_once('./lib/mysql.php');
require_once('./lib/db.php');


$db = new Db;
$tpl = new Template;

$i_data["community_id"] = 1;
$i_data["question_id"] = 2;
$i_data["recorded_by_id"] = 3;
$i_data["recorded_date"] = "2015-03-01";
$i_data["response"] = 2;
$i_data["comments"] = "Some comment";

pr($db->getTableSchema("circuit_riders"));

try {
	var_dump($db->insert($i_data,"data"));
}
catch(Exception $e) {
	if(strpos($e->getMessage(),"Integrity constraint violation: 1452")!==FALSE)
		echo "FK constraint";
	else {
		echo "Generic Error";
	}
}


?>
<link rel="stylesheet" href="lib/style.css" />
<div id="current_selection">Seleccionado: <span id="department"></span> <span id="municipality"></span> <span id="community"></span> </div>
<form action="form.php?p=eval_form_new" method="POST" id="city_select_form">
<label for="department">Departamento</label><input type="text" name="department" id="department" class="autocomplete" placeholder="Departamento">
<label for="municipality">Municipio</label><input type="text" name="municipality" id="municipality" class="autocomplete" placeholder="Municipio">
<label for="community">Comunidad</label><input type="text" name="community" id="community" class="autocomplete" placeholder="Comunidad">
<input type="hidden" name="community_id" id="community_id">
<?php $tpl->circuit_rider_dropdown($db); ?><br>
<input type="submit">
</form>

<script src="lib/jquery.min.js"></script>'
<script src="lib/jquery.autocomplete.js"></script>'
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
}
$('input#department').autocomplete({
    serviceUrl: './action.php?p=json_communities',
	minChars: 0,
    onSelect: function (suggestion) {
        console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
		disableMunicipalityAC();
		attachMunicipalityAC(suggestion.value);
		$("#current_selection #department").text(suggestion.value);
    },
	onInvalidateSelection: disableMunicipalityAC
});
function attachMunicipalityAC(department) {
	$("input#municipality").prop('disabled', false)
	$('input#municipality').autocomplete({
		serviceUrl: './action.php?p=json_communities&department='+department,
		minChars: 0,
		onSelect: function (suggestion) {
			console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
			disableCommunityAC();
			attachCommunityAC(department, suggestion.value);
		$("#current_selection #municipality").text(suggestion.value);
		},
		onInvalidateSelection: disableCommunityAC
	});
}
function attachCommunityAC(department, municipality) {
	$("input#community").prop('disabled', false);
	$('input#community').autocomplete({
		serviceUrl: './action.php?p=json_communities&department='+department+'&municipality='+municipality,
		minChars: 0,
		onSelect: function (suggestion) {
			console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
			$("input#community_id").val(suggestion.data);
		$("#current_selection #community").text(suggestion.value);
		},
		onInvalidateSelection: function() {	$("input#community_id").val(""); $("#current_selection #community").text("");}
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
</script>