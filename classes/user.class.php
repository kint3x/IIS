<?php
require_once ROOT."/classes/database.class.php";


Class User{
	
	public $error;

	private $email,$password,$name,$surname,$role,$address;


	public function __construct($email){
		$db = new Database();
		if($db->error) {
			$this->error = true;
			return "Nedá sa pripojiť k DB";
		}
		$conn = $db->handle;

		$res = $conn->query("SELECT * FROM User WHERE email = '{$email}'");

		if($res->num_rows < 1){
			$this->error = true;
			return;
		}

		$res = $res->fetch_assoc();

		$this->email = $res["email"];
		$this->password = $res["password"];
		$this->name = $res["name"];
		$this->surname = $res["surname"];
		$this->role = $res["role"];
		$this->address = $res["address"];


		$this->error = false;

	}

	public function get_data(){
		if($this->error) return false;

		return array(
			"email" => $this->email,
			"password" => $this->password,
			"name" => $this->name,
			"surname" => $this->surname,
			"role" => $this->role,
			"address" => $this->address
		);
	}


}