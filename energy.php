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
include("checkwork.php");

if ($player->maxenergy > 149)
{
	include("templates/private_header.php");
	echo "<fieldset><legend><b>Hospital</b></legend>\n";
	echo "<i>Não podemos aumentar mais sua energia máxima!</i><br/>\n";
	echo '<a href="hospital.php">Retornar ao hospital.</a>';
	echo '</fieldset>';
	include("templates/private_footer.php");
	exit;
}
else
{
	//Add possibility of PARTIAL healing in next version?
	
	$heal = $player->maxenergy + 10;
		if($player->level < 30){
		$cost = ceil($heal * 15);
		}
		else if (($player->level > 29) and ($player->level < 60)){
		$cost = ceil($heal * 30);
		}else{
		$cost = ceil($heal * 45);
		}
	
	if ($_GET['act'])
	{
		if ($player->gold < $cost)
		{
			include("templates/private_header.php");
			echo "<fieldset><legend><b>Hospital</b></legend>\n";
			echo "<i>Você não tem ouro suficiente!</i><br/>\n";
			echo '<a href="hospital.php">Retornar ao hospital.</a>';
	                echo '</fieldset>';
	 		include("templates/private_footer.php");
			exit;
		}
		else
		{
			$query = $db->execute("update `players` set `gold`=?, `maxenergy`=? where `id`=?", array($player->gold - $cost, $player->maxenergy + 10, $player->id));
			$player = check_user($secret_key, $db); //Get new stats
			include("templates/private_header.php");
			echo "<i>Sua energia maxima foi aumentada por " . $cost . " de ouro.</i>\n";
			echo '<a href="hospital.php">Retornar ao hospital.</a>';
			include("templates/private_footer.php");
			exit;
		}
	}
	
	include("templates/private_header.php");
	//Add option to change price of hospital (life to heal * set number chosen by GM in admin panel)
?>
<fieldset>
<legend><b>Hospital</b></legend>
<i>Para aumentar sua energia maxima para <b><?=$player->maxenergy +10 ?></b> irá custar <b><?=$cost?></b> de ouro.</i><br />(o preço varia de acordo com seu nível, quanto menor seu nível, mais barato será para aumentar sua energia).<br/><a href="energy.php?act=heal">Aumentar!</a>
<?php
	include("templates/private_footer.php");
	exit;
}
?>