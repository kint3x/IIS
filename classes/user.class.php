<?php

require_once ROOT."/classes/database.class.php";

Class User {
	public static $error_message = "";

	private $user_data = array();

	/**
	 * Delete user with a given id
	 */
	public static function delete_user($id) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}
	
		$conn = $db->handle;
		$stmt = $conn->prepare('DELETE FROM User WHERE id = ?');
		$stmt->bind_param('i', $id);

		if (!($stmt->execute())) {
			self::$error_message = 'Problém pri mazaní užívateľa.';
			$db->close();
			return false;
		}

		$db->close();
		
		self::$error_message = 'Užívateľ bol úspešne zmazaný.';
		return true;
	}

	/**
	 * Registers a new user. Returns 'false' in case of any problems and sets
	 * saves the error description in 'error_message'.
	 */
	public static function register_user($email,  $password , $role = USER_REGULAR) {
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
		$stmt = $conn->prepare('INSERT INTO User (email, password, role) VALUES (?, ?, ?)');
		$stmt->bind_param('ssd', $email, $password, $role);

		if (!($stmt->execute())) {
			self::$error_message = 'Problém pri registrácii užívateľa.';
			$db->close();
			return false;
		}

		$id = $conn->insert_id;
		$db->close();
		
		self::$error_message = 'Užívateľ bol úspešne zaregistrovaný.';
		return $id;
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

		self::$error_message = 'Heslo bolo úspešne overené.';
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
			$db->close();
			return false;
		}
		
		$db->close();

		self::$error_message = 'Email bol úspešne overený.';
		return true;
	}

	/**
	 * Create an object representing a logged in user.
	 */
	public function __construct($email, $password){
		$message = 'Nesprávny email alebo heslo';

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
			self::$error_message = $message;
			$db->close();
			throw new Exception($message);
		}

		$rows = $res->fetch_assoc();

		if (!(password_verify($password, $rows['password']))) {
			// Wrong password
			self::$error_message = $message;
			$db->close();
			throw new Exception($message);			
		}
		
		// Save the data
		$this->user_data = $rows;
		
		$db->close();
		self::$error_message = 'Užívateľ bol úspešne prihlásený.';
	}

	/**
	 * Return user data.
	 */
	public function get_user_data() {
		if (!isset($this->user_data['id'])) {
			return false;
		}
		
		$this->update_user();
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

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		$stmt = $conn->prepare('UPDATE User SET password = ? WHERE id = ?');
		$stmt->bind_param("si", $password, $this->user_data['id']);
		
		if (!($stmt->execute())) {
			$db->close();
			self::$error_message = 'Nastala chyba pri zmene hesla.';
			return false;
		}

		$db->close();

		$this->update_user();

		self::$error_message = 'Heslo bolo úspešne zmenené.';
		return true;
	}

	/**
	 * Change user password by id.
	 */
	public static function change_password_by_id($newPassword,$id) {		
		
		// Check if new password matches the criteria
		if (!self::verify_password($newPassword)) {
			return false;
		};

		$password = password_hash($newPassword, PASSWORD_DEFAULT);

		$db = new Database();

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		$stmt = $conn->prepare('UPDATE User SET password = ? WHERE id = ?');
		$stmt->bind_param("si", $password, $id);
		
		if (!($stmt->execute())) {
			$db->close();
			self::$error_message = 'Nastala chyba pri zmene hesla.';
			return false;
		}

		$db->close();

		return true;
	}

	/* Get user by id */
	public static function get_user_data_by_id($id){
		$db = new Database();
		
		if($db->error) {	
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}
		
		$conn = $db->handle;
		$stmt = $conn->prepare('SELECT * FROM User WHERE id = ?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$res = $stmt->get_result();
		$rows = $res->fetch_assoc();

		$db->close();
		
		return $rows;

	}

	/**
	 * Get the email registered to the given id.
	 */
	public static function get_email_by_id($id) {
		$db = new Database();
		
		if($db->error) {	
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}
		
		$conn = $db->handle;
		$stmt = $conn->prepare('SELECT * FROM User WHERE id = ?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$res = $stmt->get_result();
		$rows = $res->fetch_assoc();

		$db->close();
		
		self::$error_message = 'Email bol úspešne získaný.';
		return $rows['email'];
	}

	/**
	 * Change this user's data.
	 */
	public function change_user_data($email, $name, $surname, $street, $city, $zip, $state) {
		$res = self::change_user_data_by_id($this->user_data['id'], $email, $name, $surname, $street, $city, $zip, $state);
		$res = $res && $this->update_user();

		self::$error_message = 'Údaje boli úspešne zmenené.';
		return $res;
	}

	/**
	 * Change user data for the given user.
	 */
	public static function change_user_data_by_id($id, $email, $name, $surname, $street, $city, $zip, $state) {
		// Check if user wants to also change his email
		if ($email != self::get_email_by_id($id)) {
			if (!self::verify_email($email)) {
				return false;
			}
		}
		
		$db = new Database();
		
		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}
		
		$conn = $db->handle;
		$stmt = $conn->prepare("UPDATE User SET email = ?, name = ?, surname = ?, street = ?, city = ?, zip = ?, state = ? WHERE id = ?");
		$stmt->bind_param(
			"sssssisi",
			$email,
			$name,
			$surname,
			$street,
			$city,
			$zip,
			$state,
			$id
		);
		
		if (!$stmt->execute()) {
			$db->close();
			$stmt->close();
			self::$error_message = 'Nastala chyba pri zmene údajov.';
			return false;
		};
		
		$db->close();

		self::$error_message = 'Údaje boli úspešne zmenené.';
		return true;
	}

	/**
	 * Checks if the logged in user is admin.
	 */
	public function is_admin() {
		return $this->user_data['role'] == USER_ADMIN;
	}

	/**
	 * Makes sure user data is updated after changes were made.
	 */
	public function update_user() {
		$db = new Database();
		
		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;

		// User identified by id
		$res = $conn->query("SELECT * FROM User WHERE id = {$this->user_data['id']}");

		if ($res === NULL) {
			self::$error_message = "Zadaný užívateľ sa nenašiel.";
			return false;
		}

		$rows = $res->fetch_assoc();

		// Save the data
		$this->user_data = $rows;
		
		$db->close();

		self::$error_message = 'Údaje boli úspešne aktualizované.';
		return true;
	}

	public static function delete_user_by_id($id){
		$db = new Database();
		$conn = $db->handle;

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$stmt = $conn->prepare("DELETE FROM User WHERE id=?");
		$stmt->bind_param("d",$id);

		if (!$stmt->execute()) {
			$db->close();
			$stmt->close();
			self::$error_message = 'Nastala chyba pri odstraňovaní užívateľa.';
			return false;
		};
		
		$db->close();

		self::$error_message = 'Užívateľ bol úspešne odstránený.';
		return true;
	}

}