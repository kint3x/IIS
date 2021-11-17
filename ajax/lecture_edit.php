<?php
require_once "../defines.php";
require_once ROOT."/classes/lecture.class.php";
require_once ROOT."/classes/conferences.class.php";
require_once ROOT."/classes/room.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre správu konferencie sa musíte prihlásiť.');
    return;
}



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
    
        
    $lecture = Lecture::get_lecture_by_id($_POST["id"]);
    
    // Couldn't find the lecture
    if ($lecture === false) {
        echo_json_response(false, Lecture::$error_message);
        return false;
    }
        
    $conference = Conferences::get_conference_by_id($lecture['conference_id']);
        
    // Couldn't find the lecture
    if ($conference === false) {
        echo_json_response(false, Conferences::$error_message);
        return false;
    }
        
    // Owner of the conference or an admin can edit time and rooms.
    $can_edit_all = user_owns_conference($conference['id_user']) || is_admin();

    // Owner of the lecture can edit name, description and images
    $can_edit = $can_edit_all || user_owns_lecture($lecture['id']);
        
    if (!$can_edit) {
        echo_json_response(false, "Na úpravu danej prednášky nemáte právo.");
        return false;
    }

    // Make timestamps and check for conflicts
    if ($_POST["time_from"] != '') {
        $start = DateTime::createFromFormat(DATE_FORMAT_SIMPLE_TABLE, $_POST["time_from"])->getTimestamp();
    } else {
        $start = NULL;
    }

    if ($_POST["time_to"] != '') {
        $end = DateTime::createFromFormat(DATE_FORMAT_SIMPLE_TABLE, $_POST["time_to"])->getTimestamp();
    } else {
        $end = NULL;
    }

    $time_changed = $start != $lecture['time_from'] || $end != $lecture['time_to'];
    $room_changed = $_POST["room_id"] != $lecture['room_id'];

    if (($time_changed && !$can_edit_all) || ($room_changed && !$can_edit_all)) {
        echo_json_response(false, "Na vykonanie daných zmien nemáte dostatočné oprávnenie.");
        return false;
    }

    if ($start > $end) {
        echo_json_response(false, "Konferencia musí začať skôr ako skončí.");
        return;
    }

    if (Room::is_free($_POST['room_id'], $_POST['id'], $start, $end) === false) {
        echo_json_response(false, "Miestnost je v danom časovom intervale obsadená.");
        return;
    }

    $res = Lecture::update(
        $_POST["id"],
        $_POST["name"],
        $_POST["description"],
        $start,
        $end,
        $_POST["img_url"],
        $_POST["room_id"],
        $_POST["id_user"],
        $_POST["conference_id"],
        $_POST["status"]);

    echo_json_response($res, Lecture::$error_message);
    return;
}   