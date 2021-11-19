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
        
    </script>

    <body>
        <?php 
        
        echo get_navbar();

        verify_conference();

        $conference = Conferences::get_conference_by_id($_GET['id']);
        $tags = Tag::get_conference_tags($conference['id']);
        ?>

        <div class="container-fluid">
            <div class="row">
                
                <?php get_conference_sidebar($conference); ?>
                
                <div class="col-lg-8 align-self-center">
                    <div class="card mb-12">
                        <img class="card-img-top img-top-fixed-height" src="<?php echo $conference['image_url']; ?>" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $conference['name'];?></h5>
                            <p class="card-text"><?php echo $conference['description'];?></p>
                            
                            <?php
                            foreach ($tags as $tag) {
                                echo '<div onclick="searchByTag('.$tag['id'].')" style="cursor:pointer" class="badge badge-dark">'.$tag['name'].'</div>';
                            }
                            ?>
                            
                        </div>                            

                        <ul class="list-group list-group-flush d-flex flex-row flex-wrap">
                            <?php 
                                    if (
                                        $conference['street'] != ""
                                        && $conference['city'] != ""
                                        ) {
                                            ?>
                                            <li class="list-group-item col-sm-12 pl-list-item">
                                                <h6>Miesto konania</h6>
                                                <span><?php echo $conference['street']; ?></span>
                                                <br>
                                                <span><?php echo $conference['zip'].' '.$conference['city']; ?></span>
                                                <br>
                                                <span><?php echo $conference['state']; ?></span>
                                            </li>
                                            <?php
                                    }
                                ?>
                                
                            <li class="list-group-item col-sm-6 pl-list-item">
                                <h6>Od</h6><?php echo date(DATE_FORMAT_CARD, $conference['time_from']);?>
                            </li>
                            <li class="list-group-item col-sm-6">
                                <h6>Do</h6><?php echo date(DATE_FORMAT_CARD, $conference['time_to']);?>
                            </li>
                            <li class="list-group-item col-sm-6 pl-list-item">
                                <h6>Cena</h6><?php echo $conference['price'];?> &euro;
                            </li>
                            <li class="list-group-item col-sm-6">
                                <h6>Voľné miesta</h6><?php echo Conferences::get_number_tickets_left($conference['id']);?>
                            </li>
                        </ul>
                        <div class="card-footer">
                            <a style="cursor:pointer;color:white;"  class="btn btn-margin btn-primary" onclick="add_to_cart(<?php echo $conference['id'];?>, this)">Pridať do košíka</a>
                            
                            <!-- Logged in owner should be able to edit the conference -->
                            <?php if(user_owns_conference($conference['id_user'])) {
                                ?>
                                    <a href="/conferences/edit.php?id=<?php echo $conference['id'];?>" class="btn btn-outline-dark">Upraviť</a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <!-- Otázky -->
                    <div class="otazky" style="margin-top:10px">
                        
                    </div>

                </div>
            </div>
        </div>

        <script>
        function searchByTag(tag_id) {
            var url = window.location.href;

            url =  "/?tag=" + encodeURIComponent(tag_id);
            window.location.href = url;
        }
        </script>

        <script>
        $(document).ready(function(){
            var formData={
                    "conference_id" : "<?php echo $_GET['id']; ?>",
                    "html" : true 
                };
                $(".otazky").html("<img src='/img/loading-buffering.gif'/>");
                $.ajax({
                    type: "POST",
                    url: "/ajax/questions_get.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                  }).done(function (data) {
                    if(data.success){
                        $(".otazky").html(data.error);
                    }
                    else{
                        $(".otazky").html("Nepodarilo sa načítať otázky: "+data.error);
                    }

                  });
        });

        $(document).on("click", '#btn-send-question', function(event) { 
            var msg = $("#txt-send").val();
                var formData={
                    "conference_id" : "<?php echo $_GET['id']; ?>",
                    "msg" : msg 
                };

                $.ajax({
                    type: "POST",
                    url: "/ajax/questions_get.php",
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
                        $("#txt-send").val("");
                        $("#error-msg").html(data.error);
                    }

                  });
        });

        function delete_question(id){
             var formData={
                    "conference_id" : "<?php echo $_GET['id']; ?>",
                    "delete" : id 
                };

                $.ajax({
                    type: "POST",
                    url: "/ajax/questions_get.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                  }).done(function (data) {
                    if(data.success){
                        $("#question-id"+id).hide();
                    }
                    else{
                        alert(data.error);
                    }

                  });
        }
        </script>

    </body>
</html