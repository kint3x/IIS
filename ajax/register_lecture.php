<?php

require_once "../defines.php";
require_once ROOT."/classes/lecture.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre registráciu konferencie je potrebné sa prihlásiť.');
    return;
}

if (isset($_POST['name']) && isset($_POST['description'])) {
    $res = Lecture::register_lecture($_POST['name'], $_POST['description'], $_SESSION['user']->get_user_data()['id']);

    echo_json_response($res, Lecture::$error_message);
    return;
}