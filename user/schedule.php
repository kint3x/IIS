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
                <h4 class='mb-2'>
                    Rozvrh
                </h4>
                
                <div class="form-group row d-flex justify-content-center mb-2">
                    <div class="col-sm-1 d-flex justify-content-end">
                        <button class="btn btn-outline-dark" id="prev"><</button>
                    </div>
                    
                    <div class="col-sm-5">
                        <input type="week" class="form-control" id="weekPicker" name="weekPicker" placeholder="yyyy-Www" value="">
                    </div>
                    
                    <div class="col-sm-1 d-flex justify-content-start">
                        <button class="btn btn-outline-dark" id="next">></button>
                    </div>
                </div>
                
                <div class="row" id="info"></div>

                <div class="row">
                    <?php 
                    $days = ["Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok", "Sobota", "Nedeľa"];

                    foreach ($days as $idx => $day) {
                        ?>
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <?php echo $day;?><br>
                                    <small id="<?php echo "date".$idx;?>"></small>
                                </div>
                                <ul class="list-group list-group-flush" id="<?php echo "schedule".$idx;?>">
                                
                                </ul>
                            </div>    
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        
        </div>
        </div>
    </body>

    <script>
    // Compute the week number
    Date.prototype.formatWeek = function() {    
        // Copy date
        d = new Date(Date.UTC(this.getFullYear(), this.getMonth(), this.getDate()));
        // Set to nearest Thursday: current date + 4 - current day number
        // Make Sunday's day number 7
        d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
        // Get first day of year
        var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
        // Calculate full weeks to nearest Thursday
        var weekNo = Math.ceil((((d - yearStart) / 86400000) + 1)/7);

        if (weekNo < 10) {
            weekNo = '0' + String(weekNo);
        } else {
            weekNo = String(weekNo);
        }

        return date.getFullYear() + '-W' + weekNo;
    };

    // Calculate the start, expects the date input in the yyyy-Www format
    function getDateOfWeek(date) {
        var date_parts = date.split('-W', 2);
        var y = date_parts[0];
        var w = date_parts[1]

        var simple = new Date(Date.UTC(y, 0, 1 + (w - 1) * 7));
        var dow = simple.getDay();
        var ISOweekStart = simple;
        if (dow <= 4)
            ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
        else
            ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
        
        return ISOweekStart;
    }

    var date = new Date()

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
                    var li = $(el).closest('li');
                    li.remove();
                } else {
                    var alert = "<div class='alert alert-warning alert-dismissible' role='alert'>" 
                        + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
                        + data.error 
                        + "</div>";
                    $("#info").css('display','block');
                    $("#info").html(alert);
                }
            });
    }

    // Update the calendar
    $("#weekPicker").change(function(e) {
        // Update the date variable
        date = getDateOfWeek($("#weekPicker").val());

        for (let i = 0; i < 7; i++) {
            var day = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate() + i));
            
            // Set proper date for each day
            $("#date"+i).html(day.getDate() + "." + (day.getMonth() + 1) + ".");

            // Compute unix timestamps
            const s_in_hour = 3600;
            var ts_start = day.getTime() / 1000 - s_in_hour;
            var ts_end = day.getTime() / 1000 + 23*s_in_hour;
            
            // Fetch the schedule for the given day
            var formData = {
                start: ts_start,
                end: ts_end
            };
        
            // Remove all the displayed lectures;
            $("#schedule"+i).html('');

            $.ajax({
                type: "POST",
                url: "/ajax/get_daily_schedule.php",
                data: formData,
                dataType: "json",
                encode: true
            }).done(function (data) {
                var schedule = $("#schedule"+i);

                // Append new lectures
                if (data.success) {
                    data.lectures.forEach((el) => {
                        // Convert unix timestamps back to dates
                        var time_start = new Date(el.time_from * 1000);
                        time_start = ('0' + time_start.getHours()).substr(-2) + ':' + ('0' + time_start.getMinutes()).substr(-2);
                        var time_end = new Date(el.time_to * 1000);
                        time_end = ('0' + time_end.getHours()).substr(-2) + ':' + ('0' + time_end.getMinutes()).substr(-2);

                        var li = "<li class='list-group-item'>"
                        + '<small>' + time_start + ' - ' + time_end + '</small>'
                        + "<button class='close font-weight-light' onclick='removeFromSchedule(this)' value='" + el.id + "' aria-label='close'>&times;</button>"
                        + '<br>'
                        + el.name
                        + "<br>"
                        + '<small>' + el.room + '</small>'
                        + '</li>';

                        schedule.html(schedule.html() + li);
                    });
                }
            });

            e.preventDefault();
        }
    })

    // Set the input to the current week
    $("#weekPicker").val(date.formatWeek()).trigger('change');

    // Previos week
    $("#prev").on('click', function (e) {
        date.setDate(date.getDate() - 7);
        $("#weekPicker").val(date.formatWeek()).trigger('change');
    })

    // Next week
    $("#next").on('click', function (e) {
        date.setDate(date.getDate() + 7);
        $("#weekPicker").val(date.formatWeek()).trigger('change');
    })


    </script>
</html>