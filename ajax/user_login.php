<?php

require_once "../defines.php";
require_once ROOT."/classes/database.class.php";

session_start();

if (isset($_POST['email']) && isset($_POST['password'])) {
	try {
		$user = new User($_POST['email'], $_POST['password']);
		$_SESSION['user'] = $user;
		$res = true;
	}
	catch (exception $e) {
		$res = false;
	}
	
	echo_json_response($res, User::$error_message);

	return;
}