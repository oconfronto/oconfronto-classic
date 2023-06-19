<?php
$useimage = "images/startbackground.gif";

function LoadGif ($imgname) 
{
    $im = @imagecreatefromgif ($imgname); /* Attempt to open */
    if (!$im) { /* See if it failed */
        $im = imagecreatetruecolor (150, 30); /* Create a blank image */
        $bgc = imagecolorallocate ($im, 255, 255, 255);
        $tc = imagecolorallocate ($im, 0, 0, 0);
        imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
        /* Output an errmsg */
        imagestring ($im, 1, 5, 5, "Erro carregando $imgname", $tc);
    }
    return $im;
}
header("Content-Type: image/gif");
$img = LoadGif($useimage);



  $white = imagecolorallocate($img, 255, 255, 255);
  $black = imagecolorallocate($img, 255, 255, 255);



  imagettftext($img, 10, 0, 95, 56, $white, "/path/ariblk.ttf", ucfirst($user['username']));

  imagettftext($img, 10, 0, 76, 90, $black, "/path/ariblk.ttf", $user['level']);


	if ($user['guild'] == NULL or $user['guild'] == '') {
	$gangue = Nenhum;
	}else{
	$gangue = $db->GetOne("select `name` from `guilds` where `id`=?", array($user['guild']));
	}


  imagettftext($img, 10, 0, 64, 123, $black, "/path/ariblk.ttf", $gangue);

  imagettftext($img, 10, 0, 96, 156, $black, "/path/ariblk.ttf", $voca);


imagegif($img);

}

?>
