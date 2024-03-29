<?php
require_once "../defines.php";
require_once ROOT."/classes/schedule.class.php";
require_once ROOT."/classes/room.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre zobrazenie rozvrhu sa musíte prihlásiť.');
    return;
}

if (isset($_POST["start"]) 
    && isset($_POST["end"])) {

    $lectures = Schedule::get_user_schedule($_SESSION['user']->get_user_data()['id'], $_POST["start"], $_POST["end"]);

    if ($lectures === false) {
        echo_json_response(false, Schedule::$error_message);
        return;
    }

    echo json_encode(
        array(
            'success' => true,
            'lectures' => $lectures
        )
    );
    return;
}   