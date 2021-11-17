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
      
<div class='container-fluid'>
   <div class='row'>
      <div class='col-sm-12 align-self-center pb-1'>
         <h1>Správa konferencií</h1>
      </div>
   </div>
   <div class="row">
      
		      	<?php 

		      	get_admin_sidebar();

		      	?>

		<div class="col-xl-8">
      	<?php 
      	$table = new SimpleTable("Conference",
				    	array(
				    		"table_id" => "conf", //unikatne id tabulky na stranke
				    		"ajax_url" => "/ajax/admin_conf_action.php", // ajax pre spracovanie poziadavkov
				    		"delete" => true,
				    		"edit" => true,
				    		"edit_redirect" => "/conferences/edit.php?id=",
				    		"add" => true ,
				    		"add_redirect" => "/conferences/create.php"
						)
				    );

      		$table->table_structure['id']['name'] = "ID";
      		$table->table_structure['id_user']['name'] = "Užívateľ";
      		$table->table_structure['name']['name'] = "Názov";
      		$table->table_structure['price']['name'] = "Cena";
      		$table->table_structure['capacity']['name'] = "Kapacita";

      		$table->table_structure['description']['show_column'] = false;
      		$table->table_structure['street']['show_column'] = false;
      		$table->table_structure['city']['show_column'] = false;
      		$table->table_structure['zip']['show_column'] = false;
      		$table->table_structure['state']['show_column'] = false;
      		$table->table_structure['time_from']['show_column'] = false;
      		$table->table_structure['time_to']['show_column'] = false;
      		$table->table_structure['image_url']['show_column'] = false;


      		$table->table_structure['id_user']['foreign_key'] = array(
				    	"table" => "User",
				    	"fk_key_name" => "id",
				    	"table_vars" => array("id" => "ID užívateľa", "email" => "Užívateľ"),
				    	"form_var" => "email",
				    	"custom_where" => "", // ked to je napriklad v uzivatelovi a chces obmedzit co mu da do selectu
				    );
      		
      			

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