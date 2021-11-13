<?php
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
		
		$stmt = $conn->query('SELECT * FROM Conference ORDER BY time_from ASC');
		$conferences = $stmt->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		return $conferences;
	}

	/**
	 * Return an array representing a conference with the given id.
	 */
	public static function get_conference_by_id($conference_id) {
		$db = new Database();
		
		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT * FROM Conference WHERE id = ?');
		$stmt->bind_param('i', $conference_id);
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		$conference = $res->fetch_assoc();
		
		$db->close();

		return $conference;
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
		
		$stmt = $conn->prepare('SELECT * FROM Conference WHERE id_user = ? ORDER BY time_from ASC');
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
			$image_url
		) {
			
		if ($time_from > $time_to) {
			self::$error_message = 'Neplatné časové hodnoty počiatku a ukončenia konferencie.';
			return false;
		}
		
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare(
			'INSERT INTO Conference'
			.'(id_user, name, description, time_from, time_to, price, capacity, image_url)'
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
			$image_url
		);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$stmt = $conn->query('SELECT LAST_INSERT_ID()');
		$new_id = $stmt->fetch_all();

		$db->close();

		return $new_id;
	}

	/**
	 * Calculate how many tickets are left for the given conference. returns -1 if doesnt exist
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

		if($res->num_rows == 0){
			return -1;
		}

		$rows = $res->fetch_all();

		$capacity = $rows[0][0] - $reserved_tickets;
		
		$db->close();

		return $capacity;
	}

		/**
	 * Returns the conferences matching the given name and id.
	 */
	public static function search_by_owner_name_tag($user_id, $name, $tag_id) {
		$db = new Database();

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		if ($name === false) {
			// only by tag
			$stmt = $conn->prepare("SELECT * FROM Conference WHERE id_user = ? "
								  ."AND id IN (SELECT conference_id FROM cross_conf_tag WHERE tag_id = ?)");
			$stmt->bind_param('ii', $user_id, $tag_id);
		} else if ($tag_id === false) {
			// only by name
			$stmt = $conn->prepare("SELECT * FROM Conference WHERE id_user = ? AND name LIKE ?");
			$name =  "%".$name."%";
			$stmt->bind_param('is', $user_id, $name);
		} else {
			$stmt = $conn->prepare("SELECT * FROM Conference WHERE id_user = ? AND name LIKE ? "
								  ."AND id IN (SELECT conference_id FROM cross_conf_tag WHERE tag_id = ?)");
			$name =  "%".$name."%";
			$stmt->bind_param('isi', $user_id, $name, $tag_id);
		}

		
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