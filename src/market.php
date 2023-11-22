<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Mercado");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkhp.php");
include(__DIR__ . "/checkwork.php");

$order=$_GET['order'];

if($order=="item"){
$orderby="blueprint_items.name";
}
elseif($order=="cost"){
$orderby="market.price";
}
elseif($order=="efec"){
$orderby="blueprint_items.effectiveness";
}
elseif($order=="voc"){
$orderby="blueprint_items.voc";
}
elseif($order=="seller"){
$orderby="market.seller";
}else{
$orderby="market.price"; $abc="asc";
}

switch($_GET['act'])
{
	case "remove":
		if (!$_GET['item']){
		include(__DIR__ . "/templates/private_header.php");
		echo "Um erro desconhecido ocorreu.<br/><a href=\"market.php\">Voltar</a>.";
		include(__DIR__ . "/templates/private_footer.php");
		break;
		}

		$verifik = $db->execute("select market.seller, blueprint_items.name, items.item_bonus, items.mark from `market`, `blueprint_items`, `items` where market.ite_id=blueprint_items.id and market.market_id=items.id and market.market_id=?", [$_GET['item']]);
		if ($verifik->recordcount() == 0)
		{
		include(__DIR__ . "/templates/private_header.php");
		echo "Um erro desconhecido ocorreu.<br/><a href=\"market.php\">Voltar</a>.";
		include(__DIR__ . "/templates/private_footer.php");
		break;
		}

		$item = $verifik->fetchrow();

		if ($item['seller'] != $player->username){
		include(__DIR__ . "/templates/private_header.php");
		echo "Você não pode remover este item do mercado.<br/><a href=\"market.php\">Voltar</a>.";
		include(__DIR__ . "/templates/private_footer.php");
		break;
		}

		if (!$_GET['confirm']){
		include(__DIR__ . "/templates/private_header.php");
		echo "Tem certeza que seseja remover seu item do mercado? (" . $item['name'] . ")<br/><a href=\"market.php?act=remove&item=" . $_GET['item'] . "&confirm=yes\">Sim</a> | <a href=\"market.php\">Voltar</a>.";
		include(__DIR__ . "/templates/private_footer.php");
		}else{
		$mark_sold=$db->execute("update `items` set `mark`='f' where `id`=?", [$_GET['item']]);
		$query_delete=$db->execute("delete from `market` where `market_id`=?", [$_GET['item']]);
		include(__DIR__ . "/templates/private_header.php");
		echo "Você removeu seu item do mercado<br/><a href=\"market.php\">Voltar</a>.";
		include(__DIR__ . "/templates/private_footer.php");
		}
	break;


	case "amulet":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select market.market_id, market.price, market.seller, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.voc, items.item_bonus, items.for, items.vit, items.agi, items.res from `market`, `blueprint_items`, `items` where ";
		$query .= ($_GET['fromprice'] != "")?"market.price >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"market.price <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		$query .= "market.ite_id=blueprint_items.id and market.market_id=items.id and blueprint_items.type='amulet' and market.serv='$player->serv' order by $orderby $abc";
		
		//Construct values array for adoDB
		$values = [];
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
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\" selected=\"selected\">Amuletos</option>\n";
		echo "<option value=\"weapon\">Armas</option>\n";
		echo "<option value=\"armor\">Armaduras</option>\n";
		echo "<option value=\"boots\">Botas</option>\n";
		echo "<option value=\"legs\">Calças</option>\n";
		echo "<option value=\"helmet\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\">Escudos</option>\n";
		}
		echo "<option value=\"potion\">Poções</option>\n";
		echo "<option value=\"addon\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br />";
		
  		if ($query->recordcount() == 0)
		{
			echo "<center><b>Nenhum iten encontrado! Tente procurar outra coisa.</b></center>";
		}
		else
		{

			echo "<fieldset>";
			echo "<legend><b>Mercado</b></legend>";
			echo "<table width=\"100%\" border=\"0\">";
			echo "<tr>";
			echo "<th width=\"40%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&order=item&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Item</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=efec&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vitalidade</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=cost&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Preço</a></b></td>";
			echo "<th width=\"20%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=voc&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vocação</a></b></td>";
			echo "<th width=\"10%\"><b>Ação</b></td>";
			echo "</tr>";

			while ($item = $query->fetchrow())
			{
				echo "<tr>\n";
				$bonus1 = $item['item_bonus'] > 0 ? " +" . $item['item_bonus'] . "" : "";
				$bonus2 = $item['for'] > 0 ? " <font color=\"gray\">+" . $item['for'] . "F</font>" : "";
				$bonus3 = $item['vit'] > 0 ? " <font color=\"green\">+" . $item['vit'] . "V</font>" : "";
				$bonus4 = $item['agi'] > 0 ? " <font color=\"blue\">+" . $item['agi'] . "A</font>" : "";
				$bonus5 = $item['res'] > 0 ? " <font color=\"red\">+" . $item['res'] . "R</font>" : "";
				echo "<td>" . $item['name'] . " <font size=\"1\">" . $bonus1 . "" . $bonus2 . "" . $bonus3 . "" . $bonus4 . "" . $bonus5 . "</font></td>";
				echo "<td>" . ($item['effectiveness'] + ($item['item_bonus'] * 2)) . "</td>";
				echo "<td>" . $item['price'] . "</td>";
				echo "<td>";
				if ($item['voc'] == 1 && $item['needpromo'] == 'f')
				{
				echo "Caçador";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'f')
				{
				echo "Espadachim";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'f')
				{
				echo "Bruxo";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 't')
				{
				echo "Arqueiro";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 't')
				{
				echo "Guerreiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 't')
				{
				echo "Mago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 't')
				{
				echo "Vocações superiores";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 'p')
				{
				echo "Arqueiro Royal";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'p')
				{
				echo "Cavaleiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'p')
				{
				echo "Arquimago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 'p')
				{
				echo "Vocações supremas";
				}else{
				echo "Todas";
				}
				echo "</td>";
				if($item['seller']==$player->username){
				echo "<td><a href=\"market.php?act=remove&item=" . $item['market_id'] . "\">Remover</a></td>\n";
				}else{
				echo "<td><a href=\"market_buy.php?act=buy&item=" . $item['market_id'] . "\">Comprar</a></td>\n";
				}

			}
			echo "</tr>";
			echo "</table>";
			echo "</fieldset>";
		}

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";
		
		include(__DIR__ . "/templates/private_footer.php");
		break;



	case "weapon":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select market.market_id, market.price, market.seller, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.voc, items.item_bonus, items.for, items.vit, items.agi, items.res from `market`, `blueprint_items`, `items` where ";
		$query .= ($_GET['fromprice'] != "")?"market.price >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"market.price <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		$query .= "market.ite_id=blueprint_items.id and market.market_id=items.id and blueprint_items.type='weapon' and market.serv='$player->serv' order by $orderby $abc";
		
		//Construct values array for adoDB
		$values = [];
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
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\">Amuletos</option>\n";
		echo "<option value=\"weapon\" selected=\"selected\">Armas</option>\n";
		echo "<option value=\"armor\">Armaduras</option>\n";
		echo "<option value=\"boots\">Botas</option>\n";
		echo "<option value=\"legs\">Calças</option>\n";
		echo "<option value=\"helmet\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\">Escudos</option>\n";
		}
		echo "<option value=\"potion\">Poções</option>\n";
		echo "<option value=\"addon\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br />";
		
  		if ($query->recordcount() == 0)
		{
			echo "<center><b>Nenhum iten encontrado! Tente procurar outra coisa.</b></center>";
		}
		else
		{

			echo "<fieldset>";
			echo "<legend><b>Mercado</b></legend>";
			echo "<table width=\"100%\" border=\"0\">";
			echo "<tr>";
			echo "<th width=\"40%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&order=item&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Item</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=efec&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Ataque</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=cost&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Preço</a></b></td>";
			echo "<th width=\"20%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=voc&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vocação</a></b></td>";
			echo "<th width=\"10%\"><b>Ação</b></td>";
			echo "</tr>";

			while ($item = $query->fetchrow())
			{
				echo "<tr>\n";
				$bonus1 = $item['item_bonus'] > 0 ? " +" . $item['item_bonus'] . "" : "";
				$bonus2 = $item['for'] > 0 ? " <font color=\"gray\">+" . $item['for'] . "F</font>" : "";
				$bonus3 = $item['vit'] > 0 ? " <font color=\"green\">+" . $item['vit'] . "V</font>" : "";
				$bonus4 = $item['agi'] > 0 ? " <font color=\"blue\">+" . $item['agi'] . "A</font>" : "";
				$bonus5 = $item['res'] > 0 ? " <font color=\"red\">+" . $item['res'] . "R</font>" : "";
				echo "<td>" . $item['name'] . " <font size=\"1\">" . $bonus1 . "" . $bonus2 . "" . $bonus3 . "" . $bonus4 . "" . $bonus5 . "</font></td>";
				echo "<td>" . ($item['effectiveness'] + ($item['item_bonus'] * 2)) . "</td>";
				echo "<td>" . $item['price'] . "</td>";
				echo "<td>";
				if ($item['voc'] == 1 && $item['needpromo'] == 'f')
				{
				echo "Caçador";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'f')
				{
				echo "Espadachim";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'f')
				{
				echo "Bruxo";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 't')
				{
				echo "Arqueiro";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 't')
				{
				echo "Guerreiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 't')
				{
				echo "Mago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 't')
				{
				echo "Vocações superiores";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 'p')
				{
				echo "Arqueiro Royal";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'p')
				{
				echo "Cavaleiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'p')
				{
				echo "Arquimago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 'p')
				{
				echo "Vocações supremas";
				}else{
				echo "Todas";
				}
				echo "</td>";
				if($item['seller']==$player->username){
				echo "<td><a href=\"market.php?act=remove&item=" . $item['market_id'] . "\">Remover</a></td>\n";
				}else{
				echo "<td><a href=\"market_buy.php?act=buy&item=" . $item['market_id'] . "\">Comprar</a></td>\n";
				}

			}
			echo "</tr>";
			echo "</table>";
			echo "</fieldset>";
		}

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";
		
		include(__DIR__ . "/templates/private_footer.php");
		break;


	case "armor":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select market.market_id, market.price, market.seller, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.voc, items.item_bonus, items.for, items.vit, items.agi, items.res from `market`, `blueprint_items`, `items` where ";
		$query .= ($_GET['fromprice'] != "")?"market.price >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"market.price <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		$query .= "market.ite_id=blueprint_items.id and market.market_id=items.id and blueprint_items.type='armor' and market.serv='$player->serv' order by $orderby $abc";
		
		//Construct values array for adoDB
		$values = [];
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
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\">Amuletos</option>\n";
		echo "<option value=\"weapon\">Armas</option>\n";
		echo "<option value=\"armor\" selected=\"selected\">Armaduras</option>\n";
		echo "<option value=\"boots\">Botas</option>\n";
		echo "<option value=\"legs\">Calças</option>\n";
		echo "<option value=\"helmet\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\">Escudos</option>\n";
		}
		echo "<option value=\"potion\">Poções</option>\n";
		echo "<option value=\"addon\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br />";
		
  		if ($query->recordcount() == 0)
		{
			echo "<center><b>Nenhum iten encontrado! Tente procurar outra coisa.</b></center>";
		}
		else
		{

			echo "<fieldset>";
			echo "<legend><b>Mercado</b></legend>";
			echo "<table width=\"100%\" border=\"0\">";
			echo "<tr>";
			echo "<th width=\"40%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&order=item&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Item</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=efec&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Defesa</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=cost&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Preço</a></b></td>";
			echo "<th width=\"20%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=voc&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vocação</a></b></td>";
			echo "<th width=\"10%\"><b>Ação</b></td>";
			echo "</tr>";

			while ($item = $query->fetchrow())
			{
				echo "<tr>\n";
				$bonus1 = $item['item_bonus'] > 0 ? " +" . $item['item_bonus'] . "" : "";
				$bonus2 = $item['for'] > 0 ? " <font color=\"gray\">+" . $item['for'] . "F</font>" : "";
				$bonus3 = $item['vit'] > 0 ? " <font color=\"green\">+" . $item['vit'] . "V</font>" : "";
				$bonus4 = $item['agi'] > 0 ? " <font color=\"blue\">+" . $item['agi'] . "A</font>" : "";
				$bonus5 = $item['res'] > 0 ? " <font color=\"red\">+" . $item['res'] . "R</font>" : "";
				echo "<td>" . $item['name'] . " <font size=\"1\">" . $bonus1 . "" . $bonus2 . "" . $bonus3 . "" . $bonus4 . "" . $bonus5 . "</font></td>";
				echo "<td>" . ($item['effectiveness'] + ($item['item_bonus'] * 2)) . "</td>";
				echo "<td>" . $item['price'] . "</td>";
				echo "<td>";
				if ($item['voc'] == 1 && $item['needpromo'] == 'f')
				{
				echo "Caçador";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'f')
				{
				echo "Espadachim";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'f')
				{
				echo "Bruxo";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 't')
				{
				echo "Arqueiro";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 't')
				{
				echo "Guerreiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 't')
				{
				echo "Mago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 't')
				{
				echo "Vocações superiores";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 'p')
				{
				echo "Arqueiro Royal";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'p')
				{
				echo "Cavaleiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'p')
				{
				echo "Arquimago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 'p')
				{
				echo "Vocações supremas";
				}else{
				echo "Todas";
				}
				echo "</td>";
				if($item['seller']==$player->username){
				echo "<td><a href=\"market.php?act=remove&item=" . $item['market_id'] . "\">Remover</a></td>\n";
				}else{
				echo "<td><a href=\"market_buy.php?act=buy&item=" . $item['market_id'] . "\">Comprar</a></td>\n";
				}

			}
			echo "</tr>";
			echo "</table>";
			echo "</fieldset>";
		}

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";
		
		include(__DIR__ . "/templates/private_footer.php");
		break;


	case "boots":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select market.market_id, market.price, market.seller, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.voc, items.item_bonus, items.for, items.vit, items.agi, items.res from `market`, `blueprint_items`, `items` where ";
		$query .= ($_GET['fromprice'] != "")?"market.price >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"market.price <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		$query .= "market.ite_id=blueprint_items.id and market.market_id=items.id and blueprint_items.type='boots' and market.serv='$player->serv' order by $orderby $abc";
		
		//Construct values array for adoDB
		$values = [];
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
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\">Amuletos</option>\n";
		echo "<option value=\"weapon\">Armas</option>\n";
		echo "<option value=\"armor\">Armaduras</option>\n";
		echo "<option value=\"boots\" selected=\"selected\">Botas</option>\n";
		echo "<option value=\"legs\">Calças</option>\n";
		echo "<option value=\"helmet\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\">Escudos</option>\n";
		}
		echo "<option value=\"potion\">Poções</option>\n";
		echo "<option value=\"addon\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br />";
		
  		if ($query->recordcount() == 0)
		{
			echo "<center><b>Nenhum iten encontrado! Tente procurar outra coisa.</b></center>";
		}
		else
		{

			echo "<fieldset>";
			echo "<legend><b>Mercado</b></legend>";
			echo "<table width=\"100%\" border=\"0\">";
			echo "<tr>";
			echo "<th width=\"40%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&order=item&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Item</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=efec&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Defesa</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=cost&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Preço</a></b></td>";
			echo "<th width=\"20%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=voc&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vocação</a></b></td>";
			echo "<th width=\"10%\"><b>Ação</b></td>";
			echo "</tr>";

			while ($item = $query->fetchrow())
			{
				echo "<tr>\n";
				$bonus1 = $item['item_bonus'] > 0 ? " +" . $item['item_bonus'] . "" : "";
				$bonus2 = $item['for'] > 0 ? " <font color=\"gray\">+" . $item['for'] . "F</font>" : "";
				$bonus3 = $item['vit'] > 0 ? " <font color=\"green\">+" . $item['vit'] . "V</font>" : "";
				$bonus4 = $item['agi'] > 0 ? " <font color=\"blue\">+" . $item['agi'] . "A</font>" : "";
				$bonus5 = $item['res'] > 0 ? " <font color=\"red\">+" . $item['res'] . "R</font>" : "";
				echo "<td>" . $item['name'] . " <font size=\"1\">" . $bonus1 . "" . $bonus2 . "" . $bonus3 . "" . $bonus4 . "" . $bonus5 . "</font></td>";
				echo "<td>" . ($item['effectiveness'] + ($item['item_bonus'] * 2)) . "</td>";
				echo "<td>" . $item['price'] . "</td>";
				echo "<td>";
				if ($item['voc'] == 1 && $item['needpromo'] == 'f')
				{
				echo "Caçador";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'f')
				{
				echo "Espadachim";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'f')
				{
				echo "Bruxo";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 't')
				{
				echo "Arqueiro";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 't')
				{
				echo "Guerreiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 't')
				{
				echo "Mago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 't')
				{
				echo "Vocações superiores";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 'p')
				{
				echo "Arqueiro Royal";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'p')
				{
				echo "Cavaleiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'p')
				{
				echo "Arquimago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 'p')
				{
				echo "Vocações supremas";
				}else{
				echo "Todas";
				}
				echo "</td>";
				if($item['seller']==$player->username){
				echo "<td><a href=\"market.php?act=remove&item=" . $item['market_id'] . "\">Remover</a></td>\n";
				}else{
				echo "<td><a href=\"market_buy.php?act=buy&item=" . $item['market_id'] . "\">Comprar</a></td>\n";
				}

			}
			echo "</tr>";
			echo "</table>";
			echo "</fieldset>";
		}

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";
		
		include(__DIR__ . "/templates/private_footer.php");
		break;

	case "legs":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select market.market_id, market.price, market.seller, market.expira, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.voc, items.item_bonus, items.for, items.vit, items.agi, items.res from `market`, `blueprint_items`, `items` where ";
		$query .= ($_GET['fromprice'] != "")?"market.price >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"market.price <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		$query .= "market.ite_id=blueprint_items.id and market.market_id=items.id and blueprint_items.type='legs' and market.serv='$player->serv' order by $orderby $abc";
		
		//Construct values array for adoDB
		$values = [];
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
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\">Amuletos</option>\n";
		echo "<option value=\"weapon\">Armas</option>\n";
		echo "<option value=\"armor\">Armaduras</option>\n";
		echo "<option value=\"boots\">Botas</option>\n";
		echo "<option value=\"legs\" selected=\"selected\">Calças</option>\n";
		echo "<option value=\"helmet\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\">Escudos</option>\n";
		}
		echo "<option value=\"potion\">Poções</option>\n";
		echo "<option value=\"addon\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br />";
		
  		if ($query->recordcount() == 0)
		{
			echo "<center><b>Nenhum iten encontrado! Tente procurar outra coisa.</b></center>";
		}
		else
		{

			echo "<fieldset>";
			echo "<legend><b>Mercado</b></legend>";
			echo "<table width=\"100%\" border=\"0\">";
			echo "<tr>";
			echo "<th width=\"40%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&order=item&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Item</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=efec&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Defesa</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=cost&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Preço</a></b></td>";
			echo "<th width=\"20%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=voc&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vocação</a></b></td>";
			echo "<th width=\"10%\"><b>Ação</b></td>";
			echo "</tr>";

			while ($item = $query->fetchrow())
			{
				if (time() > $item['expira']){
				$logmsg = "Parece que ninguém se interessou pelo seu/sua " . $item['name'] . ".<br/>Ela ficou 2 semanas no mercado, e agora foi removida do mercado.";
				addlog($player->id, $logmsg, $db);
				$query = $db->execute("update `items` set `mark`='f', `status`='unequipped' where `id`=?",[$item['market_id']]);
				$query = $db->execute("delete from `market` where `market_id`=?", [$item['market_id']]);
				}else{

				echo "<tr>\n";
				$bonus1 = $item['item_bonus'] > 0 ? " +" . $item['item_bonus'] . "" : "";
				$bonus2 = $item['for'] > 0 ? " <font color=\"gray\">+" . $item['for'] . "F</font>" : "";
				$bonus3 = $item['vit'] > 0 ? " <font color=\"green\">+" . $item['vit'] . "V</font>" : "";
				$bonus4 = $item['agi'] > 0 ? " <font color=\"blue\">+" . $item['agi'] . "A</font>" : "";
				$bonus5 = $item['res'] > 0 ? " <font color=\"red\">+" . $item['res'] . "R</font>" : "";

				echo "<td>" . $item['name'] . " <font size=\"1\">" . $bonus1 . "" . $bonus2 . "" . $bonus3 . "" . $bonus4 . "" . $bonus5 . "</font></td>";
				echo "<td>" . ($item['effectiveness'] + ($item['item_bonus'] * 2)) . "</td>";
				echo "<td>" . $item['price'] . "</td>";
				echo "<td>";
				if ($item['voc'] == 1 && $item['needpromo'] == 'f')
				{
				echo "Caçador";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'f')
				{
				echo "Espadachim";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'f')
				{
				echo "Bruxo";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 't')
				{
				echo "Arqueiro";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 't')
				{
				echo "Guerreiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 't')
				{
				echo "Mago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 't')
				{
				echo "Vocações superiores";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 'p')
				{
				echo "Arqueiro Royal";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'p')
				{
				echo "Cavaleiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'p')
				{
				echo "Arquimago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 'p')
				{
				echo "Vocações supremas";
				}else{
				echo "Todas";
				}
				echo "</td>";
				if($item['seller']==$player->username){
				echo "<td><a href=\"market.php?act=remove&item=" . $item['market_id'] . "\">Remover</a></td>\n";
				}else{
				echo "<td><a href=\"market_buy.php?act=buy&item=" . $item['market_id'] . "\">Comprar</a></td>\n";
				}
				}

			}
			echo "</tr>";
			echo "</table>";
			echo "</fieldset>";
		}

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";
		
		include(__DIR__ . "/templates/private_footer.php");
		break;
	


	case "helmet":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select market.market_id, market.price, market.seller, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.voc, items.item_bonus, items.for, items.vit, items.agi, items.res from `market`, `blueprint_items`, `items` where ";
		$query .= ($_GET['fromprice'] != "")?"market.price >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"market.price <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		$query .= "market.ite_id=blueprint_items.id and market.market_id=items.id and blueprint_items.type='helmet' and market.serv='$player->serv' order by $orderby $abc";
		
		//Construct values array for adoDB
		$values = [];
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
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\">Amuletos</option>\n";
		echo "<option value=\"weapon\">Armas</option>\n";
		echo "<option value=\"armor\">Armaduras</option>\n";
		echo "<option value=\"boots\">Botas</option>\n";
		echo "<option value=\"legs\">Calças</option>\n";
		echo "<option value=\"helmet\" selected=\"selected\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\">Escudos</option>\n";
		}
		echo "<option value=\"potion\">Poções</option>\n";
		echo "<option value=\"addon\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br />";
		
  		if ($query->recordcount() == 0)
		{
			echo "<center><b>Nenhum iten encontrado! Tente procurar outra coisa.</b></center>";
		}
		else
		{

			echo "<fieldset>";
			echo "<legend><b>Mercado</b></legend>";
			echo "<table width=\"100%\" border=\"0\">";
			echo "<tr>";
			echo "<th width=\"40%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&order=item&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Item</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=efec&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Defesa</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=cost&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Preço</a></b></td>";
			echo "<th width=\"20%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=voc&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vocação</a></b></td>";
			echo "<th width=\"10%\"><b>Ação</b></td>";
			echo "</tr>";

			while ($item = $query->fetchrow())
			{
				echo "<tr>\n";
				$bonus1 = $item['item_bonus'] > 0 ? " +" . $item['item_bonus'] . "" : "";
				$bonus2 = $item['for'] > 0 ? " <font color=\"gray\">+" . $item['for'] . "F</font>" : "";
				$bonus3 = $item['vit'] > 0 ? " <font color=\"green\">+" . $item['vit'] . "V</font>" : "";
				$bonus4 = $item['agi'] > 0 ? " <font color=\"blue\">+" . $item['agi'] . "A</font>" : "";
				$bonus5 = $item['res'] > 0 ? " <font color=\"red\">+" . $item['res'] . "R</font>" : "";
				echo "<td>" . $item['name'] . " <font size=\"1\">" . $bonus1 . "" . $bonus2 . "" . $bonus3 . "" . $bonus4 . "" . $bonus5 . "</font></td>";
				echo "<td>" . ($item['effectiveness'] + ($item['item_bonus'] * 2)) . "</td>";
				echo "<td>" . $item['price'] . "</td>";
				echo "<td>";
				if ($item['voc'] == 1 && $item['needpromo'] == 'f')
				{
				echo "Caçador";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'f')
				{
				echo "Espadachim";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'f')
				{
				echo "Bruxo";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 't')
				{
				echo "Arqueiro";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 't')
				{
				echo "Guerreiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 't')
				{
				echo "Mago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 't')
				{
				echo "Vocações superiores";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 'p')
				{
				echo "Arqueiro Royal";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'p')
				{
				echo "Cavaleiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'p')
				{
				echo "Arquimago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 'p')
				{
				echo "Vocações supremas";
				}else{
				echo "Todas";
				}
				echo "</td>";
				if($item['seller']==$player->username){
				echo "<td><a href=\"market.php?act=remove&item=" . $item['market_id'] . "\">Remover</a></td>\n";
				}else{
				echo "<td><a href=\"market_buy.php?act=buy&item=" . $item['market_id'] . "\">Comprar</a></td>\n";
				}

			}
			echo "</tr>";
			echo "</table>";
			echo "</fieldset>";
		}

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";
		
		include(__DIR__ . "/templates/private_footer.php");
		break;


	case "shield":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select market.market_id, market.price, market.seller, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.voc, items.item_bonus, items.for, items.vit, items.agi, items.res from `market`, `blueprint_items`, `items` where ";
		$query .= ($_GET['fromprice'] != "")?"market.price >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"market.price <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		$query .= "market.ite_id=blueprint_items.id and market.market_id=items.id and blueprint_items.type='shield' and market.serv='$player->serv' order by $orderby $abc";
		
		//Construct values array for adoDB
		$values = [];
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
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\">Amuletos</option>\n";
		echo "<option value=\"weapon\">Armas</option>\n";
		echo "<option value=\"armor\">Armaduras</option>\n";
		echo "<option value=\"boots\">Botas</option>\n";
		echo "<option value=\"legs\">Calças</option>\n";
		echo "<option value=\"helmet\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\" selected=\"selected\">Escudos</option>\n";
		}
		echo "<option value=\"potion\">Poções</option>\n";
		echo "<option value=\"addon\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br />";
		
  		if ($query->recordcount() == 0)
		{
			echo "<center><b>Nenhum iten encontrado! Tente procurar outra coisa.</b></center>";
		}
		else
		{

			echo "<fieldset>";
			echo "<legend><b>Mercado</b></legend>";
			echo "<table width=\"100%\" border=\"0\">";
			echo "<tr>";
			echo "<th width=\"40%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&order=item&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Item</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=efec&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Defesa</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=cost&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Preço</a></b></td>";
			echo "<th width=\"20%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=voc&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vocação</a></b></td>";
			echo "<th width=\"10%\"><b>Ação</b></td>";
			echo "</tr>";

			while ($item = $query->fetchrow())
			{
				echo "<tr>\n";
				$bonus1 = $item['item_bonus'] > 0 ? " +" . $item['item_bonus'] . "" : "";
				$bonus2 = $item['for'] > 0 ? " <font color=\"gray\">+" . $item['for'] . "F</font>" : "";
				$bonus3 = $item['vit'] > 0 ? " <font color=\"green\">+" . $item['vit'] . "V</font>" : "";
				$bonus4 = $item['agi'] > 0 ? " <font color=\"blue\">+" . $item['agi'] . "A</font>" : "";
				$bonus5 = $item['res'] > 0 ? " <font color=\"red\">+" . $item['res'] . "R</font>" : "";
				echo "<td>" . $item['name'] . " <font size=\"1\">" . $bonus1 . "" . $bonus2 . "" . $bonus3 . "" . $bonus4 . "" . $bonus5 . "</font></td>";
				echo "<td>" . ($item['effectiveness'] + ($item['item_bonus'] * 2)) . "</td>";
				echo "<td>" . $item['price'] . "</td>";
				echo "<td>";
				if ($item['voc'] == 1 && $item['needpromo'] == 'f')
				{
				echo "Caçador";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'f')
				{
				echo "Espadachim";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'f')
				{
				echo "Bruxo";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 't')
				{
				echo "Arqueiro";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 't')
				{
				echo "Guerreiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 't')
				{
				echo "Mago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 't')
				{
				echo "Vocações superiores";
				}
				elseif ($item['voc'] == 1 && $item['needpromo'] == 'p')
				{
				echo "Arqueiro Royal";
				}
				elseif ($item['voc'] == 2 && $item['needpromo'] == 'p')
				{
				echo "Cavaleiro";
				}
				elseif ($item['voc'] == 3 && $item['needpromo'] == 'p')
				{
				echo "Arquimago";
				}
				elseif ($item['voc'] == 0 && $item['needpromo'] == 'p')
				{
				echo "Vocações supremas";
				}else{
				echo "Todas";
				}
				echo "</td>";
				if($item['seller']==$player->username){
				echo "<td><a href=\"market.php?act=remove&item=" . $item['market_id'] . "\">Remover</a></td>\n";
				}else{
				echo "<td><a href=\"market_buy.php?act=buy&item=" . $item['market_id'] . "\">Comprar</a></td>\n";
				}

			}
			echo "</tr>";
			echo "</table>";
			echo "</fieldset>";
		}

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";
		
		include(__DIR__ . "/templates/private_footer.php");
		break;


	case "potion":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select market.market_id, market.price, market.seller, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.voc, items.item_bonus, items.for, items.vit, items.agi, items.res from `market`, `blueprint_items`, `items` where ";
		$query .= ($_GET['fromprice'] != "")?"market.price >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"market.price <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		$query .= "market.ite_id=blueprint_items.id and market.market_id=items.id and blueprint_items.type='potion' and market.serv='$player->serv' order by $orderby $abc";
		
		//Construct values array for adoDB
		$values = [];
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
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\">Amuletos</option>\n";
		echo "<option value=\"weapon\">Armas</option>\n";
		echo "<option value=\"armor\">Armaduras</option>\n";
		echo "<option value=\"boots\">Botas</option>\n";
		echo "<option value=\"legs\">Calças</option>\n";
		echo "<option value=\"helmet\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\">Escudos</option>\n";
		}
		echo "<option value=\"potion\" selected=\"selected\">Poções</option>\n";
		echo "<option value=\"addon\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br />";
		
  		if ($query->recordcount() == 0)
		{
			echo "<center><b>Nenhum iten encontrado! Tente procurar outra coisa.</b></center>";
		}
		else
		{
			echo "<fieldset>";
			echo "<legend><b>Mercado</b></legend>";
			echo "<table width=\"100%\" border=\"0\">";
			echo "<tr>";
			echo "<th width=\"50%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&order=item&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Item</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=cost&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Preço</a></b></td>";
			echo "<th width=\"25%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=seller&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vendedor</a></b></td>";
			echo "<th width=\"10%\"><b>Ação</b></td>";
			echo "</tr>";

			while ($item = $query->fetchrow())
			{
				echo "<tr>\n";
				echo "<td>" . $item['name'] . "</td>";
				echo "<td>" . $item['price'] . "</td>";
				echo "<td><a href=\"profile.php?id=" . $item['seller'] . "\">" . $item['seller'] . "</a></td>";
				if($item['seller']==$player->username){
				echo "<td><a href=\"market.php?act=remove&item=" . $item['market_id'] . "\">Remover</a></td>";
				}else{
				echo "<td><a href=\"market_buy.php?act=buy&item=" . $item['market_id'] . "\">Comprar</a></td>";
				}
				echo "</tr>";

			}
			echo "</table>";
			echo "</fieldset>";
		}

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";
		
		include(__DIR__ . "/templates/private_footer.php");
		break;
	
	case "addon":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select market.market_id, market.price, market.seller, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.needpromo, blueprint_items.needring, blueprint_items.voc, items.item_bonus, items.for, items.vit, items.agi, items.res from `market`, `blueprint_items`, `items` where ";
		$query .= ($_GET['fromprice'] != "")?"market.price >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"market.price <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		$query .= "market.ite_id=blueprint_items.id and market.market_id=items.id and blueprint_items.type='addon' and market.serv='$player->serv' order by $orderby $abc";
		
		//Construct values array for adoDB
		$values = [];
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
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\">Amuletos</option>\n";
		echo "<option value=\"weapon\">Armas</option>\n";
		echo "<option value=\"armor\">Armaduras</option>\n";
		echo "<option value=\"boots\">Botas</option>\n";
		echo "<option value=\"legs\">Calças</option>\n";
		echo "<option value=\"helmet\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\">Escudos</option>\n";
		}
		echo "<option value=\"potion\">Poções</option>\n";
		echo "<option value=\"addon\" selected=\"selected\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br />";
		
  		if ($query->recordcount() == 0)
		{
			echo "<center><b>Nenhum iten encontrado! Tente procurar outra coisa.</b></center>";
		}
		else
		{
			echo "<fieldset>";
			echo "<legend><b>Mercado</b></legend>";
			echo "<table width=\"100%\" border=\"0\">";
			echo "<tr>";
			echo "<th width=\"50%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&order=item&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Item</a></b></td>";
			echo "<th width=\"15%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=cost&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Preço</a></b></td>";
			echo "<th width=\"25%\"><b><a href=\"market.php?act=" . $_GET['act'] . "&fromprice=" . $_GET['fromprice'] . "&toprice=" . $_GET['toprice'] . "&fromeffect=" . $_GET['fromeffect'] . "&toeffect=" . $_GET['toeffect'] . "&order=seller&abc="; if($abc=="desc"){echo "asc";}elseif($abc=="asc"){echo "desc";}else {echo "asc";} echo "\">Vendedor</a></b></td>";
			echo "<th width=\"10%\"><b>Ação</b></td>";
			echo "</tr>";

			while ($item = $query->fetchrow())
			{
				echo "<tr>\n";
				echo "<td>" . $item['name'] . "</td>";
				echo "<td>" . $item['price'] . "</td>";
				echo "<td><a href=\"profile.php?id=" . $item['seller'] . "\">" . $item['seller'] . "</a></td>";
				if($item['seller']==$player->username){
				echo "<td><a href=\"market.php?act=remove&item=" . $item['market_id'] . "\">Remover</a></td>";
				}else{
				echo "<td><a href=\"market_buy.php?act=buy&item=" . $item['market_id'] . "\">Comprar</a></td>";
				}
				echo "</tr>";

			}
			echo "</table>";
			echo "</fieldset>";
		}

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";
		
		include(__DIR__ . "/templates/private_footer.php");
		break;

	default:
		//Show search form
		include(__DIR__ . "/templates/private_header.php");
		echo "<fieldset>";
		echo "<legend><b>Mercado</b></legend>\n";
		echo "<i>Aqui você pode comprar itens dos outros jogadores.</i><br /><br />\n";
		echo "<form method=\"get\" action=\"market.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Tipo de item:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"4\">\n";
		echo "<option value=\"amulet\">Amuletos</option>\n";
		echo "<option value=\"weapon\">Armas</option>\n";
		echo "<option value=\"armor\">Armaduras</option>\n";
		echo "<option value=\"boots\">Botas</option>\n";
		echo "<option value=\"legs\">Calças</option>\n";
		echo "<option value=\"helmet\">Elmos</option>\n";
		if ($player->voc != 'archer'){
		echo "<option value=\"shield\">Escudos</option>\n";
		}
		echo "<option value=\"potion\">Poções</option>\n";
		echo "<option value=\"addon\">Itens de Quest</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<tr>\n<td width=\"40%\">Preço:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"5\" value=\"" . stripslashes((string) $_GET['fromprice']) . "\"/> à <input type=\"text\" name=\"toprice\" size=\"8\" value=\"" . stripslashes((string) $_GET['toprice']) . "\"/></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Ataque/Defesa:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes((string) $_GET['fromeffect']) . "\" /> à <input type=\"text\" name=\"toeffect\" size=\"5\" value=\"" . stripslashes((string) $_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<td><input type=\"submit\" value=\"Procurar\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";

		echo "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><center><a href=\"market_sell.php\"><b>Vender Itens</b></a></center></div>";

		include(__DIR__ . "/templates/private_footer.php");
		break;
}