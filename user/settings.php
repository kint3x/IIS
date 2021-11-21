<?php
require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/user.class.php';

start_session_if_none();
?>

<html>

  <?php echo get_head(['title' => 'Nastavenia']); ?>

  <body>
    <?php 
    echo get_navbar();
    check_login("Pre zobrazenie tejto stránky musíte byť prihlásený.");
    ?>

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
            street: $("#street").val(),
            city: $("#city").val(),
            zip:  $("#zip").val(),
            state: $("#state").val(),
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

    ?>
    <div class='container-fluid'>
      <div class='row'>
            
        <?php get_user_sidebar(); ?>

        <div class='col-lg-8 align-self-top'>

          
          <div class='row'>
            <div class='col-sm-12 align-self-center'>
              <h2>Môj účet</h2>
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
		        <input type='text' class='form-control' id='nameSet' value='<?php echo htmlspecialchars($user_data['name'],ENT_QUOTES);?>'>
		      </div>
          
          <div class='form-group'>
            <label>Priezvisko</label>
		        <input type='text' class='form-control' id='surnameSet' value='<?php echo htmlspecialchars($user_data['surname'],ENT_QUOTES);?>'>
		      </div>
          
          <div class='form-group'>
            <label>Email</label>
		        <input type='email' class='form-control' id='emailSet' aria-describedby='emailHelp'
            value='<?php echo htmlspecialchars($user_data['email'],ENT_QUOTES);?>'>
		      </div>
          
          <h4>Adresa</h4>
          <div class="form-group row">
            <div class="col-lg-6">
              <label for="street">Ulica</label>
                <input type="text" class="form-control" id="street" value="<?php echo htmlspecialchars($user_data['street'],ENT_QUOTES);?>" autocomplete="street-address">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label for="city">Mesto</label>
              <input type="text" class="form-control" id="city" value="<?php echo htmlspecialchars($user_data['city'],ENT_QUOTES);?>" autocomplete="address-level2">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-3">
              <label for="state">Štát</label>
              <input type="text" class="form-control" id="state" value="<?php echo htmlspecialchars($user_data['state'],ENT_QUOTES);?>" autocomplete="country-name">
            </div>
            <div class="col-lg-3 mb-3">
              <label for="zip">PSČ</label>
              <input type="number" class="form-control" id="zip" value="<?php echo $user_data['zip'];?>" autocomplete="postal-code">
            </div>
          </div>
        </div>
      </div>
      <div class='row pb-1'>
        <div class='col-md-2 align-self-center'>
          <button type='submit' class='btn btn-primary btn-block'>Uložiť</button>
      </div>
      </form>
    </div>
      <div class='row pb-1' >
        <div class='col-md-2 align-self-start'>
          <button type='button' class='btn btn-outline-dark btn-block'
          aria-hidden='true' data-toggle='modal' data-target='#changePasswordModal'>Zmeniť heslo</button>
        </div>
      </div>
    </div>
  </div>

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
              <label for='passwordCurrent'>Staré heslo</label>
              <input type='password' class='form-control' name='passwordCurrent' id='passwordCurrent' autocomplete='current-password' style='margin-bottom:5px;' required>
			        <label for='passwordNew'>Nové heslo</label>
              <input type='password' class='form-control' name='passwordNew' id='passwordSet' autocomplete='new-password' style='margin-bottom:5px;' required>
			        <label for='passwordRepeat'>Potvrdenie hesla</label>
			        <input type='password' class='form-control' name='passwordRepeat' id='passwordSetAgain' autocomplete='new-password' style='margin-bottom:5px;' required>
			      </div>
          </div>
          <div class='modal-footer' >
            <button type='submit' class='btn btn-primary'>Zmeniť heslo</button>
          </div>
          </form>
        </div>
      </div>
    </div>
</html>