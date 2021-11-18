<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/lecture.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>
    
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
                        <h2>Moje rezervácie</h2>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-sm-12 align-self-center pb-1'>


                        <?php
                        $sql = "WHERE user_id = {$_SESSION['user']->get_user_data()['id']}";
                        
                        $options = [
                            "table_id" => "reservation",
                            "ajax_url" => "/ajax/lecture_table.php",
                            "edit" => false,
                            "add" => false,
                            "delete" => false, // same as $is_owner ? true : false;
                            "custom_SQL" => $sql
                        ];

                        $table = new SimpleTable("Reservation", $options);

                        $table->table_structure['id']['show_column'] = false;
                        $table->table_structure['name']['show_column'] = false;
                        $table->table_structure['email']['show_column'] = false;
                        $table->table_structure['street']['show_column'] = false;
                        $table->table_structure['city']['show_column'] = false;
                        $table->table_structure['zip']['show_column'] = false;
                        $table->table_structure['user_id']['show_column'] = false;
                        $table->table_structure['country']['show_column'] = false;
                        $table->table_structure['surname']['show_column'] = false;


                        $table->table_structure['num_tickets']['name'] = "Počet lístkov";
                        $table->table_structure['state']['name'] = "Status";
                        $table->table_structure['price']['name'] = "Cena (€)";

                        $table->table_structure['conference_id']['href_url']='/conferences/show.php?id=';

                        $table->table_structure['state']['override'] = [
                            RESERVATION_NEW => "nová",
                            RESERVATION_CONFIRMED => "schválená",
                            RESERVATION_DENIED => "zamietnutá"
                        ];

                        $table->table_structure['conference_id']['foreign_key'] = 
                        array(
                            "table" => "Conference",
                            "fk_key_name" => "id",
                            "table_vars" => array("name" => "Konferencia",),
                            "form_var" => "email",
                            "custom_where" => "", // ked to je napriklad v uzivatelovi a chces obmedzit co mu da do selectu
                        );



                        echo $table->generate_table_html();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>