<?php
session_start();

require_once "../defines.php";
require_once ROOT."/classes/database.class.php";

if (isset($_POST['email']) && isset($_POST['password'])) {
	try {
		$user = new User($_POST['email'], $_POST['password']);
		$_SESSION['user'] = $user;
		$res = true;
	}
	catch (exception $e) {
		$res = false;
	}
	
	echo json_encode(
		array(
			"success" => $res,
			"error" => User::$error_message
		)
	);

	return;
}