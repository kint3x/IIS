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
	echo "Podarilo sa vytvoriť tabuľky, ale admin účet sa nevytvoril, skúste <b>spustiť skript znova</b>.<br>";
	return;
}

$db->close();

//dummy data tags

$sql = "
INSERT INTO `Tag` (`name`) VALUES ('Enviromentalistika');
INSERT INTO `Tag` (`name`) VALUES ('Šport');
INSERT INTO `Tag` (`name`) VALUES ('Informačné technológie');
INSERT INTO `Tag` (`name`) VALUES ('Duchovné');
INSERT INTO `Tag` (`name`) VALUES ('Cestovanie');
INSERT INTO `Tag` (`name`) VALUES ('Technológie');
INSERT INTO `Tag` (`name`) VALUES ('Auto-moto');
INSERT INTO `Tag` (`name`) VALUES ('Hudba');
INSERT INTO `Tag` (`name`) VALUES ('Filmy');
INSERT INTO `Tag` (`name`) VALUES ('Veda');
INSERT INTO `Tag` (`name`) VALUES ('Zdravie');";

$db = new Database();

$conn = $db->handle;


$res = $conn->multi_query($sql);

if($res === false){
	echo "Podarilo sa vytvoriť tabuľky, ale tagy sa nenahrali, skúste <b>spustiť skript znova</b>.<br>";
	return;
}

$db->close();

echo "Databáza bola úspešen nainštalovaná, Admin user login: admin@admin.sk heslo: {$pw}<br>";
echo "<b>Prosím vymažte súbory install.php a db_init.sql z bezpečnostných dôvodov</b>";

return;
