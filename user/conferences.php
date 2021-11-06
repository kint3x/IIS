<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';

start_session_if_none();

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
                  </div>
                  <div class='row justify-content-between'>
            ";

            // TODO vypis do funkcie
            // TODO Kontrola chyb!

            foreach ($data as $row) {
              echo '
              <div class="card mb-4" style="width: 48%;">
                <img class="card-img-top my-img-top" src="'.$row['image_url'].'" alt="">
                <div class="card-body">
                  <h5 class="card-title">'.$row['name'].'</h5>
                  <p class="card-text">'.$row['description'].'</p>
                </div>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item">
                  <b>Od: </b>'.date(DATE_FORMAT, $row['time_from']).'
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <b>Do: </b>'.date(DATE_FORMAT, $row['time_from']).'.</li>
                  <li class="list-group-item"><b>Kde: </b>'.$row['place'].'</li>
                  <li class="list-group-item"><b>Cena: </b>'.$row['price'].' &euro;</li>
                  <li class="list-group-item"><b>Voľné miesta: </b>'.$row['capacity'].'</li>
                </ul>
                <div class="card-body">
                  <a href="#" class="card-link">Card link</a>
                  <a href="#" class="card-link">Another link</a>
                </div>
              </div>
              ';
            };

            echo "    </div>
                    </div>
                  </div>
                </div>
            ";

        ?>
    </body>
</html>