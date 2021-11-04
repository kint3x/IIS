<?php
require_once "../defines.php";
require_once ROOT."/classes/database.class.php";

Class Conferences{

	public function get_conferences_all() {
		$db = new Database();

		if($db->error) {
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->query('SELECT * FROM Conference');
		$conferences = $stmt->fetch_all(MYSQLI_ASSOC);
		
		$stmt->close();
		$db->close();

		return $conferences;
	}

	public function get_conferences_by_owner($owner_id) {		
		$db = new Database();

		if($db->error) {
			return False;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare('SELECT * FROM Conference WHERE id_user = ?');
		$stmt->bind_param('i', $owner_id);
		$stmt->execute();
		
		$res = $stmt->get_result();
		$conferences = $res->fetch_all(MYSQLI_ASSOC);
		
		$res->close();
		$stmt->close();
		$db->close();

		return $conferences;
	}

	public function create_conference(
			$owner_id, 
			$name,
			$description,
			$time_from,
			$time_to,
			$price,
			$capacity,
			$place
		) {
		$db = new Database();

		if ($db->error) {
			return false;
		}

		$conn = $db->handle;
		
		$stmt = $conn->prepare(
			'INSERT INTO Conference'
			.'(id_user, name, description, time_from, time_to, price, capacity, place)'
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
			$place	
		);
		$stmt->execute();
		
		$stmt->close();
		$db->close();

		return true;
	}
}