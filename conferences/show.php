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

        // No id parameter passed or the conference with the given id doesn't exists
        if (!isset($_GET['id']) || !Conferences::get_conference_by_id($_GET['id'])) {
            ?>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 align-self-center pb-2">
                        <div class='alert alert-secondary' role='alert'>
                            Je nám to ľúto, ale daná konferencia neexistuje.
                        </div>
                    </div>
                </div>
            </div>';
            
            <?php
            exit();
        }

        $conference = Conferences::get_conference_by_id($_GET['id']);
        $tags = Tag::get_conference_tags($conference['id']);
        ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12 align-self-center">
                    <div class="card mb-12">
                        <img class="card-img-top img-top-fixed-height" src="<?php echo $conference['image_url']; ?>" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $conference['name'];?></h5>
                            <p class="card-text"><?php echo $conference['description'];?></p>
                            
                            <?php
                            foreach ($tags as $tag) {
                                echo '<a href="#" class="badge badge-dark">'.$tag['name'].'</a>';
                            }
                            ?>
                        </div>

                        <ul class="list-group list-group-flush d-flex flex-row flex-wrap">
                            <li class="list-group-item col-sm-6 pl-list-item">
                                <b>Od: </b><?php echo date(DATE_FORMAT, $conference['time_from']);?>
                            </li>
                            <li class="list-group-item col-sm-6">
                                <b>Do: </b><?php echo date(DATE_FORMAT, $conference['time_to']);?>
                            </li>
                            <li class="list-group-item col-sm-6 pl-list-item">
                                <b>Cena: </b><?php echo date(DATE_FORMAT, $conference['price']);?> &euro;
                            </li>
                            <li class="list-group-item col-sm-6">
                                <b>Voľné miesta: </b><?php echo Conferences::get_number_tickets_left($conference['id']);?>
                            </li>
                        </ul>
                        <div class="card-footer">
                            <a style="cursor:pointer;color:white;"  class="btn btn-margin btn-primary" onclick="add_to_cart('.$db_entry['id'].',this)" >Pridať do košíka</a>
                            
                            <!-- Logged in owner should be able to edit the conference -->
                            <?php if(isset($_SESSION['user']) && $_SESSION['user']->get_user_data()['id'] == $conference['id_user']) {
                                ?>
                                <a href="#" class="btn btn-outline-dark">Upraviť</a>
                                
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html