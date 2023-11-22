<?php

include(__DIR__ . "/lib.php");

$checkduels = $db->execute("select * from `duels` where `active`='t'");
$duel = $checkduels->fetchrow();

if ($duel['time'] < (time() + 45)) {
    if ($duel['turn'] == 1) {
        $winner = $duel['rival'];
        $loser = $duel['owner'];
    } elseif ($duel['turn'] == 2) {
        $winner = $duel['owner'];
        $loser = $duel['rival'];
    }

    $db->execute("update `duels` set `active`='f' where `id`=?", [$duel['id']]);

    $lossmsg = "Voc� perdeu o duelo ap�s ficar 45 segundos sem responder.<br/>";
    $winmsg = "Voc� ganhou o duelo ap�s seu oponente ficar 45 segundos sem responder.<br/>";

    if ($duel['prize'] > 0) {
        $losergold = $db->GetOne("select `bank` from `players` where `id`=?", [$loser]);
        if ($losergold < $duel['prize']) {
            $winmsg .= "Parece que seu oponente n�o tinha " . $duel['prize'] . " no banco para lhe pagar, portanto voc� s� receber� " . $losergold . ".<br/>";
            $winmsg .= "Isso � um erro, contate o administrador.";
            $db->execute("update `players` set `bank`=`bank`+? where `id`=?", [$losergold, $winner]);
            $db->execute("update `players` set `bank`=`bank`-? where `id`=?", [$losergold, $loser]);
            $lossmsg .= "Voc� perdeu " . $losergold . " de ouro.";
        } else {
            $winmsg .= "" . $duel['prize'] . " de ouro foi adicionado a sua conta banc�ria.<br/>";
            $db->execute("update `players` set `bank`=`bank`+? where `id`=?", [$duel['prize'], $winner]);
            $db->execute("update `players` set `bank`=`bank`-? where `id`=?", [$duel['prize'], $loser]);
            $lossmsg .= "Voc� perdeu " . $duel['prize'] . " de ouro.";
        }
    }

    addlog($loser, $lossmsg, $db);
    addlog($winner, $winmsg, $db);

}
