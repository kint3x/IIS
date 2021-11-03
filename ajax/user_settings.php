<?php

session_start();

require_once "../defines.php";

require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

if(isset($_POST['email']) && isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['address'])){
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
	
	if ($db->error) {
		return "Nedá sa pripojiť k DB";
	}
		
	$conn = $db->handle; 
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