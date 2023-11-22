<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Selecione seu Personagem");
$acc = check_acc($secret_key, $db);

$escolheper = 55;
include(__DIR__ . "/templates/acc_header.php");

$charcount = 0;
$menosespaco = 0;
?>
<style>

table.off {
background: #FFFDE0;
}

table.on {
background: #FFFAB7;
}

table.boff {
background: #FFFDE0;
}

table.bon {
background: #FFAEAE;
}

</style>

<?php
$playerstrans = $db->execute("select * from `pending` where `pending_id`=4 and `pending_other`=?", [$acc->id]);

if ($playerstrans->recordcount() > 0) {
    $change = $playerstrans->fetchrow();
    $coconta = $db->GetOne("select `conta` from `accounts` where `id`=?", [$change['player_id']]);

    if ($change['pending_time'] < time()) {
        $trocaperso = $db->execute("update `players` set `acc_id`=?, `transpass`='f' where `username`=?", [$change['player_id'], $change['pending_status']]);
        $query = $db->execute("delete from `pending` where `id`=?", [$change['id']]);
        echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">O personagem <b>" . $change['pending_status'] . "</b> foi transferido para a conta <b>" . $coconta . "</b>.</div>";
        $insert['player_id'] = $acc->id;
        $insert['msg'] = "O personagem <b>" . $change['pending_status'] . "</b> foi transferido para a conta <b>" . $coconta . "</b>.";
        $insert['time'] = time();
        $query = $db->autoexecute('account_log', $insert, 'INSERT');

        $insert['player_id'] = $change['player_id'];
        $insert['msg'] = "O personagem <b>" . $change['pending_status'] . "</b> foi transferido para sua conta.";
        $insert['time'] = time();
        $query = $db->autoexecute('account_log', $insert, 'INSERT');
    } else {
        $valortempo = $change['pending_time'] - time();
        if ($valortempo < 60) {
            $valortempo2 = $valortempo;
            $auxiliar2 = "segundo(s)";
        } elseif ($valortempo < 3600) {
            $valortempo2 = floor($valortempo / 60);
            $auxiliar2 = "minuto(s)";
        } elseif ($valortempo < 86400) {
            $valortempo2 = floor($valortempo / 3600);
            $auxiliar2 = "hora(s)";
        } elseif ($valortempo > 86400) {
            $valortempo2 = floor($valortempo / 86400);
            $auxiliar2 = "dia(s)";
        }

        echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Foi solicitado a transferência do personagem <b>" . $change['pending_status'] . "</b> para a conta <b>" . $coconta . "</b><br/>Ele será enviado em " . $valortempo2 . " " . $auxiliar2 . ". Se você não quer fazer a transferência do personagem, <a href=\"transferchar.php?cancel=true\">clique aqui</a>.</div>";
    }
    $menosespaco = 5;
}


$query04876 = $db->execute("select * from `pending` where `pending_id`=1 and `player_id`=?", [$acc->id]);

if ($query04876->recordcount() > 0) {
    $change = $query04876->fetchrow();
    if ($change['pending_time'] < time()) {
        $trocaemail = $db->execute("update `accounts` set `email`=? where `id`=?", [$change['pending_status'], $acc->id]);
        $query = $db->execute("delete from `pending` where `pending_id`=1 and `player_id`=?", [$acc->id]);
        echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Seu email foi alterado para: <b>" . $change['pending_status'] . "</b>.</div>";
        $insert['player_id'] = $acc->id;
        $insert['msg'] = "Seu email foi alterado para: <b>" . $change['pending_status'] . "</b>.";
        $insert['time'] = time();
        $query = $db->autoexecute('account_log', $insert, 'INSERT');
    } else {
        $valortempo = $change['pending_time'] - time();
        if ($valortempo < 60) {
            $valortempo2 = $valortempo;
            $auxiliar2 = "segundo(s)";
        } elseif ($valortempo < 3600) {
            $valortempo2 = floor($valortempo / 60);
            $auxiliar2 = "minuto(s)";
        } elseif ($valortempo < 86400) {
            $valortempo2 = floor($valortempo / 3600);
            $auxiliar2 = "hora(s)";
        } elseif ($valortempo > 86400) {
            $valortempo2 = floor($valortempo / 86400);
            $auxiliar2 = "dia(s)";
        }

        echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Foi solicitada a mudança de seu email para: <b>" . $change['pending_status'] . "</b><br/>Seu email será alterado em " . $valortempo2 . " " . $auxiliar2 . ". Se não quiser mais mudar de email <a href=\"changemail.php?act=cancel\">clique aqui</a>.</div>";
    }
    $menosespaco = 5;
}


$queryactivate = $db->execute("select `id` from `players` where `acc_id`=? and `level`>=?", [$acc->id, $setting->activate_level]);

if ($acc->ref != \T && $queryactivate->recordcount() > 0) {
    $query7 = $db->execute("update `players` set `gold`=`gold`+2500, `ref`=`ref`+1 where `id`=?", [$acc->ref]);
    if($setting->promo == \T) {
        $query6 = $db->execute("update `promo` set `refs`=`refs`+1 where `player_id`=?", [$acc->ref]);
    }
    $validaconta = $db->execute("update `accounts` set `ref`='t' where `id`=?", [$acc->id]);
}


$query = $db->execute("select `id`, `username`, `avatar`, `ban`, `serv` from `players` where `acc_id`=? order by `username` asc", [$acc->id]);
if ($query->recordcount() == 0) {
    if ($menosespaco != 5) {
        echo "<br/>";
    }
    echo "<br/>";
    echo "<br/>";
    echo "<center><b>Você ainda não possui nenhum personagem, <a href=\"newchar.php\">clique aqui</a> para criar um.</b></center>";
    echo "<br/>";
} else {

    if ($menosespaco != 5) {
        echo "<br/>";
    }
    echo "<br/>";

    echo "<table width=\"100%\"><tr>";
    while($member = $query->fetchrow()) {

        echo "<td>";

        if ($member['ban'] > time()) {
            echo "<table class=\"boff\" onmouseover=\"this.className='bon'\" onmouseout=\"this.className='boff'\" width=\"130px\" align=\"center\" bgcolor=\"#FFAEAE\">";
        } else {
            echo "<table class=\"off\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\" width=\"130px\" align=\"center\" bgcolor=\"#FFFDE0\">";
        }
        echo "<tr><td><div style=\"position: relative;\">";
        echo "<center><img src=\"images/backava2.png\"/>";
        echo "<a href=\"login.php?id=" . $member['id'] . "\"><img src=\"" . $member['avatar'] . "\" alt=\"" . $member['username'] . "\" width=\"105px\" height=\"105px\" style=\"position: absolute; top: 5; left: 10;\" border=\"0\"/></a></center>";
        echo "</div></td></tr>";

        if (strlen((string) $member['username']) < 14) {
            echo "<tr><td><center><b>" . $member['username'] . "</b></center></td></tr>";
        } else {
            echo "<tr><td><center><b><font size=\"1\">" . $member['username'] . "</font></b></center></td></tr>";
        }
        if ($member['ban'] > time()) {
            echo "<tr><td><center><font size=\"1\" color=\"red\"><b>Banido</b></font></center></td></tr>";
        } elseif ($member['serv'] == 1) {
            echo "<tr><td><center><font size=\"1\">(Servidor I)</font></center></td></tr>";
        } elseif ($member['serv'] == 2) {
            echo "<tr><td><center><font size=\"1\">(Servidor II)</font></center></td></tr>";
        }
        echo "</table>";

        $charcount += 1;

        echo "</td>";

        $charcount4 = $charcount / 4;

        if (is_int($charcount4)) {
            echo "</tr><tr>";
        }



    }
    echo "</tr></table>";

    echo "<br/>";

    echo "<b><table width=\"90%\" align=\"center\"><tr><td width=\"30%\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\" align=\"left\" bgcolor=\"#FFFDE0\"><font size=\"1\"><a href=\"logout.php\">Sair</a></font></td><td width=\"30%\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\" align=\"center\" bgcolor=\"#FFFDE0\"><font size=\"1\"><a href=\"newchar.php\"><b>Criar novo Personagem</b></a></font></td><td width=\"30%\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\" align=\"right\" bgcolor=\"#FFFDE0\"><font size=\"1\"><a href=\"acc_options.php\">Alterar dados da Conta</a></font></td></tr></table>";
}

$playerstrans2 = $db->execute("select * from `pending` where `pending_id`=4 and `player_id`=?", [$acc->id]);

if ($playerstrans2->recordcount() > 0) {
    $change2 = $playerstrans2->fetchrow();
    $coconta = $db->GetOne("select `conta` from `accounts` where `id`=?", [$change2['player_id']]);

    if ($change2['pending_time'] < time()) {
        $trocachare = $db->execute("update `players` set `acc_id`=?, `transpass`='f' where `username`=?", [$change2['player_id'], $change2['pending_status']]);
        $query = $db->execute("delete from `pending` where `id`=?", [$change2['id']]);
        echo "<center><font size=\"1\">O personagem <b>" . $change2['pending_status'] . "</b> foi transferido para sua conta.</font></center><br/>";

        $insert['player_id'] = $acc->id;
        $insert['msg'] = "O personagem <b>" . $change2['pending_status'] . "</b> foi transferido para sua conta.";
        $insert['time'] = time();
        $query = $db->autoexecute('account_log', $insert, 'INSERT');

        $insert['player_id'] = $change2['pending_other'];
        $insert['msg'] = "O personagem <b>" . $change2['pending_status'] . "</b> foi transferido para a conta <b>" . $coconta . "</b>.";
        $insert['time'] = time();
        $query = $db->autoexecute('account_log', $insert, 'INSERT');
    } else {
        $valortempo = $change2['pending_time'] - time();
        if ($valortempo < 60) {
            $valortempo2 = $valortempo;
            $auxiliar2 = "segundo(s)";
        } elseif ($valortempo < 3600) {
            $valortempo2 = floor($valortempo / 60);
            $auxiliar2 = "minuto(s)";
        } elseif ($valortempo < 86400) {
            $valortempo2 = floor($valortempo / 3600);
            $auxiliar2 = "hora(s)";
        } elseif ($valortempo > 86400) {
            $valortempo2 = floor($valortempo / 86400);
            $auxiliar2 = "dia(s)";
        }

        echo "<br/><center><font size=\"1\">A tranferência do personagem <b>" . $change2['pending_status'] . "</b> para sua conta acontecerá em " . $valortempo2 . " " . $auxiliar2 . ".</font></center>";
    }
}
include(__DIR__ . "/templates/acc_footer.php");
?>