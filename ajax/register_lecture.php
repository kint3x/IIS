<?php

require_once "../defines.php";
require_once ROOT."/classes/lecture.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre navrhnutie príspevku je potrebné sa prihlásiť.');
    return;
}
if(is_length_long(htmlspecialchars($_POST['name']),150)){
    echo_json_response(false, 'Názov je príliš dlhý.');
    return;
}
if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['conference_id'])) {
    $res = Lecture::register_lecture(htmlspecialchars($_POST['name']), htmlspecialchars($_POST['description']), $_SESSION['user']->get_user_data()['id'], $_POST['conference_id']);

    echo_json_response($res, Lecture::$error_message);
    return;
}