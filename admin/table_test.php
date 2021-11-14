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
		      	<?php
				    $table = new SimpleTable("User",
				    	array(
				    		"table_id" => "users",
				    		"ajax_url" => "/ajax/admin_user_action.php",
				    		"delete" => false,
				    		"edit" => true,
				    		"add" => true
						)
				    );

				    //schovat zobrazovanie password column
				    $table->table_structure['password']['show_column'] = false;

				    //nechcem aby ho vo formulari prefillovalo
				    $table->table_structure['password']['form_edit_prefill'] = false;

				    // nahradit role 1 a 0 s vyznamom
				    $table->table_structure['role']['override'] = array(
				    	USER_ADMIN => "Admin",
				    	USER_REGULAR => "User"
				    );
				    //premenovanie stlpca v hlavicke
				    $table->table_structure['surname']['name'] = "Priezvisko";



				    echo $table->generate_table_html();
				?>
		      </div>
		   </div>
		</div>
			<?php 
			// generovanie skriptov pre konkretnu tabulku
			echo $table->generate_table_scripts();

			?>
	</body>
</html>