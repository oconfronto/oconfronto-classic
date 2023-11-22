<?php
include(__DIR__ . "/../lib.php");
$ipp = $_SERVER['REMOTE_ADDR'];

$checknosite = $db->execute("select * from `online` where `ip`=?", [$ipp]);

//checa se eu já tenho um registro
if ($checknosite->recordcount() < 1) {
    mysql_query('insert into online values (null, '.$ipp.', '.time().'")');
} else {
    mysql_query('update online set time="'.time().'" where ip="'.$ipp.'"');
}

//apagar inativos
mysql_query('delete from online where time < '.(time() - 15)); //aqui coloquei 30 segundos para o timeout

//retornar quantos estão online
$qr_on = mysql_query('select * from online');
echo mysql_num_rows($qr_on);
mysql_free_result($qr_on);

?>