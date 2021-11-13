<?php

require_once "defines.php";

start_session_if_none();

if(isset($_GET["logout"])){
  if(isset($_SESSION['user'])){
    unset($_SESSION['user']);
    header("Location: /");
  }
}


?>

<html>
  <?php echo get_head(); ?>
  <body>
    <?php echo get_navbar(); ?>

    <div class='container'>  
        <div class='row'>
          <div class='col-sm-12 align-self-center pb-1'>
            <h2>Konferencie</h2>
          </div>
      </div>
      
      <div class='d-flex flex-row pb-2 justify-content-left'>
        
        <?php
          if (isset($_SESSION['user'])) {
            ?>
            <div class='pr-2'>
              <a href='/conferences/create.php' class='btn btn-primary'>Nová konferencia</a>
            </div>
            <?php
          }
        ?>
        <div>
          <form role='search'>
          <div class='input-group'>
              <select class="form-control mr-2" name="tag">
                <?php
                  if (isset($_GET['tag'])) {
                    $selected_tag = $_GET['tag'];
                    echo '<option value="">Výber kategórie</option>';
                  } else {
                    $selected_tag = -1;
                    echo '<option selected value="">Výber kategórie</option>';
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

          // Don't search by tag
          if ($tag == "") {
            $tag = false;
          }

          $data = Conferences::search_by_name_tag($name, $tag);
          
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
          $data = Conferences::get_conferences_all();
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