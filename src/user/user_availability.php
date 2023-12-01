<?php

include(__DIR__ . "/../config.php");
$tb_name = "players";
$db = mysqli_connect($config_server, $config_username, $config_password);
mysqli_select_db($db, $config_database) || die("Couldn�t successfully connected");
$username = $_POST['user_name'];

$pat[0] = "/^\s+/";
$pat[1] = "/\s{2,}/";
$pat[2] = "/\s+\$/";
$rep[0] = "";
$rep[1] = " ";
$rep[2] = "";
$nomedouser = ucwords(preg_replace($pat, (string) $rep, (string) $username));

$query = ("Select * from $tb_name where username='$nomedouser'");
$result = mysqli_query($db, $query);
$num = mysqli_num_rows($result);
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
