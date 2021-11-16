<?php
require_once "../defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/database.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre správu konferencie sa musíte prihlásiť.');
    return;
}

if (isset($_POST["id"])
    && isset($_POST["name"])
    && isset($_POST["conference_id"])) {
        echo_json_response(true, '');
        return;
    }