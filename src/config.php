<?php

$config_server = "host.docker.internal:3306";
$config_database = "database";
$config_username = "usuariosql";
$config_password = "senhasql";
$secret_key = "895mhsdf4sf4884svdsdvs54equw"; // Secret key, make it a random word/sentence/whatever

include(__DIR__ . '/adodb/adodb.inc.php'); // Include ADOdb files

// Connect to database using the mysqli driver
$db = ADONewConnection('mysqli');
$db->Connect($config_server, $config_username, $config_password, $config_database);

$db->SetFetchMode(ADODB_FETCH_ASSOC); // Fetch associative arrays
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; // Fetch associative arrays
// $db->debug = true; // Debug
