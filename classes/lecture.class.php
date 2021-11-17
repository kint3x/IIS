<?php

require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/reservation.class.php";
require_once ROOT."/classes/table.class.php";

Class Lecture{
    public static $error_message = "";

	/**
	 * Get the id of the conference this lecture belongs to.
	 */
	public static function get_conference_id($lecture_id) {
		$db = new Database();
		
		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT conference_id FROM Lecture WHERE id = ?');
		$stmt->bind_param('i', $lecture_id);
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();

		if ($res->num_rows < 1) {
			self::$error_message = 'Prednáška s daným id neexistuje.';
			return false;
		}
		
		$conference_id = $res->fetch_all();
		$conference_id = $conference_id[0][0];

		$db->close();

		self::$error_message = 'ID konferencie bolo úspešne nájdené.';
		return $conference_id;
	}

	/**
	 * Return a list of lectures tied to the given conference.
	 */
    public static function get_conference_lectures($conference_id) {
		$db = new Database();
		
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT * FROM Lecture WHERE conference_id = ?');
		$stmt->bind_param('i', $conference_id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		
		if ($res->num_rows < 1) {
			self::$error_message = 'Pre danú konferencie ešte neboli pridané žiadne prednášky.';
			return -1;
		}

		$lectures = $res->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		self::$error_message = 'Prednášky pre danú konferenciu boli úspešne nájdené.';
		return $lectures;
	}

	public static function update(            
		$id,
    	$name,
    	$description,
    	$time_from,
    	$time_to,
    	$img_url,
    	$room_id,
    	$id_user,
    	$conference_id,
    	$status) {

		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare(
			"UPDATE Lecture SET "
			."name = ?, "
			."description = ?, "
			."time_from = ?, "
			."time_to = ?, "
			."img_url = ?, "
			."room_id = ?, "
			."id_user = ?, "
			."conference_id = ?, "
			."status = ? "
			."WHERE id = ?");
		$stmt->bind_param('ssiisiiiii', 
			$name,
			$description,
			$time_from,
			$time_to,
			$img_url,
			$room_id,
			$id_user,
			$conference_id,
			$status,
			$id);

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
	 * Return an ID of the user who owns the conference which this lecture was registered to.
	 */
	public static function get_conference_owner($lecture_id) {
		$db = new Database();
		
		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT id_user FROM Conference WHERE Conference.id IN (SELECT conference_id FROM Lecture WHERE Lecture.id = ?)');
		$stmt->bind_param('i', $lecture_id);
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();

		if ($res->num_rows < 1) {
			self::$error_message = 'Prednáška s daným id neexistuje.';
			return false;
		}
		
		$owner_id = $res->fetch_all();
		$owner_id = $owner_id[0][0];

		$db->close();

		self::$error_message = 'ID konferencie bolo úspešne nájdené.';
		return $owner_id;
	}

	/**
	 * Return a lecture (row from db) with the given id.
	 */
	public static function get_lecture_by_id($id) {
		$db = new Database();
		
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT * FROM Lecture WHERE id = ?');
		$stmt->bind_param('i', $id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		
		if ($res->num_rows < 1) {
			self::$error_message = 'Daná prednáška nebola nájdená.';
			return false;
		}

		$lecture = $res->fetch_assoc();
		
		$db->close();

		self::$error_message = 'Prednáška bola úspešne nájdená.';
		return $lecture;
	}

	/**
	 * Registers a lecture for the given conference.
	 */
    public static function register_lecture($name, $description, $id_user, $conference_id) {
        $db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('INSERT INTO Lecture (name, description, id_user, conference_id) VALUES (?, ?, ?, ?)');
		$stmt->bind_param('ssii', $name, $description, $id_user, $conference_id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri registrácii konferencie.';
			$db->close();
			return false;
		};
		
		$db->close();

		self::$error_message = 'Prednáška bola úspešne zaregistrovaná.';
		return true;
    }
}