<?php
				$mana = 65;
				$pak0 = random_int($player->mindmg, $player->maxdmg);
				$pak1 = random_int($player->mindmg, $player->maxdmg);
				$pak2 = random_int($player->mindmg, $player->maxdmg);
				$pak3 = random_int($player->mindmg, $player->maxdmg);
				$totalpak = ceil($pak0 + $pak1 + $pak2 + $pak3);

			if ($fastmagia == 1) {
       $porcento = $totalpak / 100;
       $porcento = ceil($porcento * 15);
       $totalpak += $porcento;
   } elseif ($fastmagia == 2) {
       $porcento = $totalpak / 100;
       $porcento = ceil($porcento * 45);
       $totalpak += $porcento;
   } elseif ($fastmagia == 12) {
       $porcento = $totalpak / 100;
       $porcento = ceil($porcento * 35);
       $totalpak += $porcento;
   }

					if (($bixo->hp - $totalpak) < 1){
					$bixo->hp = 0;
					$matou = 5;
					}else{
					$bixo->hp -= $totalpak;
					}

				$player->mana -= $mana;
      				$_SESSION['ataques'] .= "<font color=\"blue\">Você deu um ataque quádruplo n" . $enemy->prepo . " " . $enemy->username . " e tirou " . $totalpak . " de vida.</font><br/>";
?>