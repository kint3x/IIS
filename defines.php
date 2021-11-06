<?php

define('USER_ADMIN',1);
define('USER_REGULAR',0);
define('ROOT',__DIR__);

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