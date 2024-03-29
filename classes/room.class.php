<?php

class Room {

    public static $error_message = "";

	/**
	 * Delete the room with the given id.
	 */
	public static function delete($room_id) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare("DELETE FROM Room WHERE Room.id = ?");
		$stmt->bind_param('i', $room_id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba rušení miestnosti.';
			$db->close();
			return false;
		};

		$db->close();

		self::$error_message = 'Miestnosť bola úspešne zrušená.';
		return true;
	}

	/**
	 * Checks whether the room is free in the [time_start, time_end] time interval.
	 */
	public static function is_free($room_id, $lecture_id, $time_start, $time_end) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare("SELECT * FROM Lecture WHERE room_id = ? AND"
								." id <> ? AND ("
								." (time_from <= ? AND time_to >= ?) OR"
								." (time_from <= ? AND time_to >= ?) OR"
								." (time_from >= ? AND time_to <= ?) OR"
								." (time_from <= ? AND time_to >= ?))");
		$stmt->bind_param('iiiiiiiiii', 
			$room_id,
			$lecture_id,
			$time_start,
			$time_start, 
			$time_end,
			$time_end, 
			$time_start,
			$time_end, 
			$time_start, 
			$time_end);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};

		$res = $stmt->get_result();

		// Some lecture happening during the given interval.
		if ($res->num_rows > 0) {
			return false;
		}

		return true;
	}

	/**
	 * Update the data for the room with the given ID.
	 */
	public static function update($room_id, $name, $conference_id) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare("UPDATE Room SET name = ?, conference_id = ? WHERE id = ?");
		$stmt->bind_param('sii', $name, $conference_id, $room_id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri zmene údajov.';
			$db->close();
			return false;
		};

		$db->close();

		self::$error_message = 'Údaje boli úspešne zmenené.';
		return true;
	}

	/**
	 * Get the id of the user who owns the conference to which this room belongs.
	 */
	public static function get_owner_id($room_id) {
		$db = new Database();
		
		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT id_user FROM Conference WHERE Conference.id IN (SELECT conference_id FROM Room WHERE Room.id = ?)');
		$stmt->bind_param('i', $room_id);
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();

		if ($res->num_rows < 1) {
			self::$error_message = 'Miestnosť s daným id neexistuje.';
			return false;
		}
		
		$owner_id = $res->fetch_all();
		$owner_id = $owner_id[0][0];

		$db->close();

		self::$error_message = 'ID konferencie bolo úspešne nájdené.';
		return $owner_id;
	}

	/**
	 * Get the id of the conference this room belongs to.
	 */
	public static function get_conference_id($room_id) {
		$db = new Database();
		
		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT conference_id FROM Room WHERE id = ?');
		$stmt->bind_param('i', $room_id);
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();

		if ($res->num_rows < 1) {
			self::$error_message = 'Miestnosť s daným id neexistuje.';
			return false;
		}
		
		$conference_id = $res->fetch_all();
		$conference_id = $conference_id[0][0];

		$db->close();

		self::$error_message = 'ID konferencie bolo úspešne nájdené.';
		return $conference_id;
	}

	/**
	 * Create a room for the conference.
	 */
	public static function add_room_to_conference($conference_id, $name) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('INSERT INTO Room (conference_id, name) VALUES (?, ?)');
		$stmt->bind_param('is', $conference_id, $name);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri vkladaní údajov.';
			$db->close();
			return false;
		};
		
		$db->close();

		self::$error_message = 'Miestnosť bola úspešne pridaná.';
		return true;
    }

	/**
	 * Search for the room with the given id.
	 */
    public static function get_room_by_id($room_id) {
		$db = new Database();
		
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT * FROM Room WHERE id = ?');
		$stmt->bind_param('i', $room_id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		
		if ($res->num_rows < 1) {
			self::$error_message = 'Daná miestnosť nebola nájdená.';
			return false;
		}

		$room = $res->fetch_assoc();
		
		$db->close();

		self::$error_message = 'Miestnosť bola úspešne nájdená.';
		return $room;
	}

	/**
	 * Return a list of rooms tied to the given conference.
	 */
    public static function get_conference_rooms($conference_id) {
		$db = new Database();
		
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT * FROM Room WHERE conference_id = ?');
		$stmt->bind_param('i', $conference_id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		
		if ($res->num_rows < 1) {
			self::$error_message = 'Pre danú konferencie ešte neboli určené žiadne miestnosti.';
			return -1;
		}

		$rooms = $res->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		self::$error_message = 'Pre danú konferenciu boli úspešne nájdené miestnosti.';
		return $rooms;
	}
}