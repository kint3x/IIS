<?php
session_start();
require ("defines.php");

require(ROOT."/classes/database.class.php");

$db = new Database();

?>

<html>
    <head>
        <title></title>

         <script type="text/javascript" src="/js/jquery-3.6.0.min.js"></script>
         <link rel="stylesheet" href="/css/bootstrap.min.css"> 
         <link rel="stylesheet" href="/css/bootstrap-grid.min.css"> 
         <link rel="stylesheet" href="/css/style.css"> 
         <script type="text/javascript" src="/js/bootstrap.min.js"></script>
    </head>
    <body>
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
                    <ul class="nav navbar-nav flex-row justify-content-between ml-auto">
                        <li class="nav-item order-2 order-md-1"><a href="#" class="nav-link" title="settings"><i class="fa fa-cog fa-fw fa-lg"></i></a></li>
                        <li class="dropdown order-1">
                            <button type="button" id="dropdownMenu1" data-toggle="dropdown" class="btn btn-outline-secondary dropdown-toggle">Login <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right mt-2">
                               <li class="px-3 py-2">
                                   <form class="form" role="form" id="loginForm">
                                        <div class="form-group">
                                            <input id="emailLogin" placeholder="Email" class="form-control form-control-sm" type="text" required="">
                                        </div>
                                        <div class="form-group">
                                            <input id="passwordLogin" placeholder="Password" class="form-control form-control-sm" type="password" required="">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                                        </div>
                                        <div class="form-group text-center">
                                            <small><a href="#" data-toggle="modal" data-target="#modalRegister">Nemáte účet? Registrujte sa!</a></small>
                                        </div>
                                    </form>
                                </li>
                            </ul>
                        </li>
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
                            <input type="password" class="form-control" id="hesloReg" placeholder="Heslo">
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

        <script>
            $(document).ready(function () {
              $("#register").submit(function (event) {
                var formData = {
                  email: $("#emailReg").val(),
                  heslo: $("#hesloReg").val(),
                };

                $.ajax({
                  type: "POST",
                  url: "/ajax/user_register.php",
                  data: formData,
                  dataType: "json",
                  encode: true,
                }).done(function (data) {
                  if(!data.success){
                    var alert = "<div class='alert alert-warning' role='alert'>"+data.error+"</div>";
                    $("#reg_alert").html(alert);
                  }
                  else{
                    var succ = "<div class='alert alert-success' role='alert'>Tvoje konto bolo vytvorené, môžeš sa prihlásiť!</div>";
                    $("#reg_dialog .modal-body").html(succ);
                    $("#reg_dialog .btn-primary").hide();
                  }
                });

                event.preventDefault();
              });

              $("#loginForm").submit(function (event) {
                var formData = {
                  email: $("#emailLogin").val(),
                  heslo: $("#passwordLogin").val(),
                };

                $.ajax({
                  type: "POST",
                  url: "/ajax/user_login.php",
                  data: formData,
                  dataType: "json",
                  encode: true,
                }).done(function (data) {
                  console.log(data);
                });

                event.preventDefault();
              });


            });
        </script>
    </body>

</html>