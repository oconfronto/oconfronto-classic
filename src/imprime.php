<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Principal");

if (!$_GET['id']) {
    include(__DIR__ . "/templates/header.php");
    echo "ID da imagem não encontrado. <a href=\"index.php\">Voltar</a>.";
    include(__DIR__ . "/templates/footer.php");
    exit;
}
$query = $db->execute("select `username`, `level`, `guild`, `voc`, `promoted` from `players` where `id`=?", [$_GET['id']]);
$user = $query->fetchrow();
if ($user['voc'] == 'archer') {
    $useimage = "localhost/archer.gif";
    if ($user['promoted'] == \F) {
        $voca = "Cacador";
    } elseif ($user['promoted'] == \P) {
        $voca = "Arqueiro Royal";
    } else {
        $voca = "Arqueiro";
    }
} elseif ($user['voc'] == 'knight') {
    $useimage = "localhost/knight.gif";
    if ($user['promoted'] != \F) {
        $voca = "Guerreiro";
    } elseif ($user['promoted'] == \P) {
        $voca = "Cavaleiro";
    } else {
        $voca = "Espadachim";
    }
} elseif ($user['voc'] == 'mage') {
    $useimage = "localhost/mage2.gif";
    if ($user['promoted'] != \F) {
        $voca = "Bruxo";
    } elseif ($user['promoted'] == \P) {
        $voca = "Arquimago";
    } else {
        $voca = "Mago";
    }
}
function LoadGif($imgname)
{
    $im = @imagecreatefromgif($imgname); /* Attempt to open */
    if (!$im) { /* See if it failed */
        $im = imagecreatetruecolor(150, 30); /* Create a blank image */
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        /* Output an errmsg */
        imagestring($im, 1, 5, 5, "Erro carregando $imgname", $tc);
    }
    return $im;
}
header("Content-Type: image/gif");
$img = LoadGif($useimage);
if ($user['voc'] == 'archer') {
    $white = imagecolorallocate($img, 255, 255, 0);
    $black = imagecolorallocate($img, 255, 255, 0);
} elseif ($user['voc'] == 'knight') {
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
} elseif ($user['voc'] == 'mage') {
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 255, 255, 255);
}
imagettftext($img, 10, 0, 95, 56, $white, "ariblk.ttf", ucfirst((string) $user['username']));
imagettftext($img, 10, 0, 76, 90, $black, "ariblk.ttf", (string) $user['level']);
if ($user['guild'] == null || $user['guild'] == '') {
    $gangue = \NENHUM;
} else {
    $gangue = $db->GetOne("select `name` from `guilds` where `id`=?", [$user['guild']]);
}
imagettftext($img, 10, 0, 64, 123, $black, "ariblk.ttf", (string) $gangue);
imagettftext($img, 10, 0, 96, 156, $black, "ariblk.ttf", (string) $voca);
imagegif($img);
