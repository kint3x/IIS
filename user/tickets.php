<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/reservation.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(['title' => 'Moje vstupenky']); ?>
    
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
                        <h2>Moje vstupenky</h2>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-sm-12 align-self-center pb-1'>


                        <?php
                        $reservations = Reservation::get_all_reservation_by_user_id($_SESSION['user']->get_user_data()['id']);
                            
                        $ids = "";
                        foreach($reservations as $key => $res){
                            if($key != 0) $ids .= ",";

                            $ids.=$res['id'];
                        }

                        $sql = ($ids != "")? "WHERE reservation_id IN ($ids)" : "WHERE reservation_id IN (0)" ;


                        $options = [
                            "table_id" => "tickets",
                            "ajax_url" => "",
                            "edit" => false,
                            "add" => false,
                            "delete" => false, // same as $is_owner ? true : false;
                            "custom_SQL" => $sql
                        ];

                        $table = new SimpleTable("Ticket", $options);

                        $table->table_structure['id']['show_column'] = false;

                        $table->table_structure['reservation_id']['name'] = "Číslo rezervácie";
                        $table->table_structure['reservation_id']['show_column'] = true;
                        
                        $table->table_structure['reservation_id']['foreign_key'] = [
                            "table" => "Reservation",
                            "fk_key_name" => "id",
                            "table_vars" => array("conference_id" => "Číslo konferencie"),
                            "form_var" => "",
                            "custom_where" => "",
                        ];

                        $table->table_structure['hash']['name'] = "Kód vstupenky";
                        $table->table_structure['hash']['href_url'] = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=";
                        $table->table_structure['hash']['href_url_type'] = "VALUE";                 

                        echo $table->generate_table_html();
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $table->generate_table_scripts();?>
    </body>
</html>