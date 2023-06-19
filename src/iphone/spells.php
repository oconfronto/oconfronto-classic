<?php
	include("lib.php");
	define("PAGENAME", "Magias");
	$player = check_user($secret_key, $db);


	include("includes/header.php");

	echo "<div id=\"topbar\" class=\"transparent\">";
	echo "<div id=\"title\">Spells</div>";
	echo "<div id=\"leftnav\">";
	echo "<a href=\"account.php\">" . $lang['page_account'] . "</a>";
	echo "</div>";
	echo "</div>";
	echo "<div id=\"tributton\">";
	echo "<div class=\"links\">";
	echo "<a href=\"home.php\">" . $lang['char_home'] . "</a><a id=\"pressed\" href=\"spells.php\">" . $lang['char_spells'] . "</a><a href=\"inventory.html\">" . $lang['char_inventory'] . "</a>";
	echo "</div>";
	echo "</div>";

	echo "<table width=\"265px\" align=\"center\">";
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
	include("includes/footer.php");
?>
