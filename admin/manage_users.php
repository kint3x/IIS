<?php

	include_once "../defines.php";
	require_once ROOT."/classes/user.class.php";
	require_once ROOT."/classes/database.class.php";

	start_session_if_none();

	if_not_admin_die();
?>
<html>
    <?php echo get_head(); ?>
    
    <body>
      <?php echo get_navbar(); ?>
      
<div class='container'>
   <div class='row'>
      <div class='col-sm-12 align-self-center pb-1'>
         <h1>Správa uživateľov</h1>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="table-responsive">
            <table id="mytable" class="table table-bordred table-striped">
               <thead>
                  <th><input type="checkbox" id="checkall" /></th>
                  <th>ID</th>
                  <th>Meno</th>
                  <th>Priezvisko</th>
                  <th>Adresa</th>
                  <th>Email</th>
                  <th>Rola</th>
                  <th>Edit</th>
                  <th>Delete</th>
               </thead>
               <tbody>
               	<?php 
               	if(!isset($_GET['page'])){
               		$curr_page = 0;
               	}
               	else{
               		$curr_page = $_GET['page']-1;
               	}

               	$cnt_res = User::get_all_users_count();

               	// 10 = per page
               	$last_page = intval($cnt_res/10)+1;
               	$users = User::get_all_users(10,$curr_page*10);

               	foreach( $users as $row){
               		echo "<tr>";
               		echo '<td><input type="checkbox" class="checkthis"/></td>';  
               		echo "<td>".$row['id']."</td>";
               		echo "<td>".$row['name']."</td>";
               		echo "<td>".$row['surname']."</td>";
               		echo "<td>".$row['address']."</td>";
               		echo "<td>".$row['email']."</td>";
               		$role = ($row['role']) ? "Admin" : "Užívateľ";
               		echo "<td>".$role."</td>";
               		echo '<td><button class="btn btn-primary btn-xs" data-title="Edit" data-toggle="modal" data-target="#edit" ><span class="glyphicon glyphicon-pencil"></span></button></td>';
               		echo '<td><button class="btn btn-danger btn-xs" data-title="Delete" data-toggle="modal" data-target="#delete" ><span class="glyphicon glyphicon-trash"></span></button></td>';
               		echo "</tr>";
               	}

               	?>
                  
               </tbody>
            </table>
            <div class="clearfix"></div>
            <?php
            echo getPaginationString($curr_page+1,$last_page);
             ?>
         </div>
      </div>
   </div>
</div>

     <script>
     	$(document).ready(function(){
			$("#mytable #checkall").click(function () {
			        if ($("#mytable #checkall").is(':checked')) {
			            $("#mytable input[type=checkbox]").each(function () {
			                $(this).prop("checked", true);
			            });

			        } else {
			            $("#mytable input[type=checkbox]").each(function () {
			                $(this).prop("checked", false);
			            });
			        }
			    });
			    
			    $("[data-toggle=tooltip]").tooltip();

			$(".page-link").each(function(){
				var id = $(this).attr('id');
				var url = window.location.href;
				var index = url.indexOf('?');
				// Remove other params
				if (index > -1) {
		            url = url.substr(0,index);
		        }
		        url = url + "?page=" + encodeURIComponent(id);

				$(this).attr("href",url);
			});

		});


     </script>
	</body>

</html>