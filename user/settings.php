<?php
require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/user.class.php';

start_session_if_none();
?>

<html>

  <?php echo get_head(); ?>

  <body>
    <?php echo get_navbar(); ?>

    <script>
      $(document).ready(function () {
        // Remove allerts when closing the window
        $("#changePasswordModal").on('hide.bs.modal', function(event) {
          $("#passwordAlert").html("");
        });

        // Changing password
        $("#passwordForm").submit(function (event) {
          var formData = {
            passwordCurrent: $("#passwordCurrent").val(),
            passwordNew: $("#passwordSet").val(),
            passwordNewAgain: $("#passwordSetAgain").val()
          }
          
          $.ajax({
            type: "POST",
            url: "/ajax/user_password.php",
            data: formData,
            dataType: "json",
            encode: true,
          }).done(function (data) {
            if(data.success){
              var succ = "<div class='alert alert-success alert-dismissible' role='alert'>" 
                + "Heslo bolo úspešne zmenené." 
                + "<button class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</button>"
                + "</div>";
                $("#passwordAlert").css('display','block');
                $("#passwordAlert").html(succ);
                
              $('#passwordCurrent').val("");
              $("#passwordSet").val("");
              $("#passwordSetAgain").val("");
            }
            else{
              var alert = "<div class='alert alert-warning alert-dismissible' role='alert'>" 
              + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
              + data.error + "</div>";
              $("#passwordAlert").css('display','block');
              $("#passwordAlert").html(alert);
              
              $("#passwordSet").val("");
              $("#passwordSetAgain").val("");
            }
            
          });
          
          event.preventDefault();
        });

        // Changing settings
        $("#settingsForm").submit(function (event) {
          var formData = {
            email: $("#emailSet").val(),
            name: $("#nameSet").val(),
            surname: $("#surnameSet").val(),
            address: $("#addressSet").val()
          };
          
          $.ajax({
            type: "POST",
            url: "/ajax/user_settings.php",
            data: formData,
            dataType: "json",
            encode: true,
          }).done(function (data) {
            if(data.success){
              var succ = "<div class='alert alert-success alert-dismissible' role='alert'>" 
              + "Nastavenia boli uložené." 
              + "<button class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</button>"
              + "</div>";
              $("#settingsAlert").css('display','block');
              $("#settingsAlert").html(succ);
            }
            else{
              var alert = "<div class='alert alert-warning alert-dismissible' role='alert'>" 
              + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
              + data.error + "</div>";
              $("#settingsAlert").css('display','block');
              $("#settingsAlert").html(alert);
            }
          });
          
          event.preventDefault();
        });
      });
    </script>

    <?php
	  $user_data = $_SESSION['user']->get_user_data();

    echo "
    <div class='container'>  
      <div class='row'>
        <div class='col-sm-12 align-self-center'>
          <h1>Môj účet</h1>
        </div>
        <div class='col-sm-12 align-self-center'>
        <div id='settingsAlert'></div>
        </div>
      </div>
      <div class='row'>
        <div class='col-sm-12 align-self-center'>
          <form id='settingsForm'>
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
        </div>
      </div>
      <div class='row pb-1'>
        <div class='col-sm-2 align-self-center'>
          <button type='submit' class='btn btn-primary btn-block'>Uložiť</button>
        </div>
      </div>
      </form>
      <div class='row pb-1' >
        <div class='col-sm-2 align-self-start'>
          <button type='button' class='btn btn-outline-dark btn-block'
            aria-hidden='true' data-toggle='modal' data-target='#changePasswordModal'>Zmeniť heslo</button>
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
            <div id='passwordAlert'></div>
          
            <div class='form-group'>
              <input type='email' class='form-control' aria-describedby='emailHelp' autocomplete='username' hidden='true'>
            <label>Staré heslo</label>
              <input type='password' class='form-control' id='passwordCurrent' autocomplete='current-password' style='margin-bottom:5px;' required>
			      <label>Nové heslo</label>
              <input type='password' class='form-control' id='passwordSet' autocomplete='new-password' style='margin-bottom:5px;' required>
			      <label>Potvrdenie hesla</label>
			        <input type='password' class='form-control' id='passwordSetAgain' autocomplete='new-password' style='margin-bottom:5px;' required>
			      </div>
          </div>
          <div class='modal-footer' >
            <button type='submit' class='btn btn-primary'>Zmeniť heslo</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    ";
    ?>
</html>