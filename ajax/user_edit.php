<?php

session_start();

require_once "../defines.php";

require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

if(isset($_POST['email']) && isset($_POST['heslo']) && isset($_POST['hesloagain']) && isset($_POST['meno']) &&
   isset($_POST['priezvisko']) && isset($_POST['adresa'])){

	if(!isset($_SESSION['user'])){
		return "Neprihlásený užívateľ";
	}

	$user = new User($_SESSION['user']['email']);
	$user_data = $user->get_data();

	if($_POST['email'] != $_SESSION['user']['email']){
		if($user_data['role'] != USER_ADMIN){
			return "Iba admin môže upravovať ostatných užívateľov";
		}
	}


	$db = new Database();
	if($db->error) return "Nedá sa pripojiť k DB";
	$conn = $db->handle; 

	if($_POST['heslo'] != ""){
		if($_POST['heslo'] == $_POST['hesloagain']){
			$stmt = $conn->prepare("UPDATE User SET password = ? WHERE email = ? ");
			$pw = password_hash($_POST["heslo"],PASSWORD_DEFAULT); // funkcia nejde vlozit do bin param
			$stmt->bind_param("ss",$pw,$_POST["email"]);
		 	$stmt->execute();
		}
		else{
			echo json_encode(
				array(
					"success" => false,
					"error" => "Heslá sa nezhodujú!"
				)
			);
			return;
		}
		
	}

	$stmt = $conn->prepare("UPDATE User SET name = ? , surname = ? , address = ? WHERE email = ? ");
	$stmt->bind_param("ssss",$_POST['meno'],$_POST['priezvisko'],$_POST['adresa'],$_POST["email"]);
	$stmt->execute();

	$db->close();


	echo json_encode(
				array(
					"success" => true,
					"error" => ""
				)
			);
			return;
}