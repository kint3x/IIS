<?php

include "../defines.php";
include_once ROOT."/classes/user.class.php";
include_once ROOT."/classes/fileUpload.class.php";


session_start();


/**
 * 
 *  Ajax zachytí uploadovaný obrázok a nahrá ho do uploads podla mesiaca
 *  a vráti 'error' = true | false  'message' = error_mesage | cesta k súboru
 * 
*/


if(!isset($_SESSION['user'])){
     echo  json_encode(array(
            'error' => true,
            'message' => "Súbor je zlého formátu, povolené sú len jpg,jpeg a png"
        ));
        return;
}
// header json


header('Content-type: application/json');

    $file = FileUpload::upload_File($_FILES);

    if($file === false) {
        $error = true;
        $message = FileUpload::$error_message;
    }
    else{
        $error = false;
        $message = $file;
    }

echo  json_encode(array(
    'error' => $error,
    'message' => $message
));