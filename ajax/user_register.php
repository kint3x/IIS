<?php

require_once "../defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/reservation.class.php";

start_session_if_none();

if(isset($_POST['email']) && isset($_POST['password'])){
	$res = User::register_user(htmlspecialchars_decode($_POST['email'],ENT_QUOTES), htmlspecialchars_decode($_POST['password'],ENT_QUOTES));
	
	
	if(isset($_SESSION['created_orders'])){
		//priradit objednavky

		if(Reservation::pair_reservation_with_user($_SESSION['created_orders'],$res)){
			unset($_SESSION['created_orders']);
		}
	}
	

	if($res != false){
		echo_json_response(true, User::$error_message); return;
	} 
	echo_json_response(false, User::$error_message);
	return;
}