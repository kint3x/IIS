<?php
session_start();

require_once "../defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/database.class.php";

if(isset($_POST['email']) && isset($_POST['password'])){
	$res = User::register_user($_POST['email'], $_POST['password']);

 	echo json_encode(
		array(
			"success" => $res,
			"error" => User::$error_message
		)
	);
	
	return;
}