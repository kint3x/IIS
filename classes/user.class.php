<?php
require_once ROOT."/classes/database.class.php";


Class User{
	
	public $error;

	private $email, $password, $name, $surname, $role, $address;

	public function __construct($identif, $from_email = false){
		
		$db = new Database();

		if($db->error) {
			$this->error = true;
			return "Nedá sa pripojiť k DB";
		}
		
		$conn = $db->handle;

		// User identified either by email or id
		if ($from_email) {
			$res = $conn->query("SELECT * FROM User WHERE email = '{$identif}'");
		} else {
			$res = $conn->query("SELECT * FROM User WHERE id = '{$identif}'");
		}

		if($res->num_rows < 1){
			// No user found
			$this->error = true;
			return;
		}

		$res = $res->fetch_assoc();

		// Save the data
		$this->id = $res["id"];
		$this->email = $res["email"];
		$this->password = $res["password"];
		$this->name = $res["name"];
		$this->surname = $res["surname"];
		$this->role = $res["role"];
		$this->address = $res["address"];
		$this->error = false;

		$db->close();
	}

	public function get_data() {
		if($this->error) {
			return false;
		}
		
		return array(
			"id" => $this->id,
			"email" => $this->email,
			"password" => $this->password,
			"name" => $this->name,
			"surname" => $this->surname,
			"role" => $this->role,
			"address" => $this->address
		);
	}


}