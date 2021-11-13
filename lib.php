<?php

require_once "defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/conferences.class.php";
require_once ROOT."/classes/tag.class.php";

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
	     <script type="text/javascript" src="/js/cart.js"></script>
	     %s
	  </head>';	
	  return sprintf($head, $options["title"], $options["html"]);
}

function get_conference_card($db_entry) {
	echo '
    <div class="card mb-4" style="width: 48%;">
      	<img class="card-img-top img-top-fixed-height" src="'.$db_entry['image_url'].'" alt="">
      	<div class="card-body">
      	  	<h5 class="card-title">
				<a href="/conferences/show.php?id='.$db_entry['id'].'" class="text-decoration-none")">'.$db_entry['name'].'</a>
			</h5>
      	  	<p class="card-text text-truncate">'.$db_entry['description'].'</p>
	';
    
	$tags = Tag::get_conference_tags($db_entry['id']);

	foreach ($tags as $tag) {
        echo '<a href="#" class="badge badge-dark">'.$tag['name'].'</a>';
    }  	
		  
	echo '</div>';
	echo  '
		<ul class="list-group list-group-flush d-flex flex-row flex-wrap">
      	  	<li class="list-group-item col-sm-6 pl-list-item"><b>Od: </b>'.date(DATE_FORMAT, $db_entry['time_from']).'</li>
				<li class="list-group-item col-sm-6"><b>Do: </b>'.date(DATE_FORMAT, $db_entry['time_from']).'</li>
      	  	<li class="list-group-item col-sm-6 pl-list-item"><b>Cena: </b>'.$db_entry['price'].' &euro;</li>
      	  	<li class="list-group-item col-sm-6"><b>Voľné miesta: </b>'.Conferences::get_number_tickets_left($db_entry['id']).'</li>
      	</ul>
	';
    
    
	echo '<div class="card-footer">
	  	<a style="cursor:pointer;color:white;"  class="btn btn-margin btn-primary" onclick="add_to_cart('.$db_entry['id'].',this)" >Pridať do košíka</a>
        <a href="#" class="btn btn-outline-dark">Upraviť</a>
      </div>
    </div>';
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
          <ul class="nav navbar-nav flex-row justify-content-between ml-auto align-items-middle">';

            if(!isset($_SESSION["user"])){
                $nav .= ' 
                <li class="dropdown order-1">
                  <button type="button" data-toggle="modal" data-target="#loginModal" class="btn btn-outline-secondary">Prihlásiť sa</button>
                </li>
                 <li class="nav-item me-auto dropdown kosik_icon order-2">
                  <a
                      class="nav-link dropdown-toggle"
                      href="#"
                      id="cartToggle"
                      role="button"
                      data-toggle="modal"
                      data-target="#cartModal"
                      aria-expanded="false"
                    >
                      <i class="fas fa-shopping-cart"></i>
                    </a>
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
		            <input type="password" class="form-control" id="passwordReg" autocomplete="new-password" placeholder="Heslo" required>
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
		    </div>';
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
                      <i class="fas fa-user "></i>
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

                  <li class="nav-item me-auto dropdown kosik_icon">
                  <a
                      class="nav-link dropdown-toggle"
                      href="#"
                      id="cartToggle"
                      role="button"
                      data-toggle="modal"
                      data-target="#cartModal"
                      aria-expanded="false"
                    >
                      <i class="fas fa-shopping-cart"></i>
                    </a>
                  </li>
                  
				        </ul>
			        </div>
			      </div>
			    </nav>';
			  	}
					$nav .='

			    <!-- Shopping cart --!>
			    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <div class="title">
		                <div class="row">
		                    <div class="col">
			                    <h4><b>Košík</b></h4>
			                  </div>
			                  <div class="col align-self-center text-right text-muted">3 items</div>
		                </div>
		            	</div>
					        <button type="button" class="close font-weight-light" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body" style="padding:15px;">  
					        <div class="row justify-content-center">
						        <div class="col-md-10 cart">
						            <div class="row border-top border-bottom">
						                <div class="row main align-items-center">
						                    <div class="col-2"><img class="img-fluid" src="/img/placeholder.jpg"></div>
						                    <div class="col">
						                        <div class="row text-muted">Konference</div>
						                        <div class="row">Ako být sexi</div>
						                    </div>
						                    <div class="col"> <a href="#">-</a><span class="h5" style="margin-left:4px;margin-right:4px;">1</a><a href="#">+</a> </div>
						                    <div class="col">&euro; 44.00 <span class="close" style="cursor:pointer;">&#10005;</span></div>
						                </div>
						            </div>
						            <div class="row">
						                <div class="row main align-items-center">
						                    <div class="col-2"><img class="img-fluid" src="/img/placeholder.jpg"></div>
						                    <div class="col">
						                        <div class="row text-muted">Shirt</div>
						                        <div class="row">Cotton T-shirt</div>
						                    </div>
						                    <div class="col"> <a href="#">-</a><a href="#" class="border">1</a><a href="#">+</a> </div>
						                    <div class="col">&euro; 44.00 <span class="close">&#10005;</span></div>
						                </div>
						            </div> 
						        </div>
						        <div class="col-md-12">
						        	<div class="row" style="padding:10%;padding-bottom:0px;">
						        		<a href="/pokladna/" class="cart_button">Prejsť do pokladne</a>
						        	</div>
						        </div>
					     		</div>
					    	</div>
					  	</div>
						</div>
					</div>
					<script>

						$("#cartModal").on("shown.bs.modal", function () {
						  
						});

					</script>
					<script>
      
      $(document).ready(function () {
        $("#registerForm").submit(function (event) {
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
              var alert = "<div class=\'alert alert-warning\' role=\'alert\'>"
                + data.error
                + "<button class=\'close font-weight-light\' data-dismiss=\'alert\' aria-label=\'close\'>&times;</button>"
                + "</div>";
              $("#regAlert").html(alert);
            } else {
              var succ = "<div class=\'alert alert-success\' role=\'alert\'>Tvoje konto bolo vytvorené, môžeš sa prihlásiť!</div>";
              $(\'#regAlert\').html(succ);
              $(\'#emailReg\').val(""); 
              $(\'#passwordReg\').val(""); 
            }
          });
          

          event.preventDefault();
        });

        $("#loginForm").submit(function (event) {
          var formData = {
            email: $("#emailLogin").val(),
            password: $("#passwordLogin").val()
          };

          $.ajax({
            type: "POST",
            url: "/ajax/user_login.php",
            data: formData,
            dataType: "json",
            encode: true,
          }).done(function (data) {
            if (data.success) {
              $("#loginModal").modal(\'hide\');
              location.reload();
            } else {
              var alert = "<div class=\'alert alert-warning\' role=\'alert\'>"
                + data.error
                + "<button class=\'close font-weight-light\' data-dismiss=\'alert\' aria-label=\'close\'>&times;</button>"
                + "</div>";
              $("#loginAlert").html(alert);
            }
            
          });
          
          event.preventDefault();
        });
      });
    </script>

					';
					


    return $nav;
}
