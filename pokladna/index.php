<?php

require_once "../defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/database.class.php";
require_once ROOT."/classes/cart.class.php";
require_once ROOT."/classes/conferences.class.php";

start_session_if_none();
Cart::setup_cart_if_not();

  $shop_prefill=array(
    "name" => "",
    "surname" => "",
    "street" => "",
    "city" => "",
    "zip" => "",
    "state" => "",
    "email" => ""
  );

  $user_id = NULL;

  // user is logged in
  if(isset($_SESSION['user'])){
    $user_data = $_SESSION['user']->get_user_data();
    $user_id = $user_data['id'];
    foreach($user_data as $key => $data){
      if($data == NULL) $user_data[$key] = ""; // replace nulls
    }
    $shop_prefill=array_replace($shop_prefill, $user_data);
  }

  if(isset($_POST['name']) 
     && isset($_POST['surname'])
     && isset($_POST['email'])
     && isset($_POST['street'])
     && isset($_POST['city'])
     && isset($_POST['state'])
     && isset($_POST['zip'])
    ){
    
      $items = $_SESSION['cart']->get_items();
      if(!Reservation::check_create_reservation_availabe($items)){
        $error = Reservation::$error_message;

      }
      else{
        $_SESSION['created_orders'] = array();

        foreach($items as $item => $count){

          $ret = Reservation::create_reservation($_POST['name'],$_POST['surname'],$_POST['email'],$count,0,$item,$_POST['street'],$_POST['city'],$_POST['zip'],$user_id,$_POST['state']);

           if($ret == false){
              $error=Reservation::$error_message;
              

           }
           else{
            $_SESSION['created_orders'][] = $ret;
           }  

        }
        if(!isset($error)) {
          $_SESSION['cart']->remove_all_items();
          $success = "Rezervácia bola vytvorená!";
          $_POST = array();
        } 
        
      }

  }



$print_items = $_SESSION['cart']->get_items_structured();
?>

<html>
  <?php echo get_head(['title' => 'Pokladňa']); ?>
  <body>
    <?php echo get_navbar(); 

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
          <form class="needs-validation" validate="" method="POST">
            <div class="row">
              <?php 
                if(isset($error)){
                  echo '<div class="col-sm-12 align-self-center pb-2">
          <div class="alert alert-danger" role="alert">
            '.$error.'          </div>
      </div>';
                } 
                $register="";
                if(!isset($_SESSION['user'])){
                  $register = '  Pre sledovanie rezervácií sa prosím <a href="#" data-toggle="modal" data-target="#registerModal" data-dismiss="modal">zaregistrujte</a> a rezervácie budú spárované s vašim kontom.';
                }

                if(isset($success)){
                  echo '<div class="col-sm-12 align-self-center pb-2">
          <div class="alert alert-success" role="alert">
            '.$success.$register.'          </div>
      </div>';
                }
              ?>
              <div class="col-md-6 mb-3">
                <label for="firstName">Meno</label>
                <input type="text" name='name' class="form-control" id="firstName" placeholder="" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $shop_prefill['name'];?>" required="">
                <div class="invalid-feedback">
                  Toto pole je povinné.
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="lastName">Priezvisko</label>
                <input type="text" name='surname' class="form-control" id="lastName" placeholder="" value="<?php if(isset($_POST['surname'])) echo $_POST['surname']; else echo $shop_prefill['surname'];?>" required="">
                <div class="invalid-feedback">
                  Toto pole je povinné.
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="email">Email</label>
              <input type="email" class="form-control" name='email' id="email" placeholder="admin@gmail.com" value="<?php if(isset($_POST['email'])) echo $_POST['email']; else echo $shop_prefill['email'];?>">
              <div class="invalid-feedback">
                Prosím zadajte validný email
              </div>
            </div>

            <div class="mb-3">
              <label for="address">Ulica a číslo</label>
              <input type="text" class="form-control" name='street' id="address" placeholder="Božetěchova 13" required="" value="<?php if(isset($_POST['street'])) echo $_POST['street']; else echo $shop_prefill['street'];?>">
              <div class="invalid-feedback">
                Prosím zadajte ulicu a číslo
              </div>
            </div>

            <div class="row">
              <div class="col-md-5 mb-3">
                <label for="city">Mesto</label>
                 <input type="text" class="form-control" name='city' id="mesto" placeholder="" required="" value="<?php if(isset($_POST['city'])) echo $_POST['city']; else echo $shop_prefill['city'];?>">
                <div class="invalid-feedback">
                  Prosím zadajte platné mesto
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <label for="state">Štát</label>
                <input type="text" class="form-control" name='state' id="state" placeholder="" required="" value="<?php if(isset($_POST['state'])) echo $_POST['state']; else echo $shop_prefill['state'];?>">

                <div class="invalid-feedback">
                  Prosím zadajte platný štát.
                </div>
              </div>
              <div class="col-md-3 mb-3">
                <label for="zip">PSČ</label>
                <input type="text" class="form-control" name='zip' id="zip" placeholder="" required="" value="<?php if(isset($_POST['zip'])) echo $_POST['zip']; else echo $shop_prefill['zip'];?>">
                <div class="invalid-feedback">
                  PSČ je povinné.
                </div>
              </div>
            </div>
            <hr class="mb-4">
  
            <input class="btn btn-primary btn-lg btn-block" type="submit" value="Rezervovať vstupenky"/>
          </form>
        </div>
      </div>

    </div>

  </body>
</html>