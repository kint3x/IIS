<?php
session_start();

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
?>

<html>
    <?php echo get_head(); ?>

    <body>
        <?php echo get_navbar(); ?>
        
        <?php 
            // $conferences = new Conferences();
            // $data = $conferences->get_conferences_by_owner($_SESSION['user']['id']);
            
            echo "
                <div class='container'>  
                  <div class='row'>
                    <div class='col-sm-12 align-self-center'>
                      <h1>Moje konferencie</h1>
                    </div>
                    <div class='col-sm-12 align-self-center'>
                    <div id='settingsAlert'></div>
                    </div>
                  </div>
                  <div class='row'>
                    <div class='col-sm-12 align-self-center'>
                        <div class='list-group'>
                            <a href='#' class='list-group-item list-group-item-action'>First item</a>
                        </div>
                    </div>
                  </div>
                </div>
            ";

        ?>
    </body>
</html>