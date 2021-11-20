<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/lecture.class.php';
require_once ROOT.'/classes/room.class.php';


start_session_if_none();

?>

<html>
    <?php verify_lecture_and_generate_header(); ?>

    <body>
        <?php 
            echo get_navbar();

            check_login("Pre upravovanie prednášok musíte byť prihlásený.");
            
            $lecture = Lecture::get_lecture_by_id($_GET['id']);
        
            if ($lecture === false) {
                display_alert_container(Lecture::$error_message);
                exit();
            }

            $conference = Conferences::get_conference_by_id($lecture['conference_id']);
            
            if ($conference === false) {
                display_alert_container(Conferences::$error_message);
                exit();
            }

            $can_edit = is_admin() || user_owns_conference($conference['id_user']) || user_owns_lecture($lecture['id']);
            $only_lecture_owner = !user_owns_conference($conference['id_user']) && !is_admin() && user_owns_lecture($lecture['id']);

            // Only an admin or the conference owner can edit the staus, time and place of the lecture
            $disabled = $only_lecture_owner ? "disabled" : "";

            if (!$can_edit) {
                display_alert_container("Pre upravovanie prednášok musíte byť prihlásený.");
                exit();
            }
        ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12 align-self-center pb-1">
                    <h2>
                        Upraviť prednášku
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12" id="updateFormAlert"></div>
            </div>
            <form id="updateConference">
                <div class="form-group">
                    <!-- Need to know the lecture id -->
                    <input class="form-control" type="number" hidden="true" id="id" name="id" value="<?php echo $lecture['id'];?>">
                    <input class="form-control" type="number" hidden="true" id="conference_id" name="conference_id" value="<?php echo $lecture['conference_id'];?>">
                    <input class="form-control" type="number" hidden="true" id="id_user" name="id_user" value="<?php echo $lecture['id_user'];?>">
                    
                    <label for="name">Názov konferencie</label>
                    <input type="text" class="form-control" id="name" placeholder="Názov" value="<?php echo $lecture['name'];?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Popis konferencie</label>
                    <textarea class="form-control" id="description" placeholder="Detailný popis..."><?php echo $lecture['description'];?></textarea>
                </div>
                
                <h4>Stav</h4>    
                <div class="form-group row" id="status">
                    <div class="col-lg-4">
                        <select class="form-control" id="status" <?php echo $disabled; ?>>
                            <option <?php if ($lecture['status'] == LECTURE_CONFIRMED) {echo "selected";}?> value="<?php echo LECTURE_CONFIRMED;?>">Schválená</option>
                            <option <?php if ($lecture['status'] == LECTURE_DENIED) {echo "selected";}?> value="<?php echo LECTURE_DENIED;?>">Zamietnutá</option>
                            <option <?php if ($lecture['status'] == LECTURE_UNDEF) {echo "selected";}?> value="<?php echo LECTURE_UNDEF;?>">Navrhnutá</option>
                        </select>
                    </div>
                </div>

                <h4>Čas a miesto</h4>
                <label for="from">Začiatok konania</label>
                <?php
                    // Convert dates from timestamps to date strings
                    $from = date(DATE_FORMAT_SIMPLE_TABLE, $lecture['time_from']);
                    $to = date(DATE_FORMAT_SIMPLE_TABLE, $lecture['time_to']);
                ?>
                <div class="form-group row" id="from">
                    <div class="col-lg-4">
                        <!-- <input type="datetime-local" class="form-control timepicker" id="fromTime" value="<?php echo $from;?>" <?php echo $disabled; ?>> -->
                        <input type="datetime-local" class="form-control timepicker" id="fromTime" <?php echo $disabled; ?>>
                    </div>
                </div>
                <label for="to">Koniec konania</label>    
                <div class="form-group row" id="to">
                    <div class="col-lg-4">
                        <!-- <input type="datetime-local" class="form-control" id="toTime" value="<?php echo $to;?>" <?php echo $disabled; ?>> -->
                        <input type="datetime-local" class="form-control" id="toTime" <?php echo $disabled; ?>>
                    </div>
                </div>

                <label for="roomSelect">Miestnosť</label>    
                <div class="form-group row" id="roomSelect">
                    <div class="col-lg-4">
                        <select class="form-control" id="room" <?php echo $disabled; ?>>
                            <?php
                            $rooms = Room::get_conference_rooms($conference['id']);
                            
                            if ($lecture['room_id'] == NULL) {
                                ?>
                                <option selected value="">Výber miestnosti...</option>
                                <?php
                            }

                            foreach ($rooms as $room) {
                                $select = $lecture['room_id'] == $room['id'] ? "selected" : "";
                                echo "<option {$select} value='{$room['id']}'>{$room['name']}</option>";
                            }
                        ?>
                        </select>
                    </div>
                </div>
                <h4>Obrázok</h4>
                <div class="form-group">
                    <input type="file" id="poster"/>
                    <img id="img_loader" src="<?php echo $lecture['img_url'];?>" style="height: 50px"/>
                    <input type="hidden" name="image" id="img_url" value="<?php echo $lecture['img_url'];?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Uložiť zmeny</button>
            </form>
        </div>

        <script>
            $(document).ready(function() {
                $("#poster").on("change",function(){

                    var formData = new FormData();

                    formData.append("file",document.getElementById("poster").files[0]);

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "/ajax/file_handler.php");
                    xhr.onreadystatechange = function() // anonymous function (a function without a name).
                    {
                         if ((xhr.readyState == 4) && (xhr.status == 200)) // process is completed and http status is OK
                        {
                            
                            var response = JSON.parse(xhr.responseText);
                            if(response.error){
                                alert("Nastala chyba pri nahrávaní súboru: "+ response.message);
                            }
                            else{
                                $("#img_loader").attr("src",response.message);    
                                $("#img_url").val(response.message);                      
                            }
                        }
                    }

                    xhr.send(formData);

                    $("#img_loader").attr("src","/img/loading-buffering.gif");
                    $("#img_loader").css("display","");
                });

                $("#updateConference").submit(function (event) {
                    var formData = {
                        id: $("#id").val(),
                        name: $("#name").val(),
                        description: $("#description").val(),
                        time_to: $("#toTime").val(),
                        time_from: $("#fromTime").val(),
                        img_url: $("#img_url").val(),
                        id_user: $("#id_user").val(),
                        conference_id: $("#conference_id").val(),
                        status: $("#status").val(),
                        room_id: $("#room").val(),
                    };

                    
                    $.ajax({
                        type: "POST",
                        url: "/ajax/lecture_edit.php",
                        data: formData,
                        dataType: "json",
                        encode: true
                    }).done(function (data) {
                        if (!data.success) {
                            var alert = "<div class='alert alert-warning'>"
                            + data.error
                            + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
                            + "</div>";
                            $("#updateFormAlert").css('display','block');
                            $("#updateFormAlert").html(alert);
                        } else {
                            var succ = "<div class='alert alert-success'>"
                            + "Zmeny boli úspešne uložené"
                            + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
                            + "</div>";
                            $("#updateFormAlert").css('display','block');
                            $("#updateFormAlert").html(succ);
                        }

                        $('body').scrollTop(0);
                    });

                    event.preventDefault();
                });
            })
        </script>
    </body>
</html
