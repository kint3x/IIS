<?php
require_once "../defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/database.class.php";

start_session_if_none();
if_not_admin_die();

if(isset($_POST['action'])){
	if($_POST['action'] == "delete"){
		if(isset($_POST['user_id'])){
			$curr_user_id = $_SESSION['user']->get_user_data()['id'];
			if($_POST['user_id'] != $curr_user_id){
				$res = User::delete_user_by_id($_POST['user_id']);
				echo_json_response($res,User::$error_message);
			}
			else{
				echo_json_response(false,"Nemožno vymazať samého seba");
			}
		}
	}
}