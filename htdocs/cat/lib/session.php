<?php
	session_start();
	if(!isset($_SESSION['circuit_rider_id'])){
		header("location: login.php");
	}
?>