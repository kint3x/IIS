<?php

require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/reservation.class.php";

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

		self::$error_message = 'Načítanie konferencií sa úspešne podarilo.';
		return $conferences;
	}
	
	/**
	 * Checks if the user owns the conference.
	 */
	public static function is_owner($user_id, $conference_id) {
		$conference = self::get_conference_by_id($conference_id);
		
		if ($conference === null) {
			return false;
		}
		
		if ($conference['id_user'] != $user_id) {
			self::$error_message = 'Užívateľ nie je vlastníkom danej konferencie.';
			return false;
		}
		
		self::$error_message = 'Užívateľ je vlastníkom danej konferencie.';
		return true;
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
		
		if ($res->num_rows < 1) {
			self::$error_message = 'Daná konferencia neexistuje.';
			return false;
		}
		
		$conference = $res->fetch_assoc();


		
		$db->close();
		
		self::$error_message = 'Konferenciu sa úspešne podarilo nájsť.';
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
		
		self::$error_message = 'Konferencie sa úspešne podarilo nájsť.';
		return $conferences;
	}
	
	/**
	 * Create a new conference.
	 */
	public static function create_conference(
		$owner_id, 
		$name,
		$description,
		$street,
		$city,
		$zip,
		$state,
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
				.'(id_user, name, description, street, city, zip, state, time_from, time_to, price, capacity, image_url)'
				.'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
				$stmt->bind_param(
					'issssisiidis',
					$owner_id,
					$name,
					$description,
					$street,
					$city,
					$zip,
					$state,
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
		
		self::$error_message = 'Konferencia bola úspešne vytvorená.';
		return $new_id;
	}
	
	public static function update_conference(
		$id,			
		$name,
		$description,
		$street,
		$city,
		$zip,
		$state,
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
			
			$stmt = $conn->prepare("UPDATE Conference SET "
			."name = ?, "
			."description = ?, "
			."street = ?, "
			."city = ?, "
			."zip = ?, "
			."state = ?, "
			."time_from = ?, "
			."time_to = ?, "
			."price = ?, "
			."capacity = ?, "
			."image_url = ? "
			."WHERE id = ?"
		);
		$stmt->bind_param(
			'ssssisiidisi',
			$name,
			$description,
			$street,
			$city,
			$zip,
			$state,
			$time_from,
			$time_to,
			$price,
			$capacity,
			$image_url,
			$id
		);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri zmene údajov.';
			$db->close();
			return false;
		};
		
		$db->close();
		
		self::$error_message = 'Údaje pre danú konferenciu boli úspešne zmenené.';
		return true;
	}
	
	/**
	 * Calculate how many tickets are left for the given conference. Returns -1 if conference doesn't exist.
	 */
	public static function get_number_tickets_left($id) {
		$db = new Database();
		
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}
		
		$conn = $db->handle;
		
		$reserved_tickets = Reservation::num_reservation_for_conference($id);
		
		if ($reserved_tickets === false) {
			self::$error_message = Reservation::$error_message;
			return -1;
		}
		
		$stmt = $conn->prepare('SELECT capacity FROM Conference WHERE id = ?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$res = $stmt->get_result();
		
		if($res->num_rows < 1){
			self::$error_message = 'Daná konferencia neexistuje.';
			return -1;
		}
		
		$rows = $res->fetch_all();
		
		$capacity = $rows[0][0] - $reserved_tickets;
		
		$db->close();
		
		self::$error_message = 'Počet voľných lístkov bol úspešne zistený.';
		return $capacity;
	}
	
	/**
	 * Returns the conferences matching the given name and id.
	 * $old - if true include conferences that have already ended
	 */
	public static function search_by_owner_name_tag($user_id, $name, $tag_id, $old) {
		$db = new Database();
		
		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}
		
		$conn = $db->handle;
		
		$current_time = time();
		
		// Display conferences that have already ended
		if ($old) {
			$current_time = 0;
		}
		
		if ($name === false) {
			// only by tag
			$stmt = $conn->prepare("SELECT * FROM Conference WHERE id_user = ? AND time_to >= ? "
			."AND id IN (SELECT conference_id FROM cross_conf_tag WHERE tag_id = ?)");
			$stmt->bind_param('iii', $user_id, $current_time, $tag_id);
		} else if ($tag_id === false) {
			// only by name
			$stmt = $conn->prepare("SELECT * FROM Conference WHERE id_user = ? AND time_to >= ? AND name LIKE ?");
			$name =  "%".$name."%";
			$stmt->bind_param('iis', $user_id, $current_time, $name);
		} else {
			$stmt = $conn->prepare("SELECT * FROM Conference WHERE id_user = ? AND time_to >= ? AND name LIKE ? "
								  ."AND id IN (SELECT conference_id FROM cross_conf_tag WHERE tag_id = ?)");
			$name =  "%".$name."%";
			$stmt->bind_param('iisi', $user_id, $current_time, $name, $tag_id);
		}
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		$conferences = $res->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		self::$error_message = 'Konferencie sa úspešne podarilo nájsť.';
		return $conferences;	
	}

	/**
	 * Returns the conferences matching the given name and id.
	 * $old - if true include conferences that have alreade ended
	 */
	public static function search_by_name_tag($name, $tag_id, $old) {
		$db = new Database();

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$current_time = time();

		// Display conferences that have already ended
		if ($old) {
			$current_time = 0;
		}

		if ($name === false) {
			// only by tag
			$stmt = $conn->prepare("SELECT * FROM Conference WHERE time_to >= ? AND id IN (SELECT conference_id FROM cross_conf_tag WHERE tag_id = ?)");
			$stmt->bind_param('ii', $current_time, $tag_id);
		} else if ($tag_id === false) {
			// only by name
			$stmt = $conn->prepare("SELECT * FROM Conference WHERE time_to >= ? AND name LIKE ?");
			$name =  "%".$name."%";
			$stmt->bind_param('is', $current_time, $name);
		} else {
			$stmt = $conn->prepare("SELECT * FROM Conference WHERE time_to >= ? AND name LIKE ? "
								  ."AND id IN (SELECT conference_id FROM cross_conf_tag WHERE tag_id = ?)");
			$name =  "%".$name."%";
			$stmt->bind_param('isi', $current_time, $name, $tag_id);
		}

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		$conferences = $res->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		self::$error_message = 'Konferencie sa úspešne podarilo nájsť.';
		return $conferences;	
	}
}