<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Batalhar");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkhp.php");
include(__DIR__ . "/checkwork.php");

switch($_GET['act']) {
    case "attack":
        if (!$_GET['id']) { //No username entered
            header("Location: monster.php");
            break;
        }

        //Otherwise, get player data:
        $query = $db->execute("select * from `monsters` where `id`=?", [$_GET['id']]);
        if ($query->recordcount() == 0) { //Player doesn't exist
            include(__DIR__ . "/templates/private_header.php");
            echo "Este monstro não existe! <a href=\"monster.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        $enemy1 = $query->fetchrow(); //Get monster info
        foreach($enemy1 as $key => $value) {
            $enemy->$key = $value;
        }

        //checa os niveis
        $tolevelttyy = round($player->level * 1.8);
        if ($tolevelttyy < $enemy->level && $enemy->evento == 'f') {
            include(__DIR__ . "/templates/private_header.php");
            echo "Você não pode atacar este monstro!</b></font> <a href=\"monster.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($enemy->evento == 'n') {
            include(__DIR__ . "/templates/private_header.php");
            echo "Este monstro não existe! <a href=\"monster.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        //Player cannot attack anymore
        if ($player->energy == 0) {
            include(__DIR__ . "/templates/private_header.php");
            echo "<fieldset>";
            echo "<legend><b>Você está sem energia!</b></legend>\n";
            echo "Você deve descançar um pouco. <b>(1 minuto = 1 energia)</b>";
            echo "</fieldset><a href=\"monster.php\">Voltar</a>";
            echo "<br><br>";

            echo "<b>Poções de Energia:</b>";
            echo "<br />";
            $query = $db->execute("select items.id, items.item_id, blueprint_items.type, blueprint_items.name, blueprint_items.description, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='potion' and blueprint_items.id=137 order by blueprint_items.name asc", [$player->id]);
            if ($query->recordcount() == 0) {
                echo "Você não tem poções de energia.";
            } else {
                while($item = $query->fetchrow()) {
                    echo "<fieldset>\n<legend>";
                    echo "<b>" . $item['name'] . "</b></legend>\n";
                    echo "<table width=\"100%\">\n";
                    echo "<tr><td width=\"5%\">";
                    echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
                    echo "</td><td width=\"80%\">";
                    echo $item['description'] . "\n<br />";
                    echo "</td><td width=\"15%\">";
                    echo "<a href=\"hospt.php?act=potion&pid=" . $item['id'] . "\">Usar</a>";
                    echo "</td></tr>\n";
                    echo "</table>";
                    echo "</fieldset>\n";
                }
            }

            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($player->monsterkill >= $setting->securyty_capcha) {
            include(__DIR__ . "/templates/private_header.php");
            echo "<fieldset><legend><b>Segurança</b></legend>";
            echo "<form name=\"nobot\" method=\"post\" action=\"checkmonster.php\">";
            echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
            echo "<tr>";
            echo "<td width=\"200\" height=\"100\" style=\"border: 1px solid #666666;\"><img src=\"captcha_img.php\" border=\"0\"></td>";
            echo "<td height=\"100\"> Insira abaixo o código abaixo exatamente como você vê, lembre-se de que <b>letras maiusculas são diferentes de minusculas</b>.<br/><br/>Se Você visualizar uma conta matemática, insira seu resultado abaixo.</td>";
            echo "</tr></table>";
            echo "<b>Texto/Resultado:</b> <input type=\"text\" name=\"capt\" style=\"width: 80px\"> <input type=\"submit\" name=\"Submit\" value=\"Enviar\"></form></fieldset>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }


        //Get player's bonuses from equipment
        $query = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.optimized from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='weapon' and items.status='equipped'", [$player->id]);
        $player->atkbonus = ($query->recordcount() == 1) ? $query->fetchrow() : 0;
        $query = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.optimized from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='armor' and items.status='equipped'", [$player->id]);
        $player->defbonus = ($query->recordcount() == 1) ? $query->fetchrow() : 0;
        $query = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.optimized from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='helmet' and items.status='equipped'", [$player->id]);
        $player->defbonus2 = ($query->recordcount() == 1) ? $query->fetchrow() : 0;
        $query = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.optimized from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='legs' and items.status='equipped'", [$player->id]);
        $player->defbonus3 = ($query->recordcount() == 1) ? $query->fetchrow() : 0;
        $query = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.optimized from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='boots' and items.status='equipped'", [$player->id]);
        $player->defbonus4 = ($query->recordcount() == 1) ? $query->fetchrow() : 0;
        $query = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.optimized from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='shield' and items.status='equipped'", [$player->id]);
        $player->defbonus5 = ($query->recordcount() == 1) ? $query->fetchrow() : 0;
        $query = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.optimized from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='amulet' and items.status='equipped'", [$player->id]);
        $player->agibonus6 = ($query->recordcount() == 1) ? $query->fetchrow() : 0;

        //Calculate some variables that will be used
        $especagi = ((($player->agility) + $player->agibonus6['effectiveness'] + $player->agibonus6['item_bonus'] + $player->agibonus6['optimized']) * 3);
        $enemy->strdiff = (($enemy->strength - $player->strength) > 0) ? ($enemy->strength - $player->strength) : 0;
        $enemy->resdiff = (($enemy->vitality - ($player->resistance * 1.5)) > 0) ? ($enemy->vitality - $player->resistance) : 0;
        $enemy->agidiff = (($enemy->agility - $especagi) > 0) ? ($enemy->agility - $especagi) : 0;
        $enemy->leveldiff = (($enemy->level - $player->level) > 0) ? ($enemy->level - $player->level) : 0;
        $player->strdiff = (($player->strength - $enemy->strength) > 0) ? ($player->strength - $enemy->strength) : 0;
        $player->resdiff = (($player->resistance - $enemy->vitality) > 0) ? ($player->resistance - $enemy->vitality) : 0;
        $player->agidiff = (($especagi - $enemy->agility) > 0) ? ($especagi - $enemy->agility) : 0;
        $player->leveldiff = (($player->level - $enemy->level) > 0) ? ($player->level - $enemy->level) : 0;
        $totalstr = $enemy->strength + $player->strength;
        $totalres = $enemy->vitality + $player->resistance;
        $totalagi = $enemy->agility + $especagi;
        $totallevel = $enemy->level + $player->level;

        if ($player->voc == 'archer' && $player->promoted == 'f') {
            $multipleatk = 0.81;
            $multipledef = 1.30;
            $divideres = 1.25;
        } elseif ($player->voc == 'knight' && $player->promoted == 'f') {
            $multipleatk = 0.90;
            $multipledef = 0.90;
            $divideres = 1.3;
        } elseif ($player->voc == 'mage' && $player->promoted == 'f') {
            $multipleatk = 1.35;
            $multipledef = 0.77;
            $divideres = 1.35;
        } elseif ($player->voc == 'archer' && $player->promoted == 't') {
            $multipleatk = 0.90;
            $multipledef = 1.39;
            $divideres = 1.2;
        } elseif ($player->voc == 'knight' && $player->promoted == 't') {
            $multipleatk = 0.99;
            $multipledef = 1;
            $divideres = 1.25;
        } elseif ($player->voc == 'mage' && $player->promoted == 't') {
            $multipleatk = 1.44;
            $multipledef = 0.84;
            $divideres = 1.3;
        } elseif ($player->voc == 'archer' && $player->promoted == 'r') {
            $multipleatk = 1;
            $multipledef = 1.48;
            $divideres = 1.15;
        } elseif ($player->voc == 'knight' && $player->promoted == 'r') {
            $multipleatk = 1.08;
            $multipledef = 1.09;
            $divideres = 1.20;
        } elseif ($player->voc == 'mage' && $player->promoted == 'r') {
            $multipleatk = 1.53;
            $multipledef = 0.90;
            $divideres = 1.25;
        } elseif ($player->voc == 'archer' && $player->promoted == 's') {
            $multipleatk = 1.09;
            $multipledef = 1.56;
            $divideres = 1;
        } elseif ($player->voc == 'knight' && $player->promoted == 's') {
            $multipleatk = 1.15;
            $multipledef = 1.16;
            $divideres = 1.1;
        } elseif ($player->voc == 'mage' && $player->promoted == 's') {
            $multipleatk = 1.63;
            $multipledef = 1;
            $divideres = 1.15;
        }

        //Calculate the damage to be dealt by each player (dependent on strength and level)
        $enemy->maxdmg = (($enemy->strength * 2) + $enemy->atkbonus['effectiveness']) - (($player->defbonus['effectiveness'] + $player->defbonus['item_bonus'] + $player->defbonus['optimized']) * $multipledef) - (($player->defbonus2['effectiveness'] + $player->defbonus2['item_bonus'] + $player->defbonus2['optimized']) * $multipledef) - (($player->defbonus3['effectiveness'] + $player->defbonus3['item_bonus'] + $player->defbonus3['optimized']) * $multipledef) - (($player->defbonu4['effectiveness'] + $player->defbonus4['item_bonus'] + $player->defbonus4['optimized']) * $multipledef) - (($player->defbonus5['effectiveness'] + $player->defbonus5['item_bonus'] + $player->defbonus5['optimized']) * $multipledef) - ($player->resistance / $divideres);
        $enemy->maxdmg -= (int) ($enemy->maxdmg * ($player->leveldiff / $totallevel));
        $enemy->maxdmg = ($enemy->maxdmg <= 2) ? 2 : $enemy->maxdmg; //Set 2 as the minimum damage
        $enemy->mindmg = (($enemy->maxdmg - 4) < 1) ? 1 : ($enemy->maxdmg - 4); //Set a minimum damage range of maxdmg-4
        $player->maxdmg = ((($player->strength * 2) + $player->atkbonus['effectiveness'] + $player->atkbonus['item_bonus'] + $player->atkbonus['optimized']) * $multipleatk) - ($enemy->vitality / 1.20);
        $player->maxdmg -= (int) ($player->maxdmg * ($enemy->leveldiff / $totallevel));
        $player->maxdmg = ($player->maxdmg <= 2) ? 2 : $player->maxdmg; //Set 2 as the minimum damage
        $player->mindmg = (($player->maxdmg - 4) < 1) ? 1 : ($player->maxdmg - 4); //Set a minimum damage range of maxdmg-4

        //Calculate battle 'combos' - how many times in a row a player can attack (dependent on agility)
        $enemy->combo = ceil($enemy->agility / $especagi);
        $enemy->combo = ($enemy->combo > 3) ? 3 : $enemy->combo;
        $player->combo = ceil($especagi / $enemy->agility);
        $player->combo = ($player->combo > 3) ? 3 : $player->combo;


        //Calculate the chance to miss opposing player
        $enemy->miss = (int) (($player->agidiff / $totalagi) * 100);
        $enemy->miss = ($enemy->miss > 20) ? 20 : $enemy->miss; //Maximum miss chance of 20% (possible to change in admin panel?)
        $enemy->miss = max(8, $enemy->miss); //Minimum miss chance of 5%
        $player->miss = (int) (($enemy->agidiff / $totalagi) * 100);
        $player->miss = ($player->miss > 20) ? 20 : $player->miss; //Maximum miss chance of 20%
        $player->miss = max(8, $player->miss); //Minimum miss chance of 5%


        $battlerounds = $setting->monster_battle_rounds;

        $output = ""; //Output message


        $output .= "<div class=\"scroll\" style=\"background-color:#FFFDE0; overflow: auto; height:270px; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";

        //While somebody is still alive, battle!
        while ($enemy->hp > 0 && $player->hp > 0 && $battlerounds > 0) {


            $attacking = ($especagi >= $enemy->agility) ? $player : $enemy;
            $defending = ($especagi >= $enemy->agility) ? $enemy : $player;

            for($i = 0;$i < $attacking->combo;$i++) {
                //Chance to miss?
                $misschance = random_int(0, 100);
                if ($misschance <= $attacking->miss) {
                    $output .= $attacking->username . " tentou atacar " . $defending->username . " mas errou!<br />";
                } else {
                    $magicchance = random_int(1, 4);
                    if ($magicchance == 2 && $attacking->magiclevel > 0) {
                        $damage2 = random_int(($attacking->maxdmg * 1.20), ($attacking->maxdmg * 1.25) + ($attacking->magiclevel * 1.5)); //Calculate random damage
                        $defending->hp -= $damage2;
                        $output .= ($player->username == $defending->username) ? "<font color=\"red\">" : "<font color=\"green\">";
                        $output .= $attacking->username . " lançou um feitiço em " . $defending->username . " e tirou <b>" . $damage2 . "</b> de vida! (";
                        $output .= ($defending->hp > 0) ? $defending->hp . " de vida" : "Morto";
                        $output .= ")<br />";
                        $output .= "</font>";
                    } else {
                        $damage = random_int($attacking->mindmg, $attacking->maxdmg); //Calculate random damage
                        $defending->hp -= $damage;
                        $output .= ($player->username == $defending->username) ? "<font color=\"red\">" : "<font color=\"green\">";
                        $output .= $attacking->username . " atacou " . $defending->username . " e tirou <b>" . $damage . "</b> de vida! (";
                        $output .= ($defending->hp > 0) ? $defending->hp . " de vida" : "Morto";
                        $output .= ")<br />";
                        $output .= "</font>";
                    }

                    //Check if anybody is dead
                    if ($defending->hp <= 0) {
                        $player = ($especagi >= $enemy->agility) ? $attacking : $defending;
                        $enemy = ($especagi >= $enemy->agility) ? $defending : $attacking;
                        break 2; //Break out of the for and while loop, but not the switch structure
                    }
                }
                $battlerounds--;
                if ($battlerounds <= 0) {
                    break 2; //Break out of for and while loop, battle is over!
                }
            }

            for($i = 0;$i < $defending->combo;$i++) {
                //Chance to miss?
                $misschance = random_int(0, 100);
                if ($misschance <= $defending->miss) {
                    $output .= $defending->username . " tentou atacar " . $attacking->username . " mas errou!<br />";
                } else {
                    $magicchance = random_int(1, 4);
                    if ($magicchance == 2 && $defending->magiclevel > 0) {
                        $damage2 = random_int(($defending->maxdmg * 1.20), ($defending->maxdmg * 1.25) + ($defending->magiclevel * 1.5)); //Calculate random damage
                        $attacking->hp -= $damage2;
                        $output .= ($player->username == $defending->username) ? "<font color=\"green\">" : "<font color=\"red\">";
                        $output .= $defending->username . " lançou um feitiço em " . $attacking->username . " e tirou <b>" . $damage2 . "</b> de vida! (";
                        $output .= ($attacking->hp > 0) ? $attacking->hp . " de vida" : "Morto";
                        $output .= ")<br />";
                        $output .= "</font>";
                    } else {
                        $damage = random_int($defending->mindmg, $defending->maxdmg); //Calculate random damage
                        $attacking->hp -= $damage;
                        $output .= ($player->username == $defending->username) ? "<font color=\"green\">" : "<font color=\"red\">";
                        $output .= $defending->username . " atacou " . $attacking->username . " e tirou <b>" . $damage . "</b> de vida! (";
                        $output .= ($attacking->hp > 0) ? $attacking->hp . " de vida" : "Morto";
                        $output .= ")<br />";
                        $output .= "</font>";
                    }

                    //Check if anybody is dead
                    if ($attacking->hp <= 0) {
                        $player = ($especagi >= $enemy->agility) ? $attacking : $defending;
                        $enemy = ($especagi >= $enemy->agility) ? $defending : $attacking;
                        break 2; //Break out of the for and while loop, but not the switch structure
                    }
                }
                $battlerounds--;
                if ($battlerounds <= 0) {
                    break 2; //Break out of for and while loop, battle is over!
                }
            }

            $player = ($especagi >= $enemy->agility) ? $attacking : $defending;
            $enemy = ($especagi >= $enemy->agility) ? $defending : $attacking;

        }
        $output .= "</div>";

        if ($enemy->loot > 1) {
            $chanceloot = random_int(1, $enemy->loot);
            if ($chanceloot == $enemy->loot) {
                $veositemz = $db->execute("select `item_id`, `item_prepo`, `item_name` from `loot` where `monster_id`=?", [$enemy->id]);
                if ($veositemz->recordcount() == 0) {
                    echo "Contate ao administrador que o monstro " . $enemy->username . " está com erros.";
                    exit;
                }
                $loot_item = $veositemz->fetchrow();
                $mensagem = "<u><b>Você encontrou " . $loot_item['item_prepo'] . " " . $loot_item['item_name'] . " com " . $enemy->prepo . " " . $enemy->username . "</b></u>";
                $lootstatus = 5;
                $loot_id = $loot_item['item_id'];
            } else {
                $lootstatus = 2;
            }
        } elseif ($enemy->loot == 1) {
            $sorteioitem = random_int(1, 32);
            if ($sorteioitem == 32) {
                $sorteiaitem = $db->execute("select `id`, `name` from `blueprint_items` where `type`!=? and `price`>? and `price`<? order by rand() limit 1", [\ADDON, $enemy->mtexp * 2.5, $enemy->mtexp * 3.5]);
                if ($sorteiaitem->recordcount() == 0) {
                    $mensagem = "Contate ao administrador que o monstro " . $enemy->username . " está com erros.";
                    $lootstatus = 2;
                } else {
                    $loot_item2 = $sorteiaitem->fetchrow();
                    $mensagem = "<u><b>Você encontrou um/uma " . $loot_item2['name'] . " com " . $enemy->prepo . " " . $enemy->username . "</b></u>";
                    $lootstatus = 5;
                    $loot_id = $loot_item2['id'];
                }
            } elseif ($sorteioitem == 1) {
                $sorteiapotion = random_int(1, 3);
                if ($sorteiapotion == 3) {
                    $mensagem = "<u><b>Você encontrou uma Energy Potion com " . $enemy->prepo . " " . $enemy->username . "</b></u>";
                    $lootstatus = 5;
                    $loot_id = 137;
                } else {
                    $mensagem = "<u><b>Você encontrou uma Health Potion com " . $enemy->prepo . " " . $enemy->username . "</b></u>";
                    $lootstatus = 5;
                    $loot_id = 136;
                }
            } else {
                $lootstatus = 2;
            }
        }

        if ($player->hp <= 0) {
            //Calculate losses
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
            $query = $db->execute("update `players` set `energy`=?, `exp`=?, `gold`=?, `deaths`=?, `hp`=0, `deadtime`=? where `id`=?", [$player->energy - 1, $player->exp - $exploss3, $player->gold - $goldloss2, $player->deaths + 1, time() + $setting->dead_time, $player->id]);
        } elseif ($enemy->hp <= 0) {
            //Calculate losses
            $expwin1 = $enemy->level * 6;
            $expwin2 = (($player->level - $enemy->level) > 0) ? $expwin1 - (($player->level - $enemy->level) * 3) : $expwin1 + (($player->level - $enemy->level) * 3);
            $expwin2 = ($expwin2 <= 0) ? 1 : $expwin2;
            $expwin3 = round(0.5 * $expwin2);
            $expwin = ceil(random_int($expwin3, $expwin2));
            $goldwin = round(0.8 * $expwin);
            $goldwin = round($goldwin * 1.35);
            $output .= "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b><u>Você matou " . $enemy->prepo . " " . $enemy->username . "!</u></b></div>";
            $output .= "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Você ganhou <b>" . $enemy->mtexp . "</b> de EXP e <b>" . $goldwin . "</b> de ouro.</div>";
            if ($mensagem != "") {
                $output .= "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">" . $mensagem . "</div>";
            }
            if ($lootstatus == 5) {
                $insert['player_id'] = $player->id;
                $insert['item_id'] = $loot_id;
                $addlootitemwin = $db->autoexecute('items', $insert, 'INSERT');
            }
            if ($enemy->mtexp + $player->exp >= $player->maxexp) { //Player gained a level!
                //Update player, gained a level
                $output .= "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><u><b>Você passou de nivel!</b></u></div>";
                $newexp = $enemy->mtexp + $player->exp - $player->maxexp;

                if ($player->level <= 3) {
                    $expofnewlvl = $player->maxexp + 75;
                } else {
                    $expofnewlvl = floor(20 * ($player->level * $player->level * $player->level) / $player->level);
                }
                $query = $db->execute("update `players` set `stat_points`=?, `level`=?, `maxexp`=?, `maxhp`=?, `exp`=?, `hp`=?, `energy`=?, `gold`=?, `monsterkill`=?, `monsterkilled`=? where `id`=?", [$player->stat_points + 3, $player->level + 1, $expofnewlvl, $player->maxhp + 30, $newexp, $player->maxhp + 30, $player->energy - 1, $player->gold + $goldwin, $player->monsterkill + 1, $player->monsterkilled + 1, $player->id]);
            } else {
                //Update player
                $query = $db->execute("update `players` set `exp`=?, `gold`=?, `hp`=?, `energy`=?, `monsterkill`=?, `monsterkilled`=? where `id`=?", [$player->exp + $enemy->mtexp, $player->gold + $goldwin, $player->hp, $player->energy - 1, $player->monsterkill + 1, $player->monsterkilled + 1, $player->id]);
            }
            $heal = $player->maxhp - $player->hp;
            if ($heal > 0) {
                if ($player->level < 36) {
                    $cost = ceil($heal * 1);
                } elseif ($player->level > 35 && $player->level < 90) {
                    $cost = ceil($heal * 1.45);
                } else {
                    $cost = ceil($heal * 1.8);
                }
                $output .= "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><a href=\"hospt.php?act=heal\">Clique aqui</a> para recuperar toda sua vida por <b>" . $cost . "</b> de ouro.</div>";
            }
        } else {
            $output .= "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b><u>Os dois estão muito cançados para terminar a batalha! Ninguém venceu.</u></b></div>";
            $query = $db->execute("update `players` set `hp`=?, `energy`=?, `monsterkill`=? where `id`=?", [$player->hp, $player->energy - 1, $player->monsterkill + 1, $player->id]);

        }

        $player = check_user($secret_key, $db); //Get new stats
        include(__DIR__ . "/templates/private_header.php");
        echo "<table><tr><td width=\"100%\">";
        echo $output;
        echo "</td><td width=\"120px\"><a href=\"javascript:window.location.reload()\" border=\"0\"><center><img src=\"images/refresh.gif\" alt=\"Atacar Novamente\" width=\"65\" height=\"65\" border=\"0\"></center><br/><center><font size=\"1\">Atacar Novamente</font></center></a><br/><table align=\"center\">";



        $showitenx = $db->execute("select items.id, items.item_id, blueprint_items.name, blueprint_items.description, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='potion' order by rand() limit 7", [$player->id]);

        while($showeditexs = $showitenx->fetchrow()) {
            echo "<tr><td><div class=\"itembg\" align=\"center\">";
            echo "<div title=\"header=[" . $showeditexs['name'] . "] body=[" . $showeditexs['name'] . "]\">";
            echo "<a href=\"hospt.php?act=potion&pid=" . $showeditexs['id'] . "\"><img src=\"images/itens/" . $showeditexs['img'] . "\" border=\"0\"/></a>";
            echo "</div></td></tr>";
        }


        echo "</table></td></tr></table>";

        include(__DIR__ . "/templates/private_footer.php");
        break;


    default:

        $fromlevel = 1;
        $tolevel = round($player->level * 1.8);

        ($sql = mysql_query("SELECT * FROM monsters WHERE level>='$fromlevel' AND level<='$tolevel' AND evento='f' order by level asc")) || die(mysql_error());
        if (mysql_num_rows($sql) > 0) {//Check if any monsters were found
            include(__DIR__ . "/templates/private_header.php");
            echo "<center><i>Você pode enfrentar montros do nivel " . $fromlevel . " à " . $tolevel . ".</i></center>";
            echo "<br/>";
            echo "<table width=\"100%\">\n";
            echo "<tr><th width=\"45%\">Nome</th><th width=\"15%\">Nivel</th><th width=\"25%\">Batalha</a></th></tr>\n";
            $bool = 1;
            while ($result = mysql_fetch_array($sql)) {
                echo "<tr class=\"row" . $bool . "\">\n";
                echo "<td width=\"45%\">" . $result['username'] . "</td>\n";
                echo "<td width=\"15%\">" . $result['level'] . "</td>\n";
                echo "<td width=\"25%\"><a href=\"monster.php?act=attack&id=" . $result['id'] . "\">Atacar</a></td>\n";
                echo "</tr>\n";
                $bool = ($bool == 1) ? 2 : 1;
            }
            echo "</table>\n";
            echo "<br/><br/><b>Monstros Especiais</b>\n";
            ($sql2 = mysql_query("SELECT * FROM monsters WHERE evento='t' order by level asc")) || die(mysql_error());
            if (mysql_num_rows($sql2) > 0) {//Check if any monsters were found
                echo "<table width=\"100%\">\n";
                echo "<tr><th width=\"45%\">Nome</th><th width=\"15%\">Nivel</th><th width=\"20%\">Batalha</a></th></tr>\n";
                $bool = 1;
                while ($result = mysql_fetch_array($sql2)) {
                    echo "<tr class=\"row" . $bool . "\">\n";
                    echo "<td width=\"45%\">" . $result['username'] . "</td>\n";
                    echo "<td width=\"15%\">" . $result['level'] . "</td>\n";
                    echo "<td width=\"25%\"><a href=\"monster.php?act=attack&id=" . $result['id'] . "\">Atacar</a></td>\n";
                    echo "</tr>\n";
                    $bool = ($bool == 1) ? 2 : 1;
                }
                echo "</table>\n";
            } else {
                echo "<center><i>Nenhum monstro especial encontrado.</i></center>";
            }

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
