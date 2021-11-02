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
  <?php echo get_head() ?>
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

       $("#settingsForm").submit(function (event) {
        var formData = {
         email: $("#emailSet").val(),
         passwordCurrent: $("#passwordCurrent").val(),
         passwordNew: $("#passwordSet").val(),
         passwordNewAgain: $("#passwordSetAgain").val(),
         name: $("#nameSet").val(),
         surname: $("#surnameSet").val(),
         address: $("#addressSet").val()
        };
        
        $.ajax({
          type: "POST",
          url: "/ajax/user_edit.php",
          data: formData,
          dataType: "json",
          encode: true,
        }).done(function (data) {
          if(data.success){
            var succ = "<div class='alert alert-success' role='alert'>Nastavenia boli uložené</div>";
            $("#settings_alert").css('display','block');
            $("#settings_alert").html(succ).delay(2000).fadeOut();
          }
          else{
            var alert = "<div class='alert alert-warning' role='alert'>" + data.error + "</div>";
            $("#settings_alert").css('display','block');
            $("#settings_alert").html(alert).delay(2000).fadeOut();
            
          }
          
        $("#passwordSet").val("");
        $("#passwordSetAgain").val("");
        });

        event.preventDefault();
       });



      });
    </script>
  </body>

</html>