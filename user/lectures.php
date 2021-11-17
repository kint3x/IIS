<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/lecture.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>
    
    <body>
        <?php 
        echo get_navbar();
        check_login("Pre zobrazenie tejto stránky musíte byť prihlásený.");
        ?>
      
        <div class='container-fluid'>  
        <div class='row'>

            
            <?php get_user_sidebar(); ?>

            <div class='col-lg-8 align-self-top'>
                <div class='row'>
                    <div class='col-sm-12 align-self-center pb-1'>
                        <h2>Moje prednášky</h2>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-sm-12 align-self-center pb-1'>
                        <p>#TODO</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>