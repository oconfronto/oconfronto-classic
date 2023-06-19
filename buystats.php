<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.bbgamezone.com/     */
/*************************************/

include("lib.php");
define("PAGENAME", "Energia");
$player = check_user($secret_key, $db);
include("checkbattle.php");
include("checkhp.php");
include("checkwork.php");

if ($player->buystats == 15)
{
	include("templates/private_header.php");
	echo "<fieldset><legend><b>Treinador</b></legend>\n";
	echo "<i>Você já comprou muitos pontos de status!</i><br/>\n";
	echo '<a href="home.php">Voltar</a>.';
	echo '</fieldset>';
	include("templates/private_footer.php");
	exit;
}
else
{
	//Add possibility of PARTIAL healing in next version?
	
	$heal = $player->buystats + 1;
	$cost = $heal * 1500; //Replace 0 with variable from settings table/file
	
	if ($_GET['act'])
	{
		if ($player->gold < $cost)
		{
			include("templates/private_header.php");
			echo "<fieldset><legend><b>Treinador</b></legend>\n";
			echo "<i>Você não tem ouro suficiente!</i><br/>\n";
			echo '<a href="home.php">Voltar</a>.';
	                echo '</fieldset>';
	 		include("templates/private_footer.php");
			exit;
		}
		else
		{
			$earnstats = intval(rand(1, 3));
			$query = $db->execute("update `players` set `gold`=?, `stat_points`=?, `buystats`=? where `id`=?", array($player->gold - $cost, $player->stat_points + $earnstats, $player->buystats + 1, $player->id));
			$player = check_user($secret_key, $db); //Get new stats
			include("templates/private_header.php");

			echo "<i>Você ganhou " . $earnstats . " ponto(s) de status!</i>\n";
			echo '<a href="home.php">Voltar</a>.';
			include("templates/private_footer.php");
			exit;
		}
	}
	
	include("templates/private_header.php");
	//Add option to change price of hospital (life to heal * set number chosen by GM in admin panel)
?>
<fieldset>
<legend><b>Treinador</b></legend>
<i>Você gostaria de treinar por apenas <b><?=$cost?></b> de ouro?</i> <a href="buystats.php?act=buy">Treinar!</a><br/>
Treinando você pode ganhar de um à três pontos de status.
<?php
	include("templates/private_footer.php");
	exit;
}
?>