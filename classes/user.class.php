<?php
require_once ROOT."/classes/database.class.php";

Class User {
	public static $error_message = "";

	private $user_data = array();

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
			$stmt->close();
			$db->close();
			return false;
		}

		$stmt->close();
		$db->close();
		
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
			$stmt->close();
			$res->close();
			$db->close();
			return false;
		}
		
		$stmt->close();
		$db->close();

		return true;
	}

	/**
	 * Create an object representing a logged in user.
	 */
	public function __construct($email, $password){
		$db = new Database();

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			throw new Exception('Problém s pripojením k databáze.');
		}
		
		$conn = $db->handle;

		// User identified by email
		$stmt = $conn->prepare("SELECT * FROM User WHERE email = ?");
		$stmt->bind_param('s', $email);
		$stmt->execute();
 		$res = $stmt->get_result();

		if($res->num_rows < 1){
			// No user found
			self::$error_message = 'Zadaný užívateľ neexistuje.';
			$db->close();
			throw new Exception('Zadaný užívateľ neexistuje.');
		}

		$res = $res->fetch_assoc();

		if (!(password_verify($password, $res['password']))) {
			// Wrong password
			self::$error_message = 'Nesprávne heslo.';
			$stmt->close();
			$res->close();
			$db->close();
			throw new Exception('Nesprávne heslo.');			
		}
		
		// Save the data
		$this->user_data = $res;
		
		$stmt->close();
		$db->close();
	}

	/**
	 * Return user data.
	 */
	public function get_user_data() {		
		return $this->user_data;
	}

	/**
	 * Change user password.
	 */
	 public function change_password($currentPassword, $newPassword, $newPasswordAgain) {		
		// Check if the entered current password is correct
		if (!password_verify($currentPassword, $this->user_data['password'])) {
			self::$error_message = 'Zadané nesprávne aktuálne heslo.';
			return false;
		}
		
		// New passwords don't match
		if ($newPassword != $newPasswordAgain) {
			self::$error_message = 'Nové heslá sa nezhodujú.';
			return false;
		}
		
		// Check if new password matches the criteria
		if (!self::verify_password($newPassword)) {
			return false;
		};

		$password = password_hash($newPassword, PASSWORD_DEFAULT);

		$db = new Database();
		$conn = $db->handle;
		$stmt = $conn->prepare('UPDATE User SET password = ? WHERE id = ?');
		$stmt->bind_param("si", $password, $this->user_data['id']);
		
		if (!($stmt->execute())) {
			self::$error_message = 'Chyba pri zmene hesla.';
			return false;
		}

		return true;
	}

}