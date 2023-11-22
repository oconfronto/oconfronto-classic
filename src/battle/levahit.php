<?php

$magiaatual = $db->execute("select `magia`, `turnos` from `bixos` where `player_id`=?", [$player->id]);
$magiaatual2 = $magiaatual->fetchrow();

$misschance2 = random_int(0, 100);
if ($misschance2 <= $enemy->miss || $magiaatual2['magia'] == 6 || $magiaatual2['magia'] == 9) {
    $_SESSION['ataques'] .= "" . ucfirst((string) $enemy->prepo) . " " . $enemy->username . " tentou te atacar mas errou!<br />";
} else {
    $mak = random_int($enemy->mindmg, $enemy->maxdmg);

    if ($magiaatual2['magia'] == 2) {
        $porcento = $mak / 100;
        $porcento = ceil($porcento * 15);
        $mak += $porcento;
    } elseif ($magiaatual2['magia'] == 11) {
        $mak = ceil($mak / 2);
    } elseif ($magiaatual2['magia'] == 7) {
        $porcento = $mak / 100;
        $porcento = ceil($porcento * 20);
        $mak -= $porcento;
    }

    if ($magiaatual2['magia'] == 10) {
        if (($bixo->hp - $mak) < 1) {
            $db->execute("update `bixos` set `hp`=0 where `player_id`=?", [$player->id]);
            $_SESSION['ataques'] .= "<font color=\"green\">" . ucfirst((string) $enemy->prepo) . " " . $enemy->username . " tentou te atacar mas seu ataque voltou e ele perdeu " . $mak . " de vida.</font><br/>";
        } else {
            $db->execute("update `bixos` set `hp`=`hp`-? where `player_id`=?", [$mak, $player->id]);
            $_SESSION['ataques'] .= "<font color=\"green\">" . ucfirst((string) $enemy->prepo) . " " . $enemy->username . " tentou te atacar mas seu ataque voltou e ele perdeu " . $mak . " de vida.</font><br/>";
        }
    } else {
        $chancemagia = random_int(1, 9);
        if ($chancemagia == 9 && $enemy->magiclevel > 0) {
            $magichit = 10 + ($enemy->magiclevel * 1.35) - ($player->level * 1.25);
            $mak = ceil($mak + $magichit);
            if (($player->hp - $mak) < 1) {
                $db->execute("update `players` set `hp`=0 where `id`=?", [$player->id]);
                $_SESSION['ataques'] .= "<font color=\"purple\">" . ucfirst((string) $enemy->prepo) . " " . $enemy->username . " lançou um feitiço e você perdeu " . $mak . " de vida.</font><br/>";
                $morreu = 5;
            } else {
                $db->execute("update `players` set `hp`=`hp`-? where `id`=?", [$mak, $player->id]);
                $_SESSION['ataques'] .= "<font color=\"purple\">" . ucfirst((string) $enemy->prepo) . " " . $enemy->username . " lançou um feitiço e você perdeu " . $mak . " de vida.</font><br/>";
            }
        } elseif (($player->hp - $mak) < 1) {
            $db->execute("update `players` set `hp`=0 where `id`=?", [$player->id]);
            $_SESSION['ataques'] .= "<font color=\"red\">" . ucfirst((string) $enemy->prepo) . " " . $enemy->username . " te atacou e você perdeu " . $mak . " de vida.</font><br/>";
            $morreu = 5;
        } else {
            $db->execute("update `players` set `hp`=`hp`-? where `id`=?", [$mak, $player->id]);
            $_SESSION['ataques'] .= "<font color=\"red\">" . ucfirst((string) $enemy->prepo) . " " . $enemy->username . " te atacou e você perdeu " . $mak . " de vida.</font><br/>";
        }
    }
}
