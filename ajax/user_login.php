<?php

require_once "../defines.php";
require_once ROOT."/classes/database.class.php";

start_session_if_none();

if (isset($_POST['email']) && isset($_POST['password'])) {
	try {
		$user = new User(htmlspecialchars_decode($_POST['email'],ENT_QUOTES), htmlspecialchars_decode($_POST['password'],ENT_QUOTES));
		$_SESSION['user'] = $user;
		$_SESSION['logged_in_t'] = time();
		$res = true;
	}
	catch (Exception $e) {
		$res = false;
	}
	
	echo_json_response($res, User::$error_message);

	return;
}