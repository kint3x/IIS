<?php

class Room {

    public static $error_message = "";

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

		return $rooms;
	}
}