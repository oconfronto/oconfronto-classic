<?php
$config_server = "localhost";
$config_database = "database";
$config_username = "usuariosql";
$config_password = "senhsql";
$secret_key = "56sasfe8wrf7wequw";
$pass_encode = "sdf54dfssd56f4vsad56v4juyl74";

include('adodb/adodb.inc.php');
$db = &ADONewConnection('mysql');
$conn = $db->Connect($config_server, $config_username, $config_password, $config_database);

$db->SetFetchMode(ADODB_FETCH_ASSOC);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
//$db->debug = true; //Debug


include("languages/en.php");

?>