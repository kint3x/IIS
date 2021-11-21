<?php
require_once "../defines.php";
require_once ROOT."/classes/reservation.class.php";
require_once ROOT."/classes/conferences.class.php";

start_session_if_none();

if (!isset($_SESSION['user'])) {
    echo_json_response(false, 'Pre správu rezervácií sa musíte prihlásiť.');
    return;
}


if (!isset($_POST["action"]))  {
    echo_json_response(false, 'Chyba pri upravovaní.');
    return;
}

if ($_POST["action"] == "edit") {
    
    $user = $_SESSION['user']->get_user_data();
    $is_admin = $_SESSION['user']->is_admin();

    if ($is_admin) {
        // admin can edit everything

        if (!isset($_POST['id']) 
            || !isset($_POST['name'])
            || !isset($_POST['surname'])
            || !isset($_POST['email'])
            || !isset($_POST['street'])
            || !isset($_POST['city'])
            || !isset($_POST['zip'])
            || !isset($_POST['country'])
            || !isset($_POST['num_tickets'])
            || !isset($_POST['state'])
            ) {
            return;
        }

        $res = Reservation::update_reservation(
            $_POST['id'],
            $_POST['name'],
            $_POST['surname'],
            $_POST['email'],
            $_POST['street'],
            $_POST['city'],
            $_POST['zip'],
            $_POST['country'],
            $_POST['num_tickets'],
            $_POST['state']
        );

        echo_json_response($res, Reservation::$error_message);
        return;

    } else {
        // user can only change the reservation status

        if(!isset($_POST['id']) || !isset($_POST['state'])) {
            return;
        }

        $reservation = Reservation::get_reservation_by_id($_POST["id"]);

        if ($reservation === false) {
            echo_json_respone($reservation, Reservation::$error_message);
            return;
        }

        $conference = Conferences::get_conference_by_id($reservation['conference_id']);

        if ($conference === false) {
            echo_json_respone($conference, Conferences::$error_message);
            return;
        }

        // Check if the logged in user owns the conference
        if ($conference['id_user'] != $user['id']) {
            echo_json_response(false, "Na spravovanie rezervácií nemáte oprávnenie.");
            return;
        }

        $res = Reservation::change_status($reservation['id'], htmlspecialchars($_POST['state']));
        
        echo_json_response($res, Reservation::$error_message);
        return;
    }
}