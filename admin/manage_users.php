<?php

	include_once "../defines.php";
	require_once ROOT."/classes/user.class.php";
	require_once ROOT."/classes/database.class.php";
	require_once ROOT."/classes/table.class.php";

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
               		echo "<tr id='row".$row['id']."'>";
               		echo '<td><input type="checkbox" value="'.$row['id'].'" class="checkthis"/></td>';  
               		echo "<td>".$row['id']."</td>";
               		echo "<td>".$row['name']."</td>";
               		echo "<td>".$row['surname']."</td>";
               		echo "<td>".$row['address']."</td>";
               		echo "<td>".$row['email']."</td>";
               		$role = ($row['role']) ? "Admin" : "Užívateľ";
               		echo "<td>".$role."</td>";
               		echo '<td><button class="btn btn-primary btn-xs" data-title="Edit" data-toggle="modal" data-target="#editUserModal" >Upraviť</span></button></td>';
               		echo '<td><button class="btn btn-danger btn-xs" onclick="delete_user('.$row['id'].')" >Vymazať</button></td>';
               		echo "</tr>";
               	}

               	?>
                  
               </tbody>
            </table>
            <div class="clearfix"></div>
            <button class="btn btn-danger btn-xs" onclick="delete_checked()" style="margin-bottom: 15px;float:right;" >Vymazať označené</button>
            <?php
            echo getPaginationString($curr_page+1,$last_page);
             ?>
         </div>
      </div>
   </div>
</div>
		<!-- Modal na upravu užívateľov -->
		<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModal" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
		        <button type="button" class="close font-weight-light" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary">Save changes</button>
		      </div>
		    </div>
		  </div>
		</div>


     <script>

     	$(document).ready(function(){
     		/*
     		*	Function checks all checkboxes on page
     		*/
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

			/**
			 * 	Function changes GET parameter page, to change page of table
			 */
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

		/**
     	 * 	Function sends ajax to back-end to delete user with id
     	*/
     	function delete_user(id,ask=true){
     		if(ask){
     			var confirm = window.confirm("Naozaj chcete zmazať usera s ID "+id+" ?");
     			if(!confirm) return;
     		}
     		
     		var formData={
     			"action" : "delete",
     			"user_id" : id 
     		};

	     	$.ajax({
	            type: "POST",
	            url: "/ajax/admin_user_action.php",
	            data: formData,
	            dataType: "json",
	            encode: true,
	          }).done(function (data) {
	            if(data.success){
	              $("#row"+id).hide();
	            }
	            else{
	              var alert = "<div class='alert alert-warning alert-dismissible' role='alert'>" 
	              + "<a href='#' class='close font-weight-light' data-dismiss='alert' aria-label='close'>&times;</a>"
	              + data.error + "</div>";
	            }

	          });
     	}

     	/**
     	 * 	Deletes all checked rows.
     	 *	
     	*/
     	function delete_checked(){
     		var ids = new Array();
     		$('input[type=checkbox].checkthis').each(function () {
     			if(this.checked){
			    	ids.push($(this).val());
			    }
			});
			var confirm = window.confirm("Naozaj chcete zmazať všetky označené riadky ?");
     		if(!confirm) return;

     		ids.forEach(function(el,index,ids){
     			delete_user(el,false);
     		});
     	}

     </script>

   
	</body>

</html>