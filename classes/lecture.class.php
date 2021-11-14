<?php

require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/reservation.class.php";

Class Lecture{
    public static $error_message = "";

    public static function register_lecture($name, $description, $id_user) {
        $db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('INSERT INTO Lecture (name, description, id_user) VALUES (?, ?, ?)');
		$stmt->bind_param('ssi', $name, $description, $id_user);

		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri registrácii konferencie.';
			$db->close();
			return false;
		};
		
		$db->close();

		return true;
    }
}