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
              <select class="form-control mr-2" name="tag">
                <option selected>Výber kategórie</option>
                <?php
                  $tags = Tag::get_tags_all();
                  foreach ($tags as $tag) {
                      echo "<option value={$tag['id']}>{$tag['name']}</option>";
                  }
                ?>
              </select>
              <input type='text' class='form-control' placeholder='Názov' name='name'>
              <div class='input-group-btn d-inline-flex align-items-center'>
                  <button class='btn btn-default' type='submit'><i class='fa fa-search'></i></button>
              </div>
          </div>
          </form>
        </div>
      </div>
    
      <?php  
        // Display conferences
        if (isset($_GET["name"]) || isset($_GET["tag"])) {
          $name = isset($_GET["name"]) ? $_GET["name"] : false;
          $tag = isset($_GET["tag"]) ? $_GET["tag"] : false;
          $data = Conferences::search_by_owner_name_tag($_SESSION['user']->get_user_data()['id'], $name, $tag);
          
          // Create an alert message displayed if no results were found
          if ($tag === false) {
            $alert_message = "Pre zadaný výraz '{$_GET["name"]}' sme nenašli žiadnu konferenciu.";
          } else if ($name === false) {
            $tag_name = Tag::get_name($tag);
            $alert_message = "V kategórii '{$tag_name}' sme nenašli žiadnu konferenciu.";
          } else {
            $alert_message = "Pre zadanú kombináciu parametrov sme nenašli žiadne konferencie.";
          }
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