<?php

require_once('./lib/mysql.php');
require_once('./lib/db.php');
require_once('./lib/template.php');
try {
	$db = new Db;
	$tpl = new Template;
}
catch (Exception $e) {
	echo "<h2>Error (".$e->getCode().")</h2>";
	echo "<p>".$e->getMessage()."</p>";
}
if(isset($_GET['out'])) {
	$error = 'You have logged out';
}
if(isset($_POST['username'])) {
	session_start(); // Starting Session
	$error=''; // Variable To Store Error Message
	$cr = $db->getOneByKeys("circuit_riders",array("email"=>$_POST['username']));
	if (empty($_POST['username']) || empty($_POST['password']) || count($cr)<=0) {
		$error = "Nombre de usuario o contraseña no es válida";
	}
	else
	{
		// Define $username and $password
		$username=$_POST['username'];
		$circuit_rider_id = $cr['id'];
		$pt_password=$_POST['password'];
		if ($db->verifyUserPassword($circuit_rider_id,$pt_password)) {
			$_SESSION['circuit_rider_id']=$circuit_rider_id; // Initializing Session
			header("location: index.php"); // Redirecting To Other Page
		} else {
			$error = "Nombre de usuario o contraseña no es válida";
		}

	}
}
?>
<?=$tpl->screenSmallHead();?>
<div class="container content loginbox" style="">
	<div class="row">
		<div class="col-lg-4 col-lg-offset-4" id="form_container">
		<?php if(isset($error)) {
			echo '<div id="msg_before_form" class="alert alert-warning alert-dismissible" role="alert">'.$error.'</div>';
		} ?>
			<div class="panel panel-info">
				<div class="panel-heading"><h2 class="panel-title">Iniciar sesion</h2></div>
				<div class="panel-body">
					<form action="login.php" method="post" id="login_form">
						<label for="username">ID :</label>
						<input id="username" name="username" placeholder="correo" type="text" class="form-control">
						<input id="circuit_rider_id" name="circuit_rider_id" type="hidden">
						<label for="password">Password :</label>
						<input id="password" name="password" placeholder="**********" type="password" class="form-control">
						<br>
						<button type="submit" class="btn submit_form btn-success btn-block">Entrar</button>
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?=$tpl->scriptIncludes();?>
<script>
var canSubmit = true;
/*function attachUserAC() {
	$('input#username').autocomplete({
		serviceUrl: './action.php?p=json_circuit_riders',
		minChars: 1,
		onSelect: function (suggestion) {
			console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
			$("input#circuit_rider_id").val(suggestion.id);
			$(".submit_form").prop('disabled', false).addClass("btn-success");
			canSubmit=true;
		},
		onInvalidateSelection: function() {
			$("input#circuit_rider_id").val("");
			$(".submit_form").prop('disabled', true).removeClass("btn-success");
			canSubmit=false;
		}
	});
}
attachUserAC();
$(".submit_form").prop('disabled', true).removeClass("btn-success");

$("#login_form").submit(function(e){
	if(!canSubmit) {
		e.preventDefault();
		$("#form_container").append('<div id="msg_before_form" class="alert alert-warning alert-dismissible" role="alert">You must select your name from the list.</div>');
		return false;
	}
});*/
</script>
<?=$tpl->ScreenSmallFoot();?>