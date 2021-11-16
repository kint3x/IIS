<?php

require_once "../defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/cart.class.php";
require_once ROOT."/classes/conferences.class.php";

start_session_if_none();

?>

<html>
  <?php echo get_head(); ?>
  <body>
    <?php echo get_navbar(); 

    Cart::setup_cart_if_not();

    $print_items = $_SESSION['cart']->get_items_structured();



    ?>
    <div class="container">

      <div class="row">
        <div class="col-md-4 order-md-2 mb-4">
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">Váš košík</span>
            <span class="badge badge-secondary badge-pill"><?php 
            echo count($print_items);
          ?></span>
          </h4>
          <ul class="list-group mb-3">
            <?php

            foreach($print_items as $item){

              echo '<li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0"><a href="/conferences/show.php?id='.$item['id'].'">'.$item['name'].'</a></h6>
                <small class="text-muted">Počet: '.$item['count'].'</small>
              </div>
              <span class="text-muted">'.$item['count']*$item['price'].'€</span>
            </li>';

            }


            ?>
    
            <li class="list-group-item d-flex justify-content-between">
              <span>Celkom (EUR)</span>
              <strong><?php echo $_SESSION['cart']->get_cart_total()."€"; ?></strong>
            </li>
          </ul>

        </div>
        <div class="col-md-8 order-md-1">
          <h4 class="mb-3">Rezervácia vstupeniek</h4>
          <form class="needs-validation" novalidate="">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="firstName">Meno</label>
                <input type="text" class="form-control" id="firstName" placeholder="" value="" required="">
                <div class="invalid-feedback">
                  Toto pole je povinné.
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="lastName">Priezvisko</label>
                <input type="text" class="form-control" id="lastName" placeholder="" value="" required="">
                <div class="invalid-feedback">
                  Toto pole je povinné.
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="address">Ulica a číslo</label>
              <input type="text" class="form-control" id="address" placeholder="Božetěchova 13" required="">
              <div class="invalid-feedback">
                Prosím zadajte ulicu a číslo
              </div>
            </div>

            <div class="row">
              <div class="col-md-5 mb-3">
                <label for="city">Mesto</label>
                 <input type="text" class="form-control" id="mesto" placeholder="" required="">
                <div class="invalid-feedback">
                  Prosím zadajte platné mesto
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <label for="state">Štát</label>
                <input type="text" class="form-control" id="state" placeholder="" required="">

                <div class="invalid-feedback">
                  Prosím zadajte platný štát.
                </div>
              </div>
              <div class="col-md-3 mb-3">
                <label for="zip">PSČ</label>
                <input type="text" class="form-control" id="zip" placeholder="" required="">
                <div class="invalid-feedback">
                  PSČ je povinné.
                </div>
              </div>
            </div>
            <hr class="mb-4">
  
            <button class="btn btn-primary btn-lg btn-block" type="submit">Rezervovať vstupenky</button>
          </form>
        </div>
      </div>

    </div>

  </body>
</html>