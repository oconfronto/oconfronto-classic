<?php
$config_server = "localhost";
$config_database = "database";
$config_username = "usuariosql";
$config_password = "senhasql";
$secret_key = "895mhsdf4sf4884svdsdvs54equw"; //Secret key, make it a random word/sentence/whatever

include('adodb/adodb.inc.php'); //Include adodb files
$db = &ADONewConnection('mysql'); //Connect to database
$conn = $db->Connect($config_server, $config_username, $config_password, $config_database); //Select table

$db->SetFetchMode(ADODB_FETCH_ASSOC); //Fetch associative arrays
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; //Fetch associative arrays
//$db->debug = true; //Debug

?>
