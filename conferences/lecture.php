<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/room.class.php';
require_once ROOT.'/classes/lecture.class.php';
require_once ROOT.'/classes/table.class.php';
require_once ROOT.'/classes/schedule.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>

    <script>
        
    </script>

    <body>
        <?php 
        
        echo get_navbar();

        verify_lecture();

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

        // Check if the lecture has a room assigned
        if ($lecture['room_id'] === NULL) {
            $room_name = "-";
        } else {
            $room = Room::get_room_by_id($lecture['room_id']);

            if ($room === false) {
                $room_name = '-';
            } else {
                $room_name = $room['name'];
            }

        }

        if ($lecture['time_from'] === NULL || $lecture['time_to'] === NULL) {
            $time = '-';
        } else {
            $time = date(DATE_FORMAT_CARD, $lecture['time_from']).' - '.date(DATE_FORMAT_CARD, $lecture['time_to']);
        }

        $can_edit = is_admin() || user_owns_lecture($lecture['id']) || user_owns_conference($conference['id_user']); # TODO OWNS CONFERENCE

        ?>

        <div class="container-fluid">
            <div class="row">
                
                <?php get_conference_sidebar($conference); ?>

                <div class="col-lg-8 align-self-top">
                    <div class="card mb-12">
                        <img class="card-img-top img-top-fixed-height" src="<?php echo $lecture['img_url']; ?>" alt="Card image cap">
                        <?php
                        if ($lecture['status'] != LECTURE_CONFIRMED) {
                            ?>
                            <div class="card-header">
                                Táto prednáška zatiaľ nebola potvrdená.
                            </div>
                            <?php
                        }
                        ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $lecture['name'];?></h5>
                            <p class="card-text"><?php echo $lecture['description'];?></p>
                        </div>                            

                        <ul class="list-group list-group-flush d-flex flex-row flex-wrap">
                            

                            <li class="list-group-item col-md-6 pl-list-item">
                                <h6>Čas</h6>
                                <?php echo $time; ?>
                            </li>
                            <li class="list-group-item col-md-6">
                                <h6>Miestnosť</h6><?php echo $room_name;?>
                            </li>
                        </ul>
                        <?php
                        if(isset($_SESSION['user'])) {
                            ?> <div class="card-footer"> <?php
                            
                            if($can_edit) {
                                ?>
                                <a href="/lecture/edit.php?id=<?php echo $lecture['id'];?>" class="btn btn-outline-dark mr-2">Upraviť</a>
                                <?php
                            }
                        
                            $user_id = $_SESSION['user']->get_user_data()['id'];
                            $is_scheduled = Schedule::is_scheduled($user_id, $lecture['id']);

                            if ($is_scheduled === 1) {
                                // Lecture was scheduled
                                ?>
                                <button class="btn btn-outline-danger" id="scheduleBtn" onclick="removeFromSchedule(this)" value="<?php echo $lecture['id'];?>">Odstrániť z rozvrhu</button>
                                <?php
                            } else if ($is_scheduled === -1) {
                                // Lecture wasn't scheduled
                                ?>
                                <button class="btn btn-outline-success" id="scheduleBtn" onclick="addToSchedule(this)" value="<?php echo $lecture['id'];?>">Pridať do rozvrhu</button>
                                <?php
                            }
                            ?> </div> <?php
                        }
                        ?>
                        </div>
                        
                        <div class="otazky mb-1" style="margin-top:10px"></div>
                        
                        <div id="error-msg"></div>
                        
                    </div>
                </div>
            </div>
        </div>
                        
    <script>
    // Add  lecture to the schedule
    function addToSchedule(el) {
        var formData = {
            lecture_id: el.value
        };

        $.ajax({
                type: "POST",
                url: "/ajax/add_to_schedule.php",
                data: formData,
                dataType: "json",
                encode: true
            }).done(function (data) {
                if (data.success) {
                    var btn = $("#scheduleBtn")
                    var succBtn = '<button class="btn btn-outline-danger" id="scheduleBtn" onclick="removeFromSchedule(this)"'
                        + 'value="' + el.value + '">Odstrániť z rozvrhu</button>';
                    btn.replaceWith(succBtn);
                }
            });
    }

    // Remove the lecture from schedule
    function removeFromSchedule(el) {
        var formData = {
            lecture_id: el.value
        };

        $.ajax({
                type: "POST",
                url: "/ajax/remove_from_schedule.php",
                data: formData,
                dataType: "json",
                encode: true
            }).done(function (data) {
                if (data.success) {
                    var btn = $("#scheduleBtn")
                    var succBtn = '<button class="btn btn-outline-success" id="scheduleBtn" onclick="addToSchedule(this)"'
                        + 'value="' + el.value + '">Pridať do rozvrhu</button>';
                    btn.replaceWith(succBtn);
                }
            });
    }

    $(document).ready(function(){
        var formData={
                "lecture_id" : "<?php echo $_GET['id']; ?>",
                "html" : true 
            };
            
        $(".otazky").html("<div class='d-flex p-2 justify-content-center'><img style='height: 2rem' src='/img/loading-buffering.gif'/></div>");
        $.ajax({
            type: "POST",
            url: "/ajax/questions.php",
            data: formData,
            dataType: "json",
            encode: true,
        }).done(function (data) {
            if(data.success){
                $(".otazky").html(data.error);
            }
            else{
                var alert = "<div class='alert alert-warning'>"
                        + data.error
                        + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
                        + "</div>";
                $("#txt-send").val("");
                $("#error-msg").css('display','block');
                $("#error-msg").html(alert);
                $(".otazky").html("");
            }
        });
    });

    $(document).on("click", '#btn-send-question', function(event) { 
        var msg = $("#txt-send").val();
        
        var formData={
            "lecture_id" : "<?php echo $_GET['id']; ?>",
            "msg" : msg 
        };
        
        $.ajax({
            type: "POST",
            url: "/ajax/questions.php",
            data: formData,
            dataType: "json",
            encode: true,
            }).done(function (data) {
                if(data.success){
                    $("#txt-send").val("");
                    $("#error-msg").html("");
                    location.reload();
                }
                else{
                    var alert = "<div class='alert alert-warning'>"
                            + data.error
                            + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
                            + "</div>";
                    $("#txt-send").val("");
                    $("#error-msg").css('display','block');
                    $("#error-msg").html(alert);
                }
        });
    });

    function delete_question(id){
         var formData={
                "lecture_id" : "<?php echo $_GET['id']; ?>",
                "delete" : id 
            };

        $.ajax({
            type: "POST",
            url: "/ajax/questions.php",
            data: formData,
            dataType: "json",
            encode: true,
        }).done(function (data) {
                if(data.success){
                    $("#question-id"+id).hide();
                }
                else{
                    var alert = "<div class='alert alert-warning'>"
                            + data.error
                            + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
                            + "</div>";
                    $("#txt-send").val("");
                    $("#error-msg").css('display','block');
                    $("#error-msg").html(alert);
                }
            });
    }
    </script>

    </body>
</html