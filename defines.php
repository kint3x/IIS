<?php

define('USER_ADMIN',1);
define('USER_REGULAR',0);

define('LECTURE_UNDEF', 0);
define('LECTURE_CONFIRMED', 1);
define('LECTURE_DENIED', 2);

define('ROOT',__DIR__);

define('DATE_FORMAT_CARD', 'd.m.y - h:i');
define('DATE_FORMAT_HTML', 'Y-m-d h:i');

define('IMG_DEFAULT', '/img/placeholder.jpg');

include_once(ROOT."/lib.php");
require_once(ROOT."/classes/conferences.class.php");
require_once(ROOT."/classes/room.class.php");

/**
 * Echo an encoded json array representing the response from ajax calls.
 */
function echo_json_response($success, $error) {
    echo json_encode(
        array(
            "success" => $success,
            "error" => $error
        )
    );

    return;
}

/**
 * Checks if the user is logged in and if so checks if he owns the conference.
 */
function user_owns_conference($owner_id) {
    return (isset($_SESSION['user']) && $_SESSION['user']->get_user_data()['id'] == $owner_id);
}

/**
 * Start session if it hasn't been started yet.
 */
function start_session_if_none() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}