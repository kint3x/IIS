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
                <form id="weekForm">
                    <div class="form-group row d-flex justify-content-center">
                        
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
                </form>
                
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

    var date = new Date()

    // Set the input to the current week
    $("#weekPicker").val(date.formatWeek());

    $("#weekForm").submit(function (e) {
        var val = $("#weekPicker").val();

        e.preventDefault();
    })

    $("#prev").on('click', function (e) {
        date.setDate(date.getDate() - 7);
        $("#weekPicker").val(date.formatWeek());
    })

    $("#next").on('click', function (e) {
        date.setDate(date.getDate() + 7);
        $("#weekPicker").val(date.formatWeek());
    })

    </script>
</html>