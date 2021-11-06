<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';

session_start();
?>

<html>
    <?php echo get_head(); ?>

    <body>
        <?php echo get_navbar(); ?>
        
        <?php 
            $data = Conferences::get_conferences_by_owner($_SESSION['user']->get_user_data()['id']);

            echo "
                <div class='container'>  
                  <div class='row'>
                    <div class='col-sm-12 align-self-center pb-1'>
                      <h1>Moje konferencie</h1>
                    </div>
                    <div class='col-sm-12 align-self-center'>
                    <div id='settingsAlert'></div>
                    </div>
                  </div>
                  <div class='row'>
                    <div class='col-sm-12 align-self-center'>
                      <div class='list-group'>";
            // echo "<a href='#' class='list-group-item list-group-item-action'>nazov</a>";
            
            // echo "</div>
            // </div>
            // <div class='col-sm-6 align-self-right'>
            // <div class='list-group'>";
            
            // echo "<a href='#' class='list-group-item list-group-item-action'>nazov</a>";

            foreach ($data as $row) {
              echo "
              <a href='#' class='list-group-item list-group-item-action'>   
                {$row['name']}
              </a>
              ";
            };

            echo "    </div>
                    </div>
                  </div>
                </div>
            ";

        ?>
    </body>
</html>