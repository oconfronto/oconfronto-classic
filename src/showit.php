<table align="center">
<tr>
<td width="40"><?php
$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='amulet' and items.status='equipped'", [$player->id]);

if ($showitenx->recordcount() == 0)
{
	echo "<div class=\"itembg1\" align=\"center\">";
	echo "&nbsp;";
}
else
{
	while($showeditexs = $showitenx->fetchrow())
	{

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


		if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		echo "<div class=\"itembg2\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		echo "<div class=\"itembg3\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] == 9){
		echo "<div class=\"itembg4\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 9){
		echo "<div class=\"itembg5\" align=\"center\">";
		}else{
		echo "<div class=\"itembg1\" align=\"center\">";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Vitalidade: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<img src=\"images/itens/" . $showeditexs['img'] . "\"/>";
		echo "</div>";
	}
}
echo "</div>";
?></td>
<td width="40"><?php
$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='helmet' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<div class=\"itembg1\" align=\"center\">";
	echo "&nbsp;";
}
else
{
	while($showeditexs = $showitenx->fetchrow())
	{

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


		if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		echo "<div class=\"itembg2\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		echo "<div class=\"itembg3\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] == 9){
		echo "<div class=\"itembg4\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 9){
		echo "<div class=\"itembg5\" align=\"center\">";
		}else{
		echo "<div class=\"itembg1\" align=\"center\">";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Defesa: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<img src=\"images/itens/" . $showeditexs['img'] . "\"/>";
		echo "</div>";
	}
}
echo "</div>";
?></td>
<td width="40"><a href="inventory.php"><img src="images/bag.gif" border="0"></a></td></tr>
<tr>
<td width="40"><?php
$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='weapon' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<div class=\"itembg1\" align=\"center\">";
	echo "&nbsp;";
}
else
{
	while($showeditexs = $showitenx->fetchrow())
	{

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


		if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		echo "<div class=\"itembg2\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		echo "<div class=\"itembg3\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] == 9){
		echo "<div class=\"itembg4\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 9){
		echo "<div class=\"itembg5\" align=\"center\">";
		}else{
		echo "<div class=\"itembg1\" align=\"center\">";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Ataque: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<img src=\"images/itens/" . $showeditexs['img'] . "\"/>";
		echo "</div>";
	}
}
echo "</div>";
?></td>
<td width="40"><?php
$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='armor' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<div class=\"itembg1\" align=\"center\">";
	echo "&nbsp;";
}
else
{
	while($showeditexs = $showitenx->fetchrow())
	{

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


		if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		echo "<div class=\"itembg2\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		echo "<div class=\"itembg3\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] == 9){
		echo "<div class=\"itembg4\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 9){
		echo "<div class=\"itembg5\" align=\"center\">";
		}else{
		echo "<div class=\"itembg1\" align=\"center\">";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Defesa: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<img src=\"images/itens/" . $showeditexs['img'] . "\"/>";
		echo "</div>";
	}
}
echo "</div>";
?></td>
<td width="40"><?php
$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='shield' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<div class=\"itembg1\" align=\"center\">";
	echo "&nbsp;";
}
else
{
	while($showeditexs = $showitenx->fetchrow())
	{

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


		if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		echo "<div class=\"itembg2\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		echo "<div class=\"itembg3\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] == 9){
		echo "<div class=\"itembg4\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 9){
		echo "<div class=\"itembg5\" align=\"center\">";
		}else{
		echo "<div class=\"itembg1\" align=\"center\">";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Defesa: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<img src=\"images/itens/" . $showeditexs['img'] . "\"/>";
		echo "</div>";
	}
}
echo "</div>";
?></td></tr>
<tr>
<td width="40"><?php
if ($player->promoted == 'r') {
    echo "<div class=\"itembg1\" align=\"center\">";
    echo "<img src=\"images/itens/jewring.gif\"/>";
} elseif ($player->promoted == 's' || $player->promoted == 'p') {
    echo "<div class=\"itembg5\" align=\"center\">";
    echo "<img src=\"images/itens/newjewring.gif\"/>";
} else
{
	echo "<div class=\"itembg1\" align=\"center\">";
	echo "&nbsp;";
}
echo "</div>";
?></td>
<td width="40"><?php
$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='legs' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<div class=\"itembg1\" align=\"center\">";
	echo "&nbsp;";
}
else
{
	while($showeditexs = $showitenx->fetchrow())
	{

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


		if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		echo "<div class=\"itembg2\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		echo "<div class=\"itembg3\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] == 9){
		echo "<div class=\"itembg4\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 9){
		echo "<div class=\"itembg5\" align=\"center\">";
		}else{
		echo "<div class=\"itembg1\" align=\"center\">";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Defesa: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<img src=\"images/itens/" . $showeditexs['img'] . "\"/>";
		echo "</div>";
	}
}
echo "</div>";
?></td>
<td width="40"><?php
$showitenx = $db->execute("select items.id, items.item_id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.status, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='boots' and items.status='equipped'", [$player->id]);
if ($showitenx->recordcount() == 0)
{
	echo "<div class=\"itembg1\" align=\"center\">";
	echo "&nbsp;";
}
else
{
	while($showeditexs = $showitenx->fetchrow())
	{

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


		if ($showeditexs['item_bonus'] > 2 && $showeditexs['item_bonus'] < 6){
		echo "<div class=\"itembg2\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 5 && $showeditexs['item_bonus'] < 9){
		echo "<div class=\"itembg3\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] == 9){
		echo "<div class=\"itembg4\" align=\"center\">";
		}elseif ($showeditexs['item_bonus'] > 9){
		echo "<div class=\"itembg5\" align=\"center\">";
		}else{
		echo "<div class=\"itembg1\" align=\"center\">";
		}

		$newefec = ($showeditexs['effectiveness']) + ($showeditexs['item_bonus'] * 2);
		$showitname = "" . $showeditexs['name'] . " + " . $showeditexs['item_bonus'] . "";
		$showitinfo = "<table width=170px><tr><td width=60%><b><font size=1>Agilidade: " . $newefec . "</font></b></td><td width=40%><font size=1>" . $showitfor2 . "" . $showitvit2 . "" . $showitagi2 . "" . $showitres2 . "</font></td></tr></table>";
		echo "<div title=\"header=[" . $showitname . "] body=[" . $showitinfo . "]\">";
		echo "<img src=\"images/itens/" . $showeditexs['img'] . "\"/>";
		echo "</div>";
	}
}
echo "</div>";
?></td></tr>


</table>