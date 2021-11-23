<?php

require_once "defines.php";
require_once ROOT."/classes/user.class.php";
require_once ROOT."/classes/conferences.class.php";
require_once ROOT."/classes/tag.class.php";
require_once ROOT."/classes/lecture.class.php";

/**
 * Check if and id was passed and if so check if a conference with a given id exists.
 */
function verify_conference_and_generate_head() {
  if (!isset($_GET['id']) || !is_numeric($_GET['id']) || ($conference = Conferences::get_conference_by_id($_GET['id'])) === false) {
    echo get_head(['title' => 'Chyba']);
    echo get_navbar();
    display_alert_container('Je nám ľúto ale daná konferencia neexistuje.');
    exit();
  }

  echo get_head(['title' => $conference['name']]);
}

/**
 * Check if the signed in user owns the conference. Must be called only after verify_conference() has been called.
 */
function verify_conference_owner() {
  if (is_admin()) {
    return;
  }

  if (!isset($_SESSION['user']) || !Conferences::is_owner($_SESSION['user']->get_user_data()['id'], $_GET['id'])) {
    display_alert_container('Danú konferenciu nemôžte upravovať.');
    exit();
  }
}

/** 
 * Check whether the room belongs to the given conference.
 */
function verify_room_and_generate_head() {

  if (!isset($_GET['id']) || !is_numeric($_GET['id']) || ($room = Room::get_room_by_id($_GET['id'])) === false) {
    echo get_head(['title' => 'Chyba']);
    echo get_navbar();
    display_alert_container('Je nám to ľúto, ale daná miestnosť neexistuje.');
    exit();
  }

  echo get_head(['title' => $room['name']]);
}

/**
 * Check whether the lecture belongs to the given conference.
 */
function verify_lecture_and_generate_head() {

  if (!isset($_GET['id']) || !is_numeric($_GET['id']) || ($lecture = Lecture::get_lecture_by_id($_GET['id'])) === false) {
    echo get_head(['title' => 'Chyba']);
    echo get_navbar();
    display_alert_container('Je nám to ľúto, ale daná prednáška neexistuje.');
    exit();
  }

  echo get_head(['title' => $lecture['name']]);
}

/**
 * Removes the get parameters from the url
 */
function without_params($url) {
  $index = strpos($url, '?');

  if ($index !== false) {
    $url = substr($url, 0, $index);
  }

  return $url;
}

/**
 * Get the HTML for the sidebar.
 */
function get_sidebar($title, $page_array) {
  ?>
  <div class="col-lg-2 align-self-top mb-2">    
    <ul class="nav nav-pills flex-column">
      <li class="nav-item">
        <h5 class="nav-link text-wrap">
          <?php echo $title;?>
        </h5>
      </li>
      <li><hr class="dropdown-divider" /></li>
      <?php
        foreach ($page_array as $name => $url) {
          ?>
          <li class="nav-item">
            <a class="nav-link <?php if ($_SERVER['PHP_SELF'] == without_params($url)) {echo "active";} else {echo "text-dark";}?>" 
              href="<?php echo $url;?>"><?php echo $name;?></a>
          </li>
          <?php
        }
      ?>
    </ul>
  </div>
  <?php
}

/**
 * Get the HTML sidebar for the pages in /conference.
 */
function get_conference_sidebar($conference) {
  $menu_array = [
      "Informácie" => "/conferences/show.php?id={$conference['id']}",
      "Prednášky" => "/conferences/lectures.php?id={$conference['id']}"
  ];

  if (user_owns_conference($conference['id_user'])) {
      $menu_array["Miestnosti"] = "/conferences/rooms.php?id={$conference['id']}";
      $menu_array["Rezervácie"] = "/conferences/reservations.php?id={$conference['id']}";
  }
  
  get_sidebar($conference['name'], $menu_array); 
}

/**
 * Get the HTML sidebar for the pages in /user.
 */
function get_user_sidebar() {
  $menu_array = [
      "Nastavenia" => "/user/settings.php",
      "Konferencie" => "/user/conferences.php",
      "Prednášky" => "/user/lectures.php",
      "Rezervácie" => "/user/reservations.php",
      "Vstupenky" => "/user/tickets.php",
      "Rozvrh" => "/user/schedule.php"
  ];

  get_sidebar("Účet", $menu_array);
}

/**
 * Get the ADMIN sidebar for the pages in /user.
 */
function get_admin_sidebar() {
  $menu_array = [
      "Správa užívateľov" => "/admin/manage_users.php",
      "Správa konferencií" => "/admin/manage_conferences.php",
  ];

  get_sidebar("Administrátor",$menu_array);
}


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

/**
 * Displays a simple alert message.
 */
function display_alert($message) {
  ?>
  <div class='alert alert-secondary' role='alert'>
    <?php echo $message; ?>
  </div>
  <?php  
}

/**
 * Display alert in a container.
 */
function display_alert_container($message) {
  ?>
  <div class="container">
    <div class="row">
      <div class="col-sm-12 align-self-center pb-2">
          <div class='alert alert-secondary' role='alert'>
            <?php echo $message;?>
          </div>
      </div>
    </div>
  </div>
  <?php
}

/**
 * Show an error message and exit if the user isn't logged in.
 */
function check_login($message) {
  if (!isset($_SESSION['user'])) {
    ?>
    <div class='container'>
      <div class='row'>
        <div class='col-sm-12 align-self-center pb-2'>
          <?php display_alert($message); ?>
        </div>
      </div>
    </div>
    <?php
    exit();
  }
}

/**
 * Echo the HTML for the conference cards.
 */
function get_conference_card($conference, $sold_out) {
	$tickets_left = Conferences::get_number_tickets_left($conference['id']);
	$tickets_left = $tickets_left < 0 ? "-" : $tickets_left;

  // Don't display sold out conferences
  if ($tickets_left == 0 && !$sold_out) {
    return;
  }

	echo '
    <div class="card mb-4" style="width: 48%;">
      	<img class="card-img-top img-top-fixed-height" src="'.htmlspecialchars($conference['image_url']).'" alt="">
      	<div class="card-body">
      	  <h5 class="card-title">
				    <a href="/conferences/show.php?id='.$conference['id'].'" class="text-decoration-none")">'.htmlspecialchars($conference['name']).'</a>
			    </h5>
          <p class="card-text text-truncate">'.htmlspecialchars($conference['description']).'</p>
          <p class="card-text"><small class="text-muted">'.htmlspecialchars($conference['city']).'</small></p>
	';
    
	$tags = Tag::get_conference_tags($conference['id']);

	foreach ($tags as $tag) {
        echo '<div style="cursor:pointer" onclick="searchByTag('.$tag['id'].')" class="badge badge-dark">'.htmlspecialchars($tag['name']).'</div>';
  }

	echo '</div>';    
    
	echo '<div class="card-footer">
	  	<a style="cursor:pointer;color:white;"  class="btn btn-margin btn-primary" onclick="add_to_cart('.$conference['id'].',this)" >Pridať do košíka</a>';
    
	if (user_owns_conference($conference['id_user'])) {
		echo '<a href="/conferences/edit.php?id='.$conference['id'].'" class="btn btn-outline-dark">Upraviť</a>';
	}
    
	echo'  </div>
    </div>';
}

function get_navbar(){
	$nav = ' 
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" role="navigation">
      <div class="container">
        <a class="navbar-brand" href="/">Konferencie</a>
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar">
          &#9776;
        </button>
        <div class="collapse navbar-collapse" id="exCollapsingNavbar">
          <ul class="nav navbar-nav flex-row justify-content-between ml-auto align-items-middle">';

            if(!isset($_SESSION["user"])){
                $nav .= ' 
                <li class="dropdown order-1">
                  <button type="button" data-toggle="modal" data-target="#loginModal" class="btn btn-outline-secondary mr-2">Prihlásiť sa</button>
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
            		$admin_menu = '
                      <li><hr class="dropdown-divider" /></li>
                      <li>
                        <a class="dropdown-item" href="/admin/manage_users.php">Správa užívateľov</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="/admin/manage_conferences.php">Správa konferencií</a>
                      </li>';
            		$admin = is_curr_user_admin() ? $admin_menu : "";
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
                        <a class="dropdown-item" href="/user/settings.php">Nastavenia</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="/user/conferences.php">Konferencie</a>
                      </li>
					            <li>
                        <a class="dropdown-item" href="/user/lectures.php">Prednášky</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="/user/reservations.php">Moje rezervácie</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="/user/tickets.php">Moje vstupenky</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="/user/schedule.php">Rozvrh</a>
                      </li>
                      <li>
                      '.$admin.'
                      <li><hr class="dropdown-divider" /></li>
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
					        <div class="title col-sm-10">
		                <div class="row">
		                    <div class="col">
			                    <h4><b>Košík</b></h4>
			                  </div>
			                  <div class="col align-self-center text-right text-muted item-num"></div>
		                </div>
		            	</div>
					        <button type="button" class="close font-weight-light" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body" style="padding:15px;">  
					        <div class="row justify-content-center">
						        <div class="col-md-10 cart">
						            
						        </div>
						        <div class="col-md-12">
						        	<div class="row mt-4 justify-content-center">
						        		<a href="/pokladna/" class="btn btn-primary">Prejsť do pokladne</a>
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

function if_not_admin_die(){
	if(!isset($_SESSION['user'])) die("Neprihlásený");
	$user_data = $_SESSION['user']->get_user_data();
	if($user_data["role"] != USER_ADMIN) die("Užívateľ nie je admin");

}

function is_curr_user_admin(){
	if(!isset($_SESSION['user'])) return false;
	$user_data = $_SESSION['user']->get_user_data();
	if($user_data["role"] == USER_ADMIN) return true;
	return false;
}


function getPaginationString($page, $lastpage, $adjacents = 2)
{
    $pagination = "";
    $lpm1 = $lastpage - 1;

    if ($lastpage > 1) {
        $pagination .= '<div class="w-100"></div><nav aria-label="Strana" class="strankovanie"><ul class="pagination justify - content - center">';

        if ($page > 1) {
            $pagination .= '<li class="page-item"><a class="page-link" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;" id="' . ($page-1) . '"><</a></li>';
        }

        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination .= '<li class="page-item"><a class="pg-selected page-link" id="' . $counter . '">' . $counter . '</a></li>';
                else
                    $pagination .= '<li class="page-item"><a class="page-link" id="' . $counter . '">' . $counter . '</a></li>';
            }
        }
        elseif($lastpage >= 7 + ($adjacents * 2)){
            if($page < 1 + ($adjacents * 3))
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    if ($counter == $page)
                        $pagination .= '<li class="page-item"><a class="pg-selected page-link" id="' . $counter . '">' . $counter . '</a></li>';
                    else
                        $pagination .= '<li class="page-item"><a class="page-link" id="' . $counter . '">' . $counter . '</a></li>';
                }

                $pagination .= '<li class="page-item"><a class="page-link" id="' . $lpm1 . '">' . $lpm1 . '</a></li>';
                $pagination .= '<li class="page-item"><a class="page-link" id="' . $lastpage . '">' . $lastpage . '</a></li>';
            }
            //in middle; hide some front and some back
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination .= '<li class="page-item"><a class="page-link" id="1">1</a></li>';
                $pagination .= '<li class="page-item"><a class="page-link" id="2">2</a></li>';
                $pagination .= "<span class=\"elipses\">...</span>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= '<li class="page-item"><a class="pg-selected page-link" id="' . $counter . '">' . $counter . '</a></li>';
                    else
                        $pagination .= '<li class="page-item"><a class="page-link" id="' . $counter . '">' . $counter . '</a></li>';
                }
                $pagination .= "...";
                $pagination .= '<li class="page-item"><a class="page-link" id="' . $lpm1 . '">' . $lpm1 . '</a></li>';
                $pagination .= '<li class="page-item"><a class="page-link" id="' . $lastpage . '">' . $lastpage . '</a></li>';
            }
            //close to end; only hide early pages
            else
            {
                $pagination .= '<li class="page-item"><a class="page-link" id="1">1</a></li>';
                $pagination .= '<li class="page-item"><a class="page-link" id="2">2</a></li>';
                $pagination .= "<span class=\"elipses\">...</span>";
                for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= '<li class="page-item"><a class="pg-selected page-link" id="' . $counter . '">' . $counter . '</a></li>';
                    else
                        $pagination .= '<li class="page-item"><a class="page-link" id="' . $counter . '">' . $counter . '</a></li>';
                }
            }
        }
       //next button
		if ($page < $counter - 1)
            $pagination .= '<li class="page-item"><a class="page-link" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;" id="'. ++$page .'">></a></li>';

		$pagination .= '</ul></nav>';
    }
    return $pagination;
}
