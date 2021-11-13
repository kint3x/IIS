<?php

include_once "../defines.php";

class Tag {

    public static $error_message = "";

    /**
     * Create a new tag in the Tag table with the given name.
     */
    public static function create_tag($name) {
        $db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('INSERT INTO Tag (name) VALUES (?)');
		$stmt->bind_param('s', $name);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$db->close();

		return true;
    }

	/**
	 * Add a tag to the given conference.
	 */
    public static function add_tag_to_conference($conferrence_id, $tag_id) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('INSERT INTO cross_conf_tag (conference_id, tag_id) VALUES (?, ?)');
		$stmt->bind_param('ii', $conferrence_id, $tag_id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$db->close();

		return true;
    }

	/**
	 * Return a list of all tags.
	 */
	public static function get_tags_all() {
		$db = new Database();

		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->query('SELECT * FROM Tag ORDER BY name ASC');
		$tags = $stmt->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		return $tags;
	}

    public static function get_conference_tags($conferrence_id) {
		$db = new Database();
		
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare(
			'SELECT * FROM Tag WHERE id IN (SELECT tag_id FROM `cross_conf_tag` WHERE conference_id = ?)'
		);
		$stmt->bind_param('i', $conferrence_id);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri načítaní údajov.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		$tags = $res->fetch_all(MYSQLI_ASSOC);
		
		$db->close();

		return $tags;
    }

}