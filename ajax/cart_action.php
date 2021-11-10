<?php

require_once "../defines.php";

require_once ROOT."/classes/cart.class.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/conferences.class.php";

session_start();

Cart::setup_cart_if_not();

if(isset($_POST['cart_action'])){
	if($_POST['cart_action']=="add_to_cart"){
		if(!isset($_POST['item_id'])){
			echo json_encode(array(
				"error" => true,
				"message" => "Chýba parameter"
			));
			return;
		}
		if(is_numeric($_POST['item_id'])){
			if(Conferences::get_number_tickets_left($_POST['item_id'])>0){
				$_SESSION['cart']->add_item($_POST['item_id'],1);
				echo json_encode(array(
				"error" => false,
				"message" => ""
				));
				return;
			}
		}
		else{
			echo json_encode(array(
				"error" => true,
				"message" => "ID nie je číslo"
			));
			return;
		}
	}
	else if($_POST['cart_action']=="get_cart"){
		
	}
}