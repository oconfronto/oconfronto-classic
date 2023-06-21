<?php
	include("lib.php");
	define("PAGENAME", "Invent�rio");
	$player = check_user($secret_key, $db);
	include("checkbattle.php");
	include("checkhp.php");
	include("checkwork.php");

	$fieldnumber = 1;
	$newline = 0;

	include("includes/items/gift.php");
	include("includes/items/goldbar.php");
	include("includes/actions/transfer-potions.php");
	include("includes/actions/transfer-items.php");

	include("templates/private_header.php");

echo "<center><b>Inventario</b></center>";
echo "<br/>";

		echo "<div id=\"main_container\">";
			echo "<div id=\"drag\">";
				echo "<div id=\"left\">";

				include("showit2.php");

			echo "<table>";
				echo "<tr><td class=\"sell\">Vender</td></tr>";
				echo "<tr><td class=\"mature\">Maturar</td></tr>";
			echo "</table>";
			

$backpackquery = $db->execute("select items.id, items.tile, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img, blueprint_items.type from `items`, `blueprint_items` where items.player_id=? and items.status='unequipped' and items.item_id=blueprint_items.id and blueprint_items.type!='potion' and blueprint_items.type!='stone' and items.mark='f' order by items.tile asc limit 49", array($player->id));

if ($backpackquery && is_object($backpackquery)) {
    echo "<center><font size=\"1px\"><b>Capacidade:</b> 49</font><br/>";
    echo "<font size=\"1px\"><b>Espaço Restante:</b> ";
    if ((49 - $backpackquery->recordcount()) >= 0){ echo (49 - $backpackquery->recordcount()); }else{ echo "0"; }
    echo "</font></center>";
} else {
    // Handle the error situation when $backpackquery is not a valid object
    echo "An error occurred while querying the database.";
}


if($backpackquery === false) {
    die('The query failed with error: ' . $db->ErrorMsg());
}

echo "</div><div id=\"right\">";

echo "<table id=\"table2\" align=\"center\" style=\"display: block;height: 100%;overflow-y: scroll\">";
echo "<tr>";
while($bag = $backpackquery->fetchrow())
{



	if (($bag['item_bonus'] > 2) and ($bag['item_bonus'] < 6)){
		$colorbg = "itembg2";
	}elseif (($bag['item_bonus'] > 5) and ($bag['item_bonus'] < 9)){
		$colorbg = "itembg3";
	}elseif ($bag['item_bonus'] == 9){
		$colorbg = "itembg4";
	}elseif ($bag['item_bonus'] > 9){
		$colorbg = "itembg5";
	}else{
		$colorbg = "itembg1";
	}

		if ($bag['for'] == 0){
		$showitfor = "";
		$showitfor2 = "";
		}else{
		$showitfor2 = "+<font color=gray><b>" . $bag['for'] . " For</b></font><br/>";
		}

		if ($bag['vit'] == 0){
		$showitvit = "";
		$showitvit2 = "";
		}else{
		$showitvit2 = "+<font color=green><b>" . $bag['vit'] . " Vit</b></font><br/>";
		}

		if ($bag['agi'] == 0){
		$showitagi = "";
		$showitagi2 = "";
		}else{
		$showitagi2 = "+<font color=blue><b>" . $bag['agi'] . " Agi</b></font><br/>";
		}

		if ($bag['res'] == 0){
		$showitres = "";
		$showitres2 = "";
		}else{
		$showitres2 = "+<font color=red><b>" . $bag['res'] . " Res</b></font>";
		}

		if ($bag['type'] == 'amulet'){
		$nametype = "Vitalidade";
		} elseif ($bag['type'] == 'weapon'){
		$nametype = "Ataque";
		} elseif ($bag['type'] == 'addon'){
		$nametype = "Atributos";
		}else{
		$nametype = "Defesa";
		}

		$newefec = ($bag['effectiveness']) + ($bag['item_bonus'] * 2);
		$showitname = "" . $bag['name'] . " + " . $bag['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>" . $nametype . ": " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";

echo "<td class=\"" . $colorbg . " " . $fieldnumber . "\">";
echo "<div id=\"" . $bag['type'] . "\" class=\"drag " . $bag['id'] . "\" title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
echo "<img src=\"images/itens/" . $bag['img'] . "\" border=\"0\">";
echo "</div>";
echo "</td>";

				$fieldnumber = $fieldnumber + 1;
					$newline = $newline + 1;
					if ($newline == 7){
						echo "</tr><tr>";
						$newline = 0;
					}
}

$total = $backpackquery->recordcount();
	while($total < 49){
	echo "<td class=\"itembg1 " . $fieldnumber . "\">&nbsp;</td>";

		$fieldnumber = $fieldnumber + 1;
		$total = $total + 1;

		$newline = $newline + 1;
		if ($newline == 7){
			echo "</tr><tr>";
			$newline = 0;
		}
	}


echo "</tr></table>";

		echo "</div>";
	echo "</div>";

if ($backpackquery->recordcount() > 49){
echo "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
echo "<center><font size=\"1\"><b>Espa�o insuficiente na mochila.<br/>Venda alguns de seus itens para ver os outros.</b></font></center>";
echo "</div>";
}


echo "<br />";


$query = $db->execute("select `id` from `items` where `player_id`=? and `item_id`=136 and `mark`='f' order by rand()", array($player->id));
$numerodepocoes = $query->recordcount();

$query2 = $db->execute("select `id` from `items` where `player_id`=? and `item_id`=137 and `mark`='f' order by rand()", array($player->id));
$numerodepocoes2 = $query2->recordcount();

$query3 = $db->execute("select `id` from `items` where `player_id`=? and `item_id`=148 and `mark`='f' order by rand()", array($player->id));
$numerodepocoes3 = $query3->recordcount();

$query4 = $db->execute("select `id` from `items` where `player_id`=? and `item_id`=150 and `mark`='f' order by rand()", array($player->id));
$numerodepocoes4 = $query4->recordcount();

echo "<fieldset>";
echo "<legend><b>Poções</b></legend>";
echo "<table width=\"100%\"><tr><td><table width=\"80px\"><tr><td><div title=\"header=[Health Potion] body=[Recupera até 5 mil de vida.]\"><img src=\"images/itens/healthpotion.gif\"></div></td><td><b>x" . $numerodepocoes . "</b>";
if ($numerodepocoes > 0){
$item = $query->fetchrow();
echo "<br/><a href=\"hospt.php?act=potion&pid=" . $item['id'] . "\">Usar</a>";
}
echo "</td></tr></table></td>";
echo "<td><table width=\"80px\"><tr><td><div title=\"header=[Big Health Potion] body=[Recupera até 10 mil de vida.]\"><img src=\"images/itens/bighealthpotion.gif\"></div></td><td><b>x" . $numerodepocoes3 . "</b>";
if ($numerodepocoes3 > 0){
$item3 = $query3->fetchrow();
echo "<br/><a href=\"hospt.php?act=potion&pid=" . $item3['id'] . "\">Usar</a>";
}
echo "</td></tr></table></td>";
echo "<td><table width=\"80px\"><tr><td><div title=\"header=[Mana Potion] body=[Recupera até 500 de mana.]\"><img src=\"images/itens/manapotion.gif\"></div></td><td><b>x" . $numerodepocoes4 . "</b>";
if ($numerodepocoes4 > 0){
$item4 = $query4->fetchrow();
echo "<br/><a href=\"hospt.php?act=potion&pid=" . $item4['id'] . "\">Usar</a>";
}
echo "</td></tr></table></td>";
echo "<td><table width=\"80px\"><tr><td><div title=\"header=[Energy Potion] body=[Recupera até 50 de energia.]\"><img src=\"images/itens/energypotion.gif\"></div></td><td><b>x" . $numerodepocoes2 . "</b>";
if ($numerodepocoes2 > 0){
$item2 = $query2->fetchrow();
echo "<br/><a href=\"hospt.php?act=potion&pid=" . $item2['id'] . "\">Usar</a>";
}
echo "</td></tr></table></td><td><font size=\"1\"><a href=\"hospt.php?act=sell\">Vender Poções</a><br/><a href=\"inventory.php?transpotion=true\">Transferir Poções</a></font></td></tr></table>";
echo "</fieldset>";

echo "<br>";
echo "<fieldset>";
echo "<legend><b>Enviar Itens</b></legend>";

$verifikeuser = $db->execute("select `id` from `quests` where `quest_id`=4 and `quest_status`=90 and `player_id`=?", array($player->id));

if ($player->level < $setting->activate_level)
{
	echo "<center><font size=\"1\">Para poder transferir itens sua conta precisa estar ativa. Ela ser� ativada automaticamente quando voc� alcan�ar o n�vel " . $setting->activate_level . ".</font></center>";
}elseif ($verifikeuser->recordcount() == 0) {
	echo"<center><font size=\"1\">Voc� precisa chegar ao nivel 40 e completar uma miss�o para utilizar esta fun��o.</font></center>";
	if ($player->level > 39) {
		echo"<center><font size=\"1\"><a href=\"quest2.php\"><b>Clique aqui para fazer a miss�o.</b></a></font></center>";
	}
	}elseif ($player->transpass == f){
	echo "<form method=\"POST\" action=\"transferpass.php\">";
	echo "<table><tr><td width=\"35%\"><b>Escolha uma senha para enviar ouro e itens:</b></td><td width=\"65%\"><font size=\"1\"><b>Senha:</b></font> <input type=\"password\" name=\"pass\" size=\"15\"/><br/><font size=\"1\"><b>Confirme:</b></font> <input type=\"password\" name=\"pass2\" size=\"15\"/> <input type=\"submit\" name=\"submit\" value=\"Definir Senha\"></td></tr></table><br/><font size=\"1\">Lembre-se desta senha, ela sempre ser� usada para fazer transfer�ncias banc�rias. Se voc� perdela n�o poder� recupera-la.</font>";
	echo "</form>";
	}else{

echo "<table width=\"100%\">";
echo "<form method=\"POST\" action=\"inventory.php\">";
echo "<tr><td width=\"40%\">Usu�rio:</td><td><input type=\"text\" name=\"username\" size=\"20\"/></td></tr>";
echo "<tr><td width=\"40%\">Item:</td><td>";

$queoppa = $db->execute("select items.id, items.item_bonus, items.item_id, items.mark, blueprint_items.name from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type!='stone' and blueprint_items.type!='potion' and items.mark='f' order by blueprint_items.type, blueprint_items.name asc", array($player->id));
    if ($queoppa->recordcount() == 0) {
echo "<b>Voc� n�o possui itens.</b>";
}else{
	echo "<select name=\"itselected\">";
	while($item = $queoppa->fetchrow())
	{
	echo "<option value=\"" . $item['id'] . "\">" . $item['name'] . " +" . $item['item_bonus'] . "</option>";
	}
	echo "</select>";
	}

echo "</td></tr>";
echo "<tr><td width=\"40%\">Senha de transfer�ncia:</td><td><input type=\"password\" name=\"passcode\" size=\"20\"/></td></tr>";
echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"transferitems\" value=\"Enviar\"></td></tr>";
echo "</table></form>";
echo "<font size=\"1\"><a href=\"forgottrans.php\">Esqueceu sua senha de transfer�ncia?</a></font>";

$morelogs = 1;
}
echo "</fieldset>";
if ($morelogs == 1){
echo "<center><font size=\"1\"><a href=\"#\" onclick=\"javascript:window.open('logitem.php', '_blank','top=100, left=100, height=350, width=450, status=no, menubar=no, resizable=no, scrollbars=yes, toolbar=no, location=no, directories=no');\">Transfer�ncias realizadas nos �ltimos 14 dias.</a></font></center>";
}


echo "</div>";

include("templates/private_footer.php");
?>
