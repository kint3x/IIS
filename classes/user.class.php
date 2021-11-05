<?php
require_once ROOT."/classes/database.class.php";

Class User {
	public static $error_message = "";
	public $error;
	
	private $email, $password, $name, $surname, $role, $address;

	/**
	 * Registers a new user. Returns 'false' in case of any problems and sets
	 * saves the error description in 'error_message'.
	 */
	public static function register_user($email,  $password) {
		if (!(self::verify_email($email) && self::verify_password($password))) {
			return false;
		}

		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$password = password_hash($password, PASSWORD_DEFAULT);

		$conn = $db->handle;
		$stmt = $conn->prepare('INSERT INTO User (email, password, role) VALUES (?, ?, '.USER_REGULAR.')');
		$stmt->bind_param('ss', $email, $password);

		if (!($stmt->execute())) {
			self::$error_message = 'Problém pri registrácii užívateľa.';
			return false;
		}

		return true;
	}

	/**
	 * Checks if the password meets the required criteria.
	 */
	public static function verify_password($password) {
		$min_len = 8;
		if (strlen($password) < $min_len) {
			self::$error_message = 'Heslo musí obsahovať aspoň '.$min_len.' znakov.';
			return false; 
		}

		return true;
	}

	/**
	 * Checks if the given email has a valid format and if it's unique among all registered users.
	 */
	public static function verify_email($email) {
		$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
		
		if(!preg_match($pattern, $_POST['email'])){
			self::$error_message = 'Neplatný formát emailu.';
			return false;
		}

		$db = new Database();

		if ($db->error) {
			self::$error_message = "Problém s pripojením k databáze.";
			return false;
		}
		
		$conn = $db->handle;
		$stmt = $conn->prepare('SELECT COUNT(*) FROM User WHERE email = ?');
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$res = $stmt->get_result();
		$count = $res->fetch_all();
		$count = $count[0][0];

		if ($count > 0) {
			self::$error_message = 'Pre zadaný email už existuje účet.';
			return false;
		}

		return true;
	}

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