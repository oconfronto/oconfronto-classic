<?php
$password=$_POST['password'];
$password=$_POST['pass_word'];

if (strlen($password) < 4){
echo "no";
} else if (strlen($password) < 5){
echo "no2";
} else if (strlen($password) < 9){
echo "no3";
}else{
echo "yes";
}
?>