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
        verify_room_in_conference();

        $conference = Conferences::get_conference_by_id($_GET['id']);
        $room = Room::get_room_by_id($_GET['room']);
        
        if ($room === false) {
            display_alert_container(Room::$error_message);
            exit();
        }

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
                        <?php echo $room['name'];?>
                        <br>
                        <small class="text-muted">Rozpis prednášok</small>
                    </h2>
        
                    <?php
                        
                        $is_owner = user_owns_conference($conference['id_user']);

                        $sql = $is_owner ? "WHERE conference_id = {$conference['id']} AND room_id = {$room['id']} ORDER BY time_from ASC" :
                            "WHERE conference_id = {$conference['id']} AND room_id = {$room['id']} AND status = ".LECTURE_CONFIRMED." ORDER BY time_from ASC";

                        $options = [
                            "table_id" => "lectures",
                            "ajax_url" => "/ajax/conference_lecture.php",
                            "edit" => $is_owner,
                            "add" => false,
                            "delete" => false, // same as $is_owner ? true : false;
                            "custom_SQL" => $sql
                        ];
                        
                        $table = new SimpleTable("Lecture", $options);
                        $table->table_structure['name']['name'] = "Názov";
                        $table->table_structure['name']['form_edit_show'] = true;
                        $table->table_structure['name']['form_edit_prefill'] = true;
                        $table->table_structure['name']['editable'] = false;

                        $table->table_structure['description']['name'] = "Popis";
                        $table->table_structure['description']['show_column'] = false;
                        $table->table_structure['description']['form_edit_show'] = true;
                        $table->table_structure['description']['form_edit_prefill'] = true;
                        $table->table_structure['description']['editable'] = false;
                        
                        $table->table_structure['time_from']['name'] = "Od";
                        $table->table_structure['time_from']['form_edit_show'] = $is_owner;
                        $table->table_structure['time_from']['form_edit_prefill'] = true;
                        
                        $table->table_structure['time_to']['name'] = "Do";
                        $table->table_structure['time_to']['form_edit_show'] = $is_owner;
                        $table->table_structure['time_to']['form_edit_prefill'] = true;
                        
                        $table->table_structure['status']['name'] = "Stav";
                        $table->table_structure['status']['show_column'] = $is_owner;
                        $table->table_structure['status']['override'] = [
                            LECTURE_UNDEF => "navrhnutá",
                            LECTURE_CONFIRMED => "schválená",
                            LECTURE_DENIED => "zamietnutá"
                        ];

                        $table->table_structure['img_url']['show_column'] = false;
                        $table->table_structure['img_url']['form_edit_show'] = false;
                        
                        $table->table_structure['id']['show_column'] = false;
                        $table->table_structure['id']['form_edit_show'] = false;
                        
                        $table->table_structure['id_user']['name'] = "Email";
                        $table->table_structure['id_user']['show_column'] = true;
                        $table->table_structure['id_user']['form_edit_show'] = true;
                        $table->table_structure['id_user']['form_edit_prefill'] = true;
                        $table->table_structure['id_user']['editable'] = false;
                        $table->table_structure['id_user']['foreign_key'] = [
                            "table" => "User",
                            "fk_key_name" => "id",
                            "table_vars" => ["email" => "Email"],
                            "form_var" => "email",
                            "custom_where" => ""
                        ];

                        $table->table_structure['room_id']['name'] = "Miestnosť";
                        $table->table_structure['room_id']['show_column'] = false;
                        $table->table_structure['room_id']['form_edit_show'] = $is_owner;
                        $table->table_structure['room_id']['form_edit_prefill'] = true;
                        $table->table_structure['room_id']['foreign_key'] = [
                            "table" => "Room",
                            "fk_key_name" => "id",
                            "table_vars" => [],
                            "form_var" => "name",
                            "custom_where" => "WHERE conference_id = {$conference['id']}"
                        ];

                        $table->table_structure['conference_id']['show_column'] = false;
                        $table->table_structure['conference_id']['form_edit_show'] = false;
                        $table->table_structure['conference_id']['static_value'] = $conference['id'];

                        echo $table->generate_table_html();
                    ?>
                </div>
            </div>
        </div>

    <?php echo $table->generate_table_scripts(); ?>

    </body>
</html