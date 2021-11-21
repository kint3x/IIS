<?php
include "defines.php";
require_once ROOT."/classes/database.class.php";


$db = new Database();
		
if($db->error) {	
	echo "Nepodarilo sa pripojiť k DB!";
	return;
}

$conn = $db->handle;

$commands = file_get_contents(ROOT."/db_init.sql");
if($commands === false){
	echo "Nepodarilo sa načítať súbor db_init.sql";
	return;
}

$res = $conn->multi_query($commands);

if($res === false){
	echo "Nepodarilo sa vytvoriť tabuľky, nedostatočné oprávnenie alebo chyba pri spracovaní query.";
	return;
}

$db->close();
$db = new Database();

$conn = $db->handle;

$pw = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(8/strlen($x)) )),1,8);
$pw_hash = password_hash($pw, PASSWORD_DEFAULT); 
$res = $conn->query("INSERT INTO User (email,password,role) VALUES ('admin@admin.sk','{$pw_hash}',1)");

echo $conn->error;

if($res === false){
	echo "Podarilo sa vytvoriť tabuľky, ale admin účet sa nevytvoril, skúste spustiť skript znova.";
	return;
}

echo "Databáza bola úspešen nainštalovaná, Admin user login: admin@admin.sk heslo: {$pw}<br>";
echo "<b>Prosím vymažte súbory install.php a db_init.sql z bezpečnostných dôvodov</b>";

return;
