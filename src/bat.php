<?php
	include(__DIR__ . "/lib.php");
	define("PAGENAME", "Batalhar");
	$player = check_user($secret_key, $db);
	include(__DIR__ . "/checkbattle.php");
	include(__DIR__ . "/checkhp.php");
	include(__DIR__ . "/checkwork.php");

	include(__DIR__ . "/templates/private_header.php");

if ($player->stat_points > 0 && $player->level < 15)
{
	echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Antes de batalhar, utilize seus <b>" . $player->stat_points . "</b> pontos de status disponíveis, assim você fica mais forte! <a href=\"stat_points.php\">Clique aqui para utiliza-los!</a></div>";
}

$query = $db->execute("select * from `items` where `player_id`=? and `status`='equipped'", [$player->id]);
if ($query->recordcount() < 2 && $player->level > 4 && $player->level < 20)
{
	echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Já está na hora de você comprar seus própios itens. <a href=\"shop.php\">Clique aqui e visite o ferreiro</a>.</div>";
}
?>

<fieldset><legend><b>Batalhar</b></legend>
<b>Ajudante: </b>
<i>Olá, você deseja lutar contra <a href="monster.php">monstros</a> ou lutar contra os outros <a href="battle.php">jogadores</a>?</i>

</fieldset>
<?php
	include(__DIR__ . "/templates/private_footer.php");
?>