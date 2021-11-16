<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/room.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>

    <script>
        
    </script>

    <body>
        <?php 
        
        echo get_navbar();
        verify_conference();

        $conference = Conferences::get_conference_by_id($_GET['id']);
        $tags = Tag::get_conference_tags($conference['id']);
        ?>

        <div class="container-fluid">
            <div class="row">
                
                <?php get_conference_sidebar($conference['id_user'], $conference['id']); ?>
                
                <div class="col-sm-8 align-self-center">

                    <?php
                        $rooms = Room::get_conference_rooms($conference['id']);
                        
                        if ($rooms === false) {
                            display_alert("Zatiaľ neboli pre konferenciu určené žiadne miestnosti.");
                        }

                    ?>

                </div>
            </div>
        </div>

        <script>
        function searchByTag(tag_id) {
            var url = window.location.href;

            url =  "/?tag=" + encodeURIComponent(tag_id);
            window.location.href = url;
        }
        </script>

    </body>
</html