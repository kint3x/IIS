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

    <script>
      
      $(document).ready(function () {
        $("#registerModal").on('hide.bs.modal', function(event) {
          $("#regAlert").html("");
        });

        $("#loginModal").on('hide.bs.modal', function(event) {
          $("#loginAlert").html("");
        });

        $("#registerForm").submit(function (event) {
          var formData = {
            email: $("#emailReg").val(),
            password: $("#passwordReg").val(),
          };
          
          $.ajax({
            type: "POST",
            url: "/ajax/user_register.php",
            data: formData,
            dataType: "json",
            encode: true,
          }).done(function (data) {
            if(!data.success){
              var alert = "<div class='alert alert-warning' role='alert'>"
                + data.error
                + "<button class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</button>"
                + "</div>";
              $("#regAlert").html(alert);
            } else {
              var succ = "<div class='alert alert-success' role='alert'>Tvoje konto bolo vytvorené, môžeš sa prihlásiť!</div>";
              $('#regAlert').html(succ);
              $('#emailReg').val(''); 
              $('#passwordReg').val(''); 
            }
          });
          

          event.preventDefault();
        });

        $("#loginForm").submit(function (event) {
          var formData = {
            email: $("#emailLogin").val(),
            password: $("#passwordLogin").val()
          };

          $.ajax({
            type: "POST",
            url: "/ajax/user_login.php",
            data: formData,
            dataType: "json",
            encode: true,
          }).done(function (data) {
            if (data.success) {
              $("#loginModal").modal('hide');
              location.reload();
            } else {
              var alert = "<div class='alert alert-warning' role='alert'>"
                + data.error
                + "<button class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</button>"
                + "</div>";
              $("#loginAlert").html(alert);
            }
            
          });
          
          event.preventDefault();
        });
      });
    </script>
  </body>
</html>