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
                <?php get_conference_sidebar("lectures", $conference['id'], $conference['id_user']); ?>
                <div class="col-sm-8 align-self-center">
                    
                    <button data-toggle="modal" data-target="#registerLectureModal" class="btn btn-outline-dark">Registrovať príspevok</button>
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
                    description: $("#lectureDescription").val()
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