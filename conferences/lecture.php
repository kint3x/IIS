<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/room.class.php';
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
       
        $conference_id = Lecture::get_conference_id($lecture['id']);

        if ($conference_id === false) {
            display_alert_container(Lecture::$error_message);
            exit();
        }
       
        $conference = Conferences::get_conference_by_id($conference_id);
        
        if ($conference === false) {
            display_alert_container(Conferences::$error_message);
            exit();
        }

        ?>

        <div class="container-fluid">
            <div class="row">
                
                <?php get_conference_sidebar($conference); ?>
                
                <div class="col-sm-8 align-self-top">
                    <h2 class="mb-1">
                        <?php echo $lecture['name'];?>
                    </h2>
       
                </div>
            </div>
        </div>
    </body>
</html