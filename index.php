<?php
session_start();
require ("defines.php");

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
       $("#register").submit(function (event) {
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
          var alert = "<div class='alert alert-warning' role='alert'>"+data.error+"</div>";
          $("#reg_alert").html(alert);
         }
         else{
          var succ = "<div class='alert alert-success' role='alert'>Tvoje konto bolo vytvorené, môžeš sa prihlásiť!</div>";
          $("#reg_dialog .modal-body").html(succ);
          $("#reg_dialog .btn-primary").hide();
         }
        });

        event.preventDefault();
       });

       $("#loginForm").submit(function (event) {
        var formData = {
         email: $("#emailLogin").val(),
         password: $("#passwordLogin").val(),
        };

        $.ajax({
         type: "POST",
         url: "/ajax/user_login.php",
         data: formData,
         dataType: "json",
         encode: true,
        }).done(function (data) {
         if(data.success){
          location.reload();
         }
        });

        event.preventDefault();
       });
      });
    </script>
  </body>
</html>