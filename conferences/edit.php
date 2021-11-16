<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/tag.class.php';
require_once ROOT.'/classes/reservation.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>

    <body>
        <?php 
            echo get_navbar(); 

            verify_conference();
            check_login('Pre upravovanie konferencie musíte byť prihlásený.');
            verify_conference_owner();

            $conference = Conferences::get_conference_by_id($_GET['id']);
        ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12 align-self-center pb-1">
                    <h2>
                        Upraviť konferenciu
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12" id="updateFormAlert"></div>
            </div>
            <form id="updateConference">
                <div class="form-group">
                    <!-- Need to know the conference id -->
                    <input class="form-control" type="number" hidden="true" id="id" name="id" value="<?php echo $conference['id'];?>">
                    
                    <label for="name">Názov konferencie</label>
                    <input type="text" class="form-control" id="name" placeholder="Názov" value="<?php echo $conference['name'];?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Popis konferencie</label>
                    <textarea class="form-control" id="description" placeholder="Detailný popis..."><?php echo $conference['description'];?></textarea>
                </div>

                <h4>Adresa</h4>
                <div class="form-group row">
                    <div class="col-sm-6">
                        <label for="street">Ulica</label>
                        <input type="text" class="form-control" id="street" value="<?php echo $conference['street'];?>" autocomplete="street-address" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6">
                        <label for="city">Mesto</label>
                        <input type="text" class="form-control" id="city" value="<?php echo $conference['city'];?>" autocomplete="address-level2" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="state">Štát</label>
                        <input type="text" class="form-control" id="state" value="<?php echo $conference['state'];?>" autocomplete="country-name" required>
                    </div>
                    <div class="col-sm-3">
                        <label for="zip">PSČ</label>
                        <input type="number" class="form-control" id="zip" value="<?php echo $conference['zip'];?>" autocomplete="postal-code" required>
                    </div>
                </div>

                <h4>Čas</h4>
                <label for="from">Začiatok konania</label>
                <?php
                    // Convert dates from timestamps to date strings
                    $from = date(DATE_FORMAT_HTML, $conference['time_from']);
                    $to = date(DATE_FORMAT_HTML, $conference['time_to']);
                    
                    // Split full strings to time and date
                    $from_time = substr($from, strpos($from, ' ') + 1, strlen($from));
                    $from_date = substr($from, 0, strpos($from, ' '));
                    $to_time = substr($to, strpos($to, ' ') + 1, strlen($to));
                    $to_date = substr($to, 0, strpos($to, ' '));
                ?>
                <div class="form-group row" id="from">
                    <div class="col-sm-3">
                        <input type="time" class="form-control timepicker" id="fromTime" value="<?php echo $from_time;?>">
                    </div>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" id="fromDate" value="<?php echo $from_date;?>">
                    </div>
                </div>
                <label for="to">Koniec konania</label>    
                <div class="form-group row" id="to">
                    <div class="col-sm-3">
                        <input type="time" class="form-control" id="toTime" value="<?php echo $to_time;?>">
                    </div>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" id="toDate" value="<?php echo $to_date;?>">
                    </div>
                </div>

                <h4>Cena a kapacita</h4>
                <div class="form-group row">    
                    <div class="form-group col-sm-3">
                        <label for="price">Cena lístku v &euro;</label>
                        <input type=number class="form-control" min=0 step="0.01" pattern="\d+\.\d\d" id="price" value="<?php echo $conference['price'];?>">
                    </div>
                    
                    <?php
                    $tickets_reserved = Reservation::num_reservation_for_conference($conference['id']);
                    ?>

                    <div class="form-group col-sm-3">
                        <label for="capacity">Počet voľných miest</label>
                        <input type=number class="form-control" min="<?php echo $tickets_reserved;?>" pattern="\d+" id="capacity" step="1" value="<?php echo $conference['capacity'];?>">
                    </div>
                </div>

                <h4>Doplňujúce informácie</h4>
                <div class="form-group">
                    <label for="description">Obrázok konferencie</label>
                    <br>
                    <input type="file" id="poster"/>
                    <img id="img_loader" src="<?php echo $conference['image_url'];?>" style="height: 50px" />
                    <input type="hidden" name="image" id="img_url" value="<?php echo $conference['image_url'];?>">
                </div>
                <label for="tags">Kategórie</label>
                <div class="form-group row" name="tags">
                    <div class="col-sm-4">
                        <select class="form-control" multiple id="tags">
                        <?php
                            $tags_selected = Tag::get_conference_tags($conference['id']);
                            $tags = Tag::get_tags_all();

                            foreach ($tags as $tag) {
                                $select = in_array($tag, $tags_selected) ? "selected" : "";
                                echo "<option {$select} value={$tag['id']}>{$tag['name']}</option>";
                            }
                        ?>
                        </select>
                    </div>
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
                        url: "/ajax/update_conference.php",
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
