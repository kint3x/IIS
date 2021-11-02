<?php

session_start();

require_once "../defines.php";

require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

if(isset($_POST['email']) && isset($_POST['passwordCurrent']) && isset($_POST['passwordNew']) && isset($_POST['passwordNewAgain']) && isset($_POST['name']) &&
   isset($_POST['surname']) && isset($_POST['address'])){

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
	if ($db->error) return "Nedá sa pripojiť k DB";
	$conn = $db->handle; 

	if ($_POST['passwordNew'] != "") {
		// Check if the entered password matches the current password
		// if (!password_verify($_POST['passwordNew'], $_SESSION['user']['password'])) {
		// 	echo json_encode(
		// 		"success" => false,
		// 		"error" => "Nesprávne aktuálne heslo."
		// 	);
		// }

		if ($_POST['passwordNew'] == $_POST['passwordNewAgain']){
			$stmt = $conn->prepare("UPDATE User SET password = ? WHERE email = ? ");
			$pw = password_hash($_POST['passwordNew'], PASSWORD_DEFAULT); // funkcia nejde vlozit do bin param
			$stmt->bind_param("ss", $pw, $_POST["email"]);
		 	$stmt->execute();
		}
		else {
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
	$stmt->bind_param("ssss", $_POST['name'], $_POST['surname'], $_POST['address'], $_POST["email"]);
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