<?php
				$mana = 15;
				$curar = random_int(100, 200);
				$player->hp += $curar;
					if (($player->hp + $curar) > $player->maxhp){
					$_SESSION['ataques'] .= "<font color=blue>Você fez um feitiço e recuperou toda sua vida.</font><br/>";
					}else{
					$_SESSION['ataques'] .= "<font color=blue>Você fez um feitiço e recuperou " . $curar . " de vida.</font><br/>";
					}
				$player->mana -= $mana;
?>