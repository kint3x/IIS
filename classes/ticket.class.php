<?php

require_once ROOT."/classes/database.class.php";

class Ticket{
	

	static function generate_ticket($reservation_id){
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;

		$hash = bin2hex(random_bytes(20));

		$res =$conn->query("INSERT INTO Ticket (reservation_id,hash) VALUES ('$reservation_id','$hash')");



		if($res== false){
			return false;
		}
		return true;
	}
}