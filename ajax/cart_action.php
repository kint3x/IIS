<?php

require_once "../defines.php";

require_once ROOT."/classes/cart.class.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/conferences.class.php";

session_start();

Cart::setup_cart_if_not();

if(isset($_POST['item_id'])){
	if(!is_numeric($_POST['item_id'])){
		echo_json_response(false, "Neplatné číslo konferencie.");
		return;
	} 
}
if(isset($_POST['id'])){
	if(!is_numeric($_POST['id'])){
		echo_json_response(false, "Neplatné číslo konferencie.");
		return;
	} 
}


if(isset($_POST['cart_action'])){
	if($_POST['cart_action']=="add_to_cart"){
		if(!isset($_POST['item_id'])){
			echo json_encode(false, "Chýba číslo konferencie.");
			return;
		}
		
		$ret = $_SESSION['cart']->add_item($_POST['item_id'],1);
		if($ret !== true) {
			echo_json_response(false, $ret);
			return;
		}
			echo_json_response(true, "");

		return;

	}
	else if($_POST['cart_action']=="get_cart"){
		echo json_encode($_SESSION['cart']->get_items_structured());
	}
	else if($_POST['cart_action']=="remove_from_cart"){
		if(isset($_POST['id'])){
			 $_SESSION['cart']->remove_item($_POST['id']);
			echo_json_response(true,"");
			return;
		}

		echo_json_response(true,"Chýba ID konferencie.");
		return;
	}
	else if($_POST['cart_action']=="decrease_item"){
		if(isset($_POST['id'])){
			$_SESSION['cart']->decrease_item($_POST['id'],1);
			echo_json_response(true,"");
			return;
		}

		echo_json_response(true,"Chýba ID konferencie.");
	}
	else if($_POST['cart_action']=="increase_item"){
		if(isset($_POST['id'])){
			$ret = $_SESSION['cart']->increase_item($_POST['id'],1);
			if($ret !== true){
				echo_json_response(false,$ret);
				return;
			}
			echo_json_response(true,"");
			return;
		}

		echo_json_response(true,"Chýba ID");
		return;
	}
}