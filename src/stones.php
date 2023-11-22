<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Pedras");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/templates/private_header.php");

 		if ($player->level < 50){
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Seu nível é inferior á 50.<br/></i>\n";
		echo "<a href=\"home.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

include(__DIR__ . "/checkwork.php");


	if ($_GET['pedra'] && $_POST['maturar']) {
		$query = $db->execute("select * from `items` where `id`=? and `item_id`=141 and `player_id`=?", [$_GET['pedra'], $player->id]);
		if ($query->recordcount() == 0)
		{
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode usar esta pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}


		$verifica = $db->execute("select items.id, items.item_bonus, items.item_id, items.status, items.mark, blueprint_items.name, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and items.id=?", [$player->id, $_POST['itemid']]);

   		if ($verifica->recordcount() == 0) {
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Item não encontrado.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		$item = $verifica->fetchrow();

 		if ($item['mark'] == 't') {
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode maturar um item que está à venda no mercado.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($item['type'] == 'potion') {
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode maturar este tipo de item.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($item['type'] == 'stone') {
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode maturar este tipo de item.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($item['type'] == 'addon') {
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode maturar este tipo de item.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}


 		if ($item['item_bonus'] != 9){
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você só pode maturar items +9.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		
		$win=random_int(0, 100);
			if ($win < 31){
			$query = $db->execute("delete from `items` where `id`=? and `player_id`=?", [$_GET['pedra'], $player->id]);
			echo "Infelizmente a maturação não deu certo, e você perdeu sua pedra.";
			echo "<br/><a href=\"stones.php\">voltar.</a>";
			}else{
			$points=random_int(13, 14);
			if ($item['status'] == 'equipped' && $item['type'] == 'amulet' && $points == 13){
			$addhp = 100;
			}elseif ($item['status'] == 'equipped' && $item['type'] == 'amulet' && $points == 14){
			$addhp = 125;	
			}else{
			$addhp = 0;
			}
			$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", [$player->hp + $addhp, $player->maxhp + $addhp, $player->id]);
			$query = $db->execute("delete from `items` where `id`=? and `player_id`=?", [$_GET['pedra'], $player->id]);
			$query = $db->execute("update `items` set `item_bonus`=? where `id`=?", [$points, $_POST['itemid']]);
			echo "A maturação do item foi feita com sucesso! Seu item agora é +" . $points . "";
			echo "<br/><a href=\"stones.php\">voltar.</a>";
			}
		include(__DIR__ . "/templates/private_footer.php");
		exit;
	}


	if ($_GET['pedra'] && $_POST['usar']) {
		$query5587 = $db->execute("select * from `items` where `id`=? and `player_id`=?", [$_GET['pedra'], $player->id]);
		if ($query5587->recordcount() == 0)
		{	
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode usar esta pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		$pedra = $query5587->fetchrow();

		if ($pedra['item_id'] != 144 && $pedra['item_id'] != 145 && $pedra['item_id'] != 146 && $pedra['item_id'] != 147){
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode usar esta pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}
		

		$verifica = $db->execute("select items.id, items.for, items.vit, items.agi, items.res, items.item_id, items.status, items.mark, blueprint_items.name, blueprint_items.type from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and items.id=?", [$player->id, $_POST['itemid']]);

   		if ($verifica->recordcount() == 0) {
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Item não encontrado.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		$item = $verifica->fetchrow();

 		if ($item['mark'] == 't') {
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode maturar um item que está à venda no mercado.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($item['type'] == 'potion') {
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode maturar este tipo de item.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($item['type'] == 'stone') {
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode maturar este tipo de item.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($item['type'] == 'addon') {
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode maturar este tipo de item.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}


 		if ($pedra['item_id'] == 144 && $item['for'] > 4){
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você só pode usar esta pedra em itens com 4 de força ou menos.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($pedra['item_id'] == 145 && $item['vit'] > 4){
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você só pode usar esta pedra em itens com 4 de vitalidade ou menos.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($pedra['item_id'] == 146 && $item['agi'] > 4){
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você só pode usar esta pedra em itens com 4 de agilidade ou menos.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($pedra['item_id'] == 147 && $item['res'] > 4){
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você só pode usar esta pedra em itens com 4 de resistencia ou menos.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		
		$win=random_int(0, 100);
			if ($win < 31){
			$query = $db->execute("delete from `items` where `id`=? and `player_id`=?", [$_GET['pedra'], $player->id]);
			echo "Infelizmente a maturação não deu certo, e você perdeu sua pedra.";
			echo "<br/><a href=\"stones.php\">voltar.</a>";
			}else{
			$query = $db->execute("delete from `items` where `id`=? and `player_id`=?", [$_GET['pedra'], $player->id]);
			
			if ($pedra['item_id'] == 144) {
			$query = $db->execute("update `items` set `for`=`for`+5 where `id`=?", [$_POST['itemid']]);
			$novaforca = $item['for'] + 5;
			echo "A pedra foi usada com sucesso! Seu item agora tem " . $novaforca . " de força.";
			}elseif ($pedra['item_id'] == 145) {

			$addhp = $item['status'] == 'equipped' ? 125 : 0;
			$query = $db->execute("update `players` set `hp`=?, `maxhp`=? where `id`=?", [$player->hp + $addhp, $player->maxhp + $addhp, $player->id]);

			$query = $db->execute("update `items` set `vit`=`vit`+5 where `id`=?", [$_POST['itemid']]);
			$novaforca = $item['vit'] + 5;
			echo "A pedra foi usada com sucesso! Seu item agora tem " . $novaforca . " de vitalidade.";
			}elseif ($pedra['item_id'] == 146) {
			$query = $db->execute("update `items` set `agi`=`agi`+5 where `id`=?", [$_POST['itemid']]);
			$novaforca = $item['agi'] + 5;
			echo "A pedra foi usada com sucesso! Seu item agora tem " . $novaforca . " de agilidade.";
			}elseif ($pedra['item_id'] == 147) {
			$query = $db->execute("update `items` set `res`=`res`+5 where `id`=?", [$_POST['itemid']]);
			$novaforca = $item['res'] + 5;
			echo "A pedra foi usada com sucesso! Seu item agora tem " . $novaforca . " de resistencia.";
			}
			echo "<br/><a href=\"stones.php\">voltar.</a>";
			}
		include(__DIR__ . "/templates/private_footer.php");
		exit;
	}




	if ($_GET['pedra'] && $_GET['type'] == 'energy') {
		$query = $db->execute("select * from `items` where `id`=? and `item_id`=142 and `player_id`=?", [$_GET['pedra'], $player->id]);
		if ($query->recordcount() == 0)
		{
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode usar esta pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

 		if ($player->maxenergy > 199){
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você só pode usar a pedra de energia uma vez.<br/></i>\n";
		echo "<a href=\"stones.php\">voltar.</a>";
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

			$query = $db->execute("delete from `items` where `id`=? and `player_id`=?", [$_GET['pedra'], $player->id]);
			$query = $db->execute("update `players` set `maxenergy`=200 where `id`=?", [$player->id]);
			echo "A pedra foi utilizada com sucesso. Sua nova energia máxima é de 200.";
			echo "<br/><a href=\"stones.php\">voltar.</a>";

		include(__DIR__ . "/templates/private_footer.php");
		exit;
	}


	if ($_GET['pedra'] && $_GET['act'] == 'sell') {
		$query = $db->execute("select * from `items` where `id`=? and `item_id`=? and `player_id`=?", [$_GET['pedra'], $_GET['sellid'], $player->id]);
		if ($query->recordcount() == 0)
		{
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode vender esta pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}
			

		$verifica2 = $db->execute("select items.id, items.item_id, items.mark, blueprint_items.price from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and items.id=?", [$player->id, $_GET['pedra']]);
		$item2 = $verifica2->fetchrow();

			$query = $db->execute("update `players` set `gold`=? where `id`=?", [$player->gold + $item2['price'], $player->id]);
			echo "Você vendeu sua pedra pelo preço de compra, " . $item2['price'] . " de ouro.";
			echo "<br/><a href=\"stones.php\">voltar.</a>";
			$query = $db->execute("delete from `items` where `id`=? and `player_id`=?", [$_GET['pedra'], $player->id]);

		include(__DIR__ . "/templates/private_footer.php");
		exit;
	}



	if ($_GET['buyid'] && !$_GET['status'] == 'confirm') {
		$query4 = $db->execute("select * from `blueprint_items` where `id`=? and `type`='stone'", [$_GET['buyid']]);
		if ($query4->recordcount() == 0)
		{
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>O item que você deseja comprar não é uma pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		$item4 = $query4->fetchrow();

			echo "<b>Você gostaria de comprar:</b><br />";
			echo "<fieldset>";
			echo "<legend><b>" . $item4['name'] . "</b></legend>";
			echo "<table width=\"100%\">";
			echo "<tr><td width=\"5%\">";
			echo "<img src=\"images/itens/" . $item4['img'] . "\"/>";
			echo "</td><td width=\"68%\">" . $item4['description'] . "</td>";
			echo "<td width=\"30%\">";
			echo "<b>Preço:</b> " . $item4['price'] . "";
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			echo "</fieldset>";

			echo "<a href=\"stones.php?buyid=" . $item4['id'] . "&status=confirm\">Comprar</a> | <a href=\"stones.php\">Voltar.</a>";

		include(__DIR__ . "/templates/private_footer.php");
		exit;
	}



	if ($_GET['buyid'] && $_GET['status'] == 'confirm') {
		$query5 = $db->execute("select * from `blueprint_items` where `id`=? and `type`='stone'", [$_GET['buyid']]);
		if ($query5->recordcount() == 0)
		{
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>O item que você deseja comprar não é uma pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		$item5 = $query5->fetchrow();

		if ($item5['price'] > $player->gold)
		{
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não possui dinheiro suficiente.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		$insert['player_id'] = $player->id;
		$insert['item_id'] = $_GET['buyid'];
		$query2 = $db->autoexecute('items', $insert, 'INSERT');

			$query = $db->execute("update `players` set `gold`=? where `id`=?", [$player->gold - $item5['price'], $player->id]);
			echo "Você acaba de comprar uma " . $item5['name'] . " por " . $item5['price'] . " de ouro.";
			echo "<br/><a href=\"stones.php\">Voltar.</a>";

		include(__DIR__ . "/templates/private_footer.php");
		exit;
	}






if ($_GET['pedra'] && $_GET['type'] == 'mature' && !$_POST['maturar']) {
		$query = $db->execute("select * from `items` where `id`=? and `player_id`=?", [$_GET['pedra'], $player->id]);
		if ($query->recordcount() == 0)
		{
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode usar esta pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		$potion = $query->fetchrow();

    		if ($potion['item_id'] != 141){
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Este item não é uma pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		if ($potion['item_id'] == 141){
		
		echo "Selecione um item para tentar maturar. Seu item deverá ser +9.<br/>";
		echo "<b>Atenção:</b> Lembre-se que seu item tem 30% de chance de quebrar durante o processo.<br/><br/>";

		$queoppa = $db->execute("select items.id, items.item_bonus, items.item_id, items.mark, blueprint_items.name from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and items.item_bonus=9 and items.mark='f' and blueprint_items.type!='addon' and blueprint_items.type!='stone' and blueprint_items.type!='potion' order by blueprint_items.type asc", [$player->id]);
  		if ($queoppa->recordcount() == 0) {
			echo "<b>Você não possui itens disponíveis para maturar.</b>";
		}else{
		echo "<form method=\"POST\" action=\"stones.php?pedra=" . $_GET['pedra'] . "\"><b>Selecione:</b> <select name=\"itemid\">";
			while($item = $queoppa->fetchrow())
			{
			echo "<option value=\"" . $item['id'] . "\">" . $item['name'] . " +" . $item['item_bonus'] . "</option>";
			}
		echo "</select> <input type=\"submit\" name=\"maturar\" value=\"Maturar\"></form>";
		}

		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

}

if ($_GET['pedra'] && $_GET['type'] == 'status' && !$_POST['usar']) {
		$query = $db->execute("select * from `items` where `id`=? and `player_id`=?", [$_GET['pedra'], $player->id]);
		if ($query->recordcount() == 0)
		{
		
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Você não pode usar esta pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		$potion = $query->fetchrow();

    		if ($potion['item_id'] != 144 && $potion['item_id'] != 145 && $potion['item_id'] != 146 && $potion['item_id'] != 147){
		echo "<fieldset><legend><b>Erro</b></legend>\n";
		echo "<i>Este item não é uma pedra.<br/></i>\n";
		echo '<a href="stones.php">voltar.</a>';
		echo "</fieldset>\n";
		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		if ($potion['item_id'] == 144){
		echo "Selecione um item para tentar adicionar 5 status de força. Você pode usar esta pedra apenas uma vez por item.<br/>";
		echo "<b>Atenção:</b> Lembre-se que seu item tem 30% de chance de quebrar durante o processo.<br/><br/>";

		$queoppa = $db->execute("select items.id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.item_id, items.mark, blueprint_items.name from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and items.for<5 and items.mark='f' and blueprint_items.type!='addon' and blueprint_items.type!='stone' and blueprint_items.type!='potion' order by blueprint_items.type asc", [$player->id]);
  		if ($queoppa->recordcount() == 0) {
			echo "<b>Você não possui itens disponíveis.</b>";
		}else{
		echo "<form method=\"POST\" action=\"stones.php?pedra=" . $_GET['pedra'] . "\"><b>Selecione:</b> <select name=\"itemid\">";
			while($item = $queoppa->fetchrow())
			{
			echo "<option value=\"" . $item['id'] . "\">" . $item['name'] . " +" . $item['item_bonus'] . " +" . $item['for'] . "F +" . $item['vit'] . "V +" . $item['agi'] . "A +" . $item['res'] . "R</option>";
			}
		echo "</select><input type=\"hidden\" name=\"usar\" value=\"confirm\"><input type=\"submit\" name=\"force1\" value=\"Usar pedra de Força\"></form>";
		}

		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		if ($potion['item_id'] == 145){
		echo "Selecione um item para tentar adicionar 5 status de vitalidade. Você pode usar esta pedra apenas uma vez por item.<br/>";
		echo "<b>Atenção:</b> Lembre-se que seu item tem 30% de chance de quebrar durante o processo.<br/><br/>";

		$queoppa = $db->execute("select items.id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.item_id, items.mark, blueprint_items.name from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and items.vit<5 and items.mark='f' and blueprint_items.type!='addon' and blueprint_items.type!='stone' and blueprint_items.type!='potion' order by blueprint_items.type asc", [$player->id]);
  		if ($queoppa->recordcount() == 0) {
			echo "<b>Você não possui itens disponíveis.</b>";
		}else{
		echo "<form method=\"POST\" action=\"stones.php?pedra=" . $_GET['pedra'] . "\"><b>Selecione:</b> <select name=\"itemid\">";
			while($item = $queoppa->fetchrow())
			{
			echo "<option value=\"" . $item['id'] . "\">" . $item['name'] . " +" . $item['item_bonus'] . " +" . $item['for'] . "F +" . $item['vit'] . "V +" . $item['agi'] . "A +" . $item['res'] . "R</option>";
			}
		echo "</select><input type=\"hidden\" name=\"usar\" value=\"confirm\"><input type=\"submit\" name=\"vite1\" value=\"Usar pedra de Vitalidade\"></form>";
		}

		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}


		if ($potion['item_id'] == 146){
		echo "Selecione um item para tentar adicionar 5 status de agilidade. Você pode usar esta pedra apenas uma vez por item.<br/>";
		echo "<b>Atenção:</b> Lembre-se que seu item tem 30% de chance de quebrar durante o processo.<br/><br/>";

		$queoppa = $db->execute("select items.id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.item_id, items.mark, blueprint_items.name from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and items.agi<5 and items.mark='f' and blueprint_items.type!='addon' and blueprint_items.type!='stone' and blueprint_items.type!='potion' order by blueprint_items.type asc", [$player->id]);
  		if ($queoppa->recordcount() == 0) {
			echo "<b>Você não possui itens disponíveis.</b>";
		}else{
		echo "<form method=\"POST\" action=\"stones.php?pedra=" . $_GET['pedra'] . "\"><b>Selecione:</b> <select name=\"itemid\">";
			while($item = $queoppa->fetchrow())
			{
			echo "<option value=\"" . $item['id'] . "\">" . $item['name'] . " +" . $item['item_bonus'] . " +" . $item['for'] . "F +" . $item['vit'] . "V +" . $item['agi'] . "A +" . $item['res'] . "R</option>";
			}
		echo "</select><input type=\"hidden\" name=\"usar\" value=\"confirm\"><input type=\"submit\" name=\"agile1\" value=\"Usar pedra de Agilidade\"></form>";
		}

		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}

		if ($potion['item_id'] == 147){
		echo "Selecione um item para tentar adicionar 5 status de agilidade. Você pode usar esta pedra apenas uma vez por item.<br/>";
		echo "<b>Atenção:</b> Lembre-se que seu item tem 30% de chance de quebrar durante o processo.<br/><br/>";

		$queoppa = $db->execute("select items.id, items.item_bonus, items.for, items.vit, items.agi, items.res, items.item_id, items.mark, blueprint_items.name from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and items.res<5 and items.mark='f' and blueprint_items.type!='addon' and blueprint_items.type!='stone' and blueprint_items.type!='potion' order by blueprint_items.type asc", [$player->id]);
  		if ($queoppa->recordcount() == 0) {
			echo "<b>Você não possui itens disponíveis.</b>";
		}else{
		echo "<form method=\"POST\" action=\"stones.php?pedra=" . $_GET['pedra'] . "\"><b>Selecione:</b> <select name=\"itemid\">";
			while($item = $queoppa->fetchrow())
			{
			echo "<option value=\"" . $item['id'] . "\">" . $item['name'] . " +" . $item['item_bonus'] . " +" . $item['for'] . "F +" . $item['vit'] . "V +" . $item['agi'] . "A +" . $item['res'] . "R</option>";
			}
		echo "</select><input type=\"hidden\" name=\"usar\" value=\"confirm\"><input type=\"submit\" name=\"resiste1\" value=\"Usar pedra de Resistencia\"></form>";
		}

		include(__DIR__ . "/templates/private_footer.php");
		exit;
		}


}

		echo "<fieldset>\n<legend>";
		echo "<b>Pedras disponíveis</b></legend>\n";
		$vendedor = $db->execute("select `id`, `name`, `price` from `blueprint_items` where `type`='stone' order by blueprint_items.name asc");
  		if ($vendedor->recordcount() == 0) {
			echo "Nenhuma pedra à venda.";
		}else{
		echo "<table width=\"100%\" border=\"0\">";
		echo "<tr>";
		echo "<th width=\"50%\"><b>Nome</b></td>";
		echo "<th width=\"25%\"><b>Preço</b></td>";
		echo "<th width=\"25%\"></td>";
		echo "</tr>";
			while($item3 = $vendedor->fetchrow())
			{
			echo "<tr><td>" . $item3['name'] . "</td><td>" . $item3['price'] . "</td><td><a href=\"stones.php?buyid=" . $item3['id'] . "\">Mais Informações</td></tr>";
			}
		echo "</table>";
		}
		echo "</fieldset><br/><br/>";





echo "<b>Suas pedras:</b>";
echo "<br />";
$query = $db->execute("select items.id, items.item_id, blueprint_items.type, blueprint_items.name, blueprint_items.description, blueprint_items.img from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='stone' and items.mark='f' order by blueprint_items.name asc", [$player->id]);
if ($query->recordcount() == 0)
{
	echo "Você não tem pedras.";
}
else
{
	while($item = $query->fetchrow())
	{
		echo "<fieldset>\n<legend>";
		echo "<b>" . $item['name'] . "</b></legend>\n";
		echo "<table width=\"100%\">\n";
		echo "<tr><td width=\"5%\">";
		echo "<img src=\"images/itens/" . $item['img'] . "\"/>";
		echo "</td><td width=\"80%\">";
		echo $item['description'] . "\n<br />";
		echo "</td><td width=\"15%\">";
		if ($item['item_id'] == 141) {
      $tipo = "mature";
  } elseif ($item['item_id'] == 142) {
      $tipo = "energy";
  } elseif ($item['item_id'] == 144) {
      $tipo = "status";
  } elseif ($item['item_id'] == 145) {
      $tipo = "status";
  } elseif ($item['item_id'] == 146) {
      $tipo = "status";
  } elseif ($item['item_id'] == 147) {
      $tipo = "status";
  }
		echo "<a href=\"stones.php?pedra=" . $item['id'] . "&type=" . $tipo . "\">Usar</a><br/><a href=\"stones.php?pedra=" . $item['id'] . "&sellid=" . $item['item_id'] . "&act=sell\">Vender</a>";
		echo "</td></tr>\n";
		echo "</table>";
		echo "</fieldset>\n";
	}
}

include(__DIR__ . "/templates/private_footer.php");
?>