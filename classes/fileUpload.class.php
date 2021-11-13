<?php

include_once ROOT."/classes/user.class.php";

Class FileUpload{

	public static $error_message = "";

	/** Uploads file/s  to /uploads from php $_FILES **/
	public static function upload_File($file){

			
			if(is_array($file['file']['name'])){
				self::$error_message = "Viacero súborov nejde uploadnúť";
				return false;
			}

			$fileName = $file['file']['name'];
			$fileType = $file['file']['type'];
			$fileError = $file['file']['error'];
			$tmp_name = $file['file']['tmp_name'];

			$file_dir = ROOT."/uploads/";

			if($fileError == UPLOAD_ERR_OK){

				// creates folder
				if(!file_exists($file_dir)){
			        if(!mkdir($file_dir, 0777, true)){
			           self::$error_message = "Nepodarilo sa vytvoriť zložku";
			           return false;
				    }
				}
				if(!in_array($fileType,array("image/jpg","image/jpeg","image/png"))){
			        
			        self::$error_message = "Súbor je zlého formátu, povolené sú len jpg,jpeg a png";

			        return false;
			    }


		        $name = time();

		      	switch($fileType){
		      		case "image/jpg":
		      			$name.=".jpg";
		      			break;
		      		case "image/jpeg":
		      			$name.=".jpeg";
		      			break;
		      		case "image/png":
		      			$name.=".png";
		      			break;
		      	}



		        if(!move_uploaded_file($tmp_name, "$file_dir/$name")){
		        	self::$error_message = 'Interná chyba MOVE';
		        	return false;
		        }
			}
			else{
				switch($fileError) {
			        case UPLOAD_ERR_INI_SIZE:
			             self::$error_message = 'Súbor je príliš veľký.';
			            break;
			        case UPLOAD_ERR_FORM_SIZE:
			             self::$error_message = 'Súbor je príliš veľký 2.';
			            break;
			        case UPLOAD_ERR_PARTIAL:
			             self::$error_message = "Nepodarilo sa nahrať súbor.";
			            break;
			        case UPLOAD_ERR_NO_FILE:
			            self::$error_message = 'Súbor nie je validný.';
			            break;
			        case UPLOAD_ERR_NO_TMP_DIR:
			             self::$error_message = 'Vnútorná chyba TMP.';
			            break;
			        case UPLOAD_ERR_CANT_WRITE:
			             self::$error_message = 'Vnútorná chyba WRITE.';
			            break;
			        case UPLOAD_ERR_EXTENSION:
			             self::$error_message = 'Vnútorný chyba UPLOAD.';
			            break;
			        default:
			             self::$error_message = 'Nepodarilo sa dokončiť upload, error '.$fileError;
			            break;
			    }
			    return false;
			}

			return "/uploads/".$name;

	}
}