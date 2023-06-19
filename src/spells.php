<?php
	include("lib.php");
	define("PAGENAME", "Magias");
	$player = check_user($secret_key, $db);

if ($_GET['extendmana']) {
	$magiascount = $db->execute("select * from `magias` where `player_id`=?", array($player->id));
	if ($magiascount->recordcount() > 11)
	{
		$newmana = $player->maxmana + $player->magic_points * 3;
		$updatte = $db->execute("update `players` set `mana`=`maxmana`+(`magic_points` * 3), `maxmana`=`maxmana`+(`magic_points` * 3), `extramana`=`extramana`+(`magic_points` * 3), `magic_points`=0 where `id`=?", array($player->id));
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Magias</b></legend>\n";
		echo "<i>Você extendeu sua mana máxima para " . $newmana . " por <b>" . $player->magic_points . " ponto(s) místico(s)</b>.</i><br/><br/>\n";
		echo "<a href=\"home.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
	}else{
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Esta opção apenas está disponível a quem já possui todas as magias.</i><br/><br/>\n";
		echo "<a href=\"home.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
	}

}elseif ($_GET['use']) {
		if (!is_numeric($_GET['spell']))
		{
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Um erro desconhecido ocorreu!</i><br/><br/>\n";
		echo "<a href=\"home.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
		}else{
	$getid = ceil($_GET['spell']);
	$magic = $db->execute("select * from `magias` where `id`=? and `player_id`=?", array($getid, $player->id));
		if ($magic->recordcount() < 1)
		{
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Um erro desconhecido ocorreu!</i><br/><br/>\n";
		echo "<a href=\"home.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
		}else{
    		$magia = $magic->fetchrow();
		if ($magia['magia_id'] == 5){
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Esta magia não pode ser desativada!</i><br/><br/>\n";
		echo "<a href=\"spells.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
		}

		if ($magia['used'] == 'f'){
		$db->execute("update `magias` set `used`='t' where `id`=? and `player_id`=?", array($getid, $player->id));
		header("Location: spells.php");
		}else{
		$db->execute("update `magias` set `used`='f' where `id`=? and `player_id`=?", array($getid, $player->id));
		header("Location: spells.php");
		}
		}
	
}
} elseif ($_GET['act']) {

	if (!$_GET['spell']) {
	header("Location: spells.php");
	}

	if (($_GET['spell']) and ($_GET['confirm'] != yes)) {
		if (!is_numeric($_GET['spell']))
		{
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Um erro desconhecido ocorreu!</i><br/><br/>\n";
		echo "<a href=\"home.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
		}else{
	$getid = ceil($_GET['spell']);
	$magic = $db->execute("select * from `blueprint_magias` where `id`=?", array($getid));
		if ($magic->recordcount() < 1)
		{
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Um erro desconhecido ocorreu!</i><br/><br/>\n";
		echo "<a href=\"home.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
		}


	$magic2 = $db->execute("select * from `magias` where `magia_id`=? and `player_id`=?", array($getid, $player->id));
		if ($magic2->recordcount() > 0)
		{
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você já possui esse feitiço.</i><br/><br/>\n";
		echo "<a href=\"spells.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
		}


    	$magia = $magic->fetchrow();

	include("templates/private_header.php");
	echo "<fieldset><legend><b>Magias</b></legend>\n";
	echo "<b>" . $magia['nome'] . ": " . $magia['descri'] . "</b><br/>";
	if ($magia['mana'] > 0) {
	echo "A mana nescesária para usar este feitiço é <b>" . $magia['mana'] . "</b>.<br/>";
	}else{
	echo "Este feitiço é um <b>feitiço passivo</b>. Depois de compra-lo ele ficará ativo para sempre.<br/>";
	}
	echo "Deseja comprar o feitiço <b>" . $magia['nome'] . "</b> por <b>" . $magia['cost'] . "</b> pontos místicos?<br/><br/>";
	echo "<a href=\"spells.php?act=buy&spell=" . $getid . "&confirm=yes\">Sim</a> | <a href=\"spells.php\">Não</a>";
	echo "</fieldset>";
	include("templates/private_footer.php");
		}
	} elseif (($_GET['spell']) and ($_GET['confirm'] == yes)) {
		if (!is_numeric($_GET['spell'])) 	
		{
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Um erro desconhecido ocorreu!</i><br/><br/>\n";
		echo "<a href=\"home.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
		}else{
	$getid = ceil($_GET['spell']);
	$magic = $db->execute("select * from `blueprint_magias` where `id`=?", array($getid));
		if ($magic->recordcount() < 1)
		{
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Um erro desconhecido ocorreu!</i><br/><br/>\n";
		echo "<a href=\"home.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
		}

	$magic2 = $db->execute("select * from `magias` where `magia_id`=? and `player_id`=?", array($getid, $player->id));
		if ($magic2->recordcount() > 0)
		{
		include("templates/private_header.php");
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você já possui esse feitiço.</i><br/><br/>\n";
		echo "<a href=\"spells.php\">Voltar</a>.";
	        echo "</fieldset>";
		include("templates/private_footer.php");
		exit;
		}

    	$magia = $magic->fetchrow();

		if ($magia['precisa'] != 'f'){
		$nescecita = explode (", ", $magia['precisa']);
		$verifica1 = $db->execute("select * from `magias` where `magia_id`=? and `player_id`=?", array($nescecita[0], $player->id));
		$verifica2 = $db->execute("select * from `magias` where `magia_id`=? and `player_id`=?", array($nescecita[1], $player->id));
		$verifica3 = $db->execute("select * from `magias` where `magia_id`=? and `player_id`=?", array($nescecita[2], $player->id));
		$verifica4 = $db->execute("select * from `magias` where `magia_id`=? and `player_id`=?", array($nescecita[3], $player->id));
		$verifica5 = $db->execute("select * from `magias` where `magia_id`=? and `player_id`=?", array($nescecita[4], $player->id));
		$soma = $verifica1->recordcount() + $verifica2->recordcount() + $verifica3->recordcount() + $verifica4->recordcount() + $verifica5->recordcount();
			if ($soma < 1){
			include("templates/private_header.php");
			echo "<fieldset><legend><b>Erro</b></legend>\n";
			echo "<i>Você precisa comprar os feitiços anteriores antes de comprar o feitiço <b>" . $magia['nome'] . "</b>.</i><br/><br/>\n";
			echo "<a href=\"spells.php\">Voltar</a>.";
	       		echo "</fieldset>";
			include("templates/private_footer.php");
			exit;
			}
		}

		if ($magia['cost'] > $player->magic_points){
			include("templates/private_header.php");
			echo "<fieldset><legend><b>Erro</b></legend>\n";
			echo "<i>Você não possui pontos místicos suficientes para comprar este feitiço.<br/>Você ganha 1 ponto místico a cada nível que passa.</i><br/><br/>\n";
			echo "<a href=\"spells.php\">Voltar</a>.";
	       		echo "</fieldset>";
			include("templates/private_footer.php");
			exit;
		}

		$insert['player_id'] = $player->id;
		$insert['magia_id'] = $getid;
		$db->autoexecute('magias', $insert, 'INSERT');
		$db->execute("update `players` set `magic_points`=? where `id`=?", array($player->magic_points - $magia['cost'], $player->id));


	include("templates/private_header.php");
	echo "<fieldset><legend><b>Magias</b></legend>\n";
	echo "Você acaba de comprar o feitiço <b>" . $magia['name'] . "</b> por <b>" . $magia['cost'] . "</b> pontos místicos.<br/><br/>";
	echo "<a href=\"spells.php\">Voltar</a>";
	echo "</fieldset>";
	include("templates/private_footer.php");
		}
	}


}else{
	include("templates/private_header.php");
	echo "<table width=\"265px\" align=\"center\">";
	echo "<tr>";
	echo "<td align=\"center\"><b>Magias</b></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>";

	echo "<div id=\"spells\">";

$magiasdisponiveis = $db->execute("select * from `blueprint_magias`");
while($spell = $magiasdisponiveis->fetchrow())
{
	$magia1 = $db->execute("select * from `magias` where `magia_id`=? and `player_id`=?", array($spell['id'], $player->id));

	if ($spell['mana'] > 0) {
	$mana = "<b>Mana:</b> " . $spell['mana'] . "";
	}else{
	$mana = "<b>Magia Passiva</b>";
	}

	if ($spell['id'] == 1){
	$top = 89;
	$left = 50;
	}elseif ($spell['id'] == 2){
	$top = 156;
	$left = 115;
	}elseif ($spell['id'] == 3){
	$top = 89;
	$left = 83;
	}elseif ($spell['id'] == 4){
	$top = 30;
	$left = 115;
	}elseif ($spell['id'] == 5){
	$top = 217;
	$left = 115;
	}elseif ($spell['id'] == 6){
	$top = 89;
	$left = 148;
	}elseif ($spell['id'] == 7){
	$top = 89;
	$left = 181;
	}elseif ($spell['id'] == 8){
	$top = 210;
	$left = 43;
	}elseif ($spell['id'] == 9){
	$top = 210;
	$left = 188;
	}elseif ($spell['id'] == 10){
	$top = 247;
	$left = 188;
	}elseif ($spell['id'] == 11){
	$top = 149;
	$left = 188;
	}elseif ($spell['id'] == 12){
	$top = 149;
	$left = 43;
	}

		if ($magia1->recordcount() > 0)
		{
		$usado = $magia1->fetchrow();
		echo "<img src=\"images/magias/" . $spell['id'] . ".jpg\" id=\"magia" . $spell['id'] . "\" border=\"0\"/>";
			if ($usado['used'] == 't'){
			echo "<a href=\"spells.php?use=magia&spell=" . $usado['id'] . "\"><div title=\"header=[" . $spell['nome'] . "] body=[" . $spell['descri'] . " " . $mana . "<br/><b>Clique para desativar.</b>]\"><img src=\"images/magias/border.gif\" id=\"block" . $spell['id'] . "\" border=\"0\"/></div></a>";
			}else{
			echo "<a href=\"spells.php?use=magia&spell=" . $usado['id'] . "\"><div title=\"header=[" . $spell['nome'] . "] body=[" . $spell['descri'] . " " . $mana . "<br/><b>Clique para ativar.</b>]\"><img src=\"images/magias/black.png\" id=\"block" . $spell['id'] . "\" border=\"0\"/></div></a>";
			}
		}else{
		echo "<div title=\"header=[" . $spell['nome'] . "] body=[" . $spell['descri'] . "<br/><b>Custo:</b> " . $spell['cost'] . " <b>|</b> " . $mana . "]\"><a href=\"spells.php?act=buy&spell=" . $spell['id'] . "\"><img src=\"images/magias/" . $spell['id'] . ".jpg\" id=\"magia" . $spell['id'] . "\" border=\"0\"/><img src=\"images/magias/none.png\" id=\"block" . $spell['id'] . "\" border=\"0\"/></div></a>";
		}
}


	echo "</div>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "<center><font size=\"1\">Você tem <b>" . $player->magic_points . " ponto(s) místico(s)</b>.<br/>Você ganha 1 ponto místico a cada nível que passa.</font></center>";

	$magiascount = $db->execute("select * from `magias` where `player_id`=?", array($player->id));
		if ($magiascount->recordcount() > 11)
		{
		echo "<center><font size=\"1\"><a href=\"spells.php?extendmana=true\">Clique aqui e troque seus <b>" . $player->magic_points . " ponto(s) místico(s)</b> por " . ($player->magic_points * 3) . " ponto(s) de mana.</a></font></center>";
		}


	include("templates/private_footer.php");
}
?>
