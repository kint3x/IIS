<?php
require_once ROOT."/classes/database.class.php";

Class Reservation{
	public static $error_message = "";
	
	/**
	 * Return the number of reservations for a given conference.
	 */
	public static function num_reservation_for_conference($conference_id) {
		$db = new Database();

		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT SUM(num_tickets) FROM Reservation WHERE conference_id = ?');
		$stmt->bind_param('i', $conference_id);
		
		if (!$stmt->execute()) {
			self::$error_message = 'Chyba pri vykonaváni dotazu.';
			$db->close();
			return false;
		};
		
		$res = $stmt->get_result();
		
		if ($res->num_rows < 1) {
			self::$error_message = 'Zadaná konferencia neexistuje.';
			$db->close();
			return false;
		};
		
		$rows = $res->fetch_all();
		$reserved_tickets = $rows[0][0];

		$db->close();

		return $reserved_tickets;
	}
}