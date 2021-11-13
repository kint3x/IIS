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

    
  </body>
</html>