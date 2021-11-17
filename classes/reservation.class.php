<?php
require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

Class Reservation{
	public static $error_message = "";
	
	/**
	 * Return the number of reservations for a given conference.
	 */
	public static function num_reservation_for_conference($conference_id) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT SUM(num_tickets) FROM Reservation WHERE conference_id = ?');
		$stmt->bind_param('i', $conference_id);
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri vykonaváni dotazu.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		
		if ($res->num_rows < 1) {
			self::$error_message = 'Zadaná konferencia neexistuje.';
			$db->close();
			return false;
		};
		
		$rows = $res->fetch_all();
		$reserved_tickets = $rows[0][0];

		$db->close();

		self::$error_message = 'Počet rezervovaných lístkov bol úspešne zistený.';
		return $reserved_tickets;
	}

	public static function create_reservation($name,$surname,$email,$num_tickets,$state,$conference_id,$street,$city,$zip,$user_id,$country){
		
		$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
		if(!preg_match($pattern, $_POST['email'])){
			self::$error_message = 'Neplatný formát emailu.';
			return false;
		}

		if(!is_numeric($zip)){
			self::$error_message = 'Neplatné PSČ';
			return false;
		}

		if(!is_numeric($conference_id)){
			self::$error_message = 'Neplatné Conference_ID';
			return false;
		}



		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}


		$conn = $db->handle;
		$stmt = $conn->prepare('INSERT INTO Reservation (name, surname, email, num_tickets, state, conference_id, street, city , zip, user_id, country) VALUES (?, ?, ?, ? , ? , ? , ? , ? ,? ,?,?)');
		$stmt->bind_param('sssdsdsssds', $name, $surname, $email, $num_tickets, $state , $conference_id, $street, $city, $zip, $user_id,$country);

		if (!($stmt->execute())) {
			self::$error_message = 'Problém pri vytváraní rezervácie.'.$conn->error;
			$db->close();
			return false;
		}

		$id = $conn->insert_id;
		$db->close();
		
		return $id;
	}

	public static function check_create_reservation_availabe($reservation_ids = array()){

		foreach ($reservation_ids as $id => $count){
			$num = Conferences::get_number_tickets_left($id);

			if($num < $count){
				$conf = get_conference_by_id($id);
				if($conf == false){
					self::$error_message = "Konferencia s ID {$id} neexistuje.";
					return false;
				}
				$left = Conferences::get_number_tickets_left($id);
				self::$error_message = "Konferencia {$conf['name']} má už len {$left} lístkov na rezerváciu.";
				return false;
			}
		}
		return true;
	}

	public static function pair_reservation_with_user($reservations = array(),$user_id){
		$db = new Database();
		$res = true;
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;

		foreach($reservations as $id){
			$res=$conn->query("UPDATE Reservation SET user_id = {$user_id} WHERE id = {$id}");

		}

		return true;
		$db->close();
	}
}