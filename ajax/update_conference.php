<?php

require_once "../defines.php";
require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

start_session_if_none();

if (isset($_POST['id'])
    && isset($_POST['name'])
    && isset($_POST['description'])
    && isset($_POST['street'])
    && isset($_POST['city'])
    && isset($_POST['zip'])
    && isset($_POST['state'])
    && isset($_POST['fromTime'])
    && isset($_POST['fromDate'])
    && isset($_POST['toTime'])
    && isset($_POST['toDate'])
    && isset($_POST['price'])
    && isset($_POST['capacity'])
    && isset($_POST['image_url'])) {

    if (!isset($_SESSION['user'])) {
        echo_json_response(false, "Na vykonanie daných zmien nemáte oprávnenie.");
        return;
    }
    
    $conference = Conferences::get_conference_by_id($_POST['id']);

    if ($conference === false) {
        echo_json_response($conference, Conferences::$error_message);
        return;
    }

    if (!user_owns_conference($conference['id_user'])) {
        echo_json_response(false, 'Na úpravu danej konferencie nemáte právo.');
        return;
    }

    $tickets_left = Conferences::get_number_tickets_left($conference['id']);

    if ($_POST['capacity'] < $tickets_left) {
        echo_json_response(false, "Kapacita musí byť vačšia ako počet rezervovaných vstupeniek");
        return;
    }

    // Create timestamps from the entered time and date values
    $format = "Y-m-d H:i";
    $from_ts = DateTime::createFromFormat($format, $_POST['fromDate']." ".$_POST['fromTime'])->getTimestamp();
    $to_ts = DateTime::createFromFormat($format, $_POST['toDate']." ".$_POST['toTime'])->getTimestamp();

    // Create new conference
    $res = Conferences::update_conference(
        $_POST['id'],
        htmlspecialchars_decode($_POST['name'],ENT_QUOTES),
        htmlspecialchars_decode($_POST['description'],ENT_QUOTES),
        htmlspecialchars_decode($_POST['street'],ENT_QUOTES),
        htmlspecialchars_decode($_POST['city'],ENT_QUOTES),
        $_POST['zip'],
        htmlspecialchars_decode($_POST['state'],ENT_QUOTES),
        $from_ts,
        $to_ts,
        $_POST['price'],
        $_POST['capacity'],
        htmlspecialchars_decode($_POST['image_url'],ENT_QUOTES)
    );
    
    // Conference wasn't updated
    if ($res === false) {
        echo_json_response($res, Conferences::$error_message);
        return;
    }
    
    // Update tags
    Tag::remove_tags_from_conference($_POST['id']);

    // No tags were set
    if (!isset($_POST['tags'])) {
        echo_json_response($res, Conferences::$error_message);
        return;
    }

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