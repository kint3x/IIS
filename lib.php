<?php

require_once ROOT."/classes/user.class.php";

//lib
function get_head($params=array()){

	$options["html"] = "";
	$options["title"] = "IIS Konferencia";

	$options = array_replace_recursive($options, $params);

	$head = '<head>
		<meta charset="utf-8">

	    <title>%s</title>

	     <script type="text/javascript" src="/js/jquery-3.6.0.min.js"></script>
	     <link rel="stylesheet" href="/css/bootstrap.min.css"> 
	     <link rel="stylesheet" href="/css/bootstrap-grid.min.css"> 
	     <link rel="stylesheet" href="/css/style.css"> 
	     <link rel="stylesheet" href="/css/font-awesome.css"> 
	     <script type="text/javascript" src="/js/bootstrap.min.js"></script>
	     <script type="text/javascript" src="/js/font-awesome.js"></script>
	     %s
	  </head>';	
	  return sprintf($head,$options["title"],$options["html"]);
}

function get_navbar(){
	$nav = ' 
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" role="navigation">
      <div class="container">
        <a class="navbar-brand" href="#">IIS Konferencia</a>
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar">
          &#9776;
        </button>
        <div class="collapse navbar-collapse" id="exCollapsingNavbar">
          <ul class="nav navbar-nav">
            <li class="nav-item"><a href="/" class="nav-link">Domov</a></li>
          </ul>
          <ul class="nav navbar-nav flex-row justify-content-between ml-auto">';

            if(!isset($_SESSION["user"])){
                $nav .= ' 
                <li class="dropdown order-1">
                  <button type="button" id="dropdownMenu1" data-toggle="dropdown" class="btn btn-outline-secondary dropdown-toggle">Prihlásiť sa <span class="caret"></span></button>
                  <ul class="dropdown-menu dropdown-menu-right mt-2" style="min-width:200px">
                    <li class="px-3 py-2">
                      <form class="form" role="form" id="loginForm">
                        <div class="form-group">
                          <input id="emailLogin" placeholder="Email" class="form-control form-control-sm" type="text" required="">
                        </div>
                        <div class="form-group">
                          <input id="passwordLogin" placeholder="Password" class="form-control form-control-sm" type="password" required="">
                        </div>
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary btn-block">Prihlásiť sa</button>
                        </div>
                        <div class="form-group text-center">
                          <small><a href="#" data-toggle="modal" data-target="#modalRegister">Nemáte účet? Registrujte sa!</a></small>
                        </div>
                      </form>
                    </li>
                  </ul>
                </li>';
            }
            else{
                $nav .= '
                <!-- Icon dropdown -->
                  <li class="nav-item order-2 order-md-1"><a href="#" class="nav-link" title="settings" data-toggle="modal" data-target="#modalProfileSettings"><i class="fa fa-cog fa-fw fa-lg"></i></a></li>
                  <li class="nav-item me-3 me-lg-0 dropdown">
                    <a
                      class="nav-link dropdown-toggle"
                      href="#"
                      id="navbarDropdown"
                      role="button"
                      data-toggle="dropdown"
                      aria-expanded="false"
                    >
                      <i class="fas fa-user"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                      <li>
                        <a class="dropdown-item" href="#">Moje konferencie</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">Rozvrh</a>
                      </li>
                      <li><hr class="dropdown-divider" /></li>
                      <li>
                        <a class="dropdown-item" href="/?logout=true">Odhlásiť sa</a>
                      </li>
                    </ul>
                  </li>
                ';
            }
           if(!isset($_SESSION["user"])){
          	 $nav .=' 
		          </ul>
		        </div>
		      </div>
		    </nav> 
		    <div id="modalRegister" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		      <div class="modal-dialog" id="reg_dialog">
		        <div class="modal-content">
		          <form id="register">
		          <div class="modal-header">
		            <h3>Registrácia</h3>
		            <button type="button" class="close font-weight-light" data-dismiss="modal" aria-hidden="true">×</button>
		          </div>
		          <div class="modal-body">

		             <div class="form-group">
		              <label for="emailReg">Email</label>
		              <input type="email" class="form-control" id="emailReg" aria-describedby="emailHelp" placeholder="Prosím zadajte email">
		              <small id="emailHelp" class="form-text text-muted">email musí byť formátu email@priklad.com</small>
		             </div>
		             <div class="form-group">
		              <label for="hesloReg">Heslo</label>
		              <input type="password" class="form-control" id="passwordReg" placeholder="Heslo">
		             </div>
		          <div id="reg_alert"></div>
		          </div>
		          <div class="modal-footer">
		            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		            <button type="submit" class="btn btn-primary">Registrovať</button>
		          </div>
		        </div>
		        </form>
		      </div>
		    </div>
		    ';
			}
			else{
    
			    $user = new User($_SESSION['user']['email']);
			    $user_data= $user->get_data();

			    $nav .='
				    </ul>
			        </div>
			      </div>
			    </nav>
			    <div id="modalProfileSettings" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			      <div class="modal-dialog" id="reg_dialog">
			        <div class="modal-content">
			         <form id="settingsForm">
			          <div class="modal-header">
			            <h3>Upraviť profil</h3>
			            <button type="button" class="close font-weight-light" data-dismiss="modal" aria-hidden="true">×</button>
			          </div>
			          <div class="modal-body">

			             <div class="form-group">
			              <label for="emailReg">Email</label>
			              <input type="email" class="form-control" id="emailSet" aria-describedby="emailHelp"
			              value="'.$user_data['email'].'" readonly>
			             </div>

			             <div class="form-group">
			              <label for="hesloReg">Zmeniť heslo</label>
						  <input type="password" class="form-control" id="passwordCurrent" autocomplete="current-password" placeholder="Aktuálne heslo" style="margin-bottom:5px;">
			              <input type="password" class="form-control" id="passwordSet" autocomplete="new-password" placeholder="Nové heslo" style="margin-bottom:5px;">
			              <input type="password" class="form-control" id="passwordSetAgain" autocomplete="new-password" placeholder="Potvrdenie hesla">
			             </div>

			           	<div class="form-group">
			           		<label>Meno a Priezvisko</label>
			             <div class="row">
						    <div class="col">
						      <input type="text" class="form-control" placeholder="Meno" id="nameSet"
						      value="'.$user_data['name'].'">
						    </div>
						    <div class="col">
						      <input type="text" class="form-control" placeholder="Priezvisko" id="surnameSet"
						      value="'.$user_data['surname'].'">
						    </div>
						  </div>
						</div>

			             <div class="form-group">
					    <label for="adresa">Adresa</label>
					    <textarea class="form-control" id="addressSet" rows="3">'.$user_data['address'].'</textarea>
					  </div>
			          <div id="settings_alert"></div>
			          </div>
			          <div class="modal-footer">
			            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			            <button type="submit" class="btn btn-primary">Nastaviť</button>
			          </div>
			        </div>
			        </form>
			      </div>
			    </div>

			    ';
			}
    return $nav;
}