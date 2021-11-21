<?php
require_once "../defines.php";
require_once ROOT."/classes/Room.class.php";
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

if ($action == "add") {

    if (isset($_POST["conference_id"]) && isset($_POST["name"])) {
        $conference = Conferences::get_conference_by_id($_POST['conference_id']);

        if ($conference === false) {
            echo_json_response($conference, Conferences::$error_message);
            return;
        }

        if (!user_owns_conference($conference['id_user'])) {
            echo_json_response(false, "Na spravovanie danej konferencie nemáte právo.");
            return;
        }

        $res = Room::add_room_to_conference($_POST["conference_id"], $_POST["name"]);
        echo_json_response($res, Room::$error_message);
        return;
    }

} else if ($action == "edit") {
    
    if (isset($_POST["conference_id"]) && isset($_POST["name"]) && isset($_POST["id"])) {

        $owner_id = Room::get_owner_id($_POST["id"]);

        if ($owner_id === false) {
            echo_json_response(false, Room::$error_message);
            return false;
        }

        if ($owner_id != $_SESSION['user']->get_user_data()['id'] && !is_admin()) {
            echo_json_response(false, "Na úpravu danej miestnosti nemáte právo.");
            return false;
        }
        if(is_length_long($_POST['name'],100)){
            echo_json_response(false, "Názov miestnosti je príliš dlhý.");
            return false;
        }

        $res = Room::update($_POST["id"], htmlspecialchars($_POST["name"]), $_POST["conference_id"]);

        echo_json_response($res, Room::$error_message);
        return;
    }   
 
} else if ($action == "delete") {

    if (isset($_POST["id"])) {

        $owner_id = Room::get_owner_id($_POST["id"]);

        if ($owner_id === false) {
            echo_json_response(false, Room::$error_message);
            return false;
        }

        if ($owner_id != $_SESSION['user']->get_user_data()['id'] && !is_admin()) {
            echo_json_response(false, "Na zrušenie danej miestnosti nemáte právo.");
            return false;
        }

        $res = Room::delete($_POST['id']);

        echo_json_response($res, Room::$error_message);
        return;
    }

}