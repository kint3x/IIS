<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
require_once ROOT.'/classes/tag.class.php';
require_once ROOT.'/classes/table.class.php';
require_once ROOT.'/classes/lecture.class.php';

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
        $is_logged = isset($_SESSION['user']);
        $is_owner = user_owns_conference($conference['id_user']);

        ?>

        <div class="container-fluid">
            <div class="row">                
                
                <?php get_conference_sidebar($conference); ?>
                
                <div class="col-sm-8 align-self-center">
                    
                    <?php
                        $lectures = Lecture::get_conference_lectures($conference['id']);
                        
                        if ($lectures === false) {
                            display_alert(Lecture::$error_message);
                            exit();
                        }

                        if ($lectures === -1) {  
                            ?>
                            <div class='alert alert-secondary' role='alert'>
                                Zatiaľ pre danú konferenciu neboli schválené žiadne prednášky.
                                <a href="" data-toggle="modal" data-target="#registerLectureModal">Podať návrh?</a>
                            </div>
                            <?php
                        } else {
                            
                            $sql = $is_owner ? "WHERE conference_id = {$conference['id']}" : 
                                "WHERE conference_id = {$conference['id']} AND status = ".LECTURE_CONFIRMED;

                            $options = [
                                "table_id" => "lectures",
                                "ajax_url" => "/ajax/conference_lecture.php",
                                "edit" => $is_owner,
                                "add" => false,
                                "delete" => false, // same as $is_owner ? true : false;
                                "custom_SQL" => $sql
                            ];
    
                            ?>
                            <div class="d-flex flex-row justify-content-between">
                                <h2 class="mb-1">
                                    Prednášky
                                </h2>
                                <?php                            
                                    if ($is_logged) {
                                        ?>
                                        <button data-toggle="modal" data-target="#registerLectureModal" class="btn btn-outline-dark">Registrovať príspevok</button> 
                                        <?php
                                    }
                                ?> 
                            </div>
                            <?php

                            $table = new SimpleTable("Lecture", $options);

                            $table->table_structure['name']['name'] = "Názov";
                            $table->table_structure['name']['form_edit_show'] = true;
                            $table->table_structure['name']['form_edit_prefill'] = true;
                            $table->table_structure['name']['editable'] = false;

                            $table->table_structure['description']['name'] = "Popis";
                            $table->table_structure['description']['show_column'] = false;
                            $table->table_structure['description']['form_edit_show'] = true;
                            $table->table_structure['description']['form_edit_prefill'] = true;
                            $table->table_structure['description']['editable'] = false;
                            
                            $table->table_structure['time_from']['name'] = "Od";
                            $table->table_structure['time_from']['form_edit_show'] = $is_owner;
                            $table->table_structure['time_from']['form_edit_prefill'] = true;
                            
                            $table->table_structure['time_to']['name'] = "Do";
                            $table->table_structure['time_to']['form_edit_show'] = $is_owner;
                            $table->table_structure['time_to']['form_edit_prefill'] = true;
                            
                            $table->table_structure['status']['name'] = "Stav";
                            $table->table_structure['status']['show_column'] = $is_owner;
                            $table->table_structure['status']['override'] = [
                                LECTURE_UNDEF => "navrhnutá",
                                LECTURE_CONFIRMED => "schválená",
                                LECTURE_DENIED => "zamietnutá"
                            ];

                            $table->table_structure['img_url']['show_column'] = false;
                            $table->table_structure['img_url']['form_edit_show'] = false;
                            
                            $table->table_structure['id']['show_column'] = false;
                            $table->table_structure['id']['form_edit_show'] = false;
                            
                            $table->table_structure['id_user']['name'] = "Email";
                            $table->table_structure['id_user']['form_edit_show'] = true;
                            $table->table_structure['id_user']['form_edit_prefill'] = true;
                            $table->table_structure['id_user']['editable'] = false;
                            $table->table_structure['id_user']['foreign_key'] = [
                                "table" => "User",
                                "fk_key_name" => "id",
                                "table_vars" => ["email" => "Email"],
                                "form_var" => "email",
                                "custom_where" => ""
                            ];


                            $table->table_structure['room_id']['name'] = "Miestnosť";
                            $table->table_structure['room_id']['show_column'] = true;
                            $table->table_structure['room_id']['form_edit_prefill'] = true;
                            $table->table_structure['room_id']['form_edit_show'] = $is_owner;
                            $table->table_structure['room_id']['foreign_key'] = [
                                "table" => "Room",
                                "fk_key_name" => "id",
                                "table_vars" => ["name" => "Miestnosť"],
                                "form_var" => "name",
                                "custom_where" => "WHERE conference_id = {$conference['id']}"
                            ];

                            $table->table_structure['conference_id']['show_column'] = false;
                            $table->table_structure['conference_id']['form_edit_show'] = false;
                            $table->table_structure['conference_id']['static_value'] = $conference['id'];

                            echo $table->generate_table_html();
                        }

                        ?>                    
                </div>
            </div>
        </div>

        <div id='registerLectureModal' class='modal fade' role='dialog'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title'>Registrácia príspevku</h4>
                        <button type='button' class='close font-weight-light' data-dismiss='modal'>&times;</button>
                    </div>
                    <div class='modal-body'>
                    <form id='lectureForm'>
                        <div id='lectureAlert'></div>
                        <div class='form-group'>
                            <input type='number' class='form-control' name='conferenceID' id='conferenceId' hidden="true" value="<?php echo $conference['id'];?>" required>
	                	</div>
                        <div class='form-group'>
                            <label for='lectureName'>Názov prednášky</label>
                            <input type='text' class='form-control' name='lectureName' id='lectureName' placeholder='Názov' style='margin-bottom:5px;' required>
	                	</div>
                        <div class='form-group'>
                            <label for='lectureDescription'>Popis prednášky</label>
                            <textarea type='text' class='form-control' name='lectureDescription' id='lectureDescription' placeholder='Stručne popíšte Vašu prednášku...' style='margin-bottom:5px;' required></textarea>
	                	</div>
                    </div>
                    <div class='modal-footer' >
                        <button type='submit' class='btn btn-primary'>Odoslať návrh</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div id='registrationSuccessModal' class='modal fade' role='dialog'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title'>Úspech!</h4>
                        <button type='button' class='close font-weight-light' data-dismiss='modal'>&times;</button>
                    </div>
                    <div class='modal-body'>
                        <p>
                            Váš príspevok bol úspešne zaregistrovaný. Teraz musíte počkať na schválenie správcom konferencie. Status svojho príspevku môžte sledovať v záložke 'Moje prednášky'.
                            Po schválení uvidíte v detailoch prednášky čas a miesto konania, ktoré Vám bolo pridelené. Taktiež budete môcť upraviť popis prednášky, či pridať fotku.
                        </p>
                    </div>
                    <div class='modal-footer' >
                        <button type='submit' class='btn btn-outline-dark' data-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>

        <?php
        echo $table->generate_table_scripts();
        ?>

        <script>
        function searchByTag(tag_id) {
            var url = window.location.href;

            url =  "/?tag=" + encodeURIComponent(tag_id);
            window.location.href = url;
        }

        $(document).ready(function () {
            // Remove allerts when closing the window
            $("#registerLectureModal").on('hide.bs.modal', function(event) {
                $("#lectureAlert").html("");
            });

            // Registering a lecture
            $("#lectureForm").submit(function (event) {
                var formData = {
                    name: $("#lectureName").val(),
                    description: $("#lectureDescription").val(),
                    conference_id: $("#conferenceId").val()
                }

                $.ajax({
                    type: "POST",
                    url: "/ajax/register_lecture.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                }).done(function (data) {
                    if(data.success){
                        $('#registerLectureModal').modal('hide');
                        $('#registrationSuccessModal').modal('show');
                        $('#lectureName').val("");
                        $('#lectureDescription').val("");
                    }
                    else{             
                        var alert = "<div class='alert alert-warning alert-dismissible' role='alert'>" 
                            + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
                            + data.error 
                            + "</div>";
                        $("#lectureAlert").css('display','block');
                        $("#lectureAlert").html(alert);
                    }

                });

                event.preventDefault();
            });
        });
        </script>

    </body>
</html