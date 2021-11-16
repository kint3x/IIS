<?php

require_once ROOT."/classes/database.class.php";


class SimpleTable{

	private $db_table_name;
	private $db_table_pk;

	public $options;
	public $error_message;
	public $table_structure;

	public $custom_actions; 
	/*
	 array( 
	 	array( "action" => "approve", 
	 	       "send_vars" = array("id","name","...")
	 	       )
	 	)
	*/


	public function __construct($db_table_name,$options = array()){
		self::initialize_var();
		$this->db_table_name = $db_table_name;
		// nahradí zadané options existujúcimi
		$this->options= array_replace_recursive($this->options, $options);

		self::get_table_struct();

	}

	private function initialize_var(){
		$this->options['edit'] = false;
		$this->options['delete'] = false;
		$this->options['add'] = false;
		$this->options['ajax_url'] = "";
		$this->options['pagination'] = true;
		$this->options['perpage'] = 10;
		$this->options['table_id'] = "myTable"; //default tablename
		$this->options['custom_SQL'] = ""; //e.g. WHERE user = 5 ...

	}

	private function get_table_struct(){
		$db = new Database();
		if($db->error) {
			self::$error_message = 'Problém s pripojením k databáze.';
			return False;
		}

		$conn = $db->handle;

		$res = $conn->query("DESCRIBE ".$this->db_table_name);

		while($row = $res->fetch_assoc()){

			$type = "";
			if(strpos($row['Type'],"int") !== false ) $type = "int";
			if(strpos($row['Type'],"varchar") !== false ) $type = "varchar";
			if(strpos($row['Type'],"text") !== false ) $type = "text";

			// if column is unicate or not any key, it can be editable
			$editable = false;
			if(($row['Key'] == "UNI") || ($row['Key'] == "")) $editable = true;
			if($row['Key'] == "MUL") $editable = true;

			if($row['Key'] == "PRI") $this->db_table_pk = $row['Field'];

			$this->table_structure[$row['Field']] = array(
				"name" => $row['Field'], //name that shows up in form and table
				"type" => $type, // type (better not to edit)
				"editable" => $editable, //can be edited
				"form_edit_show" => true, //will be shown in edit form
				"form_edit_prefill" => true, //should be value prefilled when opening form?
				"show_column" => true, // will column show up ?
				"override" => array(), // override ints to string for example: "1" => "Admin", in table, value will be 1 but text will be Admin
				"foreign_key" => array(), // if is FK , give me
				/*
				*	array("table" => "name",
					"fk_key_name" => "id", name in Foreign table
				 "table_vars" => array("id" => "ID","name" => "MENO"..), which vars show in table
					
					"form_var" => "name" // var that will be loaded to form to choose, FK will be sent in ajax
					"custom_where" => "WHERE room = 4" // custom where when loading FK table

					)
				*
				*/
			);
			
		}
	}



	public function generate_table_html(){
		if(!isset($_GET[$this->options['table_id'].'_page'])){
            $curr_page = 0;
       	}
       	else{
       		$curr_page = $_GET[$this->options['table_id'].'_page']-1;
       	}

       	$cnt_res = Self::get_all_rows_count();

       	$last_page = intval($cnt_res/$this->options['perpage'])+1;

       	$rows = self::get_all_rows($curr_page);

       	if($this->options['table_id'] != ""){
       		$table_id_attr = 'id="'.$this->options['table_id'].'"';
       	}
       	else{
       		$table_id_attr="";
       	}
       	
       	//generate head
       	

       	$html = '<div class="table-responsive table-'.$this->options['table_id'].'"><table '.$table_id_attr.' class="table table-bordred table-striped">';
       	$html .= '<thead>';
       	if($this->options['delete'] == true)
       	$html .= '<th><input type="checkbox" id="'.$this->options['table_id'].'_checkall"/></th>';
       	foreach($this->table_structure as $column){
       		if(!$column['show_column']) continue;
       		if(count($column['foreign_key']) > 0 ){
       			foreach($column['foreign_key']['table_vars'] as $meno => $var){
       				$html .= '<th>'.$var.'</th>'; //jeden stlpec
       			}
       		}
       		else{
       			$html .= '<th>'.$column['name'].'</th>'; //jeden stlpec
       		}
       		

       	}
       	//controls
       	if($this->options['edit']) $html .= '<th>Editovať</th>';
       	if($this->options['delete']) $html .= '<th>Vymazať</th>';

       	$html .= "</thead>";
       	$html .= '<tbody>';
       	foreach($rows as $row){
       		$html .= "<tr id='{$this->options['table_id']}_row_".$row[$this->db_table_pk]."'>";
       		if($this->options['delete'] == true)
       		$html .= '<td><input type="checkbox" value="'.$row[$this->db_table_pk].'" class="checkthis"/></td>';
       		foreach($row as $ckey => $column){
       			$visible = $this->table_structure[$ckey]['show_column'] ? "":"style='display:none;'";
       			$col_val = $column;
       			if(array_key_exists($column,$this->table_structure[$ckey]['override'])){
       				$column = $this->table_structure[$ckey]['override'][$column];
       			}
       			
 				if(count($this->table_structure[$ckey]['foreign_key']) > 0 ){
 					$row=self::get_FK_row_values($this->table_structure[$ckey]['foreign_key'],$column);
 					$html .= "<td col-name='$ckey' col-val='$col_val' style='display:none;'>{$column}</td>";

       				foreach($this->table_structure[$ckey]['foreign_key']['table_vars'] as $meno => $var){
       			 			
       			 			$html .= "<td col-name='$meno' col-val='$row[$meno]'>{$row[$meno]}</td>";
       			 			
       			 	}
       			}else{
       				$html .= "<td col-name='$ckey' col-val='$col_val' {$visible}>{$column}</td>";
       			}
       			
       		}
       		//controls
       		if($this->options['edit']) $html .= '<td><button class="btn btn-primary btn-xs" edit-row="'.$row[$this->db_table_pk].'" data-title="Edit" data-toggle="modal" data-target="#edit'.$this->options['table_id'].'Modal" onclick="load_form_'.$this->options['table_id'].'(this)">Upraviť</span></button></td>';
       		if($this->options['delete']) $html .= '<td><button class="btn btn-danger btn-xs" onclick="delete_row_'.$this->options['table_id'].'('.$row[$this->db_table_pk].')" >Vymazať</button></td>';
       		$html .= "</tr>";
       	}
       	
       	

       	$html .= '</tbody></table><div class="clearfix"></div>';

       	if($this->options['delete']) $html.= '<button class="ml-2 mb-10 btn btn-danger btn-xs" onclick="delete_checked_'.$this->options['table_id'].'()" style="float:right;" >Vymazať označené</button>';
       	if($this->options['add']) $html .= '<button class="ml-2 mb-10 btn btn-success btn-xs" style="margin-bottom: 15px;float:right;" data-toggle="modal" data-target="#add'.$this->options['table_id'].'Modal">Pridať záznam</button>';
       	if($this->options['edit']) $html.= self::get_table_edit_modal();
       	if($this->options['add']) $html.= self::get_table_add_modal();

       	if($this->options['pagination']){
       		$html .= getPaginationString($curr_page+1,$last_page);
       	}
       	$html .= "</div>";
       	return $html;

	}



	private function get_table_edit_modal(){
		$modal = '
		<!-- Modal na upravu tabuľky '.$this->options['table_id'].'-->
		<div class="modal fade" id="edit'.$this->options['table_id'].'Modal" tabindex="-1" role="dialog" aria-labelledby="edit'.$this->options['table_id'].'Modal" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Úprava záznamu</h5>
		        <button type="button" class="close font-weight-light" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		      	<div id="edit'.$this->options['table_id'].'Modal_MSG"></div>
		        <form id="edit_row_'.$this->options['table_id'].'">
		        	';

		        foreach($this->table_structure as $key => $column){
		        	
		        	$modal.=self::generate_form_column($column,$key);

		        }

		        	$modal .='
		        </form>
		        <img src="/img/loading-buffering.gif" style="display:none;"/>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary" onclick="save_form_'.$this->options['table_id'].'()">Save changes</button>
		      </div>
		    </div>
		  </div>
		</div>';

		return $modal;
	}

	private function get_table_add_modal(){
		$modal = '
		<!-- Modal na pridanie zaznamu tabuľky '.$this->options['table_id'].'-->
		<div class="modal fade" id="add'.$this->options['table_id'].'Modal" tabindex="-1" role="dialog" aria-labelledby="add'.$this->options['table_id'].'Modal" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Úprava záznamu</h5>
		        <button type="button" class="close font-weight-light" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		      	<div id="add'.$this->options['table_id'].'Modal_MSG"></div>
		        <form id="add_row_'.$this->options['table_id'].'">
		        	';

		        foreach($this->table_structure as $key => $column){
		        	if($key == $this->db_table_pk) continue;
		        	$modal.=self::generate_form_column($column,$key,"add_");

		        }

		        	$modal .='
		        </form>
		        <img src="/img/loading-buffering.gif" style="display:none;"/>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary" onclick="add_form_'.$this->options['table_id'].'()">Pridať</button>
		      </div>
		    </div>
		  </div>
		</div>';

		return $modal;
	}

	private function generate_form_column($column,$key,$idprefix=""){
		$prefill= $column['form_edit_prefill'] ? "true" : "false";
		$editable = $column['editable'] ? "" : "readonly";
		$visible = $column['form_edit_show'] ? "" : "style='display:none;'"; 
		$is_number = count($column['override']) + count($column['foreign_key']) == 0;

		$html = "<div class='form-group' {$visible}>";
		if($column['type'] == "varchar" || ($column['type'] == "int" && $is_number) ){
			$html.="<label>{$column['name']}</label>";
			$html.="<input type='text' js-prefill='{$prefill}' class='form-control' id='{$idprefix}form_{$this->options['table_id']}_{$key}' {$editable}>";
		}
		else if($column['type'] == "int"){
			$html.="<label>{$column['name']}</label>";
			$html.="<select class='form-control' id='{$idprefix}form_{$this->options['table_id']}_{$key}' {$editable}  js-prefill='{$prefill}'>";
			foreach($column['override'] as $okey => $show) {
				$html.="<option value='{$okey}'>{$show}</option>";
			}

			if(count($column['foreign_key']) > 0){
				$rows = self::get_FK_all_rows($column['foreign_key']);
				foreach($rows as $row){
					$html.="<option value='{$row[$column['foreign_key']['fk_key_name']]}'>{$row[$column['foreign_key']['form_var']]}</option>";
				}
			}
		
			$html.="</select>";
		}
		else if($column['type'] == "text"){
			$html.="<label>{$column['name']}</label>";
			$html.="<textarea class='form-control' id='{$idprefix}form_{$this->options['table_id']}_{$key}' {$editable}  js-prefill='{$prefill}'></textarea>";
		}

		$html.="</div>";
		return $html;
	}


	private function get_all_rows_count(){
		$db = new Database();
        $conn = $db->handle;
        $cnt_req = $conn->query("SELECT COUNT(*) FROM ".$this->db_table_name);
        $cnt_res = $cnt_req->fetch_all()[0][0];
        $db->close();
        return $cnt_res;
	}

	private function get_all_rows($curr_page=0){
		$db = new Database();
        $conn = $db->handle;

        $query = "SELECT * FROM ".$this->db_table_name." ".$this->options['custom_SQL'] ;

        if($this->options['pagination'] ){
        	$query.= " LIMIT ".$this->options['perpage']." OFFSET ".$curr_page*$this->options['perpage'];
        }

        $req = $conn->query($query);

        $rows = array();
        while ($row = $req->fetch_assoc()){
        	$rows[] = $row;
        }
        $db->close();
        return $rows;
	}


	private function get_FK_all_rows($fk = array()){

		$db = new Database();
        $conn = $db->handle;

        $query = "SELECT * FROM ".$fk['table']." ".$fk['custom_where'] ;

        $req = $conn->query($query);

        $rows = array();
        while ($row = $req->fetch_assoc()){
        	$rows[] = $row;
        }
        $db->close();
        return $rows;
	}


	private function get_FK_row_values($fk,$val){
		$db = new Database();
        $conn = $db->handle;

        $query = "SELECT * FROM ".$fk['table']." WHERE {$fk['fk_key_name']} = '{$val}' LIMIT 1";
        $req = $conn->query($query);

        $rows = array();

        $row = $req->fetch_assoc();

        $db->close();
        return $row;
	}

	public function generate_table_scripts(){
		$scripts = "<script>";

		$scripts.='
			$(document).ready(function(){
	     		/*
	     		*	Function checks all checkboxes on page
	     		*/
				$("#'.$this->options['table_id'].' #'.$this->options['table_id'].'_checkall").click(function () {
				        if ($("#'.$this->options['table_id'].' #'.$this->options['table_id'].'_checkall").is(\':checked\')) {
				            $("#'.$this->options['table_id'].' input[type=checkbox]").each(function () {
				                $(this).prop("checked", true);
				            });

				        } else {
				            $("#'.$this->options['table_id'].' input[type=checkbox]").each(function () {
				                $(this).prop("checked", false);
				            });
				        }
				    });

				/**
				 * 	Function changes GET parameter page, to change page of table
				 */
				$(".table-'.$this->options['table_id'].' .page-link").each(function(){
					var id = $(this).attr(\'id\');
					var url = window.location.href;
					var index = url.indexOf(\'?\');
					// Remove other params
					if (index > -1) {
					   	var href = new URL(url);
						href.searchParams.set("'.$this->options['table_id'].'_page",encodeURIComponent(id));
						url = href.href;
			           
			        }
			        else{
			        	url = url + "?'.$this->options['table_id'].'_page=" + encodeURIComponent(id);
			        }

					$(this).attr("href",url);
				});

			});

			/**
	     	* 	Function sends ajax to back-end to delete user with id
	     	*/
	     	function delete_row_'.$this->options['table_id'].'(id,ask=true){
	     		if(ask){
	     			var confirm = window.confirm("Naozaj chcete riadok s ID "+id+" ?");
	     			if(!confirm) return;
	     		}
	     		
	     		var formData={
	     			"action" : "delete",
	     			"'.$this->db_table_pk.'" : id 
	     		};

		     	$.ajax({
		            type: "POST",
		            url: "'.$this->options['ajax_url'].'",
		            data: formData,
		            dataType: "json",
		            encode: true,
		          }).done(function (data) {
		            if(data.success){
		              $("#'.$this->options['table_id'].'_row_"+id).hide();
		            }
		            else{
		             	alert(data.error);
		            }

		          });
	     	}
	     	/*
	     	 * 	Deletes all checked rows.
	     	 *	
	     	*/
	     	function delete_checked_'.$this->options['table_id'].'(){
	     		var ids = new Array();
	     		$(\'.table-'.$this->options['table_id'].' input[type=checkbox].checkthis\').each(function () {
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

	     	function load_form_'.$this->options['table_id'].'(btn){
	     		$("#edit'.$this->options['table_id'].'Modal_MSG").hide();
	     		$(btn).parent().parent().find("td").each(function(){
	     			var name=$(this).attr("col-name");
	     			var val = $(this).attr("col-val");

	     			if(name === \'undefined\') return true;
	     			var element = $("#form_'.$this->options['table_id'].'_"+name);
	     			if(element.length < 1 ) return true;

	     			if($(element).attr("js-prefill") == "true"){
	     				$(element).val(val);

	     			}
	     		});

	     	}

	     	function save_form_'.$this->options['table_id'].'(){

	     		var formData={
	     			"action" : "edit",
	     		';
	     		foreach($this->table_structure as $key => $column){

	     			$scripts .= "'{$key}' : $('#form_{$this->options['table_id']}_{$key}').val() , \n";
	     		}

	     		$scripts .= '

	     		};

		     	$.ajax({
		            type: "POST",
		            url: "'.$this->options['ajax_url'].'",
		            data: formData,
		            dataType: "json",
		            encode: true,
		          }).done(function (data) {
		          	$("#edit'.$this->options['table_id'].'Modal_MSG").show();
		            if(data.success){
		              $("#edit'.$this->options['table_id'].'Modal_MSG").html(\'<div class="alert alert-success" role="alert">Úspešne zmenené<button class="close font-weight-light" data-dismiss="alert" aria-label="close">×</button></div>\');
		            location.reload(); 
		            }
		            else{
		             	$("#edit'.$this->options['table_id'].'Modal_MSG").html(\'<div class="alert alert-warning" role="alert">\'+data.error+\'<button class="close font-weight-light" data-dismiss="alert" aria-label="close">×</button></div>\');
		            }

		          });
	     	}

	     	function add_form_'.$this->options['table_id'].'(){

	     		var formData={
	     			"action" : "add",
	     		';
	     		foreach($this->table_structure as $key => $column){

	     			$scripts .= "'{$key}' : $('#add_form_{$this->options['table_id']}_{$key}').val() , \n";
	     		}

	     		$scripts .= '

	     		};

		     	$.ajax({
		            type: "POST",
		            url: "'.$this->options['ajax_url'].'",
		            data: formData,
		            dataType: "json",
		            encode: true,
		          }).done(function (data) {
		          	$("#add'.$this->options['table_id'].'Modal_MSG").show();
		            if(data.success){
		              $("#add'.$this->options['table_id'].'Modal_MSG").html(\'<div class="alert alert-success" role="alert">Úspešne pridané<button class="close font-weight-light" data-dismiss="alert" aria-label="close">×</button></div>\');
		              ';
		              	foreach($this->table_structure as $key => $column){
	     					 $scripts.= "$('#add_form_{$this->options['table_id']}_{$key}').val(''); \n";
	     				}

	     			$scripts .='
	     			location.reload(); 	
		            }
		            else{
		             	$("#add'.$this->options['table_id'].'Modal_MSG").html(\'<div class="alert alert-warning" role="alert">\'+data.error+\'<button class="close font-weight-light" data-dismiss="alert" aria-label="close">×</button></div>\');
		            }

		          });
	     	}

			';
			

		$scripts.= "</script>";

		return $scripts;
	}

}