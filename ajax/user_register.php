<?php
session_start();

require_once "../defines.php";

require_once ROOT."/classes/database.class.php";

if(isset($_POST['email']) && isset($_POST['heslo'])){
	//check mail
	$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
	if(!preg_match($pattern, $_POST['email'])){
		echo json_encode(
			array(
				"success" => false,
				"error" => "Zadaný email nie je platný"
			)
		);
		return;
	}
	//check či už nahodou neexistuje

	$db = new Database();
	if($db->error) return "Nedá sa pripojiť k DB";
	$conn = $db->handle;

	//prepared statement pre user inputy, ak nemate vstup od pouzivatela staci $res = $conn->query("SELECT ...")
	$stmt = $conn->prepare("SELECT COUNT(*) FROM User WHERE email = ?");
	$stmt->bind_param("s",$_POST["email"]);
 	$stmt->execute();
 	$res = $stmt->get_result();
 	$res = $res->fetch_all();

	if($res[0][0] > 0){
		echo json_encode(
			array(
				"success" => false,
				"error" => "Pre zadaný email už registrácia existuje"
			)
		);
		return;
	}

	//keby chceme kontrolovat aj heslo tak overenie ale asi netreba
	if(strlen($_POST["heslo"])<4){
		echo json_encode(
			array(
				"success" => false,
				"error" => "Heslo musí obsahovať aspoň 4 znaky"
			)
		);
		return;
	}
	//registracia noveho užívateľa

	$stmt = $conn->prepare("INSERT INTO User (email,password,role) VALUES (?, ? , ".USER_REGULAR.")");
	$pw = password_hash($_POST["heslo"],PASSWORD_DEFAULT); // funkcia nejde vlozit do bin param
	$stmt->bind_param("ss",$_POST["email"],$pw);
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