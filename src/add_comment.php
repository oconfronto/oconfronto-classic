<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Principal");
$player = check_user($secret_key, $db);

include(__DIR__ . "/templates/private_header.php");

$tbl_name="forum_question"; // Table name

if (!$_POST['detail']) {
		echo "<fieldset><legend><b>Erro</b></legend>Você precisa preencher todos os campos!<BR>";
		echo "<a href='edit_comment.php'>Voltar</a></fieldset>";
            include(__DIR__ . "/templates/private_footer.php");
            exit;
}


	$procuramengperfil = $db->execute("select `perfil` from `profile` where `player_id`=?", [$player->id]);

$topic=$_POST['detail'];
$topic2=strip_tags((string) $topic);
$texto=nl2br($topic2);

$listaExtensao = ['JPG' => 1, 'jpg' => 2, 'PNG' => 3, 'png' => 4, 'BMP' => 5, 'bmp' => 6, 'GIF' => 7, 'gif' => 8];
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





	if ($procuramengperfil->recordcount() == 0)
	{
		$insert['player_id'] = $player->id;
		$insert['perfil'] = $mostraimg;
		$upddadet = $db->autoexecute('profile', $insert, 'INSERT');

echo "<fieldset><legend><b>Sucesso</b></legend>Perfil atualizado com sucesso!<BR>";
echo "<a href=\"profile.php?id=" . $player->username . "\">Visualizar perfil</a></fieldset>";
	}
	else
	{
	$updatethecomment = $db->execute("update `profile` set `perfil`=? where `player_id`=?", [$mostraimg, $player->id]);
echo "<fieldset><legend><b>Sucesso</b></legend>Perfil atualizado com sucesso!<BR>";
echo "<a href=\"profile.php?id=" . $player->username . "\">Visualizar perfil</a></fieldset>";
	}

// get data that sent from form

mysql_close();
?>
<?php
include(__DIR__ . "/templates/private_footer.php");
?>