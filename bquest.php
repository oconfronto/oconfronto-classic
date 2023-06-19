<?php

include("lib.php");
define("PAGENAME", "Batalhar");
$player = check_user($secret_key, $db);
include("checkhp.php");
include("checkwork.php");

$iid = "";
$iname = "";	

	$verificacao = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=?", array($player->id, 12));
	$quest = $verificacao->fetchrow();

		if ($verificacao->recordcount() == 0)
		{
		header("Location: home.php");
		}

	if (($quest['quest_status'] != 2) and ($quest['quest_status'] != 4) and ($quest['quest_status'] != 6))
	{
	header("Location: home.php");
	}

	if ($quest['quest_status'] == 2)
	{
		$questnivel = 1;
		if ($player->voc == 'knight'){
		$enemy->username = "Alexia";
		}elseif ($player->voc == 'archer'){
		$enemy->username = "Dem�nio";
		}elseif ($player->voc == 'mage'){
		$enemy->username = "Detros";
		}

		$enemy->level = 250;
		$enemy->strength = 575;
		$enemy->vitality = 405;
		$enemy->agility = 530;
		$enemy->hp = 5000;
		$enemy->mtexp = 10000;

	}
	elseif ($quest['quest_status'] == 4)
	{
		$questnivel = 2;
		if ($player->voc == 'knight'){
		$enemy->username = "Ramthysts";
		}elseif ($player->voc == 'archer'){
		$enemy->username = "Dem�nio";
		}elseif ($player->voc == 'mage'){
		$enemy->username = "Azura";
		}

		$enemy->level = 265;
		$enemy->strength = 590;
		$enemy->vitality = 420;
		$enemy->agility = 540;
		$enemy->hp = 5500;
		$enemy->mtexp = 12000;

	}
	elseif ($quest['quest_status'] == 6)
	{
		$questnivel = 3;
		if ($player->voc == 'knight'){
		$enemy->username = "Friden";
		$iid = "151";
		$iname = "a Friden Sword";	
		}elseif ($player->voc == 'archer'){
		$enemy->username = "Baltazar";
		$iid = "153";
		$iname = "o Baltazar's Bow";
		}elseif ($player->voc == 'mage'){
		$enemy->username = "Draconos";
		$iid = "152";
		$iname = "a Wand of Dracula";

		}

		$enemy->level = 285;
		$enemy->strength = 615;
		$enemy->vitality = 450;
		$enemy->agility = 565;
		$enemy->hp = 6500;
		$enemy->mtexp = 15000;

	}


		//Player cannot attack anymore
		if ($player->energy < 10)
		{
			include("templates/private_header.php");
			echo "Voc� est� sem energia! Voc� deve descan�ar um pouco. <a href=\"monster.php\">Voltar</a>.";
			include("templates/private_footer.php");
			exit;
		}
		
		//Player is dead
		if ($player->hp == 0)
		{
			include("templates/private_header.php");
			echo "Voc� est� morto! Por favor visite o hospital ou espere 30 minutos! <a href=\"monster.php\">Voltar</a>.";
			include("templates/private_footer.php");
			exit;
		}
		
		//Get player's bonuses from equipment
		$query = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='weapon' and items.status='equipped'", array($player->id));
		$player->atkbonus = ($query->recordcount() == 1)?$query->fetchrow():0;
		$query50 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='armor' and items.status='equipped'", array($player->id));
		$player->defbonus1 = ($query50->recordcount() == 1)?$query50->fetchrow():0;
		$query51 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='helmet' and items.status='equipped'", array($player->id));
		$player->defbonus2 = ($query51->recordcount() == 1)?$query51->fetchrow():0;
		$query52 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='legs' and items.status='equipped'", array($player->id));
		$player->defbonus3 = ($query52->recordcount() == 1)?$query52->fetchrow():0;
		$query54 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='shield' and items.status='equipped'", array($player->id));
		$player->defbonus5 = ($query54->recordcount() == 1)?$query54->fetchrow():0;
		$query55 = $db->query("select blueprint_items.effectiveness, blueprint_items.name, items.item_bonus, items.for, items.agi, items.res from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='boots' and items.status='equipped'", array($player->id));
		$player->agibonus6 = ($query55->recordcount() == 1)?$query55->fetchrow():0;


	$checamagiastatus = $db->execute("select * from `magias` where `magia_id`=5 and `player_id`=?", array($player->id));

		if ($player->voc == 'archer'){
			if ($checamagiastatus->recordcount() > 0){
			$varataque = 0.31;
			$vardefesa = 0.15;
			$vardivide = 0.15;
			}else{
			$varataque = 0.29;
			$vardefesa = 0.14;
			$vardivide = 0.14;
			}
		}
		else if ($player->voc == 'mage'){
			if ($checamagiastatus->recordcount() > 0){
			$varataque = 0.265;
			$vardefesa = 0.15;
			$vardivide = 0.14;
			}else{
			$varataque = 0.245;
			$vardefesa = 0.14;
			$vardivide = 0.13;
			}
		}
		else if ($player->voc == 'knight'){
			if ($checamagiastatus->recordcount() > 0){
			$varataque = 0.22;
			$vardefesa = 0.17;
			$vardivide = 0.15;
			}else{
			$varataque = 0.20;
			$vardefesa = 0.16;
			$vardivide = 0.14;
			}
		}

			if ($player->promoted == 'f') {
			$multipleatk = 1 + ($varataque * 1.6);
			$multipledef = 1 + ($vardefesa * 1.6);
			$divideres = 2.3 - ($vardivide * 1.6);
			}elseif ($player->promoted == 't') {
			$multipleatk = 1 + ($varataque * 2.4);
			$multipledef = 1 + ($vardefesa * 2.4);
			$divideres = 2.3 - ($vardivide * 2.4);
			}elseif ($player->promoted == 'r') {
			$multipleatk = 1 + ($varataque * 3.2);
			$multipledef = 1 + ($vardefesa * 3.2);
			$divideres = 2.3 - ($vardivide * 3.2);
			}elseif ($player->promoted == 's') {
			$multipleatk = 1 + ($varataque * 4);
			$multipledef = 1 + ($vardefesa * 4);
			$divideres = 2.3 - ($vardivide * 4);
			}

		
		//Calculate some variables that will be used
		$forcadoplayer = ceil(($player->strength + $player->atkbonus['effectiveness'] + ($player->atkbonus['item_bonus'] * 2) + ($player->atkbonus['for'] + $player->defbonus1['for'] + $player->defbonus2['for'] + $player->defbonus3['for'] + $player->defbonus5['for'] + $player->agibonus6['for'])) * $multipleatk);
		$agilidadedoplayer = ceil($player->agility + $player->agibonus6['effectiveness'] + ($player->agibonus6['item_bonus'] * 2) + ($player->atkbonus['agi'] + $player->defbonus1['agi'] + $player->defbonus2['agi'] + $player->defbonus3['agi'] + $player->defbonus5['agi'] + $player->agibonus6['agi']));
		$resistenciadoplayer = ceil((($player->resistance + ($player->defbonus1['effectiveness'] + $player->defbonus2['effectiveness'] + $player->defbonus3['effectiveness'] + $player->defbonus5['effectiveness']) + (($player->defbonus1['item_bonus'] * 2) + ($player->defbonus2['item_bonus'] * 2) + ($player->defbonus3['item_bonus'] * 2) + ($player->defbonus5['item_bonus'] * 2)) + ($player->atkbonus['res'] + $player->defbonus1['res'] + $player->defbonus2['res'] + $player->defbonus3['res'] + $player->defbonus5['res'] + $player->agibonus6['res'])) * $multipledef) / 1.35);


		if ($player->voc != 'archer'){
		$forcadomonstro = ($enemy->strength * 1.3);
		}else{
		$forcadomonstro = ($enemy->strength * 1.18);
		}
		$agilidadedomonstro = ($enemy->agility / 1.35);
		$resistenciadomonstro = ($enemy->vitality * 1.4);

		$especagi = $agilidadedoplayer;

		$enemy->strdiff = (($forcadomonstro - $forcadoplayer) > 0)?($forcadomonstro - $forcadoplayer):0;
		$enemy->resdiff = (($resistenciadomonstro - ($resistenciadoplayer * 1.5)) > 0)?($resistenciadomonstro - $resistenciadoplayer):0;
		$enemy->agidiff = (($agilidadedomonstro - $especagi) > 0)?($agilidadedomonstro - $especagi):0;
		$enemy->leveldiff = (($enemy->level - $player->level) > 0)?($enemy->level - $player->level):0;
		$player->strdiff = (($forcadoplayer - $forcadomonstro) > 0)?($forcadoplayer - $forcadomonstro):0;
		$player->resdiff = (($resistenciadoplayer - $resistenciadomonstro) > 0)?($resistenciadoplayer - $resistenciadomonstro):0;
       		$player->agidiff = (($especagi - $agilidadedomonstro) > 0)?($especagi - $agilidadedomonstro):0;
		$player->leveldiff = (($player->level - $enemy->level) > 0)?($player->level - $enemy->level):0;
		$totalstr = $forcadomonstro + $forcadoplayer;
		$totalres = $resistenciadomonstro + $resistenciadoplayer;
		$totalagi = $agilidadedomonstro + $especagi;
		$totallevel = $enemy->level + $player->level;
	
		//Calculate the damage to be dealt by each player (dependent on strength and level)
		$enemy->maxdmg = ($forcadomonstro - ($resistenciadoplayer / $divideres));
		$enemy->maxdmg = $enemy->maxdmg - intval($enemy->maxdmg * ($player->leveldiff / $totallevel));
		$enemy->maxdmg = ($enemy->maxdmg <= 2)?2:$enemy->maxdmg; //Set 2 as the minimum damage
		$enemy->mindmg = (($enemy->maxdmg - 4) < 1)?1:($enemy->maxdmg - 4); //Set a minimum damage range of maxdmg-4

		$player->maxdmg = ($forcadoplayer - ($resistenciadomonstro / 1.20));
		$player->maxdmg = $player->maxdmg - intval($player->maxdmg * ($enemy->leveldiff / $totallevel));
		$player->maxdmg = ($player->maxdmg <= 2)?2:$player->maxdmg; //Set 2 as the minimum damage
		$player->mindmg = (($player->maxdmg - 4) < 1)?1:($player->maxdmg - 4); //Set a minimum damage range of maxdmg-4
		
		//Calculate battle 'combos' - how many times in a row a player can attack (dependent on agility)
		$enemy->combo = ceil($agilidadedomonstro / $especagi);
		$enemy->combo = ($enemy->combo > 3)?3:$enemy->combo;
  		$player->combo = ceil($especagi / $agilidadedomonstro);
		$player->combo = ($player->combo > 3)?3:$player->combo;
		

		//Calculate the chance to miss opposing player
		$enemy->miss = intval(($player->agidiff / $totalagi) * 100);
		$enemy->miss = ($enemy->miss > 20)?20:$enemy->miss; //Maximum miss chance of 20% (possible to change in admin panel?)
		$enemy->miss = ($enemy->miss <= 8)?8:$enemy->miss; //Minimum miss chance of 5%
		$player->miss = intval(($enemy->agidiff / $totalagi) * 100);
		$player->miss = ($player->miss > 20)?20:$player->miss; //Maximum miss chance of 20%
		$player->miss = ($player->miss <= 8)?8:$player->miss; //Minimum miss chance of 5%


		$battlerounds = 180;
		
		$output = ""; //Output message
		
		
		$output .= "<div class=\"scroll\" style=\"background-color:#FFFDE0; overflow: auto; height:270px; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";

		//While somebody is still alive, battle!
		while ($enemy->hp > 0 && $player->hp > 0 && $battlerounds > 0)
		{

			
			$attacking = ($especagi >= $enemy->agility)?$player:$enemy;
			$defending = ($especagi >= $enemy->agility)?$enemy:$player;
			
			for($i = 0;$i < $attacking->combo;$i++)
			{
				//Chance to miss?
				$misschance = intval(rand(0, 100));
				if ($misschance <= $attacking->miss)
				{
					$output .= $attacking->username . " tentou atacar " . $defending->username . " mas errou!<br />";
				}
				else
				{
					$damage = rand($attacking->mindmg, $attacking->maxdmg); //Calculate random damage				
					$defending->hp -= $damage;
					$output .= ($player->username == $defending->username)?"<font color=\"red\">":"<font color=\"green\">";
					$output .= $attacking->username . " atacou " . $defending->username . " e tirou <b>" . $damage . "</b> de vida! (";
					$output .= ($defending->hp > 0)?$defending->hp . " de vida":"Morto";
					$output .= ")<br />";
					$output .= "</font>";

					//Check if anybody is dead
					if ($defending->hp <= 0)
					{
						$player = ($especagi >= $enemy->agility)?$attacking:$defending;
						$enemy = ($especagi >= $enemy->agility)?$defending:$attacking;
						break 2; //Break out of the for and while loop, but not the switch structure
					}
				}
				$battlerounds--;
				if ($battlerounds <= 0)
				{
					break 2; //Break out of for and while loop, battle is over!
				}
			}
			
			for($i = 0;$i < $defending->combo;$i++)
			{
				//Chance to miss?
				$misschance = intval(rand(0, 100));
				if ($misschance <= $defending->miss)
				{
					$output .= $defending->username . " tentou atacar " . $attacking->username . " mas errou!<br />";
				}else{
					$damage = rand($defending->mindmg, $defending->maxdmg); //Calculate random damage
					$attacking->hp -= $damage;
					$output .= ($player->username == $defending->username)?"<font color=\"green\">":"<font color=\"red\">";
					$output .= $defending->username . " atacou " . $attacking->username . " e tirou <b>" . $damage . "</b> de vida! (";
					$output .= ($attacking->hp > 0)?$attacking->hp . " de vida":"Morto";
					$output .= ")<br />";
					$output .= "</font>";

					//Check if anybody is dead
					if ($attacking->hp <= 0)
					{
						$player = ($especagi >= $enemy->agility)?$attacking:$defending;
						$enemy = ($especagi >= $enemy->agility)?$defending:$attacking;
						break 2; //Break out of the for and while loop, but not the switch structure
					}
				}
				$battlerounds--;
				if ($battlerounds <= 0)
				{
					break 2; //Break out of for and while loop, battle is over!
				}
			}
			
			$player = ($especagi >= $enemy->agility)?$attacking:$defending;
			$enemy = ($especagi >= $enemy->agility)?$defending:$attacking;

		}
					$output .= "</div>";
		
		if ($player->hp <= 0)
		{
			//Calculate losses
			$exploss1 = $player->level * 7;
			$exploss2 = (($player->level - $enemy->level) > 0)?($enemy->level - $player->level) * 4:0;
			$exploss = $exploss1 + $exploss2;
			$goldloss = intval(0.4 * $player->gold);
			$goldloss = intval(rand(1, $goldloss));

			$output .= "<br/><div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b><u>Voc� foi morto por " . $enemy->username . "!</u></b></div>";
			$output .= "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Voc� perdeu <b>" . $exploss . "</b> de EXP e <b>" . $goldloss . "</b> de ouro.</div>";
			$exploss3 = (($player->exp - $exploss) <= 0)?$player->exp:$exploss;
			$goldloss2 = (($player->gold - $goldloss) <= 0)?$player->gold:$goldloss;
			//Update player (the loser)
			$query = $db->execute("update `players` set `energy`=?, `exp`=?, `gold`=?, `deaths`=?, `hp`=0, `deadtime`=? where `id`=?", array($player->energy - 10, $player->exp - $exploss3, $player->gold - $goldloss2, $player->deaths + 1, time() + $setting->dead_time, $player->id));
						
		}
		else if ($enemy->hp <= 0)
		{
			//Calculate losses
			$expwin1 = $enemy->level * 6;
			$expwin2 = (($player->level - $enemy->level) > 0)?$expwin1 - (($player->level - $enemy->level) * 3):$expwin1 + (($player->level - $enemy->level) * 3);
			$expwin2 = ($expwin2 <= 0)?1:$expwin2;
			$expwin3 = round(0.5 * $expwin2);
			$expwin = ceil(rand($expwin3, $expwin2));
			$goldwin = round(0.8 * $expwin);
			$goldwin = round($goldwin * 1.35);
			if ($setting->eventoouro > time()){
			$goldwin = round($goldwin * 2);
			}
			$output .= "<br/><div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b><u>Voc� matou " . $enemy->username . "!</u></b></div>";
			$output .= "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Voc� ganhou <b>" . $expdomonstro . "</b> de EXP e <b>" . $goldwin . "</b> de ouro.</div>";

			if ($questnivel == 1){
			$query = $db->execute("update `quests` set `quest_status`=? where `player_id`=? and `quest_id`=?", array(3, $player->id, 12));
			}elseif ($questnivel == 2){
			$query = $db->execute("update `quests` set `quest_status`=? where `player_id`=? and `quest_id`=?", array(5, $player->id, 12));
			}elseif ($questnivel == 3){

			$output .= "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">Voc� encontrou " . $iname . " com " . $enemy->username . ".</div>";

			$insert['player_id'] = $player->id;
			$insert['item_id'] = $iid;
			$addlootitemwin = $db->autoexecute('items', $insert, 'INSERT');

			$query = $db->execute("update `quests` set `quest_status`=? where `player_id`=? and `quest_id`=?", array(7, $player->id, 12));
			}

			$output .= "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><a href=\"promo1.php\">Clique aqui</a> para continuar sua miss�o</div>";

			if ($expdomonstro + $player->exp >= $player->maxexp) //Player gained a level!
			{
				//Update player, gained a level
				$output .= "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><u><b>Voc� passou de nivel!</b></u></div>";
				$newexp = $expdomonstro + $player->exp - $player->maxexp;


$plevel = ($player->level + 1);
$dividecinco = ($plevel / 5);
$dividecinco = floor($dividecinco);

$ganha = 100 + ($dividecinco * 15) + $player->extramana;

$db->execute("update `players` set `mana`=?, `maxmana`=? where `id`=?", array($ganha, $ganha, $player->id));


				if ($player->level <= 3){
				$expofnewlvl = $player->maxexp + 75;
				}else{
				$expofnewlvl = floor(20 * ($player->level * $player->level * $player->level)/$player->level);
				}
				$query = $db->execute("update `players` set `magic_points`=?, `stat_points`=?, `level`=?, `maxexp`=?, `maxhp`=?, `exp`=?, `hp`=?, `energy`=?, `gold`=?, `monsterkill`=?, `monsterkilled`=? where `id`=?", array($player->magic_points + 1, $player->stat_points + 3, $player->level + 1, $expofnewlvl, $player->maxhp + 30, $newexp, $player->maxhp + 30, $player->energy - 10, $player->gold + $goldwin, $player->monsterkill + 1, $player->monsterkilled + 1, $player->id));
			}
			else
			{
				//Update player
				$query = $db->execute("update `players` set `exp`=?, `gold`=?, `hp`=?, `energy`=?, `monsterkill`=?, `monsterkilled`=? where `id`=?", array($player->exp + $expdomonstro, $player->gold + $goldwin, $player->hp, $player->energy - 10, $player->monsterkill + 1, $player->monsterkilled + 1, $player->id));
			}
			
		}
		else
		{
			$output .= "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\"><b><u>Os dois est�o muito can�ados para terminar a batalha! Ningu�m venceu.</u></b></div>";
			$query = $db->execute("update `players` set `hp`=?, `energy`=?, `monsterkill`=? where `id`=?", array($player->hp, $player->energy - 10, $player->monsterkill + 1, $player->id));
			
		}
		
		$player = check_user($secret_key, $db); //Get new stats
		include("templates/private_header.php");
		echo $output;
		include("templates/private_footer.php");
?>