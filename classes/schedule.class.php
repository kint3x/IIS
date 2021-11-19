<?php

require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/lecture.class.php";
require_once ROOT."/classes/user.class.php";

Class Schedule{
    public static $error_message = "";

	/**
	 * Remove the lecture from user's schedule.
	 */
	// public static function remove_from_schedule($user_id, $lecture_id)

	/**
	 * Check whether the user has already scheduled this lecture.
	 */
	// public static function is_scheduled($user_id, $lecture_id)

	/**
	 * Adds the given lecture to the user's schedule.
	 */
    public static function add_to_schedule($user_id, $lecture_id) {
        $db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('INSERT INTO Schedule (id_user, id_lecture) VALUES (?, ?)');
		$stmt->bind_param('ii', $user_id, $lecture_id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri pridávaní prednášky do rozvrhu.';
			$db->close();
			return false;
		};
		
		$db->close();

		self::$error_message = 'Prednáška bola úspešne pridaná do rozvrhu.';
		return true;
    }

	/**
	 * Return the in user's schedule that start in the interval of [$time_start, $time_end).
	 */
	public static function get_user_schedule($user_id, $time_start, $time_end) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		// Return the id, name, start and end time of the lecture and also the name of the room where the lecture is held
		$stmt = $conn->prepare('SELECT l.id, l.name as name, l.time_from, l.time_to, r.name as room'
							  .' FROM Lecture l LEFT JOIN Room r ON l.room_id = r.id WHERE l.id IN (SELECT id_lecture FROM Schedule WHERE id_user = ?)'
							  .' AND l.time_from >= ? AND l.time_from < ? ORDER BY l.time_from');
		$stmt->bind_param('iii', $user_id, $time_start, $time_end);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri vyhľadávaní prednášok v rozvrhu.';
			$db->close();
			return false;
		};

		$res = $stmt->get_result();
		$lectures = $res->fetch_all(MYSQLI_ASSOC);

		$db->close();

		self::$error_message = 'Prednášky v rozvrhu boli úspešne nájdené.';
		return $lectures;
	}
}