<?php

session_start();

require_once "../defines.php";

require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

if(isset($_POST['passwordCurrent']) && isset($_POST['passwordNew']) && isset($_POST['passwordNewAgain'])){

	if(!isset($_SESSION['user'])){
		return "Neprihlásený užívateľ";
	}

	$user = new User($_SESSION['user']['email']);
	$user_data = $user->get_data();
	
	// Check if the entered password matches the current password
	if (!password_verify($_POST['passwordCurrent'], $user_data['password'])) {
		echo json_encode(
			array(
				"success" => false,
				"error" => "Nesprávne aktuálne heslo."
				)
			);
			
		return;
	}

	# Check if the entered pwds are the same
	if ($_POST['passwordNew'] == $_POST['passwordNewAgain']){
		$db = new Database();
		if ($db->error){
			return "Nedá sa pripojiť k DB";
		} 
		
		$conn = $db->handle; 
		$stmt = $conn->prepare("UPDATE User SET password = ? WHERE email = ? ");
		$pw = password_hash($_POST['passwordNew'], PASSWORD_DEFAULT);
		$stmt->bind_param("ss", $pw, $user_data['email']);
		$stmt->execute();
		$db->close();
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


	echo json_encode(
		array(
			"success" => true,
			"error" => ""
		)
	);

	return;
}