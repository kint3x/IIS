<?php

require_once "../defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/database.class.php";

start_session_if_none();

if(isset($_POST['passwordCurrent']) && isset($_POST['passwordNew']) && isset($_POST['passwordNewAgain'])){
	if(!isset($_SESSION['user'])){
		echo_json_response(false, 'Neprihlásený užívateľ.');
		return;
	}

	$res = $_SESSION['user']->change_password(
		$_POST['passwordCurrent'],
		$_POST['passwordNew'],
		$_POST['passwordNewAgain']
	);

	echo_json_response($res, User::$error_message);

	return;
}