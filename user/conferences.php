<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>
    
    <body>
      <?php 
      echo get_navbar();
      check_login("Pre zobrazenie tejto stránky musíte byť prihlásený.");
      ?>
      


      <div class='container-fluid'>  
        <div class='row'>

          <?php get_user_sidebar(); ?>

          <div class='col-sm-8 align-self=top'>
            <div class='row'>
              <div class='col-sm-12 align-self-center pb-1'>
                <h2>Moje konferencie</h2>
              </div>
            </div>
            
            <div class='d-flex flex-row pb-2 justify-content-left'>
              <div class='pr-2'>
                <a href='/conferences/create.php' class='btn btn-primary'>Pridať</a>
              </div>
            <div>
              <form role='search'>
                <div class='input-group'>
                  <select class="form-control mr-2" name="tag">
                    <?php
                  if (isset($_GET['tag'])) {
                    $selected_tag = $_GET['tag'];
                    echo '<option value="">Kategória</option>';
                  } else {
                    $selected_tag = -1;
                    echo '<option selected value="">Kategória</option>';
                  }
                  
                  $tags = Tag::get_tags_all();
                  foreach ($tags as $tag) {
                    $selected = $selected_tag == $tag['id'] ? "selected" : "";
                    echo "<option {$selected} value={$tag['id']}>{$tag['name']}</option>";
                  }
                  ?>
              </select>
              <input type='text' class='form-control' placeholder='Názov' name='name'
              value=<?php echo isset($_GET['name']) ? $_GET['name'] : ""; ?>>
              <div class='input-group-btn d-inline-flex align-items-center pr-2'>
                <button class='btn btn-default' type='submit'><i class='fa fa-search'></i></button>
              </div>
                <div class='form-check form-check-inline pr-2'>
                  <input type='checkbox' class='form-check-input' name='old' id='oldCheck' 
                  <?php if (isset($_GET['old'])) {echo "checked";}?>>
                  <label class='form-check-label' for='oldCheck'>Ukončené konferencie</label>
                </div>
                <div class='form-check form-check-inline'>
                  <input type='checkbox' class='form-check-input' name='soldOut' id='soldOutCheck' 
                  <?php if (isset($_GET['soldOut'])) {echo "checked";}?>>
                  <label class='form-check-label' for='soldOutCheck'>Vypredané konferencie</label>
                </div>
              </div>
            </form>
          </div>
        </div>
        
        <?php  
        // Display conferences
        $name = isset($_GET["name"]) ? $_GET["name"] : "";
        $tag = isset($_GET["tag"]) ? $_GET["tag"] : false;
        $old = isset($_GET["old"]) ? true : false;
        
        if (isset($_GET["name"]) || isset($_GET["tag"]) || isset($_GET["old"])) {
          
          // Don't search by tag
          if ($tag == "") {
            $tag = false;
          }
          
          // Create an alert message displayed if no results were found
          if ($tag === false) {
            $alert_message = "Pre zadaný výraz '{$_GET["name"]}' sme nenašli žiadnu konferenciu.";
          } else if ($name == "") {
            $tag_name = Tag::get_name($tag);
            $alert_message = "V kategórii '{$tag_name}' sme nenašli žiadnu konferenciu.";
          } else {
            $alert_message = "Pre zadanú kombináciu parametrov sme nenašli žiadne konferencie.";
          }
        } else {
          $alert_message = 'Zatiaľ ste nevytvorili žiadnu konferenciu.';
        }
        
        $data = Conferences::search_by_owner_name_tag($_SESSION['user']->get_user_data()['id'], $name, $tag, $old);
        
        $sold_out = isset($_GET["soldOut"]) ? true : false;
        
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
            get_conference_card($row, $sold_out);
          };
          
          echo "</div>";
        }
        
        ?>
          </div>
        </div>
      </div>
    </div>

      <script>
        function searchByTag(tag_id) {
          var url = window.location.href;
          var index = url.indexOf('?');

          // Remove other params
          if (index > -1) {
            url = url.substr(0,index);
          }

          url = url + "?tag=" + encodeURIComponent(tag_id);
          window.location.href = url;
        }
      </script>

    </body>
</html>