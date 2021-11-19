<?php

require_once "../defines.php";
require_once ROOT."/classes/schedule.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre pridanie prednášky do svojho rozvrhu je potrebné sa prihlásiť.');
    return;
}

if (isset($_POST["lecture_id"])) {
    $res = Schedule::add_to_schedule($_SESSION['user']->get_user_data()['id'], $_POST['lecture_id']);

    echo_json_response($res, Schedule::$error_message);
    return;
}