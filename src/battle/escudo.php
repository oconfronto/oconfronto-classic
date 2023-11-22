<?php

$mana = 75;

$magiaatual = $db->GetOne("select `magia` from `bixos` where `player_id`=?", [$player->id]);

if ($player->mana < $mana) {
    $_SESSION['ataques'] .= "Você tentou lançar um feitiço mas está sem mana sufuciente.<br/>";
    $otroatak = 5;
    $mana = 0;
} elseif ($magiaatual != 0) {
    $_SESSION['ataques'] .= "Você não pode ativar um feitiço passivo enquanto outro está ativo.<br/>";
    $otroatak = 5;
    $mana = 0;
} else {

    $misschance = random_int(0, 100);
    if ($misschance <= $player->miss) {
        $_SESSION['ataques'] .= "Você tentou lançar um feitiço n" . $enemy->prepo . " " . $enemy->username . " mas errou!<br />";
    } else {
        $db->execute("update `bixos` set `magia`=? where `player_id`=?", [10, $player->id]);
        $db->execute("update `bixos` set `turnos`=? where `player_id`=?", [5, $player->id]);
        $_SESSION['ataques'] .= "<font color=\"blue\">Você lançou o feitiço escudo místico.</font><br/>";
    }

    $db->execute("update `players` set `mana`=`mana`-? where `id`=?", [$mana, $player->id]);
}
