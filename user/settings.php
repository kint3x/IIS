<?php
session_start();

require_once '../defines.php';
?>

<html>

  <?php echo get_head(); ?>

  <body>
    <?php echo get_navbar(); ?>

    <?php
    // TODO check login?
    $user = new User($_SESSION['user']['email']);
	$user_data= $user->get_data();

    echo "
    <div class='container'>  
      <div class='row'>
        <div class='col-sm-12 align-self-center'>
          <h1>Môj účet</h1>
        </div>
      </div>
      <div class='row'>
        <div class='col-sm-12 align-self-center'>
          <form id='settingsForm'>
    ";
        
    echo "
		  <div class='form-group'>
		    <label>Meno</label>
		    <input type='text' class='form-control' id='nameSet' value='".$user_data['name']."'>
		  </div>

          <div class='form-group'>
		    <label>Priezvisko</label>
		    <input type='text' class='form-control' id='surnameSet' value='".$user_data['surname']."'>
		  </div>

          <div class='form-group'>
		    <label>Email</label>
		    <input type='email' class='form-control' id='emailSet' aria-describedby='emailHelp'
			    value='".$user_data['email']."'>
		  </div>

          <div class='form-group'>
			<label>Adresa</label>
			<textarea class='form-control' id='addressSet' rows='3'>".$user_data['address']."</textarea>
		  </div>
          </form>
        </div>
      </div>
    ";
    
    echo "
      <div class='row pb-1' >
        <div class='col-sm-2 align-self-start'>
          <button type='button' class='btn btn-outline-dark btn-block' data-dismiss='modal'
            aria-hidden='true' data-toggle='modal' data-target='#changePasswordModal'>Zmeniť heslo</button>
        </div>
      </div>
    ";
      
    echo "
      <div class='row'>
        <div class='col-sm-2 align-self-center'>
          <button type='submit' class='btn btn-primary btn-block'>Uložiť</button>
        </div>
      </div>
    ";

    echo "
    <div id='changePasswordModal' class='modal fade' role='dialog'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h4 class='modal-title'>Zmena hesla</h4>
            <button type='button' class='close font-weight-light' data-dismiss='modal'>&times;</button>
          </div>
          <div class='modal-body'>
            <form id='passwordForm'>
            
            <div class='form-group'>
			  <label>Staré heslo</label>
              <input type='password' class='form-control' id='passwordCurrent' autocomplete='current-password' style='margin-bottom:5px;'>
			  <label>Nové heslo</label>
              <input type='password' class='form-control' id='passwordSet' autocomplete='new-password' style='margin-bottom:5px;'>
			  <label>Potvrdenie hesla</label>
			    <input type='password' class='form-control' id='passwordSetAgain' autocomplete='new-password' style='margin-bottom:5px;'>
			</div>
            </form>
          </div>
          <div class='modal-footer' >
            <button type='submit' class='btn btn-primary'>Zmeniť heslo</button>
          </div>
        </div>
      </div>
    </div>
    ";
    ?>
</html>