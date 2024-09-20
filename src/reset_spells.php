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


$querySpell = $db->execute("SELECT SUM(b.cost) AS total_cost FROM magias a JOIN blueprint_magias b ON a.magia_id = b.id JOIN players c ON a.player_id = c.id WHERE c.id = ?", array($player->id));
$row = $querySpell->fetchrow();
$points = intval($row["total_cost"]) - 10;
$cost = 0;

if ($_GET['act']) {
    if ($player->gold < $cost) {
        include("templates/private_header.php");
        echo "<fieldset><legend><b>Treinador</b></legend>\n";
        echo "<i><font color=\"red\">Você não tem ouro!</i><br>\n";
        echo '<a href="home.php">Voltar.</a>';
        echo "</fieldset>\n";
        include("templates/private_footer.php");
        exit;
    } else {
        $query = $db->execute("DELETE FROM magias WHERE player_id = ? AND magia_id != 4", array($player->id));
        $query3 = $db->execute("UPDATE players SET magic_points = ?, gold = ? WHERE id = ?", array($points, $player->gold - $cost, $player->id));        
        $player = check_user($secret_key, $db); //Get new stats
        include("templates/private_header.php");
        echo "<fieldset><legend><b>Treinador</b></legend>\n";
        echo "<i>Pronto, suas spells foram resetadas e você ganhou " . $points . " pontos de spells!<br/></i>\n";
        echo '<a href="home.php">Voltar.</a>';
        echo "</fieldset>\n";
        include("templates/private_footer.php");
        exit;
    }
}

include("templates/private_header.php");
?>
<fieldset>
    <legend><b>Treinador</b></legend>
    <i>Você gostaria de redistribuir seus pontos de spells por <?= $cost ?> de ouro?<br /><br /><a
            href="reset_spells.php?act=confirm">Sim</a> | <a href="home.php">Voltar</a>.
</fieldset>
<?php
include("templates/private_footer.php");
?>