<?php

require_once "../defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/database.class.php";

start_session_if_none();

if(isset($_POST['email']) && isset($_POST['password'])){
	$res = User::register_user($_POST['email'], $_POST['password']);
	echo_json_response($res, User::$error_message);

	return;
}