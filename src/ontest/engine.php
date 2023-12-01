<?php

include(__DIR__ . "/../lib.php");
$ipp = $_SERVER['REMOTE_ADDR'];

$checknosite = $db->execute("select * from `online` where `ip`=?", [$ipp]);

//checa se eu j� tenho um registro
if ($checknosite->recordcount() < 1) {
    mysqli_query($db, 'insert into online values (null, '.$ipp.', '.time().'")');
} else {
    mysqli_query($db, 'update online set time="'.time().'" where ip="'.$ipp.'"');
}

//apagar inativos
mysqli_query($db, 'delete from online where time < '.(time() - 15)); //aqui coloquei 30 segundos para o timeout

//retornar quantos est�o online
$qr_on = mysqli_query($db, 'select * from online');
echo mysqli_num_rows($qr_on);
mysqli_free_result($qr_on);
