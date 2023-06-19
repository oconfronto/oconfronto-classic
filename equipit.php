<?php
	include("lib.php");
	$player = check_user($secret_key, $db);

if ($_GET['itid'])
{
	$query = $db->execute("select `status`, `item_id` from `items` where `id`=? and `player_id`=?", array($_GET['itid'], $player->id));
	if ($query->recordcount() == 1)
	{
		$item = $query->fetchrow();
		switch($item['status'])
		{
			case "unequipped": //User wants to equip item
				//$itemtype = $db->getone("select `type` from `blueprint_items` where `id`=?", array($item['item_id']));
				
				//Equip the selected item
				$ckitexs = $db->execute("select items.id, items.item_id, items.mark, items.player_id, blueprint_items.voc, blueprint_items.needlvl, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and items.id=?", array($player->id, $_GET['itid']));
				$ddckitexs = $ckitexs->fetchrow();
				if ($ckitexs->recordcount() == 0)
				{
				include("templates/private_header.php");
				echo "Um erro desconhecido ocorreu. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}
				if (($ddckitexs['voc'] == '1') and ($player->voc != 'archer'))
				{
				include("templates/private_header.php");
				echo "Você não pode usar este item. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}
				if (($ddckitexs['voc'] == '2') and ($player->voc != 'knight'))
				{
				include("templates/private_header.php");
				echo "Você não pode usar este item. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}
				if (($ddckitexs['voc'] == '3') and ($player->voc != 'mage'))
				{
				include("templates/private_header.php");
				echo "Você não pode usar este item. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}
				if (($ddckitexs['type'] == 'shield') and ($player->voc == 'archer'))
				{
				include("templates/private_header.php");
				echo "Arqueiros não podem usar escudos. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}


				if ($ddckitexs['needlvl'] > $player->level)
				{
				include("templates/private_header.php");
				echo "Você não tem nível suficiente para usar este item. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}

				if ($ddckitexs['type'] == 'addon')
				{
				include("templates/private_header.php");
				echo "Você não pode usar este item. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}

				if ($ddckitexs['mark'] == t)
				{
				include("templates/private_header.php");
				echo "Você não pode usar um item que está à venda no mercado. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}

				if (($ddckitexs['needpromo'] == 't') and ($player->promoted == 'f'))
				{
				include("templates/private_header.php");
				echo "Apenas usuários de vocação superior podem usar este item. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}

				if (($ddckitexs['needpromo'] == 'p') and ($player->promoted != 'p'))
				{
				include("templates/private_header.php");
				echo "Apenas usuários de vocação suprema podem usar este item. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}

				if (($ddckitexs['needring'] == 't') and ($player->promoted == 'f' or $player->promoted == 't'))
				{
				include("templates/private_header.php");
				echo "Você precisa estar usando o Jeweled Ring para poder usar este item. <a href=\"inventory.php\">Voltar</a>.";
				include("templates/private_footer.php");
				exit;
				}

				//Check if another item is already equipped
				$unequip = $db->getone("select items.id from `items`, `blueprint_items` where items.item_id = blueprint_items.id and blueprint_items.type=(select `type` from `blueprint_items` where `id`=?) and items.player_id=? and `status`='equipped'", array($item['item_id'], $player->id));
				if ($unequip) //If so, then unequip it (only one item may be equipped at any one time)
				{
					$player = check_user($secret_key, $db); //Get new stats
					$query = $db->execute("select items.item_id, items.item_bonus, items.vit, blueprint_items.type, blueprint_items.effectiveness from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.id=?", array($unequip));
					$item = $query->fetchrow();
					if ($item['type'] == amulet){
					$maxhp = $player->maxhp - (($item['effectiveness'] + ($item['item_bonus'] * 2) + $item['vit']) * 25);
					$playerhp = $player->hp - (($item['effectiveness'] + ($item['item_bonus'] * 2) + $item['vit']) * 25);
					$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", array($playerhp, $maxhp, $player->id));
					}elseif ($item['type'] != amulet){
					$maxhp = $player->maxhp - ($item['vit'] * 25);
					$playerhp = $player->hp - ($item['vit'] * 25);
					$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", array($playerhp, $maxhp, $player->id));
					}
					$query = $db->execute("update `items` set `status`='unequipped' where `id`=?", array($unequip));
				}
				$player = check_user($secret_key, $db); //Get new stats
				$query = $db->execute("select items.item_id, items.item_bonus, items.vit, blueprint_items.type, blueprint_items.effectiveness from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.id=?", array($_GET['itid']));
				$item = $query->fetchrow();
				if ($item['type'] == amulet){
				$maxhp = $player->maxhp + (($item['effectiveness'] + ($item['item_bonus'] * 2) + $item['vit']) * 25);
				$playerhp = $player->hp + (($item['effectiveness'] + ($item['item_bonus'] * 2) + $item['vit']) * 25);
				$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", array($playerhp, $maxhp, $player->id));
				}elseif ($item['type'] != amulet){
				$maxhp = $player->maxhp + ($item['vit'] * 25);
				$playerhp = $player->hp + ($item['vit'] * 25);
				$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", array($playerhp, $maxhp, $player->id));
				}
				$query = $db->execute("update `items` set `status`='equipped' where `id`=?", array($_GET['itid']));
				break;
      			case "equipped": //User wants to unequip item
					$player = check_user($secret_key, $db); //Get new stats
					$query = $db->execute("select items.item_id, items.item_bonus, items.vit, blueprint_items.type, blueprint_items.effectiveness from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.id=?", array($_GET['itid']));
					$item = $query->fetchrow();
					if ($item['type'] == amulet){
					$maxhp = $player->maxhp - (($item['effectiveness'] + ($item['item_bonus'] * 2) + $item['vit']) * 25);
					$playerhp = $player->hp - (($item['effectiveness'] + ($item['item_bonus'] * 2) + $item['vit']) * 25);
					$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", array($playerhp, $maxhp, $player->id));
					}elseif ($item['type'] != amulet){
					$maxhp = $player->maxhp - ($item['vit'] * 25);
					$playerhp = $player->hp - ($item['vit'] * 25);
					$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", array($playerhp, $maxhp, $player->id));
					}
				$query = $db->execute("update `items` set `status`='unequipped' where `id`=?", array($_GET['itid']));
				break;
			default: //Set status to unequipped, in case the item had no status when it was inserted into db
					$player = check_user($secret_key, $db); //Get new stats
					$query = $db->execute("select items.item_id, items.item_bonus, items.vit, blueprint_items.type, blueprint_items.effectiveness from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.id=?", array($_GET['itid']));
					$item = $query->fetchrow();
					if ($item['type'] == amulet){
					$maxhp = $player->maxhp - (($item['effectiveness'] + ($item['item_bonus'] * 2) + $item['vit']) * 25);
					$playerhp = $player->hp - (($item['effectiveness'] + ($item['item_bonus'] * 2) + $item['vit']) * 25);
					$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", array($playerhp, $maxhp, $player->id));
					}elseif ($item['type'] != amulet){
					$maxhp = $player->maxhp - ($item['vit'] * 25);
					$playerhp = $player->hp - ($item['vit'] * 25);
					$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", array($playerhp, $maxhp, $player->id));
					}
				$query = $db->execute("update `items` set `status`='unequipped' where `id`=?", array($_GET['itid']));
				break;
		}
	}
}


header("Location: inventory.php");
exit;
?>