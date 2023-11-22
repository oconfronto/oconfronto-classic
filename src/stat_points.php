<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include(__DIR__ . "/lib.php");
define("PAGENAME", "Pontos de status");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkhp.php");
include(__DIR__ . "/checkwork.php");

if ($player->voc == 'archer') {
    $antigaforca = "Pontaria";
} elseif ($player->voc == 'knight') {
    $antigaforca = "Força";
} elseif ($player->voc == 'mage') {
    $antigaforca = "Magia";
}

if ($_POST['add']) {
    $error = 0;

    if (!is_numeric($_POST['for'])) {
        include(__DIR__ . "/templates/private_header.php");
        echo "O valor " . $_POST['for'] . " não é válido! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }

    if (!is_numeric($_POST['vit'])) {
        include(__DIR__ . "/templates/private_header.php");
        echo "O valor " . $_POST['vit'] . " não é válido! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }

    if (!is_numeric($_POST['agi'])) {
        include(__DIR__ . "/templates/private_header.php");
        echo "O valor " . $_POST['agi'] . " não é válido! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }

    if (!is_numeric($_POST['res'])) {
        include(__DIR__ . "/templates/private_header.php");
        echo "O valor " . $_POST['res'] . " não é válido! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }

    if ($_POST['for'] < 0) {
        include(__DIR__ . "/templates/private_header.php");
        echo "Você precisa adicionar quantias maiores que 0! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }

    if ($_POST['vit'] < 0) {
        include(__DIR__ . "/templates/private_header.php");
        echo "Você precisa adicionar quantias maiores que 0! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }


    if ($_POST['agi'] < 0) {
        include(__DIR__ . "/templates/private_header.php");
        echo "Você precisa adicionar quantias maiores que 0! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }

    if ($_POST['res'] < 0) {
        include(__DIR__ . "/templates/private_header.php");
        echo "Você precisa adicionar quantias maiores que 0! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }



    if ($_POST['for'] < 0 && $_POST['vit'] < 0 && $_POST['agi'] < 0 && $_POST['res'] < 0) {
        include(__DIR__ . "/templates/private_header.php");
        echo "Você precisa adicionar quantias maiores que 0! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }



    if ($total > $player->stat_points) {
        include(__DIR__ . "/templates/private_header.php");
        echo "Você não possui pontos de status suficientes! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }

    $total1 = ceil($_POST['for']);
    $total2 = ceil($_POST['vit']);
    $total3 = ceil($_POST['agi']);
    $total4 = ceil($_POST['res']);

    $total = ceil($total1 + $total2 + $total3 + $total4);

    if ($total > $player->stat_points) {
        include(__DIR__ . "/templates/private_header.php");
        echo "Você não possui pontos de status suficientes! <a href=\"stat_points.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        $error = 1;
        exit;
    }
    include(__DIR__ . "/templates/private_header.php");
    if ($_POST['for'] > 0) {
        $query = $db->execute("update `players` set `stat_points`=?, `strength`=? where `id`=?", [$player->stat_points - ceil($_POST['for']), $player->strength + ceil($_POST['for']), $player->id]);
        $player = check_user($secret_key, $db); //Get new stats
        echo "<b>Você aumentou sua " . $antigaforca . "! Agora está em " . $player->strength . ".</b><br />";
    }
    if ($_POST['vit'] > 0) {
        $addinghp = ceil($_POST['vit'] * 25);
        $query = $db->execute("update `players` set `stat_points`=?, `vitality`=?, `hp`=?, `maxhp`=? where `id`=?", [$player->stat_points - ceil($_POST['vit']), $player->vitality + ceil($_POST['vit']), $player->hp + $addinghp, $player->maxhp + $addinghp, $player->id]);
        $player = check_user($secret_key, $db); //Get new stats
        echo "<b>Você aumentou sua vitalidade! Agora está em " . $player->vitality . ".</b><br />";
    }
    if ($_POST['agi'] > 0) {
        $query = $db->execute("update `players` set `stat_points`=?, `agility`=? where `id`=?", [$player->stat_points - ceil($_POST['agi']), $player->agility + ceil($_POST['agi']), $player->id]);
        $player = check_user($secret_key, $db); //Get new stats
        echo "<b>Você aumentou sua agilidade! Agora está em " . $player->agility . ".</b><br />";
    }
    if ($_POST['res'] > 0) {
        $query = $db->execute("update `players` set `stat_points`=?, `resistance`=? where `id`=?", [$player->stat_points - ceil($_POST['res']), $player->resistance + ceil($_POST['res']), $player->id]);
        $player = check_user($secret_key, $db); //Get new stats
        echo "<b>Você aumentou sua resistência! Agora está em " . $player->resistance . ".</b><br />";
    }
    echo "<a href=\"stat_points.php\">Voltar</a>.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;

}

include(__DIR__ . "/templates/private_header.php");

echo $msg;

if ($player->stat_points == 0) {
    ?>
<b>Treinador:</b><br />
<i>Desculpe, mas você não tem nenhum ponto de status para utilizar.<br />
Por favor, volte quando você passar de nivel.</i><br/><a href="home.php">Voltar</a>.
<?php
} else {
    ?>
<b>Treinador:</b><br />
<i>Você tem <?=$player->stat_points?> pontos de status para usar. Em que você quer utilizar cada um deles?</i>
<br /><br />
<fieldset><legend><b>Status</b></legend>
<table>
<form method="POST" action="stat_points.php">
<tr><td><div title="header=[<?=$antigaforca?>] body=[Aumenta seu poder de ataque.]"><b><?=$antigaforca?>:</b> <?=$player->strength?></div></td><td>+ <input type="text" name="for" size="3" value="0"></td></tr>
<tr><td><div title="header=[Vitalidade] body=[Adiciona +25 à sua vida total.]"><b>Vitalidade:</b> <?=$player->vitality?></div></td><td>+ <input type="text" name="vit" size="3" value="0"></td></tr>
<tr><td><div title="header=[Agilidade] body=[Desvia de ataques de inimigos e da ataques multiplos com mais facilidade.]"><b>Agilidade:</b> <?=$player->agility?></div></td><td>+ <input type="text" name="agi" size="3" value="0"></td></tr>
<tr><td><div title="header=[Resistência] body=[Aumenta sua defesa.]"><b>Resistência:</b> <?=$player->resistance?></div></td><td>+ <input type="text" name="res" size="3" value="0">&nbsp;&nbsp;<input type="submit" name="add" value="Adicionar Status"></td></tr>
</form>
</table>
</fieldset>
<?php
}
include(__DIR__ . "/templates/private_footer.php");
?>