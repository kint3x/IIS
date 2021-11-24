<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/room.class.php';
require_once ROOT.'/classes/table.class.php';

start_session_if_none();

?>

<html>
    <?php 
    verify_conference_and_generate_head();
    ?>

    <script>
        
    </script>

    <body>
        <?php 
        
        echo get_navbar();
        check_login('Pre správu konferencie musíte byť prihlásený.');
        verify_conference_owner();        

        $conference = Conferences::get_conference_by_id($_GET['id']);
        ?>

        <div class="container-fluid">
            <div class="row">
                
                <?php get_conference_sidebar($conference); ?>
                
                <div class="col-lg-8 align-self-center">
                    ?>
                    <h2 class="pb-1">
                        Miestnosti
                    </h2>

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
                                <a href="" data-toggle="modal" data-target="#addRoomModal">Pridať?</a>
                            </div>
                            <?php
                        } else {
                            $options = [
                                "table_id" => "rooms",
                                "ajax_url" => "/ajax/rooms.php",
                                "delete" => true,
                                "edit" => true,
                                "add" => true,
                                "custom_SQL" => "WHERE conference_id = {$conference['id']} ORDER BY name ASC"
                            ];
    
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
        
        <div id='addRoomModal' class='modal fade' role='dialog'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title'>Pridanie miestnosti</h4>
                        <button type='button' class='close font-weight-light' data-dismiss='modal'>&times;</button>
                    </div>
                    <div class='modal-body'>
                    <form id='roomForm'>
                        <div id='roomAlert'></div>
                        <div class='form-group'>
                            <input type='number' class='form-control' name='conferenceID' id='conferenceId' hidden="true" value="<?php echo $conference['id'];?>" required>
	                	</div>
                        <div class='form-group'>
                            <label for='roomName'>Názov</label>
                            <input type='text' class='form-control' name='roomName' id='roomName' placeholder='Názov' style='margin-bottom:5px;' required>
	                	</div>
                    </div>
                    <div class='modal-footer' >
                        <button type='submit' class='btn btn-primary'>Pridať miestnosť</button>
                    </div>
                    </form>
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