<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>

    <script>
        $(document).ready(function() {
            $("#createConference").submit(function(event) {
                var formData = {
                    name: $("#name").val(),
                    description: $("#description").val(),
                    fromTime: $("#fromTime").val(),
                    fromDate: $("#fromDate").val(),
                    toTime: $("#toTime").val(),
                    toDate: $("#toDate").val(),
                    price: $("#price").val(),
                    capacity: $("#capacity").val()
                };

                $.ajax({
                    type: "POST",
                    url: "/ajax/add_conference.php",
                    data: formData,
                    dataType: "json",
                    encode: true
                }).done(function (data) {
                    if (!data.success) {
                        var alert = "<div class='alert alert-warning'>"
                        + data.error
                        + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
                        + "</div>";
                        $("#createFormAlert").css('display','block');
                        $("#createFormAlert").html(alert);
                    }
                });

                event.preventDefault();
            });
        })
    </script>

    <body>
        <?php echo get_navbar(); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12 align-self-center pb-2">
                    <h1>
                        Pridať konferenciu
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12" id="createFormAlert"></div>
            </div>
            <form id="createConference">
                <div class="form-group">
                  <label for="name">Názov konferencie</label>
                  <input type="text" class="form-control" id="name" placeholder="Názov" required>
                </div>
                <div class="form-group">
                  <label for="description">Popis konferencie</label>
                  <textarea class="form-control" id="description" placeholder="Detailný popis..."></textarea>
                </div>
                <label for="from">Začiatok konania</label>    
                <div class="form-group row" id="from">
                    <div class="col-sm-2">
                        <input type="time" class="form-control timepicker" id="fromTime">
                    </div>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" id="fromDate">
                    </div>
                </div>
                <label for="to">Koniec konania</label>    
                <div class="form-group row" id="to">
                    <div class="col-sm-2">
                        <input type="time" class="form-control" id="toTime">
                    </div>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" id="toDate">
                    </div>
                </div>
                <div class="form-group">
                    <label for="price">Cena lístku v &euro;</label>
                    <input type=number class="form-control" min=0 step="0.01" pattern="\d+\.\d\d" id="price" value="0.00">
                </div>
                <div class="form-group">
                    <label for="capacity">Počet voľných miest</label>
                    <input type=number class="form-control" min=0 pattern="\d+" id="capacity" value="0" step="1">
                </div>
                <button type="submit" class="btn btn-primary">Pridať</button>
            </form>
        </div>
    </body>
</html