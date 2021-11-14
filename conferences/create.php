<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/tag.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>

    <body>
        <?php 
        echo get_navbar();
        check_login("Pre pridanie konferencie je potrbené sa prihlásiť.");
        ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12 align-self-center pb-1">
                    <h2>
                        Pridať konferenciu
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12" id="createFormAlert"></div>
            </div>
            <form id="createConference">
                <div class="form-group">
                    <label for="name">Názov konferencie</label>
                    <input type="text" class="form-control" id="name" placeholder="Názov" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="description">Popis konferencie</label>
                    <textarea class="form-control" id="description" placeholder="Detailný popis..." autocomplete="off"></textarea>
                </div>
                <h4>Adresa</h4>
                <div class="form-group row">
                    <div class="col-sm-6">
                        <label for="street">Ulica</label>
                        <input type="text" class="form-control" id="street" placeholder="Zadajte adresu..." autocomplete="street-address" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6">
                        <label for="city">Mesto</label>
                        <input type="text" class="form-control" id="city" placeholder="" autocomplete="address-level2" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="state">Štát</label>
                        <input type="text" class="form-control" id="state" placeholder="" autocomplete="country-name" required>
                    </div>
                    <div class="col-sm-3">
                        <label for="zip">PSČ</label>
                        <input type="number" class="form-control" id="zip" placeholder="" autocomplete="postal-code" required>
                    </div>
                </div>
                <h4>Čas</h4>
                <label for="from">Začiatok konania</label>    
                <div class="form-group row" id="from">
                    <div class="col-sm-3">
                        <input type="time" class="form-control timepicker" id="fromTime" autocomplete="off">
                    </div>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" id="fromDate" autocomplete="off">
                    </div>
                </div>
                <label for="to">Koniec konania</label>    
                <div class="form-group row" id="to">
                    <div class="col-sm-3">
                        <input type="time" class="form-control" id="toTime" autocomplete="off">
                    </div>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" id="toDate" autocomplete="off">
                    </div>
                </div>
                <h4>Cena a kapacita</h4>
                <div class="form-group row">
                    <div class="form-group col-sm-3">
                        <label for="price">Cena lístku v &euro;</label>
                        <input type=number class="form-control" min=0 step="0.01" pattern="\d+\.\d\d" id="price" value="0.00" autocomplete="off">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="capacity">Počet voľných miest</label>
                        <input type=number class="form-control" min=0 pattern="\d+" id="capacity" value="0" step="1" autocomplete="off">
                    </div>
                </div>
                <h4>Doplňujúce informácie</h4>
                <div class="form-group">
                    <label for="description">Obrázok konferencie</label>
                    <br>
                    <input type="file" id="poster"/>
                    <img id="img_loader" src="/img/loading-buffering.gif" style="height: 50px; display:none;" />
                    <input type="hidden" name="image" id="img_url"/>
                </div>
                <label for="tags">Kategórie</label>
                <div class="form-group row" name="tags">
                    <div class="col-sm-4">
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
                <button type="submit" class="btn btn-primary">Pridať</button>
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

                $("#createConference").submit(function (event) {
                    var formData = {
                        name: $("#name").val(),
                        description: $("#description").val(),
                        street: $("#street").val(),
                        city: $("#city").val(),
                        zip:  $("#zip").val(),
                        state: $("#state").val(),
                        tags: $("#tags").val(),
                        fromTime: $("#fromTime").val(),
                        fromDate: $("#fromDate").val(),
                        toTime: $("#toTime").val(),
                        toDate: $("#toDate").val(),
                        price: $("#price").val(),
                        capacity: $("#capacity").val(),
                        image_url: $("#img_url").val()
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
                            window.location.href = "/conferences/show.php?id=" + encodeURIComponent(data.conference_id);
                        }
                    });

                    event.preventDefault();
                });
            })
        </script>
    </body>
</html
