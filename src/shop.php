<?php

/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include(__DIR__ . "/lib.php");
define("PAGENAME", "Ferreiro");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkhp.php");
include(__DIR__ . "/checkwork.php");

if ($player->hp <= 0) {
    include(__DIR__ . "/templates/private_header.php");
    echo "<fieldset>";
    echo "<legend><b>Você está morto!</b></legend>\n";
    echo "Vá ao <a href=\"hospt.php\">hospital</a> ou espere 30 minutos.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}

switch($_GET['act']) {
    case "buy":
        if (!$_GET['id']) { //No item ID
            header("Location: shop.php");
            break;
        }

        //Select the item from the database
        $query = $db->execute("select `id`, `name`, `price`, `type`, `voc`, `canbuy` from `blueprint_items` where `id`=?", [$_GET['id']]);

        //Invalid item (it doesn't exist)
        if ($query->recordcount() == 0) {
            header("Location: shop.php");
            break;
        }

        $item = $query->fetchrow();
        if ($item['price'] > $player->gold) {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Desculpe, mas você não pode pagar por isto!</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($item['type'] == 'shield' && $player->voc == 'archer') {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Desculpe, mas arqueiros não podem usar escudos!</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($item['voc'] == '1' && $player->voc != 'archer') {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Desculpe, mas você não pode comprar esse tipo de item!</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }


        if ($item['voc'] == '2' && $player->voc != 'knight') {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Desculpe, mas você não pode comprar esse tipo de item!</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }


        if ($item['voc'] == '3' && $player->voc != 'mage') {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Desculpe, mas você não pode comprar esse tipo de item!</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($item['type'] == 'addon') {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Desculpe, mas eu não vendo este tipo de item!</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($item['canbuy'] == 'f') {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Desculpe, mas eu não vendo este tipo de item!</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }


        if ($item['canbuy'] == 's') {

            $checaquest = $db->execute("select `id` from `quests` where `player_id`=? and `quest_status`=90 and `quest_id`=11", [$player->id]);
            if ($checaquest->recordcount() == 0) {
                include(__DIR__ . "/templates/private_header.php");
                echo "<b>Ferreiro:</b><br />\n";
                echo "<i>Desculpe, mas eu não vendo este tipo de item!</i><br /><br />\n";
                echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }
        }

        $query1 = $db->execute("update `players` set `gold`=? where `id`=?", [$player->gold - $item['price'], $player->id]);
        $insert['player_id'] = $player->id;
        $insert['item_id'] = $item['id'];
        $query2 = $db->autoexecute('items', $insert, 'INSERT');
        if ($query1 && $query2) { //If successful
            $player = check_user($secret_key, $db); //Get new user stats

            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Obrigado, aproveite sua nova <b>" . $item['name'] . "</b>!</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        } else {
            //Error logging here
        }
        break;

    case "sell":
        if ($_POST['comfirm'] && ($_POST['actione']) == 'vendeer') {
            include(__DIR__ . "/templates/private_header.php");
            if (!$_POST['id']) {
                echo "Você precisa selecionar algum item para vender.<br/><a href=\"inventory.php\">Voltar</a>.";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }
            $totalprico = 0;
            $totalsell = 0;
            echo "<form method=\"POST\" action=\"shop.php?act=sell\">\n";
            echo "<b>Deseja vender:</b><br/>";
            foreach($_POST['id'] as $msg) {
                $multipleitem = $db->execute("select items.id, items.item_id, items.item_bonus, items.status, items.mark, blueprint_items.name, blueprint_items.price, blueprint_items.type from `blueprint_items`, `items` where items.item_id=blueprint_items.id and items.player_id=? and items.id=?", [$player->id, $msg]);
                if ($multipleitem->recordcount() == 0) {
                    echo "Este item não te pertence.<br />";
                } else {
                    $multisell = $multipleitem->fetchrow();
                    if ($multisell['status'] == 'equipped') {
                        echo "Você não pode vender um item que está em uso.<br />";
                    } elseif ($multisell['type'] == 'stone') {
                        echo "Você não pode vender pedras.<br />";
                    } elseif ($multisell['item_id'] == 111 || $multisell['item_id'] == 116) {
                        echo "Você não pode vender este item, caso contrário não poderá terminar sua missão.<br />";
                    } else {
                        if ($multisell['item_bonus'] > 10) {
                            $precodavenda = floor(($multisell['price'] / 2) + (($multisell['item_bonus'] * $multisell['price']) / 5) + 3_000_000);
                        } else {
                            $precodavenda = floor(($multisell['price'] / 2) + (($multisell['item_bonus'] * $multisell['price']) / 5));
                        }

                        $multisellfor = $multisell['for'] == 0 ? "" : " +<font color=\"gray\">" . $multisell['for'] . "F</font>";

                        $multisellvit = $multisell['vit'] == 0 ? "" : " +<font color=\"green\">" . $multisell['vit'] . "V</font>";

                        $multisellagi = $multisell['agi'] == 0 ? "" : " +<font color=\"blue\">" . $multisell['agi'] . "A</font>";

                        $multisellres = $multisell['res'] == 0 ? "" : " +<font color=\"red\">" . $multisell['res'] . "R</font>";
                        echo "<b>1x</b> " . $multisell['name'] . " +" . $multisell['item_bonus'] . "" . $multisellfor . "" . $multisellvit . "" . $multisellagi . "" . $multisellres . " por " . $precodavenda . " de ouro.<br/>";
                        echo "<input type=\"hidden\" name=\"id[]\" value=\"" . $multisell['id'] . "\" />\n";
                        $totalprico += $precodavenda;
                        $totalsell += 1;
                    }
                }
            }
            if ($totalsell > 0) {
                echo "<b>Vendendo:</b> " . $totalsell . " item(s) por " . $totalprico . " de ouro.<br/><br/><input type=\"submit\" name=\"multiconfirm\" value=\"Desejo vender todos estes itens\" />  <a href=\"inventory.php\">Voltar</a>.\n";
                echo "</form>\n";
            }
            include(__DIR__ . "/templates/private_footer.php");
            break;
        } elseif ($_POST['comfirm'] && $_POST['actione'] != 'vendeer') {
            include(__DIR__ . "/templates/private_header.php");
            echo "Selecione uma ação.<br/><a href=\"inventory.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        } elseif ($_POST['multiconfirm']) {
            include(__DIR__ . "/templates/private_header.php");
            $totalprico2 = 0;
            foreach($_POST['id'] as $msg) {
                $multipleitem = $db->execute("select items.id, items.item_id, items.item_bonus, items.status, items.mark, blueprint_items.name, blueprint_items.price, blueprint_items.type from `blueprint_items`, `items` where items.item_id=blueprint_items.id and items.player_id=? and items.id=?", [$player->id, $msg]);
                if ($multipleitem->recordcount() == 0) {
                    echo "Este item não te pertence.<br />";
                } else {
                    $multisell = $multipleitem->fetchrow();

                    if ($multisell['status'] == 'equipped') {
                        echo "Você não pode vender um item que está em uso.<br />";
                    } elseif ($multisell['type'] == 'stone') {
                        echo "Você não pode vender pedras.<br />";
                    } elseif ($multisell['item_id'] == 111 || $multisell['item_id'] == 116) {
                        echo "Você não pode vender este item, caso contrário não poderá terminar sua missão.<br />";
                    } else {
                        if ($multisell['item_bonus'] > 10) {
                            $precodavenda = floor(($multisell['price'] / 2) + (($multisell['item_bonus'] * $multisell['price']) / 5) + 3_000_000);
                        } else {
                            $precodavenda = floor(($multisell['price'] / 2) + (($multisell['item_bonus'] * $multisell['price']) / 5));
                        }

                        $totalprico2 += $precodavenda;

                        if ($multisell['mark'] == 't') {
                            $query = $db->execute("delete from `market` where `market_id`=?", [$msg]);
                        }
                        $query = $db->execute("delete from `items` where `id`=?", [$msg]);
                        $multisellfor = $multisell['for'] == 0 ? "" : " +<font color=\"gray\">" . $multisell['for'] . "F</font>";

                        $multisellvit = $multisell['vit'] == 0 ? "" : " +<font color=\"green\">" . $multisell['vit'] . "V</font>";

                        $multisellagi = $multisell['agi'] == 0 ? "" : " +<font color=\"blue\">" . $multisell['agi'] . "A</font>";

                        $multisellres = $multisell['res'] == 0 ? "" : " +<font color=\"red\">" . $multisell['res'] . "R</font>";
                        echo "Você vendeu seu/sua <b>" . $multisell['name'] . " +" . $multisell['item_bonus'] . "</b>" . $multisellfor . "" . $multisellvit . "" . $multisellagi . "" . $multisellres . " por <b>" . $precodavenda . "</b> de ouro.<br/>";
                    }
                }
            }
            $query = $db->execute("update `players` set `gold`=? where `id`=?", [$player->gold + $totalprico2, $player->id]);
            echo "<br/><a href=\"inventory.php\">Voltar</a>.";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        } else {

            if (!$_GET['id']) { //No item ID
                header("Location: shop.php");
                break;
            }

            //Select the item from the database
            $query = $db->execute("select items.id, items.item_id, items.item_bonus, items.status, items.mark, blueprint_items.name, blueprint_items.price, blueprint_items.type from `blueprint_items`, `items` where items.item_id=blueprint_items.id and items.player_id=? and items.id=?", [$player->id, $_GET['id']]);

            //Either item doesn't exist, or item doesn't belong to user
            if ($query->recordcount() == 0) {
                include(__DIR__ . "/templates/private_header.php");
                echo "Este item não existe!";
                echo "<a href=\"inventory.php\">Voltar</a>.";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }

            $sell = $query->fetchrow(); //Get item info
            if ($sell['item_bonus'] > 10) {
                $valordavenda = floor(($sell['price'] / 2) + (($sell['item_bonus'] * $sell['price']) / 5) + 3_000_000);
            } else {
                $valordavenda = floor(($sell['price'] / 2) + (($sell['item_bonus'] * $sell['price']) / 5));
            }

            if ($sell['item_id'] == 111 || $sell['item_id'] == 116) {
                include(__DIR__ . "/templates/private_header.php");
                echo "Você não pode vender este item, caso contrário não poderá terminar sua missão.<br />\n";
                echo "<a href=\"inventory.php\">Voltar</a>.";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }

            if ($sell['type'] == 'stone') {
                include(__DIR__ . "/templates/private_header.php");
                echo "Você não pode vender pedras.<br />\n";
                echo "<a href=\"inventory.php\">Voltar</a>.";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }

            if ($sell['status'] == 'equipped') {
                include(__DIR__ . "/templates/private_header.php");
                echo "Você não pode vender um item que está em uso.<br />\n";
                echo "<a href=\"inventory.php\">Voltar</a>.";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }

            //Check to make sure clicking Sell wasn't an accident
            if (!$_POST['sure']) {
                include(__DIR__ . "/templates/private_header.php");
                echo "Você tem certeza que quer vender o/a <b>" . $sell['name'] . "</b> por <b>" . $valordavenda . "</b> de ouro?<br /><br />\n";
                echo "<form method=\"post\" action=\"shop.php?act=sell&id=" . $sell['id'] . "\">\n";
                echo "<input type=\"submit\" name=\"sure\" value=\"Sim, tenho certeza!\" />\n";
                echo "</form>\n";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }

            //Delete item from database, add gold to player's account
            if ($sell['mark'] == 't') {
                $query = $db->execute("delete from `market` where `market_id`=?", [$sell['id']]);
            }
            $query = $db->execute("delete from `items` where `id`=?", [$sell['id']]);
            $query = $db->execute("update `players` set `gold`=? where `id`=?", [$player->gold + $valordavenda, $player->id]);

            $player = check_user($secret_key, $db); //Get updated user info

            include(__DIR__ . "/templates/private_header.php");
            echo "Você vendeu seu/sua <b>" . $sell['name'] . "</b> por <b>" . $valordavenda . "</b> de ouro.<br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
        }
        break;




    case "mature":
        if (!$_GET['id']) { //No item ID
            header("Location: shop.php");
            break;
        }

        //Select the item from the database
        $query = $db->execute("select items.id, items.item_bonus, items.status, items.mark, blueprint_items.name, blueprint_items.price, blueprint_items.type, blueprint_items.canmature from `blueprint_items`, `items` where items.item_id=blueprint_items.id and items.player_id=? and items.id=?", [$player->id, $_GET['id']]);

        //Either item doesn't exist, or item doesn't belong to user
        if ($query->recordcount() == 0) {
            include(__DIR__ . "/templates/private_header.php");
            echo "Este item não existe!";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        $mature = $query->fetchrow(); //Get item info


        $precol = ceil(($mature['price'] / 3.5) * ($mature['item_bonus'] / 1.85));

        if ($mature['item_bonus'] == 0) {
            $precol = ceil($mature['price'] / 3.5);
        }

        if ($mature['item_bonus'] == 1) {
            $precol = ceil(($mature['price'] / 3.5) * 1.3);
        }

        if ($mature['item_bonus'] == 2) {
            $precol = ceil(($mature['price'] / 3.5) * 1.7);
        }

        if ($mature['item_bonus'] == 3) {
            $precol = ceil(($mature['price'] / 3.5) * 2);
        }



        if ($precol > $player->gold) {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Desculpe, mas você não pode pagar pela maturação. (" . $precol . " de ouro)</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($mature['item_bonus'] > 8) {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Seu item já está maturado ao máximo! (+9)</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($mature['mark'] == 't') {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Você não pode maturar itens a venda no mercado.</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($mature['type'] == 'addon' || $mature['type'] == 'potion' || $mature['type'] == 'stone') {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Você não pode maturar este tipo de item.</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if ($mature['canmature'] != 't') {
            include(__DIR__ . "/templates/private_header.php");
            echo "<b>Ferreiro:</b><br />\n";
            echo "<i>Você não pode maturar este item.<br/>Itens com preços mais baixos que 1000 moedas de ouro geralmente não podem ser maturados.</i><br /><br />\n";
            echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        if (!$_POST['sure']) {
            include(__DIR__ . "/templates/private_header.php");
            echo "Você tem certeza que quer maturar seu/sua <b>" . $mature['name'] . "</b> por <b>" . $precol . "</b> de ouro?<br />Maturar os seus itens os deixam com mais atributos. (+2 pontos)<br /><br />\n";
            echo "<form method=\"post\" action=\"shop.php?act=mature&id=" . $mature['id'] . "\">\n";
            echo "<input type=\"submit\" name=\"sure\" value=\"Sim, tenho certeza!\" />\n";
            echo "</form>\n";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }

        $addhp = $mature['type'] == 'amulet' && $mature['status'] == 'equipped' ? 50 : 0;

        $query = $db->execute("update `items` set `item_bonus`=? where `id`=?", [$mature['item_bonus'] + 1, $mature['id']]);
        $query = $db->execute("update `players` set `hp`=?, `maxhp`=?, `gold`=? where `id`=?", [$player->hp + $addhp, $player->maxhp + $addhp, $player->gold - $precol, $player->id]);

        $player = check_user($secret_key, $db); //Get updated user info

        include(__DIR__ . "/templates/private_header.php");
        echo "Você maturou seu/sua <b>" . $mature['name'] . "</b> por <b>" . $precol . "</b> de ouro.<br />";
        echo "Os atributos de seu item subiram em <b>2 pontos</b>.<br /><br />\n";
        echo "<a href=\"inventory.php\">Retornar ao inventário</a> | <a href=\"shop.php\">Retornar a loja</a>";
        include(__DIR__ . "/templates/private_footer.php");
        break;



    case "aceptespec":

        if ($player->level > 159) {
            $checaquest3 = $db->execute("select `id` from `quests` where `player_id`=? and `quest_status`=90 and `quest_id`=11", [$player->id]);
            if ($checaquest3->recordcount() > 0) {
                include(__DIR__ . "/templates/private_header.php");
                echo "<fieldset>\n";
                echo "<legend><b>Ferreiro</b></legend>\n";
                echo "Você já me pagou!<br/><br/>";
                echo "<a href=\"home.php\">Voltar</a>.";
                echo "</fieldset>";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }


            if ($player->gold > 499999) {
                include(__DIR__ . "/templates/private_header.php");
                $query88 = $db->execute("update `players` set `gold`=? where `id`=?", [$player->gold - 500000, $player->id]);
                $insert['player_id'] = $player->id;
                $insert['quest_id'] = 11;
                $insert['quest_status'] = 90;
                $query9886 = $db->autoexecute('quests', $insert, 'INSERT');
                echo "<fieldset>\n";
                echo "<legend><b>Ferreiro</b></legend>\n";
                echo "Obrigado. Agora de uma olhada nos meus itens especiais.<br/><br/>";
                echo "<a href=\"shop.php?act=espec\">Ver itens</a> | <a href=\"home.php\">Voltar</a>.";
                echo "</fieldset>";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            } else {
                include(__DIR__ . "/templates/private_header.php");
                echo "<fieldset>\n";
                echo "<legend><b>Ferreiro</b></legend>\n";
                echo "Você não possui ouro suficiente.<br/><br/>";
                echo "<a href=\"home.php\">Voltar</a>.";
                echo "</fieldset>";
                include(__DIR__ . "/templates/private_footer.php");
                break;
            }

        } else {
            include(__DIR__ . "/templates/private_header.php");
            echo "<fieldset>\n";
            echo "<legend><b>Ferreiro</b></legend>\n";
            echo "Você não possui nivel suficiente.<br/><br/>";
            echo "<a href=\"home.php\">Voltar</a>.";
            echo "</fieldset>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }
        break;





    case "espec":


        $checaquest2 = $db->execute("select `id` from `quests` where `player_id`=? and `quest_status`=90 and `quest_id`=11", [$player->id]);
        if ($checaquest2->recordcount() == 0) {
            include(__DIR__ . "/templates/private_header.php");
            echo "<fieldset>\n";
            echo "<legend><b>Ferreiro</b></legend>\n";
            echo "Possuo alguns itens especiais comigo, posso começar a vender alguns desses itens para você se você me pagar uma taxa de 500000 de ouro.<br/><br/>";
            echo "<a href=\"shop.php?act=aceptespec\">Eu pago</a> | <a href=\"home.php\">Voltar</a>.";
            echo "</fieldset>";
            include(__DIR__ . "/templates/private_footer.php");
            break;
        }


        //Construct query
        $query = "select `id`, `name`, `description`, `price`, `effectiveness`, `type`, `img`, `voc`, `needlvl`, `needpromo`, `needring` from `blueprint_items` where `canbuy`='s' order by `price` asc";

        $query = $db->execute($query);

        include(__DIR__ . "/templates/private_header.php");

        echo "<b>Ferreiro:</b><br/>\n";
        echo "<i>Estes são os itens especiais que eu tenho para você:</i><br /><br />\n";

        if ($query->recordcount() == 0) {
            echo "Nenhum iten encontrado! Volte mais tarde.";
        } else {
            while ($item = $query->fetchrow()) {
                echo "<fieldset>\n";
                echo "<legend><b>" . $item['name'] . "</b></legend>\n";
                echo "<table width=\"100%\">\n";
                echo "<tr><td width=\"5%\">";
                echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
                echo "</td><td width=\"75%\">";
                echo $item['description'] . "\n<br />";
                if ($item['type'] == 'amulet') {
                    $atributz = "Vitalidade";
                } elseif ($item['type'] == 'boots') {
                    $atributz = "Agilidade";
                } elseif ($item['type'] == 'weapon') {
                    $atributz = "Ataque";
                } else {
                    $atributz = "Defesa";
                }
                echo "<b>" . $atributz . ":</b> " . $item['effectiveness'] . "\n <b>Vocação:</b> ";


                if ($item['voc'] == 1 && $item['needpromo'] == 'f') {
                    echo "Caçador";
                } elseif ($item['voc'] == 2 && $item['needpromo'] == 'f') {
                    echo "Espadachim";
                } elseif ($item['voc'] == 3 && $item['needpromo'] == 'f') {
                    echo "Bruxo";
                } elseif ($item['voc'] == 1 && $item['needpromo'] == 't') {
                    echo "Arqueiro";
                } elseif ($item['voc'] == 2 && $item['needpromo'] == 't') {
                    echo "Guerreiro";
                } elseif ($item['voc'] == 3 && $item['needpromo'] == 't') {
                    echo "Mago";
                } elseif ($item['voc'] == 0 && $item['needpromo'] == 't') {
                    echo "Vocações superiores";
                } elseif ($item['voc'] == 1 && $item['needpromo'] == 'p') {
                    echo "Arqueiro Royal";
                } elseif ($item['voc'] == 2 && $item['needpromo'] == 'p') {
                    echo "Cavaleiro";
                } elseif ($item['voc'] == 3 && $item['needpromo'] == 'p') {
                    echo "Arquimago";
                } elseif ($item['voc'] == 0 && $item['needpromo'] == 'p') {
                    echo "Vocações supremas";
                } else {
                    echo "Todas";
                    if ($item['type'] == 'shield') {
                        echo " <font size=\"1\">(exceto arqueiros)</font>";
                    }
                }

                echo "</td><td width=\"20%\">";
                echo "<b>Preço:</b> " . $item['price'] . "<br />";
                echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Comprar</a><br />";
                echo "</td></tr>\n";
                if ($item['needlvl'] > 1) {
                    if ($player->level < $item['needlvl']) {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa ter nivel " . $item['needlvl'] . " ou mais para usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa ter nivel " . $item['needlvl'] . " ou mais para usar este item.</b></center></td></tr>\n";
                    }
                }
                if ($item['needring'] == "t") {
                    if ($player->promoted == "r" || $player->promoted == "s" || $player->promoted == "p") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    }
                }
                echo "</table>";
                echo "</fieldset>\n<br />";
            }
        }
        include(__DIR__ . "/templates/private_footer.php");
        break;




    case "amulet":
        //Check in case somebody entered 0
        $_GET['fromprice'] = ($_GET['fromprice'] == 0) ? "" : $_GET['fromprice'];
        $_GET['toprice'] = ($_GET['toprice'] == 0) ? "" : $_GET['toprice'];
        $_GET['fromeffect'] = ($_GET['fromeffect'] == 0) ? "" : $_GET['fromeffect'];
        $_GET['toeffect'] = ($_GET['toeffect'] == 0) ? "" : $_GET['toeffect'];

        //Construct query
        $query = "select `id`, `name`, `description`, `price`, `effectiveness`, `img`, `needpromo`, `needring` from `blueprint_items` where ";
        $query .= ($_GET['name'] != "") ? "`name` LIKE  ? and " : "";
        $query .= ($_GET['fromprice'] != "") ? "`price` >= ? and " : "";
        $query .= ($_GET['toprice'] != "") ? "`price` <= ? and " : "";
        $query .= ($_GET['fromeffect'] != "") ? "`effectiveness` >= ? and " : "";
        $query .= ($_GET['toeffect'] != "") ? "`effectiveness` <= ? and " : "";

        $query .= "`type`='amulet' and `canbuy`='t' order by `price` asc";

        //Construct values array for adoDB
        $values = [];
        if ($_GET['name'] != "") {
            $values[] = "%".trim((string) $_GET['name'])."%";
        }
        if ($_GET['fromprice']) {
            $values[] = (int) $_GET['fromprice'];
        }
        if ($_GET['toprice']) {
            $values[] = (int) $_GET['toprice'];
        }
        if ($_GET['fromeffect']) {
            $values[] = (int) $_GET['fromeffect'];
        }
        if ($_GET['toeffect']) {
            $values[] = (int) $_GET['toeffect'];
        }

        $query = $db->execute($query, $values); //Search!

        include(__DIR__ . "/templates/private_header.php");

        echo "<fieldset>";
        echo "<legend><b>Ferreiro</b></legend>\n";
        echo "<i>O quê você gostaria de ver, senhor?</i><br /><br />\n";
        echo "<form method=\"get\" action=\"shop.php\">\n";
        echo "<table width=\"100%\">\n";
        echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
        echo "<td width=\"60%\"><select name=\"act\" size=\"3\">\n";
        echo "<option value=\"amulet\" selected=\"selected\">Amuletos</option>\n";
        echo "<option value=\"weapon\">Armas</option>\n";
        echo "<option value=\"armor\">Armaduras</option>\n";
        echo "<option value=\"boots\">Botas</option>\n";
        echo "<option value=\"legs\">Calças</option>\n";
        if ($player->voc == 'archer') {
            echo "<option value=\"helmets\">Elmos</option>\n";
        } else {
            echo "<option value=\"helmets\">Elmos</option>\n";
            echo "<option value=\"shield\">Escudos</option>\n";
        }
        echo "</select></td>\n</tr>\n";
        echo "<tr>\n<td></td>";
        echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\" /> à <input type=\"text\" name=\"toprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['toprice']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
        echo "</table>";
        echo "</form>\n";
        echo "</fieldset>";
        echo "<br /><br />";
        echo "<b>Ferreiro:</b><br />\n";

        if ($query->recordcount() == 0) {
            echo "Nenhum iten encontrado! Tente procurar outra coisa.";
        } else {
            while ($item = $query->fetchrow()) {
                echo "<fieldset>\n";
                echo "<legend><b>" . $item['name'] . "</b></legend>\n";
                echo "<table width=\"100%\">\n";
                echo "<tr><td width=\"5%\">";
                echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
                echo "</td><td width=\"75%\">";
                echo $item['description'] . "\n<br />";
                echo "<b>Vitalidade:</b> " . $item['effectiveness'] . "\n";
                echo "</td><td width=\"20%\">";
                echo "<b>Preço:</b> " . $item['price'] . "<br />";
                echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Comprar</a><br />";
                echo "</td></tr>\n";
                if ($item['needpromo'] == "t") {
                    if ($player->promoted != "f") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    }
                }
                if ($item['needring'] == "t") {
                    if ($player->promoted == "r" || $player->promoted == "s" || $player->promoted == "p") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    }
                }
                echo "</table>";
                echo "</fieldset>\n<br />";
            }
        }

        if ($player->level > 159 && $player->level < 250) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a></center></div>";
        } elseif ($player->level > 159 && $player->level > 249) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a> | <a href=\"stones.php\"><b>Loja de pedras</b></a></center></div>";
        }

        include(__DIR__ . "/templates/private_footer.php");
        break;

    case "weapon":
        //Check in case somebody entered 0
        $_GET['fromprice'] = ($_GET['fromprice'] == 0) ? "" : $_GET['fromprice'];
        $_GET['toprice'] = ($_GET['toprice'] == 0) ? "" : $_GET['toprice'];
        $_GET['fromeffect'] = ($_GET['fromeffect'] == 0) ? "" : $_GET['fromeffect'];
        $_GET['toeffect'] = ($_GET['toeffect'] == 0) ? "" : $_GET['toeffect'];

        //Construct query

        if ($player->voc == 'archer') {
            $vocnumber = 1;
        } elseif ($player->voc == 'knight') {
            $vocnumber = 2;
        } elseif ($player->voc == 'mage') {
            $vocnumber = 3;
        }

        $query = "select `id`, `name`, `description`, `price`, `effectiveness`, `img`, `needpromo`, `needlvl`, `needring` from `blueprint_items` where ";
        $query .= ($_GET['name'] != "") ? "`name` LIKE  ? and " : "";
        $query .= ($_GET['fromprice'] != "") ? "`price` >= ? and " : "";
        $query .= ($_GET['toprice'] != "") ? "`price` <= ? and " : "";
        $query .= ($_GET['fromeffect'] != "") ? "`effectiveness` >= ? and " : "";
        $query .= ($_GET['toeffect'] != "") ? "`effectiveness` <= ? and " : "";

        $query .= "`type`='weapon' and `voc`='$vocnumber' and `canbuy`='t' order by `price` asc";

        //Construct values array for adoDB
        $values = [];
        if ($_GET['name'] != "") {
            $values[] = "%".trim((string) $_GET['name'])."%";
        }
        if ($_GET['fromprice']) {
            $values[] = (int) $_GET['fromprice'];
        }
        if ($_GET['toprice']) {
            $values[] = (int) $_GET['toprice'];
        }
        if ($_GET['fromeffect']) {
            $values[] = (int) $_GET['fromeffect'];
        }
        if ($_GET['toeffect']) {
            $values[] = (int) $_GET['toeffect'];
        }

        $query = $db->execute($query, $values); //Search!

        include(__DIR__ . "/templates/private_header.php");

        echo "<fieldset>";
        echo "<legend><b>Ferreiro</b></legend>\n";
        echo "<i>O quê você gostaria de ver, senhor?</i><br /><br />\n";
        echo "<form method=\"get\" action=\"shop.php\">\n";
        echo "<table width=\"100%\">\n";
        echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
        echo "<td width=\"60%\"><select name=\"act\" size=\"3\">\n";
        echo "<option value=\"amulet\">Amuletos</option>\n";
        echo "<option value=\"weapon\" selected=\"selected\">Armas</option>\n";
        echo "<option value=\"armor\">Armaduras</option>\n";
        echo "<option value=\"boots\">Botas</option>\n";
        echo "<option value=\"legs\">Calças</option>\n";
        if ($player->voc == 'archer') {
            echo "<option value=\"helmets\">Elmos</option>\n";
        } else {
            echo "<option value=\"helmets\">Elmos</option>\n";
            echo "<option value=\"shield\">Escudos</option>\n";
        }
        echo "</select></td>\n</tr>\n";
        echo "<tr>\n<td></td>";
        echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\" /> à <input type=\"text\" name=\"toprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['toprice']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
        echo "</table>";
        echo "</form>\n";
        echo "</fieldset>";
        echo "<br /><br />";
        echo "<b>Ferreiro:</b><br />\n";

        if ($query->recordcount() == 0) {
            echo "Nenhum iten encontrado! Tente procurar outra coisa.";
        } else {
            while ($item = $query->fetchrow()) {
                echo "<fieldset>\n";
                echo "<legend><b>" . $item['name'] . "</b></legend>\n";
                echo "<table width=\"100%\">\n";
                echo "<tr><td width=\"5%\">";
                echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
                echo "</td><td width=\"75%\">";
                echo $item['description'] . "\n<br />";
                echo "<b>Ataque:</b> " . $item['effectiveness'] . "\n";
                echo "</td><td width=\"20%\">";
                echo "<b>Preço:</b> " . $item['price'] . "<br />";
                echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Comprar</a><br />";
                echo "</td></tr>\n";
                if ($item['needlvl'] > 1) {
                    if ($player->level < $item['needlvl']) {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa ter nivel " . $item['needlvl'] . " ou mais para usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa ter nivel " . $item['needlvl'] . " ou mais para usar este item.</b></center></td></tr>\n";
                    }
                }
                if ($item['needpromo'] == "t") {
                    if ($player->promoted != "f") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    }
                }
                if ($item['needring'] == "t") {
                    if ($player->promoted == "r" || $player->promoted == "s" || $player->promoted == "p") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    }
                }
                echo "</table>";
                echo "</fieldset>\n<br />";
            }
        }

        if ($player->level > 159 && $player->level < 250) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a></center></div>";
        } elseif ($player->level > 159 && $player->level > 249) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a> | <a href=\"stones.php\"><b>Loja de pedras</b></a></center></div>";
        }

        include(__DIR__ . "/templates/private_footer.php");
        break;

    case "armor":
        //Check in case somebody entered 0
        $_GET['fromprice'] = ($_GET['fromprice'] == 0) ? "" : $_GET['fromprice'];
        $_GET['toprice'] = ($_GET['toprice'] == 0) ? "" : $_GET['toprice'];
        $_GET['fromeffect'] = ($_GET['fromeffect'] == 0) ? "" : $_GET['fromeffect'];
        $_GET['toeffect'] = ($_GET['toeffect'] == 0) ? "" : $_GET['toeffect'];

        //Construct query
        $query = "select `id`, `name`, `description`, `price`, `effectiveness`, `img`, `needpromo`, `needring` from `blueprint_items` where ";
        $query .= ($_GET['name'] != "") ? "`name` LIKE  ? and " : "";
        $query .= ($_GET['fromprice'] != "") ? "`price` >= ? and " : "";
        $query .= ($_GET['toprice'] != "") ? "`price` <= ? and " : "";
        $query .= ($_GET['fromeffect'] != "") ? "`effectiveness` >= ? and " : "";
        $query .= ($_GET['toeffect'] != "") ? "`effectiveness` <= ? and " : "";

        $query .= "`type`='armor' and `canbuy`='t' order by `price` asc";

        //Construct values array for adoDB
        $values = [];
        if ($_GET['name'] != "") {
            $values[] = "%".trim((string) $_GET['name'])."%";
        }
        if ($_GET['fromprice']) {
            $values[] = (int) $_GET['fromprice'];
        }
        if ($_GET['toprice']) {
            $values[] = (int) $_GET['toprice'];
        }
        if ($_GET['fromeffect']) {
            $values[] = (int) $_GET['fromeffect'];
        }
        if ($_GET['toeffect']) {
            $values[] = (int) $_GET['toeffect'];
        }

        $query = $db->execute($query, $values); //Search!

        include(__DIR__ . "/templates/private_header.php");

        echo "<fieldset>";
        echo "<legend><b>Ferreiro</b></legend>\n";
        echo "<i>O quê você gostaria de ver, senhor?</i><br /><br />\n";
        echo "<form method=\"get\" action=\"shop.php\">\n";
        echo "<table width=\"100%\">\n";
        echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
        echo "<td width=\"60%\"><select name=\"act\" size=\"3\">\n";
        echo "<option value=\"amulet\">Amuletos</option>\n";
        echo "<option value=\"weapon\">Armas</option>\n";
        echo "<option value=\"armor\" selected=\"selected\">Armaduras</option>\n";
        echo "<option value=\"boots\">Botas</option>\n";
        echo "<option value=\"legs\">Calças</option>\n";
        if ($player->voc == 'archer') {
            echo "<option value=\"helmets\">Elmos</option>\n";
        } else {
            echo "<option value=\"helmets\">Elmos</option>\n";
            echo "<option value=\"shield\">Escudos</option>\n";
        }
        echo "</select></td>\n</tr>\n";
        echo "<tr>\n<td></td>";
        echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\" /> à <input type=\"text\" name=\"toprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['toprice']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
        echo "</table>";
        echo "</form>\n";
        echo "</fieldset>";
        echo "<br /><br />";
        echo "<b>Ferreiro:</b><br />\n";

        if ($query->recordcount() == 0) {
            echo "Nenhum iten encontrado! Tente procurar outra coisa.";
        } else {
            while ($item = $query->fetchrow()) {
                echo "<fieldset>\n";
                echo "<legend><b>" . $item['name'] . "</b></legend>\n";
                echo "<table width=\"100%\">\n";
                echo "<tr><td width=\"5%\">";
                echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
                echo "</td><td width=\"75%\">";
                echo $item['description'] . "\n<br />";
                echo "<b>Defesa:</b> " . $item['effectiveness'] . "\n";
                echo "</td><td width=\"20%\">";
                echo "<b>Preço:</b> " . $item['price'] . "<br />";
                echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Comprar</a><br />";
                echo "</td></tr>\n";
                if ($item['needpromo'] == "t") {
                    if ($player->promoted != "f") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    }
                }
                if ($item['needring'] == "t") {
                    if ($player->promoted == "r" || $player->promoted == "s" || $player->promoted == "p") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    }
                }
                echo "</table>";
                echo "</fieldset>\n<br />";
            }
        }

        if ($player->level > 159 && $player->level < 250) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a></center></div>";
        } elseif ($player->level > 159 && $player->level > 249) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a> | <a href=\"stones.php\"><b>Loja de pedras</b></a></center></div>";
        }

        include(__DIR__ . "/templates/private_footer.php");
        break;

    case "boots":
        //Check in case somebody entered 0
        $_GET['fromprice'] = ($_GET['fromprice'] == 0) ? "" : $_GET['fromprice'];
        $_GET['toprice'] = ($_GET['toprice'] == 0) ? "" : $_GET['toprice'];
        $_GET['fromeffect'] = ($_GET['fromeffect'] == 0) ? "" : $_GET['fromeffect'];
        $_GET['toeffect'] = ($_GET['toeffect'] == 0) ? "" : $_GET['toeffect'];

        //Construct query
        $query = "select `id`, `name`, `description`, `price`, `effectiveness`, `img`, `needpromo`, `needring` from `blueprint_items` where ";
        $query .= ($_GET['name'] != "") ? "`name` LIKE  ? and " : "";
        $query .= ($_GET['fromprice'] != "") ? "`price` >= ? and " : "";
        $query .= ($_GET['toprice'] != "") ? "`price` <= ? and " : "";
        $query .= ($_GET['fromeffect'] != "") ? "`effectiveness` >= ? and " : "";
        $query .= ($_GET['toeffect'] != "") ? "`effectiveness` <= ? and " : "";

        $query .= "`type`='boots' and `canbuy`='t' order by `price` asc";

        //Construct values array for adoDB
        $values = [];
        if ($_GET['name'] != "") {
            $values[] = "%".trim((string) $_GET['name'])."%";
        }
        if ($_GET['fromprice']) {
            $values[] = (int) $_GET['fromprice'];
        }
        if ($_GET['toprice']) {
            $values[] = (int) $_GET['toprice'];
        }
        if ($_GET['fromeffect']) {
            $values[] = (int) $_GET['fromeffect'];
        }
        if ($_GET['toeffect']) {
            $values[] = (int) $_GET['toeffect'];
        }

        $query = $db->execute($query, $values); //Search!

        include(__DIR__ . "/templates/private_header.php");

        echo "<fieldset>";
        echo "<legend><b>Ferreiro</b></legend>\n";
        echo "<i>O quê você gostaria de ver, senhor?</i><br /><br />\n";
        echo "<form method=\"get\" action=\"shop.php\">\n";
        echo "<table width=\"100%\">\n";
        echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
        echo "<td width=\"60%\"><select name=\"act\" size=\"3\">\n";
        echo "<option value=\"amulet\">Amuletos</option>\n";
        echo "<option value=\"weapon\">Armas</option>\n";
        echo "<option value=\"armor\">Armaduras</option>\n";
        echo "<option value=\"boots\" selected=\"selected\">Botas</option>\n";
        echo "<option value=\"legs\">Calças</option>\n";
        if ($player->voc == 'archer') {
            echo "<option value=\"helmets\">Elmos</option>\n";
        } else {
            echo "<option value=\"helmets\">Elmos</option>\n";
            echo "<option value=\"shield\">Escudos</option>\n";
        }
        echo "</select></td>\n</tr>\n";
        echo "<tr>\n<td></td>";
        echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\" /> à <input type=\"text\" name=\"toprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['toprice']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
        echo "</table>";
        echo "</form>\n";
        echo "</fieldset>";
        echo "<br /><br />";
        echo "<b>Ferreiro:</b><br />\n";

        if ($query->recordcount() == 0) {
            echo "Nenhum iten encontrado! Tente procurar outra coisa.";
        } else {
            while ($item = $query->fetchrow()) {
                echo "<fieldset>\n";
                echo "<legend><b>" . $item['name'] . "</b></legend>\n";
                echo "<table width=\"100%\">\n";
                echo "<tr><td width=\"5%\">";
                echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
                echo "</td><td width=\"75%\">";
                echo $item['description'] . "\n<br />";
                echo "<b>Agilidade:</b> " . $item['effectiveness'] . "\n";
                echo "</td><td width=\"20%\">";
                echo "<b>Preço:</b> " . $item['price'] . "<br />";
                echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Comprar</a><br />";
                echo "</td></tr>\n";
                if ($item['needpromo'] == "t") {
                    if ($player->promoted != "f") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    }
                }
                if ($item['needring'] == "t") {
                    if ($player->promoted == "r" || $player->promoted == "s" || $player->promoted == "p") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    }
                }
                echo "</table>";
                echo "</fieldset>\n<br />";
            }
        }

        if ($player->level > 159 && $player->level < 250) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a></center></div>";
        } elseif ($player->level > 159 && $player->level > 249) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a> | <a href=\"stones.php\"><b>Loja de pedras</b></a></center></div>";
        }

        include(__DIR__ . "/templates/private_footer.php");
        break;

    case "legs":
        //Check in case somebody entered 0
        $_GET['fromprice'] = ($_GET['fromprice'] == 0) ? "" : $_GET['fromprice'];
        $_GET['toprice'] = ($_GET['toprice'] == 0) ? "" : $_GET['toprice'];
        $_GET['fromeffect'] = ($_GET['fromeffect'] == 0) ? "" : $_GET['fromeffect'];
        $_GET['toeffect'] = ($_GET['toeffect'] == 0) ? "" : $_GET['toeffect'];

        //Construct query
        $query = "select `id`, `name`, `description`, `price`, `effectiveness`, `img`, `needpromo`, `needring` from `blueprint_items` where ";
        $query .= ($_GET['name'] != "") ? "`name` LIKE  ? and " : "";
        $query .= ($_GET['fromprice'] != "") ? "`price` >= ? and " : "";
        $query .= ($_GET['toprice'] != "") ? "`price` <= ? and " : "";
        $query .= ($_GET['fromeffect'] != "") ? "`effectiveness` >= ? and " : "";
        $query .= ($_GET['toeffect'] != "") ? "`effectiveness` <= ? and " : "";

        $query .= "`type`='legs' and `canbuy`='t' order by `price` asc";

        //Construct values array for adoDB
        $values = [];
        if ($_GET['name'] != "") {
            $values[] = "%".trim((string) $_GET['name'])."%";
        }
        if ($_GET['fromprice']) {
            $values[] = (int) $_GET['fromprice'];
        }
        if ($_GET['toprice']) {
            $values[] = (int) $_GET['toprice'];
        }
        if ($_GET['fromeffect']) {
            $values[] = (int) $_GET['fromeffect'];
        }
        if ($_GET['toeffect']) {
            $values[] = (int) $_GET['toeffect'];
        }

        $query = $db->execute($query, $values); //Search!

        include(__DIR__ . "/templates/private_header.php");

        echo "<fieldset>";
        echo "<legend><b>Ferreiro</b></legend>\n";
        echo "<i>O quê você gostaria de ver, senhor?</i><br /><br />\n";
        echo "<form method=\"get\" action=\"shop.php\">\n";
        echo "<table width=\"100%\">\n";
        echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
        echo "<td width=\"60%\"><select name=\"act\" size=\"3\">\n";
        echo "<option value=\"amulet\">Amuletos</option>\n";
        echo "<option value=\"weapon\">Armas</option>\n";
        echo "<option value=\"armor\">Armaduras</option>\n";
        echo "<option value=\"boots\">Botas</option>\n";
        echo "<option value=\"legs\" selected=\"selected\">Calças</option>\n";
        if ($player->voc == 'archer') {
            echo "<option value=\"helmets\">Elmos</option>\n";
        } else {
            echo "<option value=\"helmets\">Elmos</option>\n";
            echo "<option value=\"shield\">Escudos</option>\n";
        }
        echo "</select></td>\n</tr>\n";
        echo "<tr>\n<td></td>";
        echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\" /> à <input type=\"text\" name=\"toprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['toprice']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
        echo "</table>";
        echo "</form>\n";
        echo "</fieldset>";
        echo "<br /><br />";
        echo "<b>Ferreiro:</b><br />\n";

        if ($query->recordcount() == 0) {
            echo "Nenhum iten encontrado! Tente procurar outra coisa.";
        } else {
            while ($item = $query->fetchrow()) {
                echo "<fieldset>\n";
                echo "<legend><b>" . $item['name'] . "</b></legend>\n";
                echo "<table width=\"100%\">\n";
                echo "<tr><td width=\"5%\">";
                echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
                echo "</td><td width=\"75%\">";
                echo $item['description'] . "\n<br />";
                echo "<b>Defesa:</b> " . $item['effectiveness'] . "\n";
                echo "</td><td width=\"20%\">";
                echo "<b>Preço:</b> " . $item['price'] . "<br />";
                echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Comprar</a><br />";
                echo "</td></tr>\n";
                if ($item['needpromo'] == "t") {
                    if ($player->promoted != "f") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    }
                }
                if ($item['needring'] == "t") {
                    if ($player->promoted == "r" || $player->promoted == "s" || $player->promoted == "p") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    }
                }
                echo "</table>";
                echo "</fieldset>\n<br />";
            }
        }

        if ($player->level > 159 && $player->level < 250) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a></center></div>";
        } elseif ($player->level > 159 && $player->level > 249) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a> | <a href=\"stones.php\"><b>Loja de pedras</b></a></center></div>";
        }

        include(__DIR__ . "/templates/private_footer.php");
        break;

    case "helmets":
        //Check in case somebody entered 0
        $_GET['fromprice'] = ($_GET['fromprice'] == 0) ? "" : $_GET['fromprice'];
        $_GET['toprice'] = ($_GET['toprice'] == 0) ? "" : $_GET['toprice'];
        $_GET['fromeffect'] = ($_GET['fromeffect'] == 0) ? "" : $_GET['fromeffect'];
        $_GET['toeffect'] = ($_GET['toeffect'] == 0) ? "" : $_GET['toeffect'];

        //Construct query
        $query = "select `id`, `name`, `description`, `price`, `effectiveness`, `img`, `needpromo`, `needring` from `blueprint_items` where ";
        $query .= ($_GET['name'] != "") ? "`name` LIKE  ? and " : "";
        $query .= ($_GET['fromprice'] != "") ? "`price` >= ? and " : "";
        $query .= ($_GET['toprice'] != "") ? "`price` <= ? and " : "";
        $query .= ($_GET['fromeffect'] != "") ? "`effectiveness` >= ? and " : "";
        $query .= ($_GET['toeffect'] != "") ? "`effectiveness` <= ? and " : "";

        $query .= "`type`='helmet' and `canbuy`='t' order by `price` asc";

        //Construct values array for adoDB
        $values = [];
        if ($_GET['name'] != "") {
            $values[] = "%".trim((string) $_GET['name'])."%";
        }
        if ($_GET['fromprice']) {
            $values[] = (int) $_GET['fromprice'];
        }
        if ($_GET['toprice']) {
            $values[] = (int) $_GET['toprice'];
        }
        if ($_GET['fromeffect']) {
            $values[] = (int) $_GET['fromeffect'];
        }
        if ($_GET['toeffect']) {
            $values[] = (int) $_GET['toeffect'];
        }

        $query = $db->execute($query, $values); //Search!

        include(__DIR__ . "/templates/private_header.php");

        echo "<fieldset>";
        echo "<legend><b>Ferreiro</b></legend>\n";
        echo "<i>O quê você gostaria de ver, senhor?</i><br /><br />\n";
        echo "<form method=\"get\" action=\"shop.php\">\n";
        echo "<table width=\"100%\">\n";
        echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
        echo "<td width=\"60%\"><select name=\"act\" size=\"3\">\n";
        echo "<option value=\"amulet\">Amuletos</option>\n";
        echo "<option value=\"weapon\">Armas</option>\n";
        echo "<option value=\"armor\">Armaduras</option>\n";
        echo "<option value=\"boots\">Botas</option>\n";
        echo "<option value=\"legs\">Calças</option>\n";
        if ($player->voc == 'archer') {
            echo "<option value=\"helmets\" selected=\"selected\">Elmos</option>\n";
        } else {
            echo "<option value=\"helmets\" selected=\"selected\">Elmos</option>\n";
            echo "<option value=\"shield\">Escudos</option>\n";
        }
        echo "</select></td>\n</tr>\n";
        echo "<tr>\n<td></td>";
        echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\" /> à <input type=\"text\" name=\"toprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['toprice']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
        echo "</table>";
        echo "</form>\n";
        echo "</fieldset>";
        echo "<br /><br />";
        echo "<b>Ferreiro:</b><br />\n";

        if ($query->recordcount() == 0) {
            echo "Nenhum iten encontrado! Tente procurar outra coisa.";
        } else {
            while ($item = $query->fetchrow()) {
                echo "<fieldset>\n";
                echo "<legend><b>" . $item['name'] . "</b></legend>\n";
                echo "<table width=\"100%\">\n";
                echo "<tr><td width=\"5%\">";
                echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
                echo "</td><td width=\"75%\">";
                echo $item['description'] . "\n<br />";
                echo "<b>Defesa:</b> " . $item['effectiveness'] . "\n";
                echo "</td><td width=\"20%\">";
                echo "<b>Preço:</b> " . $item['price'] . "<br />";
                echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Comprar</a><br />";
                echo "</td></tr>\n";
                if ($item['needpromo'] == "t") {
                    if ($player->promoted != "f") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    }
                }
                if ($item['needring'] == "t") {
                    if ($player->promoted == "r" || $player->promoted == "s" || $player->promoted == "p") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    }
                }
                echo "</table>";
                echo "</fieldset>\n<br />";
            }
        }

        if ($player->level > 159 && $player->level < 250) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a></center></div>";
        } elseif ($player->level > 159 && $player->level > 249) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a> | <a href=\"stones.php\"><b>Loja de pedras</b></a></center></div>";
        }

        include(__DIR__ . "/templates/private_footer.php");
        break;

    case "shield":
        //Check in case somebody entered 0
        $_GET['fromprice'] = ($_GET['fromprice'] == 0) ? "" : $_GET['fromprice'];
        $_GET['toprice'] = ($_GET['toprice'] == 0) ? "" : $_GET['toprice'];
        $_GET['fromeffect'] = ($_GET['fromeffect'] == 0) ? "" : $_GET['fromeffect'];
        $_GET['toeffect'] = ($_GET['toeffect'] == 0) ? "" : $_GET['toeffect'];

        //Construct query
        $query = "select `id`, `name`, `description`, `price`, `effectiveness`, `img`, `needpromo`, `needring` from `blueprint_items` where ";
        $query .= ($_GET['name'] != "") ? "`name` LIKE  ? and " : "";
        $query .= ($_GET['fromprice'] != "") ? "`price` >= ? and " : "";
        $query .= ($_GET['toprice'] != "") ? "`price` <= ? and " : "";
        $query .= ($_GET['fromeffect'] != "") ? "`effectiveness` >= ? and " : "";
        $query .= ($_GET['toeffect'] != "") ? "`effectiveness` <= ? and " : "";

        $query .= "`type`='shield' and `canbuy`='t' order by `price` asc";

        //Construct values array for adoDB
        $values = [];
        if ($_GET['name'] != "") {
            $values[] = "%".trim((string) $_GET['name'])."%";
        }
        if ($_GET['fromprice']) {
            $values[] = (int) $_GET['fromprice'];
        }
        if ($_GET['toprice']) {
            $values[] = (int) $_GET['toprice'];
        }
        if ($_GET['fromeffect']) {
            $values[] = (int) $_GET['fromeffect'];
        }
        if ($_GET['toeffect']) {
            $values[] = (int) $_GET['toeffect'];
        }

        $query = $db->execute($query, $values); //Search!

        include(__DIR__ . "/templates/private_header.php");

        echo "<fieldset>";
        echo "<legend><b>Ferreiro</b></legend>\n";
        echo "<i>O quê você gostaria de ver, senhor?</i><br /><br />\n";
        echo "<form method=\"get\" action=\"shop.php\">\n";
        echo "<table width=\"100%\">\n";
        echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
        echo "<td width=\"60%\"><select name=\"act\" size=\"3\">\n";
        echo "<option value=\"amulet\">Amuletos</option>\n";
        echo "<option value=\"weapon\">Armas</option>\n";
        echo "<option value=\"armor\">Armaduras</option>\n";
        echo "<option value=\"boots\">Botas</option>\n";
        echo "<option value=\"legs\">Calças</option>\n";
        if ($player->voc == 'archer') {
            echo "<option value=\"helmets\">Elmos</option>\n";
        } else {
            echo "<option value=\"helmets\">Elmos</option>\n";
            echo "<option value=\"shield\" selected=\"selected\">Escudos</option>\n";
        }
        echo "</select></td>\n</tr>\n";
        echo "<tr>\n<td></td>";
        echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\" /> à <input type=\"text\" name=\"toprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['toprice']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
        echo "</td>\n</tr>";
        echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
        echo "</table>";
        echo "</form>\n";
        echo "</fieldset>";
        echo "<br /><br />";
        echo "<b>Ferreiro:</b><br />\n";

        if ($query->recordcount() == 0) {
            echo "Nenhum iten encontrado! Tente procurar outra coisa.";
        } else {
            while ($item = $query->fetchrow()) {
                echo "<fieldset>\n";
                echo "<legend><b>" . $item['name'] . "</b></legend>\n";
                echo "<table width=\"100%\">\n";
                echo "<tr><td width=\"5%\">";
                echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
                echo "</td><td width=\"75%\">";
                echo $item['description'] . "\n<br />";
                echo "<b>Defesa:</b> " . $item['effectiveness'] . "\n";
                echo "</td><td width=\"20%\">";
                echo "<b>Preço:</b> " . $item['price'] . "<br />";
                echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Comprar</a><br />";
                echo "</td></tr>\n";


                if ($item['needpromo'] == "t") {
                    if ($player->promoted != "f") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa ter uma vocação superior para usar este item.</b></center></td></tr>\n";
                    }
                }
                if ($item['needring'] == "t") {
                    if ($player->promoted == "r" || $player->promoted == "s" || $player->promoted == "p") {
                        echo "<table style=\"width:100%; background-color:#BDF0A6;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    } else {
                        echo "<table style=\"width:100%; background-color:#EEA2A2;\"><tr><td><center><b>Você precisa estar usando o Jeweled Ring para poder usar este item.</b></center></td></tr>\n";
                    }
                }

                echo "</table>";
                echo "</fieldset>\n<br />";
            }
        }

        if ($player->level > 159 && $player->level < 250) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a></center></div>";
        } elseif ($player->level > 159 && $player->level > 249) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a> | <a href=\"stones.php\"><b>Loja de pedras</b></a></center></div>";
        }

        include(__DIR__ . "/templates/private_footer.php");
        break;

    default:
        //Show search form
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset>";
        echo "<legend><b>Ferreiro</b></legend>\n";
        echo "<i>O quê você gostaria de ver, senhor?</i><br /><br />\n";
        echo "<form method=\"get\" action=\"shop.php\">\n";
        echo "<table width=\"100%\">\n";
        echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
        echo "<td width=\"60%\"><select name=\"act\" size=\"3\">\n";
        echo "<option value=\"amulet\">Amuletos</option>\n";
        echo "<option value=\"weapon\">Armas</option>\n";
        echo "<option value=\"armor\" selected=\"selected\">Armaduras</option>\n";
        echo "<option value=\"boots\">Botas</option>\n";
        echo "<option value=\"legs\">Calças</option>\n";
        if ($player->voc == 'archer') {
            echo "<option value=\"helmets\">Elmos</option>\n";
        } else {
            echo "<option value=\"helmets\">Elmos</option>\n";
            echo "<option value=\"shield\">Escudos</option>\n";
        }
        echo "</select></td>\n</tr>\n";
        echo "<tr>\n<td></td>";
        echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"1\"/> à <input type=\"text\" name=\"toprice\" size=\"5\" value=\"59900\"/></td>\n";
        echo "</td>\n</tr>";
        echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
        echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"1\"/> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"100\"/></td>\n";
        echo "</td>\n</tr>";
        echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
        echo "</table>";
        echo "</form>\n";
        echo "</fieldset>";

        if ($player->level > 49 && $player->level < 160) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"stones.php\"><b>Loja de pedras</b></a></center></div>";
        } elseif ($player->level > 49 && $player->level > 159) {
            echo "<br/><br/><b>Visite também:</b>";
            echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"shop.php?act=espec\"><b>Loja Especial</b></a> | <a href=\"stones.php\"><b>Loja de pedras</b></a></center></div>";
        }

        include(__DIR__ . "/templates/private_footer.php");
        break;
}
