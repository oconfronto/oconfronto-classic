<?php
/*************************************/
/* Written by juliano coletto rotta  */
/*************************************/

include(__DIR__ . "/lib.php");
define("PAGENAME", "Treinador");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkhp.php");
include(__DIR__ . "/checkwork.php");

$points = (($player->strength) + ($player->vitality) + ($player->agility) + ($player->resistance) + ($player->stat_points) - 4);
$cost = 50000;
$newhp = (($player->maxhp) - (($player->vitality - 1) * 25));

if ($_GET['act']) {
    if ($player->gold < $cost) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Treinador</b></legend>\n";
        echo "<i><font color=\"red\">Você não tem ouro!</i><br>\n";
        echo '<a href="home.php">Voltar.</a>';
        echo "</fieldset>\n";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    $query = $db->execute("update `players` set `strength`=1, `vitality`=1, `agility`=1, `resistance`=1, `gold`=?, `stat_points`=?, `hp`=?, `maxhp`=? where `id`=?", [$player->gold - $cost, $points, $newhp, $newhp, $player->id]);
    $player = check_user($secret_key, $db);
    //Get new stats
    include(__DIR__ . "/templates/private_header.php");
    echo "<fieldset><legend><b>Treinador</b></legend>\n";
    echo "<i>Pronto, seus status foram resetados e você ganhou " . $points . " pontos de status!<br/></i>\n";
    echo '<a href="home.php">Voltar.</a>';
    echo "</fieldset>\n";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}

include(__DIR__ . "/templates/private_header.php");
?>
<fieldset><legend><b>Treinador</b></legend>
<i>Você gostaria de redistribuir seus pontos de status por <?=$cost?> de ouro?<br/><br/><a href="reset_stats.php?act=confirm">Sim</a> | <a href="home.php">Voltar</a>.</fieldset>
<?php
    include(__DIR__ . "/templates/private_footer.php");
?>