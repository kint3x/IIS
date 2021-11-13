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
    && isset($_POST['price'])
    && isset($_POST['capacity'])
    && isset($_POST['image_url'])) {
    
    // Get the owner id
    $owner_id = $_SESSION['user']->get_user_data()['id'];

    // Create timestamps from the entered time and date values
    $format = "Y-m-d H:i";
    $from_ts = DateTime::createFromFormat($format, $_POST['fromDate']." ".$_POST['fromTime'])->getTimestamp();
    $to_ts = DateTime::createFromFormat($format, $_POST['toDate']." ".$_POST['toTime'])->getTimestamp();
    
    // Create new conference
    $res = Conferences::create_conference(
        $owner_id,
        $_POST['name'],
        $_POST['description'],
        $from_ts,
        $to_ts,
        $_POST['price'],
        $_POST['capacity'],
        $_POST['image_url']
    );
    
    // Conference wasn't created
    if ($res === false) {
        echo_json_response($res, Conferences::$error_message);
        return;
    }

    // No tags
    if (!isset($_POST['tags'])) {
        echo_json_response($res, Conferences::$error_message);
        return;
    }

    // Add tags to the crated conference
    $conference_id = $res[0][0];
    
    foreach ($_POST['tags'] as $tag_id) {
        $res = Tag::add_tag_to_conference($conference_id, $tag_id);
        
        if (!$res) {
            echo_json_response($res, Tag::$error_message);
            return;
        }
    }
    
    // Success
    echo json_encode(
        array(
            "success" => true,
            "error" => "",
            "conference_id" => $conference_id,
        )
    );
    return;
}