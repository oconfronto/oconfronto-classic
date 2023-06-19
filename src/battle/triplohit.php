<?php
				$mana = 30;
				$pak0 = rand($player->mindmg, $player->maxdmg);
				$pak1 = rand($player->mindmg, $player->maxdmg);
				$totalpak = ceil($pak0 + $pak1);
				
				$magiaatual = $db->execute("select `magia`, `turnos` from `bixos` where `player_id`=?", array($player->id));
				$magiaatual2 = $magiaatual->fetchrow();

			if ($magiaatual2['magia'] == 1){
			$porcento = $totalpak / 100;
			$porcento = ceil($porcento * 15);
			$totalpak = $totalpak + $porcento;
			}else if($magiaatual2['magia'] == 2){
			$porcento = $totalpak / 100;
			$porcento = ceil($porcento * 45);
			$totalpak = $totalpak + $porcento;
			}else if($magiaatual2['magia'] == 12){
			$porcento = $totalpak / 100;
			$porcento = ceil($porcento * 35);
			$totalpak = $totalpak + $porcento;
			}


				if ($player->mana < $mana){
      				$_SESSION['ataques'] .= "Você tentou lançar um feitiço mas está sem mana sufuciente.<br/>";
				$otroatak = 5;
				}else{

				$misschance = intval(rand(0, 100));
				if ($misschance <= $player->miss)
				{
					$_SESSION['ataques'] .= "Você tentou lançar um feitiço n" . $enemy->prepo . " " . $enemy->username . " mas errou!<br />";
				}else{
					if (($bixo->hp - $totalpak) < 1){
					$db->execute("update `bixos` set `hp`=0 where `player_id`=?", array($player->id));
					$matou = 5;
					}else{
					$db->execute("update `bixos` set `hp`=`hp`-? where `player_id`=?", array($totalpak, $player->id));
					}

				$db->execute("update `players` set `mana`=`mana`-? where `id`=?", array($mana, $player->id));
      				$_SESSION['ataques'] .= "<font color=\"blue\">Você deu um ataque duplo n" . $enemy->prepo . " " . $enemy->username . " e tirou " . $totalpak . " de vida.</font><br/>";
				}
				}
?>