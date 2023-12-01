<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Loteria");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkhp.php");
include(__DIR__ . "/checkwork.php");

$unc1 = "last_winner_" . $player->serv . "";
$unc2 = "win_id_" . $player->serv . "";
$unc3 = "lottery_" . $player->serv . "";
$unc4 = "lottery_price_" . $player->serv . "";
$unc5 = "end_lotto_" . $player->serv . "";
$unc6 = "lottery_tic_" . $player->serv . "";
$unc7 = "lottery_premio_" . $player->serv . "";
$unc8 = "lotto_" . $player->serv . "";
if ($_GET['act'] == 'aposta') {
    $quantia = ceil($_POST['amount']);
    if ($setting->$unc3 != \F) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Erro</b></legend>\n";
        echo "Voc� s� pode apostar enquanto a loteria estiver fechada.<br/><a href=\"lottery.php\">Voltar</a>.";
        echo "</fieldset>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    if (!$_POST['amount']) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Erro</b></legend>\n";
        echo "Voc� precisa preencher todos os campos.<br/><a href=\"lottery.php\">Voltar</a>.";
        echo "</fieldset>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    if (!$_POST['g1'] && !$_POST['g2'] && !$_POST['g3']) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Erro</b></legend>\n";
        echo "Voc� precisa preencher todos os campos.<br/><a href=\"lottery.php\">Voltar</a>.";
        echo "</fieldset>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    if (!is_numeric($_POST['amount'])) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Erro</b></legend>\n";
        echo "A quantia que voc� deseja apostar n�o � v�lida.<br/><a href=\"lottery.php\">Voltar</a>.";
        echo "</fieldset>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    } elseif ($quantia < 1) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Erro</b></legend>\n";
        echo "A quantia que voc� deseja apostar n�o � v�lida.<br/><a href=\"lottery.php\">Voltar</a>.";
        echo "</fieldset>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    } elseif ($quantia > $player->gold) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Erro</b></legend>\n";
        echo "A quantia que voc� deseja apostar n�o � v�lida.<br/><a href=\"lottery.php\">Voltar</a>.";
        echo "</fieldset>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    } elseif ($quantia > (($player->level * 4000) - $player->totalbet)) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Erro</b></legend>\n";
        echo "Voc� est� apostando demais. Voc� ainda pode apostar " . (($player->level * 4000) - $player->totalbet) . " moedas de ouro esta semana.";
        echo " Aposte quantias mais baixas ou aguarde at� a semana que vem.<br/><a href=\"lottery.php\">Voltar</a>.";
        echo "</fieldset>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    } else {
        $sorteio = random_int(1, 3);
        if ($_POST['g1']) {
            $guerreironumber = 1;
        } elseif ($_POST['g2']) {
            $guerreironumber = 2;
        } elseif ($_POST['g3']) {
            $guerreironumber = 3;
        } else {
            include(__DIR__ . "/templates/private_header.php");
            echo "<fieldset><legend><b>Erro</b></legend>\n";
            echo "Voc� precisa preencher todos os campos.<br/><a href=\"lottery.php\">Voltar</a>.";
            echo "</fieldset>";
            include(__DIR__ . "/templates/private_footer.php");
            exit;
        }

        if ($guerreironumber == 1 && $sorteio == 1 || $guerreironumber == 2 && $sorteio == 2 || $guerreironumber == 3 && $sorteio == 3) {
            $premioaposta = ceil($quantia * 2);
            $db->execute("update `players` set `gold`=`gold`+?, `totalbet`=`totalbet`+? where `id`=?", [$premioaposta, $quantia, $player->id]);
            $player = check_user($secret_key, $db); //Get new stats
            include(__DIR__ . "/templates/private_header.php");
            echo "<fieldset><legend><b>Parab�ns</b></legend>\n";
            echo "O guerreiro em que voc� apostou venceu e voc� ganhou <b>" . $premioaposta . " de ouro</b>.<br/><a href=\"lottery.php\">Voltar</a>.";
            echo "</fieldset>";
            include(__DIR__ . "/templates/private_footer.php");
            exit;
        }
        $db->execute("update `players` set `gold`=`gold`-?, `totalbet`=`totalbet`+? where `id`=?", [$quantia, $quantia, $player->id]);
        $player = check_user($secret_key, $db);
        //Get new stats
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Apostas</b></legend>\n";
        echo "O guerreiro em que voc� apostou perdeu e voc� perdeu <b>" . $quantia . " de ouro</b>.<br/><a href=\"lottery.php\">Voltar</a>.";
        echo "</fieldset>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;

    }
}


if ($setting->$unc3 == \T) {
    if (time() > $setting->$unc5) {

        $query = $db->execute("update `settings` set `value`='f' where `name`='$unc3'");

        include(__DIR__ . "/templates/private_header.php");

        $wpaodsla = $db->execute("select * from `lotto` where `serv`=? order by RAND() limit 1", [$player->serv]);
        $ipwpwpwpa = $wpaodsla->fetchrow();

        if ($setting->$unc2 > 1000) {
            $query = $db->execute("update `players` set `bank`=`bank`+? where `id`=?", [$setting->$unc2, $ipwpwpwpa['player_id']]);
            $logmsg = "Voc� ganhou na loteria e <b>" . $setting->$unc2 . " de ouro</b> foram depositados na sua conta banc�ria.";
            addlog($ipwpwpwpa['player_id'], $logmsg, $db);
            $premiorecebido = "" . $setting->$unc2 . " de ouro";
        } else {
            $itotuuejdb = $db->execute("select `name` from `blueprint_items` where id=?", [$setting->$unc2]);
            $ioeowkewttttee = $itotuuejdb->fetchrow();

            $insert['player_id'] = $ipwpwpwpa['player_id'];
            $insert['item_id'] = $setting->$unc2;
            $query = $db->autoexecute('items', $insert, 'INSERT');
            $logmsg = "Voc� ganhou na loteria e recebeu um/uma <b>" . $ioeowkewttttee['name'] . "</b>.";
            addlog($ipwpwpwpa['player_id'], $logmsg, $db);
            $premiorecebido = $ioeowkewttttee['name'];
        }

        $medalha7 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", [$ipwpwpwpa['player_id'], \SORTUDO]);
        if ($medalha7->recordcount() < 1) {
            $insert['player_id'] = $ipwpwpwpa['player_id'];
            $insert['medalha'] = "Sortudo";
            $insert['motivo'] = "Ganhou na loteria.";
            $query = $db->autoexecute('medalhas', $insert, 'INSERT');
        }

        $peoeajjwwa = $db->execute("select `username` from `players` where `id`=?", [$ipwpwpwpa['player_id']]);
        $totkooowowow = $peoeajjwwa->fetchrow();


        $query = $db->execute("update `settings` set `value`=? where `name`='$unc1'", [$totkooowowow['username']]);
        $query = $db->execute("update `settings` set `value`=? where `name`='$unc7'", [$premiorecebido]);
        $query = $db->execute("update `settings` set `value`=0 where `name`='$unc6'");
        $query = $db->execute("update `settings` set `value`=0 where `name`='$unc5'");
        $query = $db->execute("delete from `lotto` where `serv`=?", [$player->serv]);

        echo "<fieldset><legend><b>A loteria est� fechada</b></legend>\n";
        echo "<table>";
        echo "<tr>";
        echo "<td><b>�ltimo ganhador:</b></td>";
        echo "<td>" . $totkooowowow['username'] . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td><b>Pr�mio recebido:</b></td>";
        echo "<td>" . $premiorecebido . "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</fieldset>";
        echo "<br/>";
        echo "<a href=\"home.php\">Voltar</a>.";


        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    if ($_POST['buy']) {
        $error = 0;

        if (!is_numeric($_POST['amount'])) {
            include(__DIR__ . "/templates/private_header.php");
            echo "O valor " . $_POST['for'] . " n�o � v�lido! <a href=\"lottery.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            $error = 1;
            exit;
        }

        if ($_POST['amount'] < 1) {
            include(__DIR__ . "/templates/private_header.php");
            echo "Voc� precisa digitar quantias maiores que 0! <a href=\"lottery.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            $error = 1;
            exit;
        }

        if ($_POST['amount'] > 99) {
            include(__DIR__ . "/templates/private_header.php");
            echo "Voc� pode comprar at� 99 tickes por vez! <a href=\"lottery.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            $error = 1;
            exit;
        }

        $total = ceil($_POST['amount'] * $setting->$unc4);

        if ($total > $player->gold) {
            include(__DIR__ . "/templates/private_header.php");
            echo "Voc� n�o possui ouro sufficiente! <a href=\"lottery.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            $error = 1;
            exit;
        }
        $query = $db->execute("update `players` set `gold`=? where `id`=?", [$player->gold - $total, $player->id]);
        $query = $db->execute("update `settings` set `value`=? where `name`='$unc6'", [$setting->$unc6 + $_POST['amount']]);
        $num = $_POST['amount'];
        $sql = "INSERT INTO lotto (player_id, serv) VALUES";
        for ($i = 0; $i < $num; $i++) {
            $sql .= "($player->id, $player->serv)" . (($i == $num - 1) ? "" : ", ");
        }
        $result = mysqli_query($db, $sql);
        include(__DIR__ . "/templates/private_header.php");
        echo "Voc� comprou " . $_POST['amount'] . " ticket(s) por " . $total . " de ouro. <a href=\"lottery.php\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        exit;

    }
    include(__DIR__ . "/templates/private_header.php");
    if ($setting->$unc2 < 1000) {
        $itcheckedcheckondb = $db->execute("select name, description, type, effectiveness, img, voc, needpromo, needring, needlvl from `blueprint_items` where id=?", [$setting->$unc2]);
        $itchecked = $itcheckedcheckondb->fetchrow();
        $premio = $itchecked['name'];
        $premiotype = 1;
    } else {
        $premio = "" . $setting->$unc2 . " de ouro";
        $premiotype = 2;
    }
    echo "<fieldset><legend><b>Loteria</b></legend>\n";
    echo "<table>";
    echo "<tr>";
    echo "<td><b>Pr�mio:</b></td>";
    echo "<td>" . $premio . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Tempo Restante:</b></td>";
    $end = $setting->$unc5 - time();
    $days = floor($end / 60 / 60 / 24);
    $hours = $end / 60 / 60 % 24;
    $minutes = $end / 60 % 60;
    $comecaem = "$days dia(s) $hours hora(s) $minutes minuto(s)";
    $nova_data = date("d/m/Y G:i", $setting->$unc5);
    echo "<td>" . $comecaem . " <a href=\"lottery.php\">Atualizar</a><br/><b>Dia:</b> " . $nova_data . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Pre�o por Ticket:</b></td>";
    echo "<td>" . $setting->$unc4 . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Tickets Vendidos:</b></td>";
    echo "<td>" . $setting->$unc6 . "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</fieldset>";
    echo "<br/><br/>";
    echo "<i>Compre tickets de loteria. Se seu ticket for sorteado voc� ganhar�:</i> ";
    if ($premiotype == 2) {
        echo "<b>" . $premio . "</b>.";
    } elseif ($premiotype === 1) {
        echo "<br/>";
        echo "<fieldset><legend><b>" . $itchecked['name'] . " + 0</b></legend>\n";
        if ($itchecked['optimized'] == 10) {
            echo "<table width=\"100%\" bgcolor=\"#CEBBEE\">\n";
        } else {
            echo "<table width=\"100%\">\n";
        }
        echo "<tr><td width=\"5%\">";
        echo "<img src=\"images/itens/" . $itchecked['img'] . "\"/>";
        echo "</td><td width=\"68%\">" . $itchecked['description'] . "<br />";
        echo "<b>";
        if ($itchecked['type'] == 'weapon') {
            echo "Ataque: ";
        } elseif ($itchecked['type'] == 'amulet') {
            echo "Vitalidade: ";
        } elseif ($itchecked['type'] == 'boots') {
            echo "Agilidade: ";
        } else {
            echo "Defesa: ";
        }
        echo "</b>";
        echo $itchecked['effectiveness'];
        echo "<td width=\"30%\">";
        echo "<b>Voca��o:</b> ";
        if ($itchecked['voc'] == 1 && $itchecked['needpromo'] == 'f') {
            echo "Arqueiro";
        } elseif ($itchecked['voc'] == 2 && $itchecked['needpromo'] == 'f') {
            echo "Cavaleiro";
        } elseif ($itchecked['voc'] == 3 && $itchecked['needpromo'] == 'f') {
            echo "Mago";
        } elseif ($itchecked['voc'] == 1 && $itchecked['needpromo'] == 't') {
            echo "Paladino";
        } elseif ($itchecked['voc'] == 2 && $itchecked['needpromo'] == 't') {
            echo "Cavaleiro de Elite";
        } elseif ($itchecked['voc'] == 3 && $itchecked['needpromo'] == 't') {
            echo "Feiticeiro";
        } elseif ($itchecked['voc'] == 0 && $itchecked['needpromo'] == 't') {
            echo "Voca��es superiores";
        } else {
            echo "Todas";
        }
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        if ($itchecked['needlvl'] > 1) {
            echo "<center><b><font color=\"red\">Para usar este item voc� precisa ter nivel " . $itchecked['needlvl'] . " ou mais.</font></b></center>";
        }
        if ($itchecked['needring'] == 't') {
            echo "<center><b><font color=\"red\">Para usar este item voc� precisa estar usando um Jeweled Ring.</font></b></center>";
        }
        echo "</fieldset>";
    }
    echo "<br/><br/>";
    echo "<fieldset><legend><b>Comprar Tickets</b></legend>\n";
    echo "<form method=\"POST\" action=\"lottery.php\">";
    echo "<b>Quantia:</b> <input type=\"text\" name=\"amount\" value=\"1\" size=\"10\" maxlength=\"2\"/><input type=\"submit\" name=\"buy\" value=\"Comprar\">";
    echo "</form>";
    echo "</fieldset>";
    $getlottocount = $db->execute("select `id` from `lotto` where `player_id`=?", [$player->id]);
    echo " <b>Cada ticket custa:</b> " . $setting->$unc4 . " de ouro | <b>Voc� j� comprou:</b> " . $getlottocount->recordcount() . " tickets.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}
include(__DIR__ . "/templates/private_header.php");
echo "<fieldset><legend><b>A loteria est� fechada</b></legend>\n";
echo "<table>";
echo "<tr>";
echo "<td><b>�ltimo ganhador:</b></td>";
echo "<td>" . $setting->$unc1 . "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Pr�mio recebido:</b></td>";
echo "<td>" . $setting->$unc7 . "</td>";
echo "</tr>";
echo "</table>";
echo "</fieldset>";
echo "<br/>";
echo "<fieldset><legend><b>Apostas</b></legend>\n";
echo "Enquanto a loteria est� fechada, voc� poder� apostar em um de nossos 3 guerreiros. Se o guerreiro em que voc� apostar vencer, voc� ganha o dobro do que apostou, caso contr�rio perde todo o dinheiro apostado.<br/><br/>";
echo "<form method=\"POST\" action=\"lottery.php?act=aposta\">";
echo "<center><b>Apostar:</b> <input type=\"text\" name=\"amount\" size=\"15\"/> ";
echo "<input type=\"submit\" name=\"g1\" value=\"Guerreiro I\"> ";
echo "<input type=\"submit\" name=\"g2\" value=\"Guerreiro II\"> ";
echo "<input type=\"submit\" name=\"g3\" value=\"Guerreiro III\"></center>";
echo "</fieldset>";
echo "<table width=\"100%\" align=\"center\">";
echo "<tr>";
echo "<td width=\"50%\" align=\"left\"><font size=\"1\"><b>Limite semanal de apostas:</b> " . ($player->level * 4000) . "</font></td>";
echo "<td width=\"50%\" align=\"right\"><font size=\"1\"><b>Total apostado:</b> " . $player->totalbet . "</font></td>";
echo "</tr>";
echo "</table>";
echo "<br/><br/>";
echo "<fieldset>";
echo "<legend><b>Apostar</b></legend>";
echo "<form method=\"POST\" action=\"duel.php\">";
echo "<table width=\"100%\">";
echo "<tr>";
echo "<td width=\"20%\"><b><font size=\"1\">Usu�rio:</font></b></td>";
echo "<td width=\"80%\"><input type=\"text\" name=\"rival\" /></td>";
echo "</tr><tr>";
echo "<td width=\"20%\"><b><font size=\"1\">Aposta:</font></b></td>";
echo "<td width=\"40%\"><input type=\"text\" name=\"prize\" value=\"0\"/></td>";
echo "<td width=\"40%\"><input type=\"submit\" name=\"submit\" value=\"Desafiar\" /></td></tr>";
echo "</table>";
echo "</form></fieldset>";
include(__DIR__ . "/templates/private_footer.php");
exit;
