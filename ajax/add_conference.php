<?php

require_once "../defines.php";
require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/user.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre pridanie konferencie je potrebné sa prihlásiť.');
    return;
}

if (isset($_POST['name'])
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
    
    // Get the owner id
    $owner_id = $_SESSION['user']->get_user_data()['id'];

    // Create timestamps from the entered time and date values
    $format = "Y-m-d H:i";
    $from_ts = DateTime::createFromFormat($format, $_POST['fromDate']." ".$_POST['fromTime'])->getTimestamp();
    $to_ts = DateTime::createFromFormat($format, $_POST['toDate']." ".$_POST['toTime'])->getTimestamp();
    
    // Default img url
    $image_url = htmlspecialchars($_POST['image_url']);
    if ($image_url == "") {
        $image_url = IMG_DEFAULT;
    }

    //check vars
    if(is_length_long(htmlspecialchars($_POST['name']),150)){
         echo_json_response(false, "Meno konferencie je príliš dlhé.");
         return;
    }
    if(is_length_long(htmlspecialchars($_POST['street']),150)){
         echo_json_response(false, "Meno ulice je príliš dlhé.");
         return;
    }
    if(is_length_long(htmlspecialchars($_POST['city']),150)){
         echo_json_response(false, "Meno mesta je príliš dlhé.");
         return;
    }
    if(is_length_long(htmlspecialchars($_POST['state']),150)){
         echo_json_response(false, "Meno štátu je príliš dlhé.");
         return;
    }
    if(!is_numeric($_POST['zip'])){
        echo_json_response(false, "PSČ nie je číslo");
        return;
    }
    if(!is_numeric($_POST['capacity'])){
        echo_json_response(false, "Kapacita nie je číslo");
        return;
    }
    $_POST['price'] = str_replace(",",".",$_POST['price']);
    if(!is_numeric($_POST['price'])){
        echo_json_response(false, "Cena nie je číslo");
        return;
    }



    // Create new conference
    $res = Conferences::create_conference(
        $owner_id,
        htmlspecialchars($_POST['name']),
        htmlspecialchars($_POST['description']),
        htmlspecialchars($_POST['street']),
        htmlspecialchars($_POST['city']),
        $_POST['zip'],
        htmlspecialchars($_POST['state']),
        $from_ts,
        $to_ts,
        $_POST['price'],
        $_POST['capacity'],
        $image_url
    );
    
    // Conference wasn't created
    if ($res === false) {
        echo_json_response($res, Conferences::$error_message);
        return;
    }

    
    $conference_id = $res[0][0];

    // No tags
    if (!isset($_POST['tags'])) {
        echo json_encode(
            array(
                "success" => true,
                "error" => "",
                "conference_id" => $conference_id,
            )
        );
        return;
    }

    // Add tags to the crated conference    
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