<?php

include(__DIR__ . "/lib.php");
$ipp = $_SERVER['REMOTE_ADDR'];

if ($_SESSION['userid'] > 0) {
    $player = check_user($secret_key, $db);

    $checknosite = $db->execute("select `time` from `online` where `ip`=?", [$ipp]);

    if ($checknosite->recordcount() < 1) {
        $insert['player_id'] = $player->id;
        $insert['ip'] = $ipp;
        $insert['time'] = time();
        $insert['login'] = time();
        $insert['serv'] = $player->serv;
        $insertchecknosite = $db->autoexecute('online', $insert, 'INSERT');
    } else {
        $updatechecknosite1 = $db->execute("update `online` set `time`=? where `player_id`=?", [time(), $player->id]);
        $updatechecknosite2 = $db->execute("update `login` set `time`=? where `friendid`=?", [time(), $player->id]);
    }

    $deletechecknosite1 = $db->execute("delete from `online` where `time`<?", [(time() - 20)]);
    $deletechecknosite2 = $db->execute("delete from `login` where `time`<?", [(time() - 20)]);

    $mailcount = $db->execute("select `id` from `mail` where `to`=? and `status`='unread'", [$player->id]);
    if ($mailcount->recordcount() > 0) {
        echo "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\" align=\"center\">";
        echo "<p>Voc&ecirc; tem " . $mailcount->recordcount() . " <a href=\"mail.php\">mensagem(s)</a> n&atilde;o lida(s)!</p>";
        echo "</div>";
    }

    $queryloginfriend = $db->execute("select `fname` from `friends` where `uid`=?", [$player->id]);

    while($loginfriend = $queryloginfriend->fetchrow()) {
        $frienddeide = $db->GetOne("select `id` from `players` where `username`=?", [$loginfriend['fname']]);
        $veruserfrindlogin1 = $db->execute("select `ip` from `online` where `player_id`=?", [$frienddeide]);
        $veruserfrindlogin2 = $db->execute("select `time` from `login` where `friendid`=? and `myid`=?", [$frienddeide, $player->id]);
        if ($veruserfrindlogin1->recordcount() == 1 && $veruserfrindlogin2->recordcount() == 0) {
            $insert['myid'] = $player->id;
            $insert['friendid'] = $frienddeide;
            $insert['time'] = time();
            $firndlogedadded = $db->autoexecute('login', $insert, 'INSERT');

            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
            echo "<center>Seu(a) amigo(a) <b>" . $loginfriend['fname'] . "</b> acabou de entrar.</center>";
            echo "</div>";
        }
    }

    $queryduelos = $db->execute("select * from `duels` where (`owner`=? or `rival`=?)", [$player->id, $player->id]);

    while($duinfo = $queryduelos->fetchrow()) {
        $owname = $db->GetOne("select `username` from `players` where `id`=?", [$duinfo['owner']]);
        $riname = $db->GetOne("select `username` from `players` where `id`=?", [$duinfo['rival']]);

        $rivalonline = $db->execute("select * from `online` where `player_id`=? and `serv`=?", [$duinfo['rival'], $player->serv]);
        $rionline = $rivalonline->recordcount() > 0 ? 1 : 0;

        $owneronline = $db->execute("select * from `online` where `player_id`=? and `serv`=?", [$duinfo['owner'], $player->serv]);
        $owonline = $owneronline->recordcount() > 0 ? 1 : 0;

        if ($duinfo['owner'] == $player->id && $rionline == 1) {
            echo "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
            echo "<center><a href=\"profile.php?id=" . $riname . "\">" . $riname . "</a> está online. Aguardando <a href=\"profile.php?id=" . $riname . "\">" . $riname . "</a> aceitar a proposta de duelo.</center><br/>";
            echo "<center><a href=\"duel.php?accept=" . $duinfo['id'] . "\">Cancelar proposta.</a></center><br/>";
            echo "</div>";
        }

        if ($duinfo['rival'] == $player->id && $owonline == 1) {
            echo "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
            echo "<center><a href=\"profile.php?id=" . $owname . "\">" . $owname . "</a> está online. Aguardando <a href=\"profile.php?id=" . $owname . "\">" . $owname . "</a> aceitar a proposta de duelo.</center><br/>";
            echo "<center><a href=\"duel.php?info=" . $duinfo['id'] . "\">Detalhes do duelo.</a></center><br/>";
            echo "</div>";
        }
    }


} else {
    $deletechecknosite = $db->execute("delete from `online` where `ip`=?", [$ipp]);
    echo "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
    echo "<center><b>Conex&atilde;o perdida com o servidor.</b></center>";
    echo "</div>";
}
