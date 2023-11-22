<?php
echo "<table id=\"table1\" align=\"center\">";
echo "<tbody><tr>";

$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='amulet' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<td class=\"mark amulet itembg1\"></td>";
}else{
	$showeditexs = $showitenx->fetchrow();

	if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		$colorbg = "itembg2";
	}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		$colorbg = "itembg3";
	}elseif ($showeditexs['item_bonus'] == 9){
		$colorbg = "itembg4";
	}elseif ($showeditexs['item_bonus'] > 9){
		$colorbg = "itembg5";
	}else{
		$colorbg = "itembg1";
	}

	echo "<td class=\"mark amulet " . $colorbg . "\">";

		if ($showeditexs['for'] == 0){
		$showitfor = "";
		$showitfor2 = "";
		}else{
		$showitfor2 = "+<font color=gray><b>" . $showeditexs['for'] . " For</b></font><br/>";
		}

		if ($showeditexs['vit'] == 0){
		$showitvit = "";
		$showitvit2 = "";
		}else{
		$showitvit2 = "+<font color=green><b>" . $showeditexs['vit'] . " Vit</b></font><br/>";
		}

		if ($showeditexs['agi'] == 0){
		$showitagi = "";
		$showitagi2 = "";
		}else{
		$showitagi2 = "+<font color=blue><b>" . $showeditexs['agi'] . " Agi</b></font><br/>";
		}

		if ($showeditexs['res'] == 0){
		$showitres = "";
		$showitres2 = "";
		}else{
		$showitres2 = "+<font color=red><b>" . $showeditexs['res'] . " Res</b></font>";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Vitalidade: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<div id=\"" . $showeditexs['type'] . "\" class=\"drag " . $showeditexs['id'] . "\"><img src=\"images/itens/" . $showeditexs['img'] . "\" border=\"0\"></div>";
		echo "</div>";

	echo "</td>";
}


$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='helmet' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<td class=\"mark helmet itembg1\"></td>";
}else{
	$showeditexs = $showitenx->fetchrow();

	if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		$colorbg = "itembg2";
	}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		$colorbg = "itembg3";
	}elseif ($showeditexs['item_bonus'] == 9){
		$colorbg = "itembg4";
	}elseif ($showeditexs['item_bonus'] > 9){
		$colorbg = "itembg5";
	}else{
		$colorbg = "itembg1";
	}

	echo "<td class=\"mark helmet " . $colorbg . "\">";

		if ($showeditexs['for'] == 0){
		$showitfor = "";
		$showitfor2 = "";
		}else{
		$showitfor2 = "+<font color=gray><b>" . $showeditexs['for'] . " For</b></font><br/>";
		}

		if ($showeditexs['vit'] == 0){
		$showitvit = "";
		$showitvit2 = "";
		}else{
		$showitvit2 = "+<font color=green><b>" . $showeditexs['vit'] . " Vit</b></font><br/>";
		}

		if ($showeditexs['agi'] == 0){
		$showitagi = "";
		$showitagi2 = "";
		}else{
		$showitagi2 = "+<font color=blue><b>" . $showeditexs['agi'] . " Agi</b></font><br/>";
		}

		if ($showeditexs['res'] == 0){
		$showitres = "";
		$showitres2 = "";
		}else{
		$showitres2 = "+<font color=red><b>" . $showeditexs['res'] . " Res</b></font>";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Defesa: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<div id=\"" . $showeditexs['type'] . "\" class=\"drag " . $showeditexs['id'] . "\"><img src=\"images/itens/" . $showeditexs['img'] . "\" border=\"0\"></div>";
		echo "</div>";

	echo "</td>";
}


	echo "<td class=\"mark none\">&nbsp;</td>";
echo "</tr><tr>";


$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='weapon' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<td class=\"mark weapon itembg1\"></td>";
}else{
	$showeditexs = $showitenx->fetchrow();

	if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		$colorbg = "itembg2";
	}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		$colorbg = "itembg3";
	}elseif ($showeditexs['item_bonus'] == 9){
		$colorbg = "itembg4";
	}elseif ($showeditexs['item_bonus'] > 9){
		$colorbg = "itembg5";
	}else{
		$colorbg = "itembg1";
	}

	echo "<td class=\"mark weapon " . $colorbg . "\">";

		if ($showeditexs['for'] == 0){
		$showitfor = "";
		$showitfor2 = "";
		}else{
		$showitfor2 = "+<font color=gray><b>" . $showeditexs['for'] . " For</b></font><br/>";
		}

		if ($showeditexs['vit'] == 0){
		$showitvit = "";
		$showitvit2 = "";
		}else{
		$showitvit2 = "+<font color=green><b>" . $showeditexs['vit'] . " Vit</b></font><br/>";
		}

		if ($showeditexs['agi'] == 0){
		$showitagi = "";
		$showitagi2 = "";
		}else{
		$showitagi2 = "+<font color=blue><b>" . $showeditexs['agi'] . " Agi</b></font><br/>";
		}

		if ($showeditexs['res'] == 0){
		$showitres = "";
		$showitres2 = "";
		}else{
		$showitres2 = "+<font color=red><b>" . $showeditexs['res'] . " Res</b></font>";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Ataque: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<div id=\"" . $showeditexs['type'] . "\" class=\"drag " . $showeditexs['id'] . "\"><img src=\"images/itens/" . $showeditexs['img'] . "\" border=\"0\"></div>";
		echo "</div>";

	echo "</td>";
}


$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='armor' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<td class=\"mark armor itembg1\"></td>";
}else{
	$showeditexs = $showitenx->fetchrow();

	if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		$colorbg = "itembg2";
	}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		$colorbg = "itembg3";
	}elseif ($showeditexs['item_bonus'] == 9){
		$colorbg = "itembg4";
	}elseif ($showeditexs['item_bonus'] > 9){
		$colorbg = "itembg5";
	}else{
		$colorbg = "itembg1";
	}

	echo "<td class=\"mark armor " . $colorbg . "\">";

		if ($showeditexs['for'] == 0){
		$showitfor = "";
		$showitfor2 = "";
		}else{
		$showitfor2 = "+<font color=gray><b>" . $showeditexs['for'] . " For</b></font><br/>";
		}

		if ($showeditexs['vit'] == 0){
		$showitvit = "";
		$showitvit2 = "";
		}else{
		$showitvit2 = "+<font color=green><b>" . $showeditexs['vit'] . " Vit</b></font><br/>";
		}

		if ($showeditexs['agi'] == 0){
		$showitagi = "";
		$showitagi2 = "";
		}else{
		$showitagi2 = "+<font color=blue><b>" . $showeditexs['agi'] . " Agi</b></font><br/>";
		}

		if ($showeditexs['res'] == 0){
		$showitres = "";
		$showitres2 = "";
		}else{
		$showitres2 = "+<font color=red><b>" . $showeditexs['res'] . " Res</b></font>";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Defesa: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<div id=\"" . $showeditexs['type'] . "\" class=\"drag " . $showeditexs['id'] . "\"><img src=\"images/itens/" . $showeditexs['img'] . "\" border=\"0\"></div>";
		echo "</div>";

	echo "</td>";
}


$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='shield' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<td class=\"mark shield itembg1\"></td>";
}else{
	$showeditexs = $showitenx->fetchrow();

	if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		$colorbg = "itembg2";
	}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		$colorbg = "itembg3";
	}elseif ($showeditexs['item_bonus'] == 9){
		$colorbg = "itembg4";
	}elseif ($showeditexs['item_bonus'] > 9){
		$colorbg = "itembg5";
	}else{
		$colorbg = "itembg1";
	}

	echo "<td class=\"mark shield " . $colorbg . "\">";

		if ($showeditexs['for'] == 0){
		$showitfor = "";
		$showitfor2 = "";
		}else{
		$showitfor2 = "+<font color=gray><b>" . $showeditexs['for'] . " For</b></font><br/>";
		}

		if ($showeditexs['vit'] == 0){
		$showitvit = "";
		$showitvit2 = "";
		}else{
		$showitvit2 = "+<font color=green><b>" . $showeditexs['vit'] . " Vit</b></font><br/>";
		}

		if ($showeditexs['agi'] == 0){
		$showitagi = "";
		$showitagi2 = "";
		}else{
		$showitagi2 = "+<font color=blue><b>" . $showeditexs['agi'] . " Agi</b></font><br/>";
		}

		if ($showeditexs['res'] == 0){
		$showitres = "";
		$showitres2 = "";
		}else{
		$showitres2 = "+<font color=red><b>" . $showeditexs['res'] . " Res</b></font>";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Defesa: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<div id=\"" . $showeditexs['type'] . "\" class=\"drag " . $showeditexs['id'] . "\"><img src=\"images/itens/" . $showeditexs['img'] . "\" border=\"0\"></div>";
		echo "</div>";

	echo "</td>";
}


echo "</tr><tr>";
	echo "<td class=\"mark none\">&nbsp;</td>";


$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='legs' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<td class=\"mark legs itembg1\"></td>";
}else{
	$showeditexs = $showitenx->fetchrow();

	if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		$colorbg = "itembg2";
	}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		$colorbg = "itembg3";
	}elseif ($showeditexs['item_bonus'] == 9){
		$colorbg = "itembg4";
	}elseif ($showeditexs['item_bonus'] > 9){
		$colorbg = "itembg5";
	}else{
		$colorbg = "itembg1";
	}

	echo "<td class=\"mark legs " . $colorbg . "\">";

		if ($showeditexs['for'] == 0){
		$showitfor = "";
		$showitfor2 = "";
		}else{
		$showitfor2 = "+<font color=gray><b>" . $showeditexs['for'] . " For</b></font><br/>";
		}

		if ($showeditexs['vit'] == 0){
		$showitvit = "";
		$showitvit2 = "";
		}else{
		$showitvit2 = "+<font color=green><b>" . $showeditexs['vit'] . " Vit</b></font><br/>";
		}

		if ($showeditexs['agi'] == 0){
		$showitagi = "";
		$showitagi2 = "";
		}else{
		$showitagi2 = "+<font color=blue><b>" . $showeditexs['agi'] . " Agi</b></font><br/>";
		}

		if ($showeditexs['res'] == 0){
		$showitres = "";
		$showitres2 = "";
		}else{
		$showitres2 = "+<font color=red><b>" . $showeditexs['res'] . " Res</b></font>";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Defesa: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<div id=\"" . $showeditexs['type'] . "\" class=\"drag " . $showeditexs['id'] . "\"><img src=\"images/itens/" . $showeditexs['img'] . "\" border=\"0\"></div>";
		echo "</div>";

	echo "</td>";
}


$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='boots' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<td class=\"mark boots itembg1\"></td>";
}else{
	$showeditexs = $showitenx->fetchrow();

	if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		$colorbg = "itembg2";
	}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		$colorbg = "itembg3";
	}elseif ($showeditexs['item_bonus'] == 9){
		$colorbg = "itembg4";
	}elseif ($showeditexs['item_bonus'] > 9){
		$colorbg = "itembg5";
	}else{
		$colorbg = "itembg1";
	}

	echo "<td class=\"mark boots " . $colorbg . "\">";

		if ($showeditexs['for'] == 0){
		$showitfor = "";
		$showitfor2 = "";
		}else{
		$showitfor2 = "+<font color=gray><b>" . $showeditexs['for'] . " For</b></font><br/>";
		}

		if ($showeditexs['vit'] == 0){
		$showitvit = "";
		$showitvit2 = "";
		}else{
		$showitvit2 = "+<font color=green><b>" . $showeditexs['vit'] . " Vit</b></font><br/>";
		}

		if ($showeditexs['agi'] == 0){
		$showitagi = "";
		$showitagi2 = "";
		}else{
		$showitagi2 = "+<font color=blue><b>" . $showeditexs['agi'] . " Agi</b></font><br/>";
		}

		if ($showeditexs['res'] == 0){
		$showitres = "";
		$showitres2 = "";
		}else{
		$showitres2 = "+<font color=red><b>" . $showeditexs['res'] . " Res</b></font>";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Agilidade: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<div id=\"" . $showeditexs['type'] . "\" class=\"drag " . $showeditexs['id'] . "\"><img src=\"images/itens/" . $showeditexs['img'] . "\" border=\"0\"></div>";
		echo "</div>";

	echo "</td>";
}

	echo "</tr>";
echo "</tbody></table>";
?>