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
          if (isset($_GET["confName"])) {
            $data = Conferences::search_owner_by_name($_SESSION['user']->get_user_data()['id'], $_GET['confName']);
          } else {
            $data = Conferences::get_conferences_by_owner($_SESSION['user']->get_user_data()['id']);
          }

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
                <div class='d-flex flex-row pb-2 justify-content-left'>
                  <div class='pr-2'>
                    <a href='#' class='btn btn-primary'>Nová konferencia</a>
                  </div>
                  <div>
                    <form role='search'>
                    <div class='input-group'>
                        <input type='text' class='form-control' placeholder='Názov' name='confName'>
                        <div class='input-group-btn d-inline-flex align-items-center'>
                            <button class='btn btn-default' type='submit'><i class='fa fa-search'></i></button>
                        </div>
                    </div>
                    </form>
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
                <a style="cursor:pointer;color:white;"  class="btn btn-primary" onclick="add_to_cart('.$row['id'].',this)" >Pridať do košíka</a>
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