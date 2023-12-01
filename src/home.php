<?php

/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Principal");
$ippe = $_SERVER['REMOTE_ADDR'];

$player = check_user($secret_key, $db);



if ($player->last_active == $player->registered) {
	$insert['player_id'] = $player->id;
	$insert['pending_id'] = 2;
	$insert['pending_status'] = 1;
	$insert['pending_time'] = time();
	$query = $db->autoexecute('pending', $insert, 'INSERT');
}


$query04457 = $db->execute("select * from `pending` where `pending_id`=2 and `player_id`=?", array($player->id));
if ($query04457->recordcount() > 0) {
	header("Location: tutorial.php");
}

include("checkbattle.php");

include("templates/private_header.php");


/*************************************/
/*     Medalhas by Jrotta            */
/*************************************/


$medalha1 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", array($player->id, Imortal));
if ($medalha1->recordcount() < 1) {
	if (($player->level > 19) and ($player->deaths == 0)) {
		$insert['player_id'] = $player->id;
		$insert['medalha'] = "Imortal";
		$insert['motivo'] = "Passou do n�vel 19 sem nunca ter morrido.";
		$query = $db->autoexecute('medalhas', $insert, 'INSERT');
		echo "Parab�ns, voc� passou do n�vel 19 sem nunca ter morrido.<br/>";
		echo "Uma medalha foi adicionada ao seu perfil por este motivo.<br/><a href=\"home.php\">Voltar</a>.";
		include("templates/private_footer.php");
		exit;
	}
}

$medalha2 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", array($player->id, Assassino));
if ($medalha2->recordcount() < 1) {
	if ($player->kills > 500) {
		$insert['player_id'] = $player->id;
		$insert['medalha'] = "Assassino";
		$insert['motivo'] = "Matou mais de 500 usu�rios.";
		$query = $db->autoexecute('medalhas', $insert, 'INSERT');
		echo "Parab�ns, voc� matou mais de 500 usu�rios.<br/>";
		echo "Uma medalha foi adicionada ao seu perfil por este motivo.<br/><a href=\"home.php\">Voltar</a>.";
		include("templates/private_footer.php");
		exit;
	}
}

$medalha3 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", array($player->id, Exterminador));
if ($medalha3->recordcount() < 1) {
	if ($player->monsterkilled > 10000) {
		$insert['player_id'] = $player->id;
		$insert['medalha'] = "Exterminador";
		$insert['motivo'] = "Matou mais de 10000 monstros.";
		$query = $db->autoexecute('medalhas', $insert, 'INSERT');
		echo "Parab�ns, voc� matou mais de 10000 monstros.<br/>";
		echo "Uma medalha foi adicionada ao seu perfil por este motivo.<br/><a href=\"home.php\">Voltar</a>.";
		include("templates/private_footer.php");
		exit;
	}
}

$medalha4 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", array($player->id, Milion�rio));
if ($medalha4->recordcount() < 1) {
	if (($player->gold + $player->bank) > 10000000) {
		$insert['player_id'] = $player->id;
		$insert['medalha'] = "Milion�rio";
		$insert['motivo'] = "Juntou mais de 10 milh�es em ouro.";
		$query = $db->autoexecute('medalhas', $insert, 'INSERT');
		echo "Parab�ns, voc� juntou mais de 10 milh�es em ouro.<br/>";
		echo "Uma medalha foi adicionada ao seu perfil por este motivo.<br/><a href=\"home.php\">Voltar</a>.";
		include("templates/private_footer.php");
		exit;
	}
}

$medalha5 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", array($player->id, Trabalhador));
if ($medalha5->recordcount() < 1) {
	if ($player->worklvl > 99) {
		$insert['player_id'] = $player->id;
		$insert['medalha'] = "Trabalhador";
		$insert['motivo'] = "Atingiu n�vel de trabalho 100.";
		$query = $db->autoexecute('medalhas', $insert, 'INSERT');
		echo "Parab�ns, voc� atingiu n�vel de trabalho 100.<br/>";
		echo "Uma medalha foi adicionada ao seu perfil por este motivo.<br/><a href=\"home.php\">Voltar</a>.";
		include("templates/private_footer.php");
		exit;
	}
}

$medalha7 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", array($player->id, Indicador));
if ($medalha7->recordcount() < 1) {
	if ($player->ref > 10) {
		$insert['player_id'] = $player->id;
		$insert['medalha'] = "Indicador";
		$insert['motivo'] = "Convidou mais de 10 amigos para o jogo.";
		$query = $db->autoexecute('medalhas', $insert, 'INSERT');
		echo "Parab�ns, voc� convidou mais de 10 amigos para o jogo.<br/>";
		echo "Uma medalha foi adicionada ao seu perfil por este motivo.<br/><a href=\"home.php\">Voltar</a>.";
		include("templates/private_footer.php");
		exit;
	}
}


$medalha8 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", array($player->id, 'Multi-Milion�rio'));
if ($medalha8->recordcount() < 1) {
	if (($player->gold + $player->bank) > 100000000) {
		$insert['player_id'] = $player->id;
		$insert['medalha'] = "Multi-Milion�rio";
		$insert['motivo'] = "Juntou mais de 100 milh�es em ouro.";
		$query = $db->autoexecute('medalhas', $insert, 'INSERT');
		echo "Parab�ns, voc� juntou mais de 100 milh�es em ouro.<br/>";
		echo "Uma medalha foi adicionada ao seu perfil por este motivo.<br/><a href=\"home.php\">Voltar</a>.";
		include("templates/private_footer.php");
		exit;
	}
}

$medalha9 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", array($player->id, 'Jogador Antigo'));
if ($medalha9->recordcount() < 1) {

	$diff2 = time() - $player->registered;
	$age2 = intval(($diff2 / 3600) / 24);

	if ($age2 > 99) {
		$insert['player_id'] = $player->id;
		$insert['medalha'] = "Jogador Antigo";
		$insert['motivo'] = "Jogador � mais de 100 dias.";
		$query = $db->autoexecute('medalhas', $insert, 'INSERT');
		echo "Parab�ns, voc� j� � jogador � mais de 100 dias.<br/>";
		echo "Uma medalha foi adicionada ao seu perfil por este motivo.<br/><a href=\"home.php\">Voltar</a>.";
		include("templates/private_footer.php");
		exit;
	}
}

$onuptime = $db->GetOne("select `login` from `online` where `ip`=?", array($ippe));
$medalha10 = $db->execute("select * from `medalhas` where `player_id`=? and `medalha`=?", array($player->id, 'Dedicado'));
if ($medalha10->recordcount() < 1) {
	if (($onuptime > 100000) and ($onuptime + 36000) < time()) {
		$insert['player_id'] = $player->id;
		$insert['medalha'] = "Dedicado";
		$insert['motivo'] = "Permaneceu online por mais de 10 horas.";
		$query = $db->autoexecute('medalhas', $insert, 'INSERT');
		echo "Parab�ns, voc� est� online � mais de 10 horas.<br/>";
		echo "Uma medalha foi adicionada ao seu perfil por este motivo.<br/><a href=\"home.php\">Voltar</a>.";
		include("templates/private_footer.php");
		exit;
	}
}




/*************************************/
/*    		        end          */
/*************************************/

$antihack2 = $db->execute("update `players` set `ban`=1325463270 where `strength`+`stat_points`+`agility`+`vitality`+`resistance`>((`level`*3)+(`buystats`*3)+15) and `ban`=0 and `gm_rank`<80");

if ($player->ban > time()) {
	session_unset();
	session_destroy();
	echo "Voc� foi banido. As vezes usu�rios s�o banidos automaticamente por algum erro em suas contas. Se voc� acha que foi banido injustamente, ou se tiver algum erro para reportar, crie outra conta e entre em contato com o [GOD]. Assim seu banimento poder� ser removido.";
	include("templates/private_footer.php");
	exit;
}

$query = $db->execute("update `players` set `last_active`=? where `id`=?", array(time(), $player->id));
?>



<table width="100%" border="0">
	<tr>
		<td width="50%">
			<b>Usu�rio:</b>
			<?= $player->username ?><br />
			<b>Registrado:</b>
			<?= date("j F, Y, g:i a", $player->registered) ?><br />
			<?php
			$diff = time() - $player->registered;
			$age = intval(($diff / 3600) / 24);
			?>
			<b>Idade do personagem:</b>
			<?= $age ?> dias<br />
			<b>Servidor:</b>
			<?= $player->serv ?><br />
			<br />
			<b>Pontos de status:</b>
			<?= $player->stat_points ?> | <a href="buystats.php">Treinar!</a><br />
			<?php
			if ($player->stat_points > 0) {
				echo "<a href=\"stat_points.php\"><b>Clique aqui para utilizar seus pontos.</b></a><br />";
			}
			?>
			<br />
			<b>Pontos m�sticos:</b>
			<?= $player->magic_points ?><br />
		<td class="red" width="60%">
			<?php
			if ($player->magic_points > 0) {
				echo "<a href=\"spells.php\"><b>Gerenciar.</b></a>";
			}
			?>
		</td>
		<?php
		if ($player->magic_points == 0) {
			echo "<a href=\"spells.php\"><b>Clique aqui para visualizar sua arvore de feiti�os.</b></a>";
		}
		?>



		<br><br>

		<?php
		$choapeaaww = $db->execute("select `id` from `quests` where `quest_id`=6 and `quest_status`=90 and `player_id`=?", array($player->id));
		if ($player->level > 24 and $player->level < 36 and $choapeaaww->recordcount() == 0) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� est� entre o nivel 25 e 35, e tem uma miss�o � fazer. <a href="quest3.php">Clique aqui</a>.</p>
			</div>
		<?php } ?>

		<?php
		$checkmission1 = $db->execute("select `id` from `quests` where `quest_id`=4 and `quest_status`=90 and `player_id`=?", array($player->id));
		if ($player->level > 39 and $checkmission1->recordcount() == 0) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� j� passou no nivel 39, e tem uma miss�o � fazer. <a href="quest2.php">Clique aqui</a>.</p>
			</div>
		<?php } ?>

		<? if ($player->level > 79 and $player->promoted == "f") {
			echo '<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
					align="center">
					<p>Voc� j� passou no nivel 79, agora voc� pode treinar sua voca��o e virar um <b>';
							if ($player->voc == 'archer') {
								echo "Arqueiro";
							} else if ($player->voc == 'knight') {
								echo "Guerreiro";
							} else if ($player->voc == 'mage') {
								echo "Mago";
							}
						echo '</b>. <a href="promote.php">Clique aqui</a>.</p>
				</div>
			';
		} ?>

		<?php
		$checaquestring = $db->execute("select `id` from `quests` where `quest_id`=2 and `quest_status`=90 and `player_id`=?", array($player->id));
		if ($player->level > 99 and $checaquestring->recordcount() == 0) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� j� passou no nivel 99, e tem uma miss�o � fazer.</b> <a href="quest1.php">Clique aqui</a>.</p>
			</div>
		<?php } ?>


		<?php
		$checaquestring = $db->execute("select `id` from `quests` where `quest_id`=7 and `quest_status`=90 and `player_id`=?", array($player->id));
		if ($player->level > 129 and $checaquestring->recordcount() == 0) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� j� passou no nivel 129, e tem uma miss�o � fazer.</b> <a href="quest4.php">Clique aqui</a>.</p>
			</div>
			<?php
		}
		?>

		<?php
		$chdrgdrg = $db->execute("select `id` from `quests` where `quest_id`=9 and `quest_status`=90 and `player_id`=?", array($player->id));
		if ($player->level > 144 and $player->level < 156 and $chdrgdrg->recordcount() == 0) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� est� entre o nivel 145 e 155, e tem uma miss�o � fazer. <a href="quest5.php">Clique aqui</a>.</p>
			</div>
		<?php } ?>

		<?php
		$chdsadasdasg = $db->execute("select `id` from `quests` where `quest_id`=11 and `quest_status`=90 and `player_id`=?", array($player->id));
		if ($player->level > 159 and $chdsadasdasg->recordcount() == 0) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� j� passou do nivel 159, e pode comprar itens especiais no ferreiro. <a href="shop.php">Clique
						aqui</a>.</p>
			</div>
		<?php } ?>

		<? if ($player->level > 239 and $player->promoted != "p") {
			echo '<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� j� passou no nivel 239, agora voc� pode treinar sua voca��o e virar um <b>';
						if ($player->voc == 'archer') {
							echo "Arqueiro Royal";
						} else if ($player->voc == 'knight') {
							echo "Cavaleiro";
						} else if ($player->voc == 'mage') {
							echo "Arquimago";
						}
					echo '</b>. <a href="promo1.php">Clique aqui</a>.</p>
			</div>';
		} ?>

		<?php
		$treinaquest1 = $db->execute("select `id` from `quests` where `quest_id`=14 and (`quest_status`=90 or `quest_status`=89) and `player_id`=?", array($player->id));
		if ($player->level > 299 and $treinaquest1->recordcount() == 0) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� j� passou do nivel 300, e tem uma miss�o � fazer. <a href="quest6.php">Clique aqui</a>.</p>
			</div>
		<?php } ?>

		<?php
		$treinaquest2 = $db->execute("select `id` from `quests` where `quest_id`=15 and `quest_status`=90 and `player_id`=?", array($player->id));
		if (($player->level > 299) and ($treinaquest1->recordcount() != 0) and ($treinaquest2->recordcount() == 0)) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� j� passou do nivel 300, e tem uma miss�o � fazer. <a href="quest7.php">Clique aqui</a>.</p>
			</div>
		<?php } ?>

		<?php
		$treinaquest3 = $db->execute("select `id` from `quests` where `quest_id`=17 and `quest_status`=90 and `player_id`=?", array($player->id));
		if (($player->level > 299) and ($treinaquest2->recordcount() != 0) and ($treinaquest3->recordcount() == 0)) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� j� passou do nivel 300, e tem uma miss�o � fazer. <a href="quest8.php">Clique aqui</a>.</p>
			</div>
		<?php } ?>

		<?php
		$treinaquest4 = $db->execute("select `id` from `quests` where `quest_id`=18 and `quest_status`=90 and `player_id`=?", array($player->id));
		if (($player->level > 299) and ($treinaquest3->recordcount() != 0) and ($treinaquest4->recordcount() == 0)) { ?>
			<div style="width:90%; background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px"
				align="center">
				<p>Voc� j� passou do nivel 300, e tem uma miss�o � fazer. <a href="quest9.php">Clique aqui</a>.</p>
			</div>
		<?php } ?>

		</td>
		<td width="50%">
			<table>
				<tr>
					<td><b>Nivel:</b></td>
					<td>
						<?= $player->level ?>
					</td>
				</tr>
				<tr>
					<td><b>EXP:</b></td>
					<td> <img src="bargen.php?exp"></td>
				</tr>
				<tr>
					<td><b>Vida:</b></td>
					<td> <img src="bargen.php?hp"></td>
				</tr>
				<tr>
					<td><b>Mana:</b></td>
					<td> <img src="bargen.php?mana"></td>
				</tr>
				<tr>
					<td><b>Energia:</b></td>
					<td> <img src="bargen.php?energy"></td>
				</tr>
			</table>
			<br>
			<b>Ouro:</b> <b>
				<font color="#DFA40F">
					<?= $player->gold ?>
				</font>
			</b><br>
			<b>Voca��o:</b>
			<?php
			if ($player->voc == 'archer' and $player->promoted == 'f') {
				echo "Ca�ador";
			} else if ($player->voc == 'knight' and $player->promoted == 'f') {
				echo "Espadachim";
			} else if ($player->voc == 'mage' and $player->promoted == 'f') {
				echo "Bruxo";
			} else if (($player->voc == 'archer') and ($player->promoted == 't' or $player->promoted == 's' or $player->promoted == 'r')) {
				echo "Arqueiro";
			} else if (($player->voc == 'knight') and ($player->promoted == 't' or $player->promoted == 's' or $player->promoted == 'r')) {
				echo "Guerreiro";
			} else if (($player->voc == 'mage') and ($player->promoted == 't' or $player->promoted == 's' or $player->promoted == 'r')) {
				echo "Mago";
			} else if ($player->voc == 'archer' and $player->promoted == 'p') {
				echo "Arqueiro Royal";
			} else if ($player->voc == 'knight' and $player->promoted == 'p') {
				echo "Cavaleiro";
			} else if ($player->voc == 'mage' and $player->promoted == 'p') {
				echo "Arquimago";
			}
			?><br>
			<b>Cl�:</b>
			<?php
			if ($player->guild == NULL or $player->guild == '') {
				echo "[Nenhum]";
			} else {
				$nomecla = $db->GetOne("select `name` from `guilds` where `id`=?", array($player->guild));
				echo "<b>[</b><a href=\"guild_home.php\">" . $nomecla . "</a><b>]</b>";
			}
			?>
			<br />
			<br />
			<b>
				<?php

				$checamagiastatus = $db->execute("select * from `magias` where `magia_id`=5 and `player_id`=?", array($player->id));
				if ($checamagiastatus->recordcount() > 0) {
					$bonusmagico = 5;
					$bonusmagico2 = " +5%";
				} else {
					$bonusmagico = 0;
					$bonusmagico2 = '';
				}

				if ($player->voc == 'archer') {
					echo "Pontaria";
				} else if ($player->voc == 'knight') {
					echo "For�a";
				} else if ($player->voc == 'mage') {
					echo "Ataque";
				}
				?>:
			</b>
			<?= $player->strength ?>
			<?php
			include("itemstatus.php");
			echo " <font color=\"gray\">+" . $forcaadebonus . "</font>";
			if ($player->promoted == 'r') {
				$bonusvalor1 = (9 + $bonusmagico);
				echo " +" . $bonusvalor1 . "%";
			} else if (($player->promoted == 's') or ($player->promoted == 'p')) {
				$bonusvalor2 = (15 + $bonusmagico);
				echo " +" . $bonusvalor2 . "%";
			} else {
				echo $bonusmagico2;
			}
			?><br />
			<b>Vitalidade:</b>
			<?= $player->vitality ?>
			<?php
			echo " <font color=\"green\">+" . $vitalidadeeeeebonus . "</font>";
			?><br />
			<b>Agilidade:</b>
			<?= $player->agility ?>
			<?php
			echo " <font color=\"blue\">+" . $agilidadeeedebonus . "</font>";
			if ($player->promoted == 'r') {
				$bonusvalor1 = (9 + $bonusmagico);
				echo " +" . $bonusvalor1 . "%";
			} else if (($player->promoted == 's') or ($player->promoted == 'p')) {
				$bonusvalor2 = (15 + $bonusmagico);
				echo " +" . $bonusvalor2 . "%";
			} else {
				echo $bonusmagico2;
			}
			?><br />
			<b>Resist�ncia:</b>
			<?= $player->resistance ?>
			<?php
			echo " <font color=\"red\">+" . $resistenciaaaadebonus . "</font>";
			if ($player->promoted == 'r') {
				$bonusvalor1 = (9 + $bonusmagico);
				echo " +" . $bonusvalor1 . "%";
			} else if (($player->promoted == 's') or ($player->promoted == 'p')) {
				$bonusvalor2 = (15 + $bonusmagico);
				echo " +" . $bonusvalor2 . "%";
			} else {
				echo $bonusmagico2;
			}
			?><br />
			<center><b><a href="reset_stats.php">Redistribuir pontos.</a></b></center>
		</td>
	</tr>
</table>
<br /><br />
<font size="1"><b>Usu�rios online no servidor: </b>
	<?php
	$calc = (time() - 200);
	$query = $db->execute("select `player_id` from `online` where `serv`=?", array($player->serv));
	$totalon = $db->execute("select `player_id` from `online`");

	while ($online = $query->fetchrow()) {
		$getname = $db->execute("select `username` from `players` where `id`=? order by `username` asc", array($online['player_id']));
		$member = $getname->fetchrow();

		echo "<a href=\"profile.php?id=" . $member['username'] . "\">";
		echo ($member['username'] == $player->username) ? "<b>" : "";
		echo $member['username'];
		echo ($member['username'] == $player->username) ? "</b>" : "";
		echo "</a> | ";
	}
	echo "<b>Total:</b> " . $query->recordcount() . "<br/>";
	echo "<b>Em todos os servidores:</b> " . $totalon->recordcount() . " <b>Recorde:</b> " . $setting->user_record . "";
	$recorde = $totalon->recordcount();
	if ($recorde > $setting->user_record) {
		$query = $db->execute("update `settings` set `value`=? where `name`='user_record'", array($recorde));
	}

	?>
</font>
<br /><br /><br />
<b>Link para convidar amigos:</b> <a
	href="http://www.oconfronto.co.nr/?r=<?= $player->id ?>">http://www.oconfronto.co.nr/?r=
	<?= $player->id ?>
</a><br />
<b>Amigos convidados:</b>
<?= $player->ref ?> (voc� ganha 2500 de ouro a cada amigo convidado).<br />
<font size="1">Seu amigo deve atingir o n�vel
	<?= $setting->activate_level ?> para voc� receber sua recompensa.
</font>


<br /><br />

<?php
include("templates/private_footer.php");
?>