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
        verify_conference();
        check_login('Pre správu konferencíí musíte byť prihlásený.');
        verify_conference_owner();        

        $conference = Conferences::get_conference_by_id($_GET['id']);
        $tags = Tag::get_conference_tags($conference['id']);
        ?>

        <div class="container-fluid">
            <div class="row">
                
                <?php get_conference_sidebar($conference); ?>
                
                <div class="col-xl-8 align-self-center">

                    <?php
                        $rooms = Room::get_conference_rooms($conference['id']);
                        
                        if ($rooms === false) {
                            display_alert(Room::$error_message);
                            exit();
                        }

                        if ($rooms === -1) {  
                            ?>
                            <div class='alert alert-secondary' role='alert'>
                                Zatiaľ ste pre danú konferenciu neurčili žiadne miestnosti.
                                <a href="#">Pridať?</a>
                            </div>
                            <?php
                        } else {
                            $options = [
                                "table_id" => "rooms",
                                "ajax_url" => "/ajax/rooms.php",
                                "delete" => true,
                                "edit" => true,
                                "add" => true,
                                "custom_SQL" => "WHERE conference_id = {$conference['id']}"
                            ];
    
                            ?>
                            <h2 class="pb-1">
                                Miestnosti
                            </h2>
                            <?php

                            $table = new SimpleTable("Room", $options);

                            $table->table_structure['name']['name'] = "Názov";
                            $table->table_structure['name']['href_url'] = "/conferences/room.php?id=";
                            $table->table_structure['id']['show_column'] = false;
                            $table->table_structure['id']['form_edit_show'] = false;
                            $table->table_structure['conference_id']['show_column'] = false;
                            $table->table_structure['conference_id']['form_edit_show'] = false;
                            $table->table_structure['conference_id']['static_value'] = $conference['id'];

                            echo $table->generate_table_html();
                        }
                        
                        ?>
                </div>
            </div>
        </div>
        
        <?php
        echo $table->generate_table_scripts();
        ?>

    </body>
</html