<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/room.class.php';
require_once ROOT.'/classes/table.class.php';

start_session_if_none();

?>

<html>
    <?php verify_conference_and_generate_head(); ?>

    <script>
        
    </script>

    <body>
        <?php 
        
        echo get_navbar();
        check_login('Pre správu konferencie musíte byť prihlásený.');
        verify_conference_owner();        

        $is_admin = $_SESSION['user']->is_admin();
        $conference = Conferences::get_conference_by_id($_GET['id']);
        ?>

        <div class="container-fluid">
            <div class="row">
                
                <?php get_conference_sidebar($conference); ?>
                
                <div class="col-lg-8 align-self-center">

                    <?php
                        $options = [
                            "table_id" => "reservations",
                            "ajax_url" => "/ajax/reservation_table.php",
                            "delete" => false,
                            "edit" => true,
                            "add" => false,
                            "custom_SQL" => "WHERE conference_id = {$conference['id']}"
                        ];

                        ?>
                        
                        <h2 class="pb-1">
                            Rezervácie
                        </h2>

                        <?php
                        $table = new SimpleTable("Reservation", $options);
                        
                        $table->table_structure['name']['name'] = "Meno";
                        $table->table_structure['name']['editable'] = $is_admin;
                        
                        $table->table_structure['surname']['name'] = "Priezvisko";
                        $table->table_structure['surname']['editable'] = $is_admin;

                        $table->table_structure['email']['name'] = "Email";
                        $table->table_structure['email']['editable'] = $is_admin;

                        $table->table_structure['user_id']['show_column'] = false;
                        $table->table_structure['user_id']['form_edit_show'] = false;

                        $table->table_structure['street']['name'] = "Ulica";
                        $table->table_structure['street']['editable'] = $is_admin;

                        $table->table_structure['city']['name'] = "Mesto";
                        $table->table_structure['city']['editable'] = $is_admin;

                        $table->table_structure['zip']['name'] = "PSČ";
                        $table->table_structure['zip']['editable'] = $is_admin;

                        $table->table_structure['country']['name'] = "Štát";
                        $table->table_structure['country']['editable'] = $is_admin;

                        $table->table_structure['price']['name'] = "Cena (&euro;)";
                        $table->table_structure['price']['editable'] = $is_admin;

                        $table->table_structure['num_tickets']['name'] = "Počet";
                        $table->table_structure['num_tickets']['editable'] = $is_admin;

                        $table->table_structure['state']['name'] = "Stav";
                        $table->table_structure['state']['editable'] = true;
                        $table->table_structure['state']['override'] = [
                            RESERVATION_NEW => "nová",
                            RESERVATION_CONFIRMED => "schválená",
                            RESERVATION_DENIED => "zamietnutá"
                        ];
                        
                        $table->table_structure['id']['show_column'] = false;
                        $table->table_structure['id']['form_edit_show'] = false;
                        
                        $table->table_structure['conference_id']['show_column'] = false;
                        $table->table_structure['conference_id']['form_edit_show'] = false;

                        echo $table->generate_table_html();
                        
                        ?>
                </div>
            </div>
        </div>

        <?php
            if (isset($table)) {
                echo $table->generate_table_scripts();
            }
        ?>

        <script>

        $(document).ready(function () {
            // Remove allerts when closing the window
            $("#addRoomModal").on('hide.bs.modal', function(event) {
                $("#roomAlert").html("");
            });
            
            // Registering a lecture
            $("#roomForm").submit(function (event) {
                var formData = {
                    action: "add",
                    name: $("#roomName").val(),
                    conference_id: $("#conferenceId").val()
                }
                
                $.ajax({
                    type: "POST",
                    url: "/ajax/rooms.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                }).done(function (data) {
                    if(data.success){
                        console.log('asdf');
                        location.reload();
                    }
                    else{             
                        var alert = "<div class='alert alert-warning alert-dismissible' role='alert'>" 
                            + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
                            + data.error 
                            + "</div>";
                        $("#roomAlert").css('display','block');
                        $("#roomAlert").html(alert);
                    }

                });

                event.preventDefault();
            });
        });
        </script>

    </body>
</html