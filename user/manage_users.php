<?php
require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/user.class.php';

session_start();
?>


<html>

  <?php echo get_head(); ?>

  <body>
    <?php echo get_navbar(); ?>
    
    <?php 
      $data = User::get_users_all();

      echo "
        <div class='container'>  
          <div class='row'>
            <div class='col-sm-12 align-self-center pb-1'>
              <h1>Uživatelia</h1>
            </div>
              <div class='col-sm-12 align-self-center'>
                <div id='settingsAlert'></div>
              </div>
            </div>
            <div class='row'>
              <div class='col-sm-12 align-self-center text-align='center'>
                <table class='table'>
                  <thead>
                    <tr>
                      <th scope='col'>#</th>
                      <th scope='col'>e-mail</th>
                      <th scope='col'>Name</th>
                      <th scope='col'>Surname</th>
                      <th scope='col'>Address</th>
                      <th scope='col'>Oprávnenie</th>
                    </tr>
                  </thead>
                  <tbody>";
                  $i = 0; 
                  foreach ($data as $row) {
                    $i++;
                    echo"
                      <tr>
                        <th scope='row'>{$i}</th>
                          <td>{$row['email']}</td>
                          <td>{$row['name']}</td>
                          <td>{$row['surname']}</td>
                          <td>{$row['address']}</td>
                    ";
                    if ($row['role'] == USER_ADMIN){
                      echo"<td>Admin</td>";
                    }else{
                      echo"<td>Uživateľ</td>";
                    }
                    echo"
                      <td><button type='button' class='btn btn-outline-dark btn-block delete_btn'
                      aria-hidden='true' id_user={$row['id']}>Vymazať</button></tf>
                      <td><button type='button' class='btn btn-primary btn-block'>Upraviť</button></tf>
                      </tr>
                    "; 
                  };
                  echo "       
                  </tbody>
                </table>            
                <div class='col-sm-2 align-self-center'>
                  <button type='submit' class='btn btn-primary btn-block'>Vymazať označené</button>
                </div>
              </div>
            </div>
          </div>";
    ?>
    <script>
      $(".delete_btn").on("click", function(){
        confirm("Zmazať uživateľa");
        $(this).attr("id_user");
      });
    </script>
  </body>
</html>