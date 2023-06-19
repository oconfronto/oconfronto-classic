<?php
/*************************************/
/* Written by juliano coletto rotta  */
/*************************************/

include("lib.php");
define("PAGENAME", "Treinador");
$player = check_user($secret_key, $db);
include("checkbattle.php");
include("checkhp.php");
include("checkwork.php");
	
	$points = (($player->strength) + ($player->vitality) + ($player->agility) + ($player->resistance) + ($player->stat_points) - 4);
	$cost = 50000;
	$newhp = (($player->maxhp) - (($player->vitality - 1) * 25));
	
	if ($_GET['act'])
	{
		if ($player->gold < $cost)
		{
			include("templates/private_header.php");
			echo "<fieldset><legend><b>Treinador</b></legend>\n";
			echo "<i><font color=\"red\">Você não tem ouro!</i><br>\n";
			echo '<a href="home.php">Voltar.</a>';
                        echo "</fieldset>\n";
			include("templates/private_footer.php");
			exit;
		}
		else
		{
			$query = $db->execute("update `players` set `strength`=1, `vitality`=1, `agility`=1, `resistance`=1, `gold`=?, `stat_points`=?, `hp`=?, `maxhp`=? where `id`=?", array($player->gold - $cost, $points, $newhp, $newhp, $player->id));
			$player = check_user($secret_key, $db); //Get new stats
			include("templates/private_header.php");
			echo "<fieldset><legend><b>Treinador</b></legend>\n";
			echo "<i>Pronto, seus status foram resetados e você ganhou " . $points . " pontos de status!<br/></i>\n";
			echo '<a href="home.php">Voltar.</a>';
                        echo "</fieldset>\n";
			include("templates/private_footer.php");
			exit;
		}
	}
	
	include("templates/private_header.php");
?>
<fieldset><legend><b>Treinador</b></legend>
<i>Você gostaria de redistribuir seus pontos de status por <?=$cost?> de ouro?<br/><br/><a href="reset_stats.php?act=confirm">Sim</a> | <a href="home.php">Voltar</a>.</fieldset>
<?php
	include("templates/private_footer.php");
?>