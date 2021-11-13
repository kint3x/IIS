<?php

define('USER_ADMIN',1);
define('USER_REGULAR',0);
define('ROOT',__DIR__);
define('DATE_FORMAT_CARD', 'd.m.y - h:i');
define('DATE_FORMAT_HTML', 'Y-m-d h:i');

include_once(ROOT."/lib.php");

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
 * Start session if it hasn't been started yet.
 */
function start_session_if_none() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}