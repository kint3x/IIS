<?php

require_once "../defines.php";
require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

start_session_if_none();

if (isset($_POST['name'])
    && isset($_POST['description'])
    && isset($_POST['fromTime'])
    && isset($_POST['fromDate'])
    && isset($_POST['toTime'])
    && isset($_POST['toDate'])
    && isset($_POST['place'])
    && isset($_POST['price'])
    && isset($_POST['capacity'])) {
    
    // Get the owner id
    $owner_id = $_SESSION['user']->get_user_data()['id'];

    // Create timestamps from the entered time and date values
    $format = "Y-m-d H:i";
    $from_ts = DateTime::createFromFormat($format, $_POST['fromDate']." ".$_POST['fromTime'])->getTimestamp();
    $to_ts = DateTime::createFromFormat($format, $_POST['toDate']." ".$_POST['toTime'])->getTimestamp();
    
    $res = Conferences::create_conference(
        $owner_id,
        $_POST['name'],
        $_POST['description'],
        $from_ts,
        $to_ts,
        $_POST['price'],
        $_POST['capacity'],
        $_POST['place']
    );

    echo_json_response($res, Conferences::$error_message);
    return;
}
?>