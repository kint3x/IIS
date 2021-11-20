<?php

require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/conferences.class.php";


class Question{

	public static $error_message;

	/*
	*	Adds new question, check if all parameters are good
	*/
	public static function add_question($lecture_id, $question, $user_id){
		if(!is_numeric($lecture_id)){
			self::$error_message = 'Neplatná prednáška.';
			return false;
		}

		if(!is_numeric($user_id)){
			self::$error_message = 'Neplatný užívateľ.';
			return false;
		}

		if(strlen($question) < 5) {
			self::$error_message = 'Otázka musí byť dlhšia ako 5 znakov';
			return false;
		}

		if(strlen($question) > 250){
			self::$error_message = 'Otázka nesmie byť dlhšia ako 250 znakov.';
			return false;
		}

		$db = new Database();
		
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}

		$conn = $db->handle;

		$stmt = $conn->prepare('INSERT INTO Question (question, user_id , lecture_id) VALUES (?, ?, ?)');
		$stmt->bind_param('sii', $question, $user_id, $lecture_id);

		if (!($stmt->execute())) {
			self::$error_message = 'Problém pri vytváraní rezervácie: '.$conn->error;
			$db->close();
			return false;
		}

		$id = $conn->insert_id;
		$db->close();

		return $id;
	} 

	/*
	*	Get questions by conference id
	*/
	public static function get_questions_by_lecture_id($lecture_id){
		if(!is_numeric($lecture_id)){
			self::$error_message = 'Neznáma prednáška.';
			return false;
		}

		$db = new Database();
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}
		$conn = $db->handle;

		$res=$conn->query("SELECT * FROM Question WHERE lecture_id={$lecture_id} ORDER BY id ASC");

		if($res == false){
			self::$error_message = 'Nepodarilo sa načítať otázky.';
			return false;
		}
		$rows = [];
		while($row = $res->fetch_assoc()){
			$rows[] = $row;
		}

		return $rows;

	}

	/*
	*	Set top question, checks if user can set top
	*/
	public static function set_question_top($id, $user_id, $top){
		
		if(!is_numeric($user_id)){
			self::$error_message = 'Neznámy užívateľ.';
			return false;
		}

		if($top != 1 && $top != 0){
			self::$error_message = 'Neznáma hodnota stavu.';
			return false;
		}

		$db = new Database();
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}
		$conn = $db->handle;

		$res=$conn->query("UPDATE Question SET top='$top' WHERE id={$id}");

		if($res == false){
			self::$error_message = 'Nepodarilo sa načítať otázky.';
			return false;
		}

		return true;
	}

	/* Deletes question */

	public static function delete_question_by_id_and_conf($id, $lecture){
		if(!is_numeric($id)){
			self::$error_message = 'Neznáma otázka.';
			return false;
		}
		if(!is_numeric($lecture)){
			self::$error_message = 'Neznáma prednáška.';
			return false;
		}


		$db = new Database();
		if ($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return false;
		}
		$conn = $db->handle;

		$res=$conn->query("DELETE FROM Question WHERE id={$id} AND lecture_id = {$lecture}");

		if($res == false){
			self::$error_message = 'Nepodarilo sa vymazať otázku.';
			return false;
		}

		return true;
	}
}