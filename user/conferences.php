<?php
session_start();

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';
?>

<html>
    <?php echo get_head(); ?>

    <body>
        <?php echo get_navbar(); ?>
        
        <?php 
            $conferences = new Conferences();
            // $conferences->create_conference(5, 'test', 'testovacia konferencia', 1, 2, 10, 8, 'tu');
            $result = $conferences->get_conferences_all($_SESSION['user']['id']);
            var_dump($result);

            ?>
    </body>
</html>