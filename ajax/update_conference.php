<?php

require_once "../defines.php";
require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

start_session_if_none();


if (isset($_POST['id'])
    && isset($_POST['name'])
    && isset($_POST['description'])
    && isset($_POST['tags'])
    && isset($_POST['fromTime'])
    && isset($_POST['fromDate'])
    && isset($_POST['toTime'])
    && isset($_POST['toDate'])
    && isset($_POST['price'])
    && isset($_POST['capacity'])
    && isset($_POST['image_url'])) {

    if (!isset($_SESSION['user']) || !Conferences::is_owner($_SESSION['user']->get_user_data()['id'], $_POST['id'])) {
        echo_json_response(false, "Na vykonanie daných zmien nemáte oprávnenie.");
        return;
    }
    
    // Create timestamps from the entered time and date values
    $format = "Y-m-d H:i";
    $from_ts = DateTime::createFromFormat($format, $_POST['fromDate']." ".$_POST['fromTime'])->getTimestamp();
    $to_ts = DateTime::createFromFormat($format, $_POST['toDate']." ".$_POST['toTime'])->getTimestamp();
    
    // Create new conference
    $res = Conferences::create_conference(
        $_POST['id'],
        $_POST['name'],
        $_POST['description'],
        $from_ts,
        $to_ts,
        $_POST['price'],
        $_POST['capacity'],
        $_POST['image_url']
    );
    
    // Conference wasn't updated
    if ($res === false) {
        echo_json_response($res, Conferences::$error_message);
        return;
    }

    // Update tags
    Tag::remove_tags_from_conference($_POST['id']);

    foreach ($_POST['tags'] as $tag_id) {
        $res = Tag::add_tag_to_conference($_POST['id'], $tag_id);
        
        if (!$res) {
            echo_json_response($res, Tag::$error_message);
            return;
        }
    }
    
    // Success
    echo_json_response($res, Tag::$error_message);
    return;
}
?>