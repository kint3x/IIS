<?php
session_start();

require_once "../defines.php";

require_once ROOT."/classes/database.class.php";

if(isset($_POST['email']) && isset($_POST['password'])){

	$db = new Database();
	if($db->error) {
		return "Nedá sa pripojiť k DB";
	}
	$conn = $db->handle;

	$stmt = $conn->prepare("SELECT * FROM User WHERE email = ? LIMIT 1");
	$stmt->bind_param("s",$_POST["email"]);
 	$stmt->execute();
 	$res = $stmt->get_result();

 	if($res->num_rows < 1){
 		echo json_encode(
			array(
				"success" => false,
				"error" => "Zadaný email nie je registrovaný."
			)
		);
		return;
 	}

 	$data = $res->fetch_assoc();

 	if(password_verify($_POST['password'], $data['password'])){
		// correct pwd

 		$_SESSION['user'] =array(
			"id" => $data['id'],
 			"role" => $data['role']
 		);

 		echo json_encode(
			array(
				"success" => true,
				"error" => ""
			)
		);
		return;	
 	}
 	else{
 		echo json_encode(
			array(
				"success" => false,
				"error" => "Nesprávne heslo."
			)
		);
		return;
 	}

}