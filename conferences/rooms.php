<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/tag.class.php';

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
                <?php get_conference_sidebar("rooms", $conference['id'], $conference['id_user']); ?>
                <div class="col-sm-8 align-self-center">
                    #TODO vypisat tabulku miestnosti + majitel konferencie moze upravovat
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