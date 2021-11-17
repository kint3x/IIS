<?php

require_once "../defines.php";
require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre zmenu údajov je potrebné sa prihlásiť.');
    return;
}

if(isset($_POST['email']) 
   && isset($_POST['name']) 
   && isset($_POST['surname']) 
   && isset($_POST['street'])
   && isset($_POST['city'])
   && isset($_POST['zip'])
   && isset($_POST['state'])) {

	if(!isset($_SESSION['user'])){
		echo_json_response(false, 'Neprihlásený užívateľ.'); return;
		return;
	}
	
	$res = $_SESSION['user']->change_user_data(
		$_POST['email'],
		$_POST['name'],
		$_POST['surname'],
		$_POST['street'],
		$_POST['city'],
		$_POST['zip'],
		$_POST['state']
	);
	
	echo_json_response($res, User::$error_message);
	return;
}