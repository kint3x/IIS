<?php
require_once "../defines.php";
require_once ROOT."/classes/database.class.php";

Class Conferences{
	public static $error_message = "";

	/**
	 * Return a list of all conferences.
	 */
	public static function get_conferences_all() {
		$db = new Database();

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->query('SELECT * FROM Conference');
		$conferences = $stmt->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		return $conferences;
	}

	/**
	 * Return a list of all conferences made by the owner.
	 */
	public static function get_conferences_by_owner($owner_id) {		
		$db = new Database();

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT * FROM Conference WHERE id_user = ?');
		$stmt->bind_param('i', $owner_id);
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		$conferences = $res->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		return $conferences;
	}

	/**
	 * Create a new conference.
	 */
	public static function create_conference(
			$owner_id, 
			$name,
			$description,
			$time_from,
			$time_to,
			$price,
			$capacity,
			$place
		) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare(
			'INSERT INTO Conference'
			.'(id_user, name, description, time_from, time_to, price, capacity, place)'
			.'VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
		$stmt->bind_param(
			'issiiiis',
			$owner_id, 
			$name,
			$description,
			$time_from,
			$time_to,
			$price,
			$capacity,
			$place	
		);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$db->close();

		return true;
	}

	/**
	 * Calculate how many tickets are left for the given conference.
	 */
	public static function get_number_tickets_left($id) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT SUM(num_tickets) FROM Reservation WHERE conference_id = ?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$res = $stmt->get_result();
		$rows = $res->fetch_all();

		$reserved_tickets = $rows[0][0];
		
		$stmt = $conn->prepare('SELECT capacity FROM Conference WHERE id = ?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$res = $stmt->get_result();
		$rows = $res->fetch_all();

		$capacity = $rows[0][0] - $reserved_tickets;
		
		$db->close();

		return $capacity;
	}

	/**
	 * Returns the conferences matching the given name and id.
	 */
	public static function search_owner_by_name($id, $name) {
		$db = new Database();

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare("SELECT * FROM Conference WHERE id_user = ? AND name LIKE ?");
		$name =  "%".$name."%";
		$stmt->bind_param('is', $id, $name);
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		$conferences = $res->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		return $conferences;	
	}
}