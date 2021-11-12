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
      
      <div class='container'>  
        <div class='row'>
          <div class='col-sm-12 align-self-center pb-1'>
            <h1>Moje konferencie</h1>
          </div>
      </div>
      
      <div class='d-flex flex-row pb-2 justify-content-left'>
        <div class='pr-2'>
          <a href='/conferences/create.php' class='btn btn-primary'>Nová konferencia</a>
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
    
      <?php  
        // Display conferences
        if (isset($_GET["confName"])) {
          $data = Conferences::search_owner_by_name($_SESSION['user']->get_user_data()['id'], $_GET['confName']);
          $alert_message = "Pre zadaný výraz '{$_GET["confName"]}' sme nenašli žiadnu konferenciu.";
        } else {
          $data = Conferences::get_conferences_by_owner($_SESSION['user']->get_user_data()['id']);
          $alert_message = 'Zatiaľ ste nevytvorili žiadnu konferenciu.';
        }
      
        if (count($data) === 0) {
          // No conferences found
          echo "
            <div class='alert alert-secondary' role='alert'>
              {$alert_message}
            </div>
          ";        
        } else {
          // Show cards for each of the conferences
          echo "<div class='row justify-content-between'>";

          foreach ($data as $row) {
            get_conference_card($row);
          };
      
          echo "</div>";
        }
        
      ?>
          </div>
        </div>
      </div>
    </body>
</html>