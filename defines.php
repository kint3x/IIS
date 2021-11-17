<?php

define('USER_ADMIN',1);
define('USER_REGULAR',0);

define('LECTURE_UNDEF', 0);
define('LECTURE_CONFIRMED', 1);
define('LECTURE_DENIED', 2);

define('ROOT',__DIR__);

define('DATE_FORMAT_CARD', 'h:i d.m.y');
define('DATE_FORMAT_HTML', 'Y-m-d h:i');
define('DATE_FORMAT_SIMPLE_TABLE', 'Y-m-dTh:i');

define('IMG_DEFAULT', '/img/placeholder.jpg');

include_once(ROOT."/lib.php");
require_once(ROOT."/classes/conferences.class.php");
require_once(ROOT."/classes/room.class.php");
require_once(ROOT."/classes/lecture.class.php");

/**
 * Converts a time string from the 'Y-m-dTh:i' format to the 'Y-m-d h:i' format.
 */
function convert_timestring($timestring) {
    $t_index = strpos($timestring, 'T');
    $time = substr($timestring, 0, $t_index);
    $date = substr($timestring, $t_index+1);

    return $time.' '.$date;
}

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
 * Check if the user is admin.
 */
function is_admin() {
    return (isset($_SESSION['user']) && $_SESSION['user']->get_user_data()['role'] == USER_ADMIN);
}

function user_owns_lecture($lecture_id) {
    $lecture = Lecture::get_lecture_by_id($lecture_id);

    if ($lecture === false) {
        return false;
    }

    return (isset($_SESSION['user']) && $_SESSION['user']->get_user_data()['id'] == $lecture['id_user']);
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