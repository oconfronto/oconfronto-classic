<?php
				$mana = 25;
				$magiaatual = $db->GetOne("select `magia` from `bixos` where `player_id`=?", array($player->id));

				if ($player->mana < $mana){
      				$_SESSION['ataques'] .= "Você tentou lançar um feitiço mas está sem mana sufuciente.<br/>";
				$otroatak = 5;
				}elseif ($magiaatual != 0){
      				$_SESSION['ataques'] .= "Você não pode ativar um feitiço passivo enquanto outro está ativo.<br/>";
				$otroatak = 5;
				}else{
				$db->execute("update `bixos` set `magia`=? where `player_id`=?", array(1, $player->id));
				$db->execute("update `bixos` set `turnos`=? where `player_id`=?", array(6, $player->id));
				$db->execute("update `players` set `mana`=`mana`-? where `id`=?", array($mana, $player->id));
      				$_SESSION['ataques'] .= "<font color=\"blue\">Você lançou o feitiço reforço.</font><br/>";
				}
?>