<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/room.class.php';
require_once ROOT.'/classes/lecture.class.php';
require_once ROOT.'/classes/table.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>

    <script>
        
    </script>

    <body>
        <?php 
        
        echo get_navbar();

        verify_lecture();

        $lecture = Lecture::get_lecture_by_id($_GET['id']);
        
        if ($lecture === false) {
            display_alert_container(Lecture::$error_message);
            exit();
        }
       
        $conference = Conferences::get_conference_by_id($lecture['conference_id']);
        
        if ($conference === false) {
            display_alert_container(Conferences::$error_message);
            exit();
        }

        // Check if the lecture has a room assigned
        if ($lecture['room_id'] === NULL) {
            $room_name = "-";
        } else {
            $room = Room::get_room_by_id($lecture['room_id']);

            if ($room === false) {
                display_alert_container(Room::$error_message);
                exit();
            }

            $room_name = $room['name'];
        }

        if ($lecture['time_from'] === NULL || $lecture['time_to'] === NULL) {
            $time = '-';
        } else {
            $time = date(DATE_FORMAT_CARD, $lecture['time_from']).' - '.date(DATE_FORMAT_CARD, $lecture['time_to']);
        }

        $can_edit = is_admin() || user_owns_lecture($lecture['id']) || user_owns_conference($conference['id_user']); # TODO OWNS CONFERENCE

        ?>

        <div class="container-fluid">
            <div class="row">
                
                <?php get_conference_sidebar($conference); ?>

                <div class="col-xl-8 align-self-top">
                    <div class="card mb-12">
                        <img class="card-img-top img-top-fixed-height" src="<?php echo $lecture['img_url']; ?>" alt="Card image cap">
                        <?php
                        if ($lecture['status'] != LECTURE_CONFIRMED) {
                            ?>
                            <div class="card-header">
                                Táto konferencia, zatiaľ nie je potvrdená.
                            </div>
                            <?php
                        }
                        ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $lecture['name'];?></h5>
                            <p class="card-text"><?php echo $lecture['description'];?></p>
                        </div>                            

                        <ul class="list-group list-group-flush d-flex flex-row flex-wrap">
                            

                            <li class="list-group-item col-md-6 pl-list-item">
                                <h6>Čas</h6>
                                <?php echo $time; ?>
                            </li>
                            <li class="list-group-item col-md-6">
                                <h6>Miestnosť</h6><?php echo $room_name;?>
                            </li>
                        </ul>
                        <?php if($can_edit) {
                            ?>
                            <div class="card-footer">
                                <a href="/lecture/edit.php?id=<?php echo $lecture['id'];?>" class="btn btn-outline-dark">Upraviť</a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html