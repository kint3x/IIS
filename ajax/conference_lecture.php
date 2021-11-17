<?php
require_once "../defines.php";
require_once ROOT."/classes/Lecture.class.php";
require_once ROOT."/classes/Conferences.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre správu konferencie sa musíte prihlásiť.');
    return;
}


if (!isset($_POST["action"]))  {
    echo_json_response(false, 'Chyba pri upravovaní.');
    return;
}

$action = $_POST["action"];

if ($action == "edit") {
    
    if (isset($_POST["id"]) 
        && isset($_POST["name"]) 
        && isset($_POST["description"]) 
        && isset($_POST["time_from"]) 
        && isset($_POST["time_to"]) 
        && isset($_POST["img_url"]) 
        && isset($_POST["room_id"]) 
        && isset($_POST["id_user"]) 
        && isset($_POST["conference_id"]) 
        && isset($_POST["status"])) {

        $owner_id = Lecture::get_conference_owner($_POST["id"]);

        // Couldn't find the owner
        if ($owner_id === false) {
            echo_json_response(false, Room::$error_message);
            return false;
        }

        // Logged in user doesn't own the conference.
        if ($owner_id != $_SESSION['user']->get_user_data()['id']) {
            echo_json_response(false, "Na úpravu danej miestnosti nemáte právo.");
            return false;
        }

        $res = Lecture::update(
            $_POST["id"],
            $_POST["name"],
            $_POST["description"],
            $_POST["time_from"],
            $_POST["time_to"],
            $_POST["img_url"],
            $_POST["room_id"],
            $_POST["id_user"],
            $_POST["conference_id"],
            $_POST["status"]);

        echo_json_response($res, Lecture::$error_message);
        return;
    }   
 
}