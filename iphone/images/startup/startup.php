<?php
// Define o header como sendo de imagem
header("Content-type: image/jpg");
 
// Cria a imagem a partir de uma imagem jpeg
$i = imagecreatefromjpeg("startbackground.jpg");
 
// Definições
$preto = imagecolorallocate($i, 0,0,0);
$branco = imagecolorallocate($i, 255,255,255);

$texto = "    O Confronto";
$fonte = "fonts/caslonishfraxx.ttf";

$texto2 = "     Carregando...";

// Escreve na imagem
imagettftext($i, 32, 0, 20,230,$branco,$fonte,$texto);
imagettftext($i, 10, 0, 170,245,$branco,$fonte,$texto2);
 
// Gera a imagem na tela
imagejpeg($i);
 
// Destroi a imagem para liberar memória
imagedestroy($i);
?>