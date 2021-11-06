<?php

require_once "defines.php";
require_once ROOT."/classes/user.class.php";

function get_head($params=array()){

	$options["html"] = "";
	$options["title"] = "Konferencie";

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
	  return sprintf($head, $options["title"], $options["html"]);
}

function get_navbar(){
	$nav = ' 
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" role="navigation">
      <div class="container">
        <a class="navbar-brand" href="#">Konferencie</a>
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
                  <button type="button" data-toggle="modal" data-target="#loginModal" class="btn btn-outline-secondary">Prihlásiť sa</button>
                </li>
                
		          </ul>
		        </div>
		      </div>
		    </nav> 
		    
			  <div id="registerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		      <div class="modal-dialog" id="regDialog">
		        <div class="modal-content">
            <form id="registerForm">
            <div class="modal-header">
            <h3>Registrácia</h3>
            <button type="button" class="close font-weight-light" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
              <div id="regAlert"></div>
		          <div class="form-group">
		            <label for="emailReg">Email</label>
		            <input type="email" class="form-control" id="emailReg" aria-describedby="emailHelp" placeholder="priklad@email.com" required>
		          </div>
		          <div class="form-group">
		            <label for="hesloReg">Heslo</label>
		            <input type="password" class="form-control" id="passwordReg" placeholder="Heslo" required>
		          </div>
		        </div>
		        <div class="modal-footer">
		          <button type="submit" class="btn btn-primary">Registrovať</button>
		        </div>
		        </div>
		        </form>
		      </div>
		    </div>

        <div id="loginModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		      <div class="modal-dialog" id="loginDialog">
		        <div class="modal-content">
            <form id="loginForm">
            <div class="modal-header">
            <h3>Prihlásenie</h3>
            <button type="button" class="close font-weight-light" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
               <div id="loginAlert"></div>
		           <div class="form-group">
		            <label for="emailLogin">Email</label>
		            <input type="email" class="form-control" id="emailLogin" aria-describedby="emailHelp" placeholder="priklad@email.com" required>
		           </div>
		           <div class="form-group">
		            <label for="hesloLogin">Heslo</label>
		            <input type="password" class="form-control" id="passwordLogin" placeholder="Heslo" required>
		           </div>
		          </div>
              <div class="modal-footer">
                <div class="flex-fill">  
                  <div class="d-flex justify-content-between align-items-center">
                    <a href="#" data-toggle="modal" data-target="#registerModal" data-dismiss="modal">Nemáte účet? Registrujte sa!</a>
		                <button type="submit" class="btn btn-primary">Prihlásiť sa</button>
                  </div>
                </div>
              </div>
            </div>
		        </form>
		      </div>
		    </ div>';
            }
            else{
                $nav .= '
                <!-- Icon dropdown -->
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
                        <a class="dropdown-item" href="/user/settings.php">Môj účet</a>
                      </li>
					            <li>
                        <a class="dropdown-item" href="/user/conferences.php">Moje konferencie</a>
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
				        </ul>
			        </div>
			      </div>
			    </nav>';
            }

    return $nav;
}