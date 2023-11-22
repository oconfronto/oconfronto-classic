<?php

$mana = 15;
if ($player->mana < $mana) {
    $_SESSION['ataques'] .= "Você tentou lançar um feitiço mas está sem mana sufuciente.<br/>";
    $otroatak = 5;
} else {
    $curar = random_int(100, 200);
    if (($player->hp + $curar) > $player->maxhp) {
        $db->execute("update `players` set `hp`=`maxhp` where `id`=?", [$player->id]);
        $_SESSION['ataques'] .= "<font color=blue>Você fez um feitiço e recuperou toda sua vida.</font><br/>";
    } else {
        $db->execute("update `players` set `hp`=`hp`+? where `id`=?", [$curar, $player->id]);
        $_SESSION['ataques'] .= "<font color=blue>Você fez um feitiço e recuperou " . $curar . " de vida.</font><br/>";
    }
    $db->execute("update `players` set `mana`=`mana`-? where `id`=?", [$mana, $player->id]);
}
