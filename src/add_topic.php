<?php

include("lib.php");
define("PAGENAME", "Principal");
$player = check_user($secret_key, $db);

include("checkforum.php");
include("templates/private_header.php");

if (!$_POST['detail'] or !$_POST['topic']) {
		echo "<fieldset><legend><b>Erro</b></legend>Você precisa preencher todos os campos!<BR>";
		echo "<a href=\"#\" onClick='javascript: history.back();'>Voltar</a></fieldset>";
            include("templates/private_footer.php");
            exit;
}

if ($_POST['category'] == 'none') {
		echo "<fieldset><legend><b>Erro</b></legend>Você precisa escolher uma categoria!<BR>";
		echo "<a href=\"#\" onClick='javascript: history.back();'>Voltar</a></fieldset>";
            include("templates/private_footer.php");
            exit;
}

if (($_POST['category'] != 'sugestoes') and ($_POST['category'] != 'gangues') and ($_POST['category'] != 'trade') and ($_POST['category'] != 'duvidas') and ($_POST['category'] != 'outros') and ($_POST['category'] != 'fan') and ($_POST['category'] != 'off') and ($player->gm_rank < 3)) {
		echo "<fieldset><legend><b>Erro</b></legend>Você não tem autorização para criar tópicos nesta categoria!<BR>";
		echo "<a href=\"#\" onClick='javascript: history.back();'>Voltar</a></fieldset>";
            include("templates/private_footer.php");
            exit;
}



$topic=$_POST['topic'];
$category=$_POST['category'];
$detail=$_POST['detail'];
$datetime=date("d/m/y H:i:s");

$notavel=strip_tags($detail);
$texto=nl2br($notavel);


$listaExtensao = array('JPG' => 1, 'jpg' => 2, 'PNG' => 3, 'png' => 4, 'BMP' => 5, 'bmp' => 6, 'GIF' => 7, 'gif' => 8);
$aux = " " . $texto . "";


while(true){
	$inicioImg = 0;

	$inicioImg = strpos($aux ,'[img]');
	
	if($inicioImg < 1) {
		break;
	}
	
	$fimImg = strpos($aux ,'[/img]');
	$tamanho = strlen($aux);
	$parteAnterior = substr($aux , 0, $inicioImg);
	$partePosterior = substr($aux , $fimImg+6, $tamanho-1);
	$parteLink = substr($aux ,  $inicioImg, $fimImg-$inicioImg+6);
	$extensao = substr($parteLink, strlen($parteLink)-9,3);

	if(!array_key_exists($extensao, $listaExtensao)){
		$parteLink  = '[IMG REMOVIDA]';
	}
	$textoFinal = $textoFinal.$parteAnterior.$parteLink;
	$aux = $partePosterior;


}
	$mostraimg = $textoFinal . "" . $aux;
	$mostraimg = substr($mostraimg, 1);

if (!$_POST['vota'])
{
$vota = "f";
}else{
$vota = "t";
}

$time = time();

	$insert['topic'] = $topic;
	$insert['category'] = $category;
	$insert['detail'] = $mostraimg;
	$insert['user_id'] = $player->id;
	$insert['datetime'] = $datetime;
	$insert['postado'] = $time;
	$insert['last_post'] = $time;
	$insert['last_post_date'] = $datetime;
	$insert['vota'] = $vota;
	$insert['serv'] = $player->serv;
	$result = $db->autoexecute('forum_question', $insert, 'INSERT');

$sql5 = $db->execute("update `players` set `posts`=`posts`+1 where `id`=?", array($player->id));

if($result){
echo "<fieldset><legend><b>Sucesso</b></legend>Tópico postado com sucesso!<BR>";
echo "<a href=main_forum.php?cat=" . $category . ">Visualizar mensagem</a></fieldset>";
}
else {
echo "<fieldset><legend><b>Erro</b></legend>Um erro inesperado ocorreu.<BR>";
echo "<a href=select_forum.php>Voltar</a></fieldset>";
}
mysql_close();
?>
<?php
include("templates/private_footer.php");
?>