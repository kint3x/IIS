<?php

require_once ROOT."/classes/database.class.php";


class SimpleTable{

	private $db_table_name;
	private $db_table_pk;

	public $options;
	public $error_message;
	public $table_structure;




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
		$this->options['table_id'] = "myTable";

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
			if($row['Key'] == "PRI") $this->db_table_pk = $row['Field'];

			$this->table_structure[$row['Field']] = array(
				"name" => $row['Field'],
				"type" => $type,
				"editable" => $editable, //can be edited
				"form_edit_show" => true, //will be shown in edit form
				"form_edit_prefill" => true,
				"show_column" => true,
				"override" => array(),
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
       	$html .= '<th><input type="checkbox" id="'.$this->options['table_id'].'_checkall"/></th>';
       	foreach($this->table_structure as $column){
       		if(!$column['show_column']) continue;
       		$html .= '<th>'.$column['name'].'</th>';

       	}
       	//controls
       	if($this->options['edit']) $html .= '<th>Editovať</th>';
       	if($this->options['delete']) $html .= '<th>Vymazať</th>';

       	$html .= "</thead>";
       	$html .= '<tbody>';
       	foreach($rows as $row){
       		$html .= "<tr id='{$this->options['table_id']}_row_".$row[$this->db_table_pk]."'>";
       		$html .= '<td><input type="checkbox" value="'.$row[$this->db_table_pk].'" class="checkthis"/></td>';
       		foreach($row as $ckey => $column){
       			$visible = $this->table_structure[$ckey]['show_column'] ? "":"style='display:none;'";
       			
       			if(array_key_exists($column,$this->table_structure[$ckey]['override'])){
       				$column = $this->table_structure[$ckey]['override'][$column];
       			}

       			$html .= "<td col-name='$ckey' {$visible}>{$column}</td>";
       		}
       		//controls
       		if($this->options['edit']) $html .= '<td><button class="btn btn-primary btn-xs" edit-row="'.$row[$this->db_table_pk].'" data-title="Edit" data-toggle="modal" data-target="#edit'.$this->options['table_id'].'Modal" onclick="load_form_'.$this->options['table_id'].'(this)">Upraviť</span></button></td>';
       		if($this->options['delete']) $html .= '<td><button class="btn btn-danger btn-xs" onclick="delete_row_'.$this->options['table_id'].'('.$row[$this->db_table_pk].')" >Vymazať</button></td>';
       		$html .= "</tr>";
       	}
       	
       	

       	$html .= '</tbody></table><div class="clearfix"></div>';

       	if($this->options['delete']) $html.= '<button class="btn btn-danger btn-xs" onclick="delete_checked_'.$this->options['table_id'].'()" style="margin-bottom: 15px;float:right;" >Vymazať označené</button>';
       	if($this->options['edit']) $html.= self::get_table_edit_modal();

       	if($this->options['pagination']){
       		$html .= getPaginationString($curr_page+1,$last_page);
       	}
       	$html .= "</div>";
       	return $html;

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
	     			"user_id" : id 
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
	     		console.log($(btn).parent().parent().find("td").attr("col-name"));
	     		';
 			//foreach($)

	     	$scripts.='
	     	}

	     	function save_form_'.$this->options['table_id'].'(){

	     		return 0;
	     	}

			';
			

		$scripts.= "</script>";

		return $scripts;
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
		        <button type="button" class="btn btn-primary" onclick="save_form_
		        '.$this->options['table_id'].'">Save changes</button>
		      </div>
		    </div>
		  </div>
		</div>';

		return $modal;
	}

	private function generate_form_column($column,$key){
		$prefill= $column['form_edit_prefill'] ? "true" : "false";
		$editable = $column['editable'] ? "" : "readonly";
		$visible = $column['form_edit_show'] ? "" : "style='display:none;'"; 
		$is_number = count($column['override']) == 0;

		$html = "<div class='form-group' {$visible}>";
		if($column['type'] == "varchar" || ($column['type'] == "int" && $is_number) ){
			$html.="<label>{$column['name']}</label>";
			$html.="<input type='text' js-prefill='{$prefill}' class='form-control' id='form_{$this->options['table_id']}_{$key}' {$editable}>";
		}
		else if($column['type'] == "int"){
			$html.="<label>{$column['name']}</label>";
			$html.="<select class='form-control' id='form_{$this->options['table_id']}_{$key}' {$editable}>";
			foreach($column['override'] as $okey => $show) {
				$html.="<option value='{$okey}'>{$show}</option>";
			}
		
			$html.="</select>";
		}
		else if($column['type'] == "text"){
			$html.="<label>{$column['name']}</label>";
			$html.="<textarea class='form-control' id='form_{$this->options['table_id']}_{$key}' {$editable}></textarea>";
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

        $query = "SELECT * FROM ".$this->db_table_name ;

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

}