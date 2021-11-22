<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/lecture.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(['title' => 'Moje prednášky']); ?>
    
    <body>
        <?php 
        echo get_navbar();
        check_login("Pre zobrazenie tejto stránky musíte byť prihlásený.");
        ?>
      
        <div class='container-fluid'>  
        <div class='row'>

            
            <?php get_user_sidebar(); ?>

            <div class='col-lg-8 align-self-top'>
                <div class='row'>
                    <div class='col-sm-12 align-self-center pb-1'>
                        <h2>Moje prednášky</h2>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-sm-12 align-self-center pb-1'>


                        <?php
                        $sql = "WHERE id_user = {$_SESSION['user']->get_user_data()['id']} ORDER BY time_from ASC";
                        
                        $options = [
                            "table_id" => "lectures",
                            "ajax_url" => "/ajax/lecture_table.php",
                            "edit" => false,
                            "add" => false,
                            "delete" => false, // same as $is_owner ? true : false;
                            "custom_SQL" => $sql
                        ];

                        $table = new SimpleTable("Lecture", $options);

                        $table->table_structure['name']['name'] = "Názov";
                        $table->table_structure['name']['href_url'] = "/conferences/lecture.php?id=";

                        $table->table_structure['description']['name'] = "Popis";
                        $table->table_structure['description']['show_column'] = false;
                        
                        $table->table_structure['time_from']['name'] = "Od";
                        $table->table_structure['time_from']['type'] = "TIMESTAMP";
                        
                        $table->table_structure['time_to']['name'] = "Do";
                        $table->table_structure['time_to']['type'] = "TIMESTAMP";
                        
                        $table->table_structure['status']['name'] = "Stav";
                        $table->table_structure['status']['show_column'] = true;
                        $table->table_structure['status']['override'] = [
                            LECTURE_UNDEF => "navrhnutá",
                            LECTURE_CONFIRMED => "schválená",
                            LECTURE_DENIED => "zamietnutá"
                        ];

                        $table->table_structure['img_url']['show_column'] = false;
                        
                        $table->table_structure['id']['show_column'] = false;
                        
                        $table->table_structure['id_user']['show_column'] = false;

                        $table->table_structure['room_id']['name'] = "Miestnosť";
                        $table->table_structure['room_id']['href_url'] = "/conferences/room.php?id=";
                        $table->table_structure['room_id']['show_column'] = true;
                        $table->table_structure['room_id']['foreign_key'] = [
                            "table" => "Room",
                            "fk_key_name" => "id",
                            "table_vars" => ["name" => "Miestnosť"],
                            "form_var" => "name",
                            "custom_where" => ""
                        ];

                        $table->table_structure['conference_id']['show_column'] = true;
                        $table->table_structure['conference_id']['href_url'] = "/conferences/show.php?id=";
                        $table->table_structure['conference_id']['foreign_key'] = [
                            "table" => "Conference",
                            "fk_key_name" => "id",
                            "table_vars" => ["name" => "Konferencia"],
                            "form_var" => "id",
                            "custom_where" => ""
                        ];

                        echo $table->generate_table_html();
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $table->generate_table_scripts();?>
    </body>
</html>