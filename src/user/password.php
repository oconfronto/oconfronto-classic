<?php
$password=$_POST['pass_word'];

if (strlen((string) $password) < 4) {
    echo "no";
} elseif (strlen((string) $password) < 5) {
    echo "no2";
} elseif (strlen((string) $password) < 9) {
    echo "no3";
} else{
echo "yes";
}
?>
