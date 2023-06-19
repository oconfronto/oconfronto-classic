<?php
include("../config.php");
$tb_name = "players";
mysql_connect($config_server, $config_username, $config_password) or die ("Can’t connect to Datebase");
mysql_select_db($config_database) or die ("Couldn’t successfully connected");
$email=$_POST['email'];
$email=$_POST['email_name'];
$query=("Select * from $tb_name where email='$email'");
$result= mysql_query($query);
$num=mysql_num_rows($result);
if ($num > 0) {//Username already exist
echo "no";
}else if (!preg_match("/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i", $email)){
echo "no2";
} else if (strlen($email) < 5){
echo "no3";
}else{
echo "yes";
}
?>