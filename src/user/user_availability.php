<?php

include(__DIR__ . "/../config.php");
$tb_name = "players";
mysql_connect($config_server, $config_username, $config_password) || die("Can’t connect to Datebase");
mysql_select_db($config_database) || die("Couldn’t successfully connected");
$username = $_POST['user_name'];

$pat[0] = "/^\s+/";
$pat[1] = "/\s{2,}/";
$pat[2] = "/\s+\$/";
$rep[0] = "";
$rep[1] = " ";
$rep[2] = "";
$nomedouser = ucwords(preg_replace($pat, (string) $rep, (string) $username));

$query = ("Select * from $tb_name where username='$nomedouser'");
$result = mysql_query($query);
$num = mysql_num_rows($result);
if ($num > 0) {
    //Username already exist
    echo "no";
} elseif (strlen($nomedouser) < 3) {
    echo "no2";
} elseif (strlen($nomedouser) > 20) {
    echo "no3";
} elseif (!preg_match("/^[A-Za-z[:space:]\-]+$/", (string) $username)) {
    echo "no4";
} else {
    echo "yes";
}
