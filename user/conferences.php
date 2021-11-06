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
          // TODO vypis forech do funkcie

          $data = Conferences::get_conferences_by_owner($_SESSION['user']->get_user_data()['id']);

          echo "
              <div class='container'>  
                <div class='row'>
                  <div class='col-sm-12 align-self-center pb-1'>
                    <h1>Moje konferencie</h1>
                  </div>
                </div>
                ";

          // Search bar
          echo "
                <div class='row pb-2'>
                  <div class='col-sm-3 align-self-center'>
                    <a href='#' class='btn btn-primary'>Nová konferencia</a>
                  </div>
                  <div class='col-sm-4 align-self-center'>
                    <div class='input-group'>
                      <input type='search' id='searchForm' class='form-control rounded' placeholder='Hľadať' aria-label='Search'
                        aria-describedby='search-addon' />
                      <button type='button' class='btn btn-primary'>
                        <i class='fas fa-search'></i>
                      </button>
                    </div>
                  </div>
                </div>
          ";

          // Show cards for each of the conferences 
          echo "<div class='row justify-content-between'>";

          foreach ($data as $row) {
            echo '
            <div class="card mb-4" style="width: 48%;">
              <img class="card-img-top my-img-top" src="'.$row['image_url'].'" alt="">
              <div class="card-body">
                <h5 class="card-title">'.$row['name'].'</h5>
                <p class="card-text text-truncate">'.$row['description'].'</p>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item">
                <b>Od: </b>'.date(DATE_FORMAT, $row['time_from']).'
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <b>Do: </b>'.date(DATE_FORMAT, $row['time_from']).'.</li>
                <li class="list-group-item"><b>Kde: </b>'.$row['place'].'</li>
                <li class="list-group-item"><b>Cena: </b>'.$row['price'].' &euro;</li>
                <li class="list-group-item"><b>Voľné miesta: </b>'.Conferences::get_number_tickets_left($row['id']).'</li>
              </ul>
              <div class="card-footer">
                <a href="#" class="btn btn-outline-dark">Upraviť</a>
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