<?php
require_once "../defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/database.class.php";

start_session_if_none();
if_not_admin_die();

if(isset($_POST['action'])){
	if($_POST['action'] == "delete"){
		if(isset($_POST['id'])){
			$curr_user_id = $_SESSION['user']->get_user_data()['id'];
			if($_POST['id'] != $curr_user_id){
				$res = User::delete_user_by_id($_POST['id']);
				echo_json_response($res,User::$error_message);
			}
			else{
				echo_json_response(false,"Nemožno vymazať samého seba");
			}
		}
	}
	if($_POST['action'] == "add"){
		if(
			isset($_POST['email']) &&
			isset($_POST['password']) &&
			isset($_POST['role']) &&
			isset($_POST['name']) &&
			isset($_POST['surname']) &&
			isset($_POST['street']) &&
			isset($_POST['city']) &&
			isset($_POST['zip']) &&
			isset($_POST['state'])
		){
			$ret = User::register_user($_POST['email'],$_POST['password'],$_POST['role']);
			if($ret === false){
				echo_json_response(false,User::$error_message);
				return;
			}

			if(User::change_user_data_by_id($ret, $_POST['email'], $_POST['name'], $_POST['surname'], $_POST['street'], $_POST['city'], $_POST['zip'], $_POST['state']) == false){
				echo_json_response(true,"Užívateľ bol registrovaný ale nepodarilo sa mu nastaviť dáta pretože:".User::$error_message);
				return;
			}

			echo_json_response(true,"Užívateľ bol úspešne pridaný.");
			return;
			
		}
		echo_json_response(false,"Interná chyba, neposlali sa všetky dáta");
	}
	if($_POST['action'] == "edit"){
		echo_json_response(true,"Nemožno vymazať samého seba");
	}
}