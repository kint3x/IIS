<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/tag.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>

    <script>
        $(document).ready(function() {
            $("#createConference").submit(function (event) {
                var formData = {
                    name: $("#name").val(),
                    description: $("#description").val(),
                    tags: $("#tags").val(),
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
                    } else {
                        // TODO redirect to conference detail
                        alert("TODO redirect to new conference detail.");
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
                <div class="col-sm-12 align-self-center pb-1">
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
                <label for="tags">Kategórie</label>
                <div class="form-group row" name="tags">
                    <div class="col-sm-3">
                        <select class="form-control" multiple id="tags">
                        <?php
                            $tags = Tag::get_tags_all();

                            foreach ($tags as $tag) {
                                echo "<option value={$tag['id']}>{$tag['name']}</option>";
                            }
                        ?>
                        </select>
                    </div>
                </div>
                <!-- <div class="pb-1">
                    <button data-toggle="modal" type="button" data-target="#newTagModal" class="btn btn-outline-dark" id="tagBtn">Pridať kategóriu</button>
                </div> -->
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

        <!-- <div class="modal fade" tabindex="-1" role="dialog" id="newTagModal">
            <div class="modal-dialog" id="loginDialog">
		        <div class="modal-content">
                    <form id="loginForm">
                    <div class="modal-header">
                        <h3>Novú kategóriu</h3>
                        <button type="button" class="close font-weight-light" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div id="loginAlert"></div>
		                <div class="form-group">
		                    <label for="emailLogin">Email</label>
		                    <input type="email" class="form-control" id="emailLogin" aria-describedby="emailHelp" placeholder="priklad@email.com" required>
		                </div>
		                <div class="form-group">
		                    <label for="hesloLogin">Heslo</label>
		                    <input type="password" class="form-control" id="passwordLogin" placeholder="Heslo" required>
		                </div>
		            </div>
                    <div class="modal-footer">
                        <div class="flex-fill">  
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" data-toggle="modal" data-target="#registerModal" data-dismiss="modal">Nemáte účet? Registrujte sa!</a>
		                        <button type="submit" class="btn btn-primary">Prihlásiť sa</button>
                        </div>
                    </div>
              </div>         
        </div> -->
    </body>
</html