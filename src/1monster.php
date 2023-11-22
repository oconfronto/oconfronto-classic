<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Batalhar");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkhp.php");
include(__DIR__ . "/checkwork.php");

$morreu = 0;
$matou = 0;
$otroatak = 0;
$newlevell = 0;
$medalha = 0;
$grupototalbonus = 0;
$fastmagia = 0;
$fastturno = 0;
$fastlancoufeitico = 0;

if ($_GET['alterar']) {
    $modefastbattle = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\FASTBATTLE, $player->id]);
    if ($modefastbattle->recordcount() < 1) {
        $insert['player_id'] = $player->id;
        $insert['value'] = \FASTBATTLE;
        $db->autoexecute('other', $insert, 'INSERT');

        $fastbattleenemyidd = $db->GetOne("select `id` from `bixos` where `player_id`=?", [$player->id]);
        if ($fastbattleenemyidd) {
            header("Location: monster.php?act=attack&acabaluta=true&id=" . ($fastbattleenemyidd * $player->id) . "");
        } else {
            header("Location: monster.php?act=attack&acabaluta=true");
        }


    } else {
        $db->execute("delete from `other` where `value`=? and `player_id`=?", [\FASTBATTLE, $player->id]);
        $fastbattleenemyidd = $db->GetOne("select `id` from `bixos` where `player_id`=?", [$player->id]);
        if ($fastbattleenemyidd) {
            header("Location: monster.php?monster.php?act=attack&id=" . ($fastbattleenemyidd * $player->id) . "");
        } else {
            header("Location: monster.php?act=attack");
        }
    }
}


switch($_GET['act']) {
    case "attack":

        $selectbixo = $db->execute("select * from `bixos` where `player_id`=?", [$player->id]);
        if ($selectbixo->recordcount() == 0) {

            if (!$_GET['id']) {
                header("Location: monster.php");
                break;
            } else {
                $mhpp = $db->execute("select `hp` from `monsters` where `id`=?", [$_GET['id'] / $player->id]);
                if ($mhpp->recordcount() < 1) {
                    header("Location: monster.php");
                    break;
                } else {
                    $surprisequest1 = random_int(1, 500);
                    $surprisequest2 = random_int(1, 900);

                    $mhpp = $mhpp->fetchrow();

                    if ($surprisequest1 == 1 && $player->level < 40) {
                        $insert['player_id'] = $player->id;
                        $insert['id'] = $_GET['id'];
                        $insert['hp'] = 1;
                        $insert['quest'] = \T;
                        $query = $db->autoexecute('bixos', $insert, 'INSERT');
                        $quest = 1;
                    } elseif ($surprisequest2 == 1 && $player->level < 50) {
                        $insert['player_id'] = $player->id;
                        $insert['id'] = $_GET['id'];
                        $insert['hp'] = 2;
                        $insert['quest'] = \T;
                        $query = $db->autoexecute('bixos', $insert, 'INSERT');
                        $quest = 1;
                    } else {
                        $insert['player_id'] = $player->id;
                        $insert['id'] = $_GET['id'] / $player->id;
                        $insert['hp'] = $mhpp['hp'];
                        $query = $db->autoexecute('bixos', $insert, 'INSERT');
                        $quest = 0;
                    }

                    if ($quest == 1) {
                        header("Location: esquest.php");
                        exit;
                    }
                    $modefastbattle = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\FASTBATTLE, $player->id]);
                    if ($modefastbattle->recordcount() > 0) {
                        header("Location: monster.php?act=attack&acabaluta=true&id=" . $_GET['id'] ."");
                    } else {
                        header("Location: monster.php?act=attack");
                    }
                    break;
                }
            }
        } else {

            $bixo1 = $selectbixo->fetchrow();
            foreach($bixo1 as $key => $value) {
                $bixo->$key = $value;
            }

            if ($bixo->quest == 't') {
                header("Location: esquest.php");
                exit;
            }
        }

        $query1 = $db->execute("select * from `monsters` where `id`=?", [$bixo->id]);
        $enemy1 = $query1->fetchrow(); //Get monster info
        foreach($enemy1 as $key => $value) {
            $enemy->$key = $value;
        }


        $expdomonstro = $setting->eventoexp > time() ? ceil($enemy->mtexp * 3.5) : ceil($enemy->mtexp * 1.75);


        //checa os niveis
        $tolevelttyy = round($player->level * 1.8);
        if ($tolevelttyy < $enemy->level && $enemy->id != 49) {
            $query = $db->execute("delete from `bixos` where `player_id`=?", [$player->id]);
            unset($_SESSION['ataques']);
            include(__DIR__ . "/templates/private_header.php");
            echo "Você não pode atacar este monstro!</b></font> <a href=\"monster.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($enemy->evento == 'n') {
            $bixoexpec1 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`=3", [$player->id, 18]);

            if ($enemy->id != 49) {
                $query = $db->execute("delete from `bixos` where `player_id`=?", [$player->id]);
                unset($_SESSION['ataques']);
                include(__DIR__ . "/templates/private_header.php");
                echo "Este monstro não existe! <a href=\"monster.php\">Voltar</a>.";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            } elseif ($enemy->id == 49 && $bixoexpec1->recordcount() < 1) {
                $query = $db->execute("delete from `bixos` where `player_id`=?", [$player->id]);
                unset($_SESSION['ataques']);
                include(__DIR__ . "/templates/private_header.php");
                echo "Este monstro não existe! <a href=\"monster.php\">Voltar</a>.";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }
        }

        //Player cannot attack anymore
        if ($player->energy < 10) {
            $query = $db->execute("delete from `bixos` where `player_id`=?", [$player->id]);
            unset($_SESSION['ataques']);
            include(__DIR__ . "/templates/private_header.php");
            echo "<fieldset>";
            echo "<legend><b>Você está sem energia!</b></legend>\n";
            echo "Você deve descançar um pouco. <b>(1 minuto = 1 energia)</b>";
            echo "</fieldset><a href=\"monster.php\">Voltar</a>";
            echo "<br><br>";


            $query = $db->execute("select `id` from `items` where `player_id`=? and `item_id`=136 and `mark`='f' order by rand()", [$player->id]);
            $numerodepocoes = $query->recordcount();

            $query2 = $db->execute("select `id` from `items` where `player_id`=? and `item_id`=137 and `mark`='f' order by rand()", [$player->id]);
            $numerodepocoes2 = $query2->recordcount();

            $query3 = $db->execute("select `id` from `items` where `player_id`=? and `item_id`=148 and `mark`='f' order by rand()", [$player->id]);
            $numerodepocoes3 = $query3->recordcount();

            echo "<fieldset>";
            echo "<legend><b>Poções</b></legend>";
            echo "<table width=\"100%\"><tr><td><table width=\"80px\"><tr><td><div title=\"header=[Health Potion] body=[Recupera até 5 mil de vida.]\"><img src=\"images/itens/healthpotion.gif\"></div></td><td><b>x" . $numerodepocoes . "</b>";
            if ($numerodepocoes > 0) {
                $item = $query->fetchrow();
                echo "<br/><a href=\"hospt.php?act=potion&pid=" . $item['id'] . "\">Usar</a>";
            }
            echo "</td></tr></table></td>";
            echo "<td><table width=\"80px\"><tr><td><div title=\"header=[Big Health Potion] body=[Recupera até 10 mil de vida.]\"><img src=\"images/itens/bighealthpotion.gif\"></div></td><td><b>x" . $numerodepocoes3 . "</b>";
            if ($numerodepocoes3 > 0) {
                $item3 = $query3->fetchrow();
                echo "<br/><a href=\"hospt.php?act=potion&pid=" . $item3['id'] . "\">Usar</a>";
            }
            echo "</td></tr></table></td>";
            echo "<td><table width=\"80px\"><tr><td><div title=\"header=[Energy Potion] body=[Recupera até 50 de energia.]\"><img src=\"images/itens/energypotion.gif\"></div></td><td><b>x" . $numerodepocoes2 . "</b>";
            if ($numerodepocoes2 > 0) {
                $item2 = $query2->fetchrow();
                echo "<br/><a href=\"hospt.php?act=potion&pid=" . $item2['id'] . "\">Usar</a>";
            }
            echo "</td></tr></table></td><td><a href=\"hospt.php?act=sell\">Vender Poções</a></td></tr></table>";
            echo "</fieldset>";

            include(__DIR__ . "/templates/private_footer.php");
            break;
        }


        //Get player's bonuses from equipment
        $query = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='weapon' and items.status='equipped'", [$player->id]);
        $player->atkbonus = ($query->recordcount() == 1) ? $query->fetchrow() : 0;
        $query50 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='armor' and items.status='equipped'", [$player->id]);
        $player->defbonus1 = ($query50->recordcount() == 1) ? $query50->fetchrow() : 0;
        $query51 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='helmet' and items.status='equipped'", [$player->id]);
        $player->defbonus2 = ($query51->recordcount() == 1) ? $query51->fetchrow() : 0;
        $query52 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='legs' and items.status='equipped'", [$player->id]);
        $player->defbonus3 = ($query52->recordcount() == 1) ? $query52->fetchrow() : 0;
        $query54 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='shield' and items.status='equipped'", [$player->id]);
        $player->defbonus5 = ($query54->recordcount() == 1) ? $query54->fetchrow() : 0;
        $query55 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='boots' and items.status='equipped'", [$player->id]);
        $player->agibonus6 = ($query55->recordcount() == 1) ? $query55->fetchrow() : 0;


        $checamagiastatus = $db->execute("select * from `magias` where `magia_id`=5 and `player_id`=?", [$player->id]);

        if ($player->voc == 'archer') {
            if ($checamagiastatus->recordcount() > 0) {
                $varataque = 0.31;
                $vardefesa = 0.15;
                $vardivide = 0.15;
            } else {
                $varataque = 0.29;
                $vardefesa = 0.14;
                $vardivide = 0.14;
            }
        } elseif ($player->voc == 'mage') {
            if ($checamagiastatus->recordcount() > 0) {
                $varataque = 0.265;
                $vardefesa = 0.15;
                $vardivide = 0.14;
            } else {
                $varataque = 0.245;
                $vardefesa = 0.14;
                $vardivide = 0.13;
            }
        } elseif ($player->voc == 'knight') {
            if ($checamagiastatus->recordcount() > 0) {
                $varataque = 0.22;
                $vardefesa = 0.17;
                $vardivide = 0.15;
            } else {
                $varataque = 0.20;
                $vardefesa = 0.16;
                $vardivide = 0.14;
            }
        }

        if ($player->promoted == 'f') {
            $multipleatk = 1 + ($varataque * 1.6);
            $multipledef = 1 + ($vardefesa * 1.6);
            $divideres = 2.3 - ($vardivide * 1.6);
        } elseif ($player->promoted == 't') {
            $multipleatk = 1 + ($varataque * 2.4);
            $multipledef = 1 + ($vardefesa * 2.4);
            $divideres = 2.3 - ($vardivide * 2.4);
        } elseif ($player->promoted == 'r') {
            $multipleatk = 1 + ($varataque * 3.2);
            $multipledef = 1 + ($vardefesa * 3.2);
            $divideres = 2.3 - ($vardivide * 3.2);
        } elseif ($player->promoted == 's') {
            $multipleatk = 1 + ($varataque * 4);
            $multipledef = 1 + ($vardefesa * 4);
            $divideres = 2.3 - ($vardivide * 4);
        } elseif ($player->promoted == 'p') {
            $multipleatk = 1 + ($varataque * 4.8);
            $multipledef = 1 + ($vardefesa * 4.8);
            $divideres = 2.3 - ($vardivide * 4.8);
        }


        //Calculate some variables that will be used
        $forcadoplayer = ceil(($player->strength + $player->atkbonus['effectiveness'] + ($player->atkbonus['item_bonus'] * 2) + ($player->atkbonus['for'] + $player->defbonus1['for'] + $player->defbonus2['for'] + $player->defbonus3['for'] + $player->defbonus5['for'] + $player->agibonus6['for'])) * $multipleatk);
        $agilidadedoplayer = ceil($player->agility + $player->agibonus6['effectiveness'] + ($player->agibonus6['item_bonus'] * 2) + ($player->atkbonus['agi'] + $player->defbonus1['agi'] + $player->defbonus2['agi'] + $player->defbonus3['agi'] + $player->defbonus5['agi'] + $player->agibonus6['agi']));
        $resistenciadoplayer = ceil((($player->resistance + ($player->defbonus1['effectiveness'] + $player->defbonus2['effectiveness'] + $player->defbonus3['effectiveness'] + $player->defbonus5['effectiveness']) + (($player->defbonus1['item_bonus'] * 2) + ($player->defbonus2['item_bonus'] * 2) + ($player->defbonus3['item_bonus'] * 2) + ($player->defbonus5['item_bonus'] * 2)) + ($player->atkbonus['res'] + $player->defbonus1['res'] + $player->defbonus2['res'] + $player->defbonus3['res'] + $player->defbonus5['res'] + $player->agibonus6['res'])) * $multipledef) / 1.35);



        $forcadomonstro = ($enemy->strength * 1.18);
        $agilidadedomonstro = ($enemy->agility / 1.15);
        $resistenciadomonstro = ($enemy->vitality * 1.4);

        $especagi = ceil($agilidadedoplayer * 2.3);

        $enemy->strdiff = (($forcadomonstro - $forcadoplayer) > 0) ? ($forcadomonstro - $forcadoplayer) : 0;
        $enemy->resdiff = (($resistenciadomonstro - ($resistenciadoplayer * 1.5)) > 0) ? ($resistenciadomonstro - $resistenciadoplayer) : 0;
        $enemy->agidiff = (($agilidadedomonstro - $especagi) > 0) ? ($agilidadedomonstro - $especagi) : 0;
        $enemy->leveldiff = (($enemy->level - $player->level) > 0) ? ($enemy->level - $player->level) : 0;
        $player->strdiff = (($forcadoplayer - $forcadomonstro) > 0) ? ($forcadoplayer - $forcadomonstro) : 0;
        $player->resdiff = (($resistenciadoplayer - $resistenciadomonstro) > 0) ? ($resistenciadoplayer - $resistenciadomonstro) : 0;
        $player->agidiff = (($especagi - $agilidadedomonstro) > 0) ? ($especagi - $agilidadedomonstro) : 0;
        $player->leveldiff = (($player->level - $enemy->level) > 0) ? ($player->level - $enemy->level) : 0;
        $totalstr = $forcadomonstro + $forcadoplayer;
        $totalres = $resistenciadomonstro + $resistenciadoplayer;
        $totalagi = $agilidadedomonstro + $especagi;
        $totallevel = $enemy->level + $player->level;

        //Calculate the damage to be dealt by each player (dependent on strength and level)
        $enemy->maxdmg = ($forcadomonstro - ($resistenciadoplayer / $divideres));
        $enemy->maxdmg -= (int) ($enemy->maxdmg * ($player->leveldiff / $totallevel));
        $enemy->maxdmg = ($enemy->maxdmg <= 2) ? 2 : $enemy->maxdmg; //Set 2 as the minimum damage
        $enemy->mindmg = (($enemy->maxdmg - 4) < 1) ? 1 : ($enemy->maxdmg - 4); //Set a minimum damage range of maxdmg-4

        $player->maxdmg = ($forcadoplayer - ($resistenciadomonstro / 1.20));
        $player->maxdmg -= (int) ($player->maxdmg * ($enemy->leveldiff / $totallevel));
        $player->maxdmg = ($player->maxdmg <= 2) ? 2 : $player->maxdmg; //Set 2 as the minimum damage
        $player->mindmg = (($player->maxdmg - 4) < 1) ? 1 : ($player->maxdmg - 4); //Set a minimum damage range of maxdmg-4

        //Calculate battle 'combos' - how many times in a row a player can attack (dependent on agility)
        $enemy->combo = ceil($agilidadedomonstro / $especagi);
        $enemy->combo = ($enemy->combo > 3) ? 3 : $enemy->combo;
        $player->combo = ceil($especagi / $agilidadedomonstro);
        $player->combo = ($player->combo > 3) ? 3 : $player->combo;


        //Calculate the chance to miss opposing player
        $enemy->miss = (int) (($player->agidiff / $totalagi) * 100);
        $enemy->miss = ($enemy->miss > 20) ? 20 : $enemy->miss; //Maximum miss chance of 20% (possible to change in admin panel?)
        $enemy->miss = max(8, $enemy->miss); //Minimum miss chance of 5%
        $player->miss = (int) (($enemy->agidiff / $totalagi) * 100);
        $player->miss = ($player->miss > 20) ? 20 : $player->miss; //Maximum miss chance of 20%
        $player->miss = max(8, $player->miss); //Minimum miss chance of 5%


        if ($bixo->hp > 0 && $player->hp > 0) {

            if (!$_POST["batalha"] && !$_GET["correr"] && !$_GET["acabaluta"] && !$_GET["hit"] && !$_GET["magic"]) {
                $otroatak = 5;
            } elseif ($_POST["magia"] == 0 && !$_POST["ataque"] && !$_GET["correr"] && !$_GET["acabaluta"] && !$_GET["hit"] && !$_GET["magic"]) {
                $otroatak = 5;
            } elseif ($_POST["ataque"] || $_GET["hit"]) {
                include(__DIR__ . "/battle/atacahit.php");
            } elseif ($_GET["correr"]) {
                include(__DIR__ . "/battle/fugir.php");
            } elseif ($_GET["acabaluta"]) {
                while ($bixo->hp > 0 && $player->hp > 0) {
                    if ($player->hp > 0) {

                        $misschance = random_int(0, 100);
                        if ($misschance <= $player->miss) {
                            $_SESSION['ataques'] .= "Você tentou atacar " . $enemy->prepo . " " . $enemy->username . " mas errou!<br />";
                        } else {

                            $playerdamage = random_int($player->mindmg, $player->maxdmg);
                            $monsterdamage = random_int($enemy->mindmg, $enemy->maxdmg);

                            $randatacktype = random_int(1, 3);
                            $specialheal = random_int(0, 2);
                            $specialdefesadupla = random_int(0, 3);
                            $specialdoublehit = random_int(0, 4);
                            $specialmistichit = random_int(0, 6);
                            $healexists = $db->execute("select * from `magias` where `magia_id`=4 and `player_id`=? and `used`='t'", [$player->id]);
                            $defesatriplaexists = $db->execute("select * from `magias` where `magia_id`=6 and `player_id`=? and `used`='t'", [$player->id]);
                            $defesaquintaexists = $db->execute("select * from `magias` where `magia_id`=9 and `player_id`=? and `used`='t'", [$player->id]);
                            $ataquequintaexists = $db->execute("select * from `magias` where `magia_id`=8 and `player_id`=? and `used`='t'", [$player->id]);
                            $ataqueescudomistico = $db->execute("select * from `magias` where `magia_id`=10 and `player_id`=? and `used`='t'", [$player->id]);

                            if ($randatacktype == 1 || $player->hp < 100) {
                                if ($player->hp < 75 && $healexists->recordcount() > 0 && $player->mana >= 15) {
                                    include(__DIR__ . "/battle/fastbattle/curar.php");

                                } elseif ($specialheal == 1 && $player->hp < 150 && $healexists->recordcount() > 0 && $player->mana >= 15) {
                                    include(__DIR__ . "/battle/fastbattle/curar.php");

                                } elseif ($specialdefesadupla == 1 && $monsterdamage > ($playerdamage / 1.5) && $defesatriplaexists->recordcount() > 0 && $player->mana >= 60 && $fastturno === 0) {
                                    include(__DIR__ . "/battle/fastbattle/defesaquinta.php");

                                } elseif ($specialdefesadupla == 1 && $monsterdamage > ($playerdamage / 2) && $defesaquintaexists->recordcount() > 0 && $player->mana >= 25 && $fastturno === 0) {
                                    include(__DIR__ . "/battle/fastbattle/defesatripla.php");

                                } else {
                                    $bixo->hp -= $playerdamage;
                                    $_SESSION['ataques'] .= ($player->username == $enemy->username) ? "<font color=\"red\">" : "<font color=\"green\">";
                                    $_SESSION['ataques'] .= "Você atacou " . $enemy->prepo . " " . $enemy->username . " e tirou " . $playerdamage . " de vida.<br/>";
                                    $_SESSION['ataques'] .= "</font>";
                                }
                            } elseif ($randatacktype == 2 || $randatacktype == 3) {
                                if ($specialmistichit == 1 && $monsterdamage > ($playerdamage / 1.3) && $ataqueescudomistico->recordcount() > 0 && $player->mana >= 75 && $fastturno === 0) {
                                    include(__DIR__ . "/battle/fastbattle/escudo.php");

                                } elseif ($specialdoublehit == 1 && $monsterdamage > ($playerdamage / 1.5) && $ataquequintaexists->recordcount() > 0 && $player->mana >= 65) {
                                    include(__DIR__ . "/battle/fastbattle/quintohit.php");

                                } elseif ($specialdoublehit == 1 && $monsterdamage > ($playerdamage / 2) && $ataquequintaexists->recordcount() > 0 && $player->mana >= 30) {
                                    include(__DIR__ . "/battle/fastbattle/triplohit.php");
                                } else {
                                    $bixo->hp -= $playerdamage;
                                    $_SESSION['ataques'] .= ($player->username == $enemy->username) ? "<font color=\"red\">" : "<font color=\"green\">";
                                    $_SESSION['ataques'] .= "Você atacou " . $enemy->prepo . " " . $enemy->username . " e tirou " . $playerdamage . " de vida.<br/>";
                                    $_SESSION['ataques'] .= "</font>";
                                }
                            }



                            if ($bixo->hp <= 0) {
                                $matou = 5;
                            }

                        }
                    } else {
                        $matou = 5;
                    }

                    if ($bixo->hp > 0) {
                        $misschance = random_int(0, 100);
                        if ($misschance <= $enemy->miss || $fastmagia == 6 && $fastturno > 0) {
                            $_SESSION['ataques'] .= $enemy->username . " tentou te atacar mas errou!<br />";
                        } else {
                            $damage = random_int($enemy->mindmg, $enemy->maxdmg); //Calculate random damage
                            if ($fastmagia == 10 && $fastturno > 0) {
                                $bixo->hp -= $damage;
                                $_SESSION['ataques'] .= "<font color=\"green\">" . ucfirst((string) $enemy->prepo) . " " . $enemy->username . " tentou te atacar mas seu ataque voltou e ele perdeu " . $damage . " de vida.</font><br/>";
                            } else {
                                $player->hp -= $damage;
                                $_SESSION['ataques'] .= ($player->username == $enemy->username) ? "<font color=\"green\">" : "<font color=\"red\">";
                                $_SESSION['ataques'] .= ucfirst((string) $enemy->prepo) . " " . $enemy->username . " te atacou e você perdeu " . $damage . " de vida.<br/>";
                                $_SESSION['ataques'] .= "</font>";
                            }

                            if ($player->hp <= 0) {
                                $morreu = 5;
                            }
                        }
                    } else {
                        $matou = 5;
                    }

                    if ($fastturno > 0) {
                        $fastturno -= 1;
                    } elseif ($fastturno === 0) {
                        $fastmagia = 0;
                    }
                }

            } elseif ($_POST["magia"] == 1 || $_GET["magic"] == 1) {
                $checamagicum = $db->execute("select * from `magias` where `magia_id`=1 and `player_id`=?", [$player->id]);
                if ($checamagicum->recordcount() > 0) {
                    include(__DIR__ . "/battle/reforco.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 2 || $_GET["magic"] == 2) {
                $checamagicdois = $db->execute("select * from `magias` where `magia_id`=2 and `player_id`=?", [$player->id]);
                if ($checamagicdois->recordcount() > 0) {
                    include(__DIR__ . "/battle/agressivo.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 3 || $_GET["magic"] == 3) {
                $checamagitrei = $db->execute("select * from `magias` where `magia_id`=3 and `player_id`=?", [$player->id]);
                if ($checamagitrei->recordcount() > 0) {
                    include(__DIR__ . "/battle/triplohit.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 4 || $_GET["magic"] == 4) {
                $checamagcuato = $db->execute("select * from `magias` where `magia_id`=4 and `player_id`=?", [$player->id]);
                if ($checamagcuato->recordcount() > 0) {
                    include(__DIR__ . "/battle/curar.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 6 || $_GET["magic"] == 5) {
                $checamagiccivo = $db->execute("select * from `magias` where `magia_id`=6 and `player_id`=?", [$player->id]);
                if ($checamagiccivo->recordcount() > 0) {
                    include(__DIR__ . "/battle/defesatripla.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 7 || $_GET["magic"] == 6) {
                $checamagicsies = $db->execute("select * from `magias` where `magia_id`=7 and `player_id`=?", [$player->id]);
                if ($checamagicsies->recordcount() > 0) {
                    include(__DIR__ . "/battle/resistencia.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 8 || $_GET["magic"] == 7) {
                $checamagicsete = $db->execute("select * from `magias` where `magia_id`=8 and `player_id`=?", [$player->id]);
                if ($checamagicsete->recordcount() > 0) {
                    include(__DIR__ . "/battle/quintohit.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 9 || $_GET["magic"] == 8) {
                $checamagicotho = $db->execute("select * from `magias` where `magia_id`=9 and `player_id`=?", [$player->id]);
                if ($checamagicotho->recordcount() > 0) {
                    include(__DIR__ . "/battle/defesaquinta.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 10 || $_GET["magic"] == 9) {
                $checamagicumueve = $db->execute("select * from `magias` where `magia_id`=10 and `player_id`=?", [$player->id]);
                if ($checamagicumueve->recordcount() > 0) {
                    include(__DIR__ . "/battle/escudo.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 11 || $_GET["magic"] == 10) {
                $checamagidiez = $db->execute("select * from `magias` where `magia_id`=11 and `player_id`=?", [$player->id]);
                if ($checamagidiez->recordcount() > 0) {
                    include(__DIR__ . "/battle/tontura.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            } elseif ($_POST["magia"] == 12 || $_GET["magic"] == 11) {
                $checamagiconze = $db->execute("select * from `magias` where `magia_id`=12 and `player_id`=?", [$player->id]);
                if ($checamagiconze->recordcount() > 0) {
                    include(__DIR__ . "/battle/subita.php");
                } else {
                    include(__DIR__ . "/battle/atacahit.php");
                }
            }

            if ($morreu != 5 && $matou != 5 && $otroatak != 5) {
                include(__DIR__ . "/battle/levahit.php");
                include(__DIR__ . "/battle/menosturno.php");
            }

        }
        if ($bixo->hp < 1 || $matou == 5) {
            include(__DIR__ . "/battle/loot.php");

            $expwin1 = $enemy->level * 6;
            $expwin2 = (($player->level - $enemy->level) > 0) ? $expwin1 - (($player->level - $enemy->level) * 3) : $expwin1 + (($player->level - $enemy->level) * 3);
            $expwin2 = ($expwin2 <= 0) ? 1 : $expwin2;
            $expwin3 = round(0.5 * $expwin2);
            $expwin = ceil(random_int($expwin3, $expwin2));
            $goldwin = round(0.8 * $expwin);
            $goldwin = round($goldwin * 1.35);
            if ($setting->eventoouro > time()) {
                $goldwin = round($goldwin * 2);
            }
            $goldwin = round($goldwin * 1.75);

            $expgroup1 = $db->execute("select `id` from `groups` where `player_id`=?", [$player->id]);
            if ($expgroup1->recordcount() > 0) {
                $goupid = $expgroup1->fetchrow();
                $expfull = 1;
            } else {
                $expfull = 5;
            }

            if ($expfull == 1) {
                $expgroup2 = $db->execute("select * from `groups` where `id`=?", [$goupid['id']]);
                $expfull = $expgroup2->recordcount() > 1 ? 1 : 5;
            }


            if ($expfull == 1) {
                $totalgrupoquery = $db->execute("select * from `groups` where `id`=?", [$goupid['id']]);
                if ($totalgrupoquery->recordcount() > 0) {
                    while($gbbbonus = $totalgrupoquery->fetchrow()) {
                        $grupototalbonus += $gbbbonus['kills'];
                    }

                    if ($grupototalbonus > 4999 && $grupototalbonus < 15000) {
                        $cacagrupbbonus = 5;
                    } elseif ($grupototalbonus > 14999 && $grupototalbonus < 30000) {
                        $cacagrupbbonus = 10;
                    } elseif ($grupototalbonus > 29999 && $grupototalbonus < 50000) {
                        $cacagrupbbonus = 15;
                    } elseif ($grupototalbonus > 49999) {
                        $cacagrupbbonus = 20;
                    } else {
                        $cacagrupbbonus = 0;
                    }
                }
                if ($cacagrupbbonus > 0) {
                    $newexppart1 = ceil($expdomonstro / 100);
                    $expdomonstro = ceil($expdomonstro + ($newexppart1 * $cacagrupbbonus));
                }
                $query = $db->execute("update `groups` set `exp`=`exp`+?, `kills`=`kills`+1 where `player_id`=?", [$expdomonstro, $player->id]);
                $expdomonstro = ceil($expdomonstro / $expgroup2->recordcount());
                while($pexp = $expgroup2->fetchrow()) {
                    $pinfoquery = $db->execute("select * from `players` where `id`=?", [$pexp['player_id']]);
                    $pinfo = $pinfoquery->fetchrow();

                    if ($expdomonstro + $pinfo['exp'] >= $pinfo['maxexp']) { //Player gained a level!
                        $newexp = $expdomonstro + $pinfo['exp'] - $pinfo['maxexp'];

                        $plevel = ($pinfo['level'] + 1);
                        $dividecinco = ($plevel / 5);
                        $dividecinco = floor($dividecinco);

                        $ganha = 100 + ($dividecinco * 15) + $pinfo['extramana'];

                        $db->execute("update `players` set `mana`=?, `maxmana`=? where `id`=?", [$ganha, $ganha, $pinfo['id']]);

                        if ($pinfo['level'] <= 3) {
                            $expofnewlvl = $pinfo['maxexp'] + 75;
                        } else {
                            $expofnewlvl = floor(20 * ($pinfo['level'] * $pinfo['level'] * $pinfo['level']) / $pinfo['level']);
                        }

                        $query = $db->execute("update `players` set `stat_points`=`stat_points`+3, `level`=`level`+1, `maxexp`=?, `hp`=`maxhp`+30, `maxhp`=`maxhp`+30, `exp`=?, `magic_points`=`magic_points`+1, `groupmonsterkilled`=`groupmonsterkilled`+1 where `id`=?", [$expofnewlvl, $newexp, $pinfo['id']]);

                        if ($pinfo['id'] != $player->id) {
                            $logwinlvlmsg = "Você avançou um nível enquanto <a href=\"profile.php?id=" . $player->username . "\">" . $player->username . "</a> matava monstros.";
                            addlog($pinfo['id'], $logwinlvlmsg, $db);
                        }

                        if ($pinfo['id'] == $player->id) {
                            $newlevell = 5;
                        }

                    } else {
                        //Update player
                        $query = $db->execute("update `players` set `exp`=`exp`+?, `groupmonsterkilled`=`groupmonsterkilled`+1 where `id`=?", [$expdomonstro, $pinfo['id']]);
                    }
                }
                $query = $db->execute("update `players` set `gold`=`gold`+?, `hp`=?, `mana`=?, `energy`=`energy`-10, `monsterkill`=`monsterkill`+1 where `id`=?", [$goldwin, $player->hp, $player->mana, $player->id]);
            } elseif ($expdomonstro + $player->exp >= $player->maxexp) {
                //Player gained a level!
                //Update player, gained a level
                $newlevell = 5;
                $newexp = $expdomonstro + $player->exp - $player->maxexp;
                $plevel = ($player->level + 1);
                $dividecinco = ($plevel / 5);
                $dividecinco = floor($dividecinco);
                $ganha = 100 + ($dividecinco * 15) + $player->extramana;
                $db->execute("update `players` set `mana`=?, `maxmana`=? where `id`=?", [$ganha, $ganha, $player->id]);
                if ($player->level <= 3) {
                    $expofnewlvl = $player->maxexp + 75;
                } else {
                    $expofnewlvl = floor(20 * ($player->level * $player->level * $player->level) / $player->level);
                }
                $query = $db->execute("update `players` set `stat_points`=`stat_points`+3, `level`=`level`+1, `maxexp`=?, `hp`=`maxhp`+30, `maxhp`=`maxhp`+30, `exp`=?, `magic_points`=`magic_points`+1, `energy`=`energy`-10, `gold`=?, `monsterkill`=`monsterkill`+1, `monsterkilled`=`monsterkilled`+1 where `id`=?", [$expofnewlvl, $newexp, $player->gold + $goldwin, $player->id]);
            } else {
                //Update player
                $query = $db->execute("update `players` set `exp`=`exp`+?, `gold`=`gold`+?, `hp`=?, `mana`=?, `energy`=`energy`-10, `monsterkill`=`monsterkill`+1, `monsterkilled`=`monsterkilled`+1 where `id`=?", [$expdomonstro, $goldwin, $player->hp, $player->mana, $player->id]);
            }

            if ($lootstatus == 5) {
                $insert['player_id'] = $player->id;
                $insert['item_id'] = $loot_id;
                $addlootitemwin = $db->autoexecute('items', $insert, 'INSERT');
                $id = $db->Insert_ID();
                $status = $db->execute("update `items` set `for`=`for`+?, `vit`=`vit`+?, `agi`=`agi`+?, `res`=`res`+? where `id`=?", [$lootbonus1, $lootbonus2, $lootbonus3, $lootbonus4, $id]);
            }


            if ($enemy->id == 49) {

                $query = $db->execute("update `quests` set `quest_status`=? where `player_id`=? and `quest_id`=?", [80, $player->id, 18]);

                $insert['player_id'] = $player->id;
                $insert['item_id'] = 160;
                $addlootitemwin = $db->autoexecute('items', $insert, 'INSERT');
            }


            if ($enemy->username == \ZEUS) {
                $medalha10 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", [$player->id, 'Lendário']);
                if ($medalha10->recordcount() < 1) {
                    $medalha = 10;
                    $medalhamsg = "Você matou Zeus e uma medalha foi adicionada ao seu perfil por este motivo.";
                    $insert['player_id'] = $player->id;
                    $insert['medalha'] = "Lendário";
                    $insert['motivo'] = "Matou o poderoso Zeus.";
                    $query = $db->autoexecute('medalhas', $insert, 'INSERT');
                }
            }


            $query = $db->execute("delete from `bixos` where `player_id`=?", [$player->id]);
            $_SESSION['ataques'] .= "<u><b>Você matou " . $enemy->prepo . " " . $enemy->username . "!</b></u>";
            $matou = 5;
        }
        if ($player->hp < 1 || $morreu == 5) {
            $exploss1 = $player->level * 7;
            $exploss2 = (($player->level - $enemy->level) > 0) ? ($enemy->level - $player->level) * 4 : 0;
            $exploss = $exploss1 + $exploss2;
            $goldloss = (int) (0.4 * $player->gold);
            $goldloss = random_int(1, $goldloss);

            $output .= "<br/><div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b><u>Você foi morto pel" . $enemy->prepo . " " . $enemy->username . "!</u></b></div>";
            $output .= "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Você perdeu <b>" . $exploss . "</b> de EXP e <b>" . $goldloss . "</b> de ouro.</div>";
            $exploss3 = (($player->exp - $exploss) <= 0) ? $player->exp : $exploss;
            $goldloss2 = (($player->gold - $goldloss) <= 0) ? $player->gold : $goldloss;
            //Update player (the loser)
            $query = $db->execute("update `players` set `energy`=?, `exp`=?, `gold`=?, `deaths`=?, `hp`=0, `mana`=0, `deadtime`=? where `id`=?", [$player->energy - 10, $player->exp - $exploss3, $player->gold - $goldloss2, $player->deaths + 1, time() + $setting->dead_time, $player->id]);
            $query = $db->execute("delete from `bixos` where `player_id`=?", [$player->id]);
            $_SESSION['ataques'] .= "<u><b>Você foi morto pel" . $enemy->prepo . " " . $enemy->username . "!</b></u>";
            $morreu = 5;
        }
        if ($fugir == 5) {
            $query = $db->execute("delete from `bixos` where `player_id`=?", [$player->id]);
            unset($_SESSION['ataques']);
            header("Location: monster.php");
            break;
        }

        $player = check_user($secret_key, $db); //Get new stats
        include(__DIR__ . "/templates/private_header.php");

        $magiaatual = $db->execute("select `magia`, `turnos` from `bixos` where `player_id`=?", [$player->id]);
        $magiaatual2 = $magiaatual->fetchrow();

        if ($bixo->hp < 1 || $matou == 5) {
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
            echo "<center><b>Você matou " . $enemy->prepo . " " . $enemy->username . "!</b><br/>Você ganhou " . $expdomonstro . " de experiência e " . $goldwin . " de ouro.</center>";
            echo "</div>";
            if ($newlevell == 5) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b><u><center>Você passou de nível!</center></u></b></div>";
            }
            if ($lootstatus == 5) {
                echo "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">" . $mensagem . "</div>";
            }
            if ($medalha == 10) {
                echo "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">" . $medalhamsg . "</div>";
            }
        } elseif ($player->hp < 1 || $morreu == 5) {
            echo "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
            echo "<center><b>Você morreu!</b><br/>Você perdeu " . $exploss3 . " de experiência e " . $goldloss2 . " de ouro.</center>";
            echo "</div>";
        } elseif ($magiaatual2['magia'] != 0) {
            if ($magiaatual2['magia'] == 1) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
                echo "<center>Ataque 15% mais forte por " . $magiaatual2['turnos'] . " turno(s).</center>";
                echo "</div>";
            } elseif ($magiaatual2['magia'] == 2) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
                echo "<center>Ataque 45% mais forte por " . $magiaatual2['turnos'] . " turno(s).<br/>Resistencia 15% mais baixa por " . $magiaatual2['turnos'] . " turno(s).</center>";
                echo "</div>";
            } elseif ($magiaatual2['magia'] == 6 || $magiaatual2['magia'] == 9) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
                echo "<center>Feitiço de defesa por " . $magiaatual2['turnos'] . " turno(s).</center>";
                echo "</div>";
            } elseif ($magiaatual2['magia'] == 7) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
                echo "<center>Defesa 20% mais alta por " . $magiaatual2['turnos'] . " turno(s).</center>";
                echo "</div>";
            } elseif ($magiaatual2['magia'] == 10) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
                echo "<center>Seu escudo místico está ativo por " . $magiaatual2['turnos'] . " turno(s).</center>";
                echo "</div>";
            } elseif ($magiaatual2['magia'] == 11) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
                echo "<center>O monstro está tonto por " . $magiaatual2['turnos'] . " turno(s).</center>";
                echo "</div>";
            } elseif ($magiaatual2['magia'] == 12) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
                echo "<center>Ataque 35% mais forte por " . $magiaatual2['turnos'] . " turno(s).</center>";
                echo "</div>";
            }
        }


        echo "<div id=\"logdebatalha\" class=\"scroll\" style=\"background-color:#FFFDE0; overflow: auto; height:270px; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
        echo $_SESSION['ataques'];
        echo "</div>";

        $heal = $player->maxhp - $player->hp;
        if ($heal > 0) {
            if ($player->level < 36) {
                $cost = ceil($heal * 1);
            } elseif ($player->level > 35 && $player->level < 90) {
                $cost = ceil($heal * 1.45);
            } else {
                $cost = ceil($heal * 1.8);
            }
        }

        if ($matou == 5) {
            unset($_SESSION['ataques']);
            echo "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b>Opções:</b> <a href=\"monster.php?act=attack&id=" . ($bixo->id * $player->id) . "\">Atacar outr" . $enemy->prepo . " " . $enemy->username . "</a> | ";
            if ($heal > 0) {
                echo "<a href=\"hospt.php?act=heal\">Recuperar vida</a> <font size=\"1\">(" . $cost . " de ouro)</font> | ";
            }
            echo "<a href=\"monster.php\">Voltar</a></div>";
            $modefastbattle = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\FASTBATTLE, $player->id]);
            if ($modefastbattle->recordcount() > 0) {
                echo "<center><font size=\"1\"><a href=\"monster.php?alterar=true\">Clique aqui</a> para alterar para modo de luta tradicional.</font></center>";
            }
        } elseif ($morreu == 5) {
            unset($_SESSION['ataques']);
            echo "<div id=\"mydiv\" style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><a href=\"hospt.php?act=heal\">Clique aqui</a> para recuperar toda sua vida por <b>" . $cost . "</b> de ouro. | <a href=\"monster.php\">Voltar</a></div>";
            $modefastbattle = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\FASTBATTLE, $player->id]);
            if ($modefastbattle->recordcount() > 0) {
                echo "<center><font size=\"1\"><a href=\"monster.php?alterar=true\">Clique aqui</a> para alterar para modo de luta tradicional.</font></center>";
            }
        } else {
            echo "<form id=\"thisform\" action=\"monster.php?act=attack&id=" . ($enemy->id * $player->id) . "\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"batalha\" value=\"lutando\"/>";
            echo "<table width=\"100%\"><tr><th width=\"52%\" bgcolor=\"#E1CBA4\"><font size=\"1\"><b>Opções:</b></font> <input type=\"submit\" name=\"ataque\" value=\"Atacar\"/> ";
            echo "<select name=\"magia\" onchange=\"document.getElementById('thisform').submit()\"><option value='0'>Magias</option>";
            $vermagia = $db->execute("select magias.magia_id, blueprint_magias.nome from `magias`, `blueprint_magias` where magias.magia_id=blueprint_magias.id and magias.used=? and magias.magia_id!=5 and magias.player_id=?", [\T, $player->id]);
            while($result = $vermagia->fetchrow()) {
                echo "<option value=" . $result['magia_id'] . ">" . $result['nome'] . "</option>";
            }
            echo "</select>";

            echo "</th><th width=\"35%\" bgcolor=\"#45E61D\"><center><font size=1><b>Inimigo:</b> " . ($bixo->hp - $totalpak) . " de vida restante.</font></center>";
            echo "</th><th width=\"13%\"><font size=\"1\"><a href=\"monster.php?act=attack&correr=true\">Fugir</a><br/><a href=\"monster.php?act=attack&acabaluta=true&id=" . ($enemy->id * $player->id) . "\">Luta Rápida</a></font></th></tr></table>";

            $modefastbattle = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\FASTBATTLE, $player->id]);
            if ($modefastbattle->recordcount() > 0) {
                echo "<br/><center><font size=\"1\"><a href=\"monster.php?alterar=true\">Clique aqui</a> para alterar para modo de luta tradicional.</font></center>";
            } else {
                echo "<br/><center><font size=\"1\"><a href=\"monster.php?alterar=true\">Clique aqui</a> para alterar para modo de luta rápida.</font></center>";
                echo "<center><font size=\"1\">Deixe suas batalhas mais rápidas usando hotkeys, <a href=\"#\" onclick=\"javascript:window.open('hotkeys.html', '_blank','top=100, left=100, height=400, width=400, status=no, menubar=no, resizable=no, scrollbars=yes, toolbar=no, location=no, directories=no');\">Clique aqui</a>.</font></center></form>";
            }

        }

        include(__DIR__ . "/templates/private_footer.php");

        break;


    default:

        $fromlevel = 1;
        $tolevel = round($player->level * 1.8);

        ($sql = mysql_query("SELECT * FROM monsters WHERE level>='$fromlevel' AND level<='$tolevel' AND evento!='n' order by level asc")) || die(mysql_error());
        if (mysql_num_rows($sql) > 0) {//Check if any monsters were found
            include(__DIR__ . "/templates/private_header.php");

            if ($player->stat_points > 0 && $player->level < 15) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Antes de batalhar, utilize seus <b>" . $player->stat_points . "</b> pontos de status disponíveis, assim você fica mais forte! <a href=\"stat_points.php\">Clique aqui para utiliza-los!</a></div>";
            }

            $query = $db->execute("select * from `items` where `player_id`=? and `status`='equipped'", [$player->id]);
            if ($query->recordcount() < 2 && $player->level > 4 && $player->level < 20) {
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Já está na hora de você comprar seus própios itens. <a href=\"shop.php\">Clique aqui e visite o ferreiro</a>.</div>";
            }


            if ($setting->eventoouro > time()) {
                $end = $setting->eventoouro - time();
                $days = floor($end / 60 / 60 / 24);
                $hours = $end / 60 / 60 % 24;
                $minutes = $end / 60 % 60;
                $acaba = "$days dia(s) $hours hora(s) $minutes minuto(s)";
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b>Evento Surpresa!</b> Ouro em dobro.<br>Tempo restante: " . $acaba . "</div>";
            }

            if ($setting->eventoexp > time()) {
                $end = $setting->eventoexp - time();
                $days = floor($end / 60 / 60 / 24);
                $hours = $end / 60 / 60 % 24;
                $minutes = $end / 60 % 60;
                $acaba = "$days dia(s) $hours hora(s) $minutes minuto(s)";
                echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b>Evento Surpresa!</b> Experiência em dobro.<br>Tempo restante: " . $acaba . "</div>";
            }


            $veriddoseugrupo = $db->execute("select `id` from `groups` where `player_id`=?", [$player->id]);
            if ($veriddoseugrupo->recordcount() > 0) {

                $seugidd = $db->GetOne("select `id` from `groups` where `player_id`=?", [$player->id]);
                $grupototalbonus = 0;
                $totalgrupoquery = $db->execute("select * from `groups` where `id`=?", [$seugidd]);
                if ($totalgrupoquery->recordcount() > 0) {
                    while($gbbbonus = $totalgrupoquery->fetchrow()) {
                        $grupototalbonus += $gbbbonus['kills'];
                    }

                    if ($grupototalbonus > 4999 && $grupototalbonus < 15000) {
                        echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><b>Bônus de Experiência:</b> 5%<br/>Mais de 5000 monstros mortos pelo grupo de caça.</center></div>";
                    } elseif ($grupototalbonus > 14999 && $grupototalbonus < 30000) {
                        echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><b>Bônus de Experiência:</b> 10%<br/>Mais de 15000 monstros mortos pelo grupo de caça.</center></div>";
                    } elseif ($grupototalbonus > 29999 && $grupototalbonus < 50000) {
                        echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><b>Bônus de Experiência:</b> 15%<br/>Mais de 30000 monstros mortos pelo grupo de caça.</center></div>";
                    } elseif ($grupototalbonus > 49999) {
                        echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><b>Bônus de Experiência:</b> 20%<br/>Mais de 50000 monstros mortos pelo grupo de caça.</center></div>";
                    }
                }
            }




            echo "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><i>Você pode enfrentar monstros do nivel " . $fromlevel . " à " . $tolevel . ".</i></center></div>";
            echo "<table width=\"100%\">\n";
            echo "<tr><th width=\"45%\">Nome</th><th width=\"15%\">Nivel</th><th width=\"25%\">Batalha</a></th></tr>\n";
            $bool = 1;
            while ($result = mysql_fetch_array($sql)) {
                echo "<tr class=\"row" . $bool . "\">\n";
                echo "<td width=\"45%\">" . $result['username'] . "</td>\n";
                echo "<td width=\"15%\">" . $result['level'] . "</td>\n";
                echo "<td width=\"25%\"><a href=\"monster.php?act=attack&id=" . ($result['id'] * $player->id) . "\">Atacar</a></td>\n";
                echo "</tr>\n";
                $bool = ($bool == 1) ? 2 : 1;
            }
            echo "</table>\n";
            include(__DIR__ . "/templates/private_footer.php");
        } else { //Display error message
            include(__DIR__ . "/templates/private_header.php");
            echo "<table width=\"100%\">\n";
            echo "<tr>\n";
            echo "<td>Seu nivel está muito avançado, agora você só pode lutar contra os outros <a href=\"battle.php\">jogadores</a>.</td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            include(__DIR__ . "/templates/private_footer.php");
        }
        break;
}
