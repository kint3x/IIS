<?php

require_once '../defines.php';
require_once ROOT.'/lib.php';
require_once ROOT.'/classes/conferences.class.php';

start_session_if_none();

?>

<html>
    <?php echo get_head(); ?>

    <body>
        <?php echo get_navbar(); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12 align-self-center pb-2">
                    <h1>
                        Pridať konferenciu
                    </h1>
                </div>
            </div>
            <form>
                <div class="form-group">
                  <label for="name">Názov konferencie</label>
                  <input type="text" class="form-control" id="name" placeholder="Názov" required>
                </div>
                <div class="form-group">
                  <label for="description">Popis konferencie</label>
                  <textarea class="form-control" id="description" placeholder="Detailný popis..."></textarea>
                </div>
                <label for="from">Začiatok konania</label>    
                <div class="form-group row" id="from">
                    <div class="col-sm-2">
                        <input type="time" class="form-control timepicker" id="fromTime" required>
                    </div>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" id="fromDate" required>
                    </div>
                </div>
                <label for="to">Koniec konania</label>    
                <div class="form-group row" id="to">
                    <div class="col-sm-2">
                        <input type="time" class="form-control" id="toTime" required>
                    </div>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" id="toDate" required>
                    </div>
                </div>
                <div class="form-group">
                  <label for="place">Miesto konania</label>
                  <textarea class="form-control" id="place" placeholder="Adresa" required></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Cena lístku v &euro;</label>
                    <input type=number class="form-control" min=0 step="0.01" pattern="\d+\.\d\d" id="price" value="0.00" required>
                </div>
                <div class="form-group">
                    <label for="capacity">Počet voľných miest</label>
                    <input type=number class="form-control" min=0 pattern="\d+" id="capacity" value="0" step="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Pridať</button>
            </form>
        </div>
    </body>
</html