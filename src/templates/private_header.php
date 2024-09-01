<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="pragma" content="no-cache">
	<title>O Confronto :: <?php echo PAGENAME ?></title>

	<?php
	$checknocur = $db->execute("select * from `other` where `value`=? and `player_id`=?", array(cursor, $player->acc_id));
	if ($checknocur->recordcount() > 0) {
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/private_style_2.css\" />";
	} else {
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/private_style_1.css\" />";
	}
	?>
	<link rel="stylesheet" type="text/css" href="css/boxover.css" />
	<link rel="stylesheet" type="text/css" href="css/inventory.css" />
	<link rel="stylesheet" type="text/css" href="css/cssverticalmenu.css" />
	<link rel="stylesheet" type="text/css" href="css/private/simpletabs.css" />
	<link rel="stylesheet" type="text/css" href="css/private/menu-inventario.css" />
	<link rel="stylesheet" type="text/css" href="css/private/magias.css" />


	<script src="js/jquery-1.3.2.min.js"></script>
	<script src="js/jquery.hotkeys.js"></script>
	<?php
	include("js/keys.php");
	?>

	<script type="text/javascript" src="js/drag.js"></script>
	<!-- initialize drag and drop -->
	<script type="text/javascript">
		// onload event
		window.onload = function () {
			rd = REDIPS.drag;	// reference to the REDIPS.drag class
			// initialization
			rd.init();

			rd.mark.exception.amulet = 'amulet';
			rd.mark.exception.helmet = 'helmet';
			rd.mark.exception.weapon = 'weapon';
			rd.mark.exception.armor = 'armor';
			rd.mark.exception.shield = 'shield';
			rd.mark.exception.legs = 'legs';
			rd.mark.exception.boots = 'boots';

			// this function (event handler) is called after element is dropped
			REDIPS.drag.myhandler_dropped = function () {

				var obj_old = REDIPS.drag.obj_old;					// reference to the original object
				var target_cell = REDIPS.drag.target_cell;				// reference to the Target cell			

				// if the DIV element was placed on allowed cell then
				if (rd.target_cell.className.indexOf(rd.mark.exception[rd.obj.id]) !== -1) {
					if (REDIPS.drag.target_cell !== REDIPS.drag.source_cell) {

						var itclassname = rd.obj_old.className;
						var itid = itclassname.split(' ')[1];
						window.location.href = 'equipit.php?itid=' + itid;

					}
				}

				else if (REDIPS.drag.target_cell !== REDIPS.drag.source_cell) {

					var itclassname = rd.obj_old.className;
					var itid = itclassname.split(' ')[1];

					if (rd.target_cell.className == 'sell') {
						window.location.href = 'shop.php?act=sell&id=' + itid;
					} else if (rd.target_cell.className == 'mature') {
						window.location.href = 'shop.php?act=mature&id=' + itid;
					} else {
						var tileclassname = rd.target_cell.className;
						var tileid = tileclassname.split(' ')[1];

						window.location.href = 'moveit.php?itid=' + itid + '&tile=' + tileid;
					}
				}

			}
		}

	</script>

	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/temporeal.js"></script>
	<script type="text/javascript" src="js/boxover.js"></script>
	<script type="text/javascript" src="js/cssverticalmenu.js"></script>
	<script language="JavaScript">
		function divDown() {
			document.getElementById('logdebatalha').scrollTop += 1000000;
		}
	</script>
	<script type="text/javascript" src="js/pagamentos.js"></script>
	<script type="text/javascript" src="js/menus.js"></script>
	<script type="text/javascript" src="bbeditor/ed.js"></script>

</head>
<?php
if (PAGENAME == 'Batalhar') {
	echo "<body onload=\"divDown()\">";
} else {
	echo "<body>";
}

$mailcount = $db->execute("select `id` from `mail` where `to`=? and `status`='unread'", array($player->id));
$logcount0 = $db->execute("select `id` from `user_log` where `player_id`=? and `status`='unread'", array($player->id));
$logcount1 = $db->execute("select `id` from `logbat` where `player_id`=? and `status`='unread'", array($player->id));
$logcount2 = $db->execute("select `id` from `log_gold` where `player_id`=? and `status`='unread'", array($player->id));
$logcount3 = $db->execute("select `id` from `log_item` where `player_id`=? and `status`='unread'", array($player->id));
$logcount4 = $db->execute("select `id` from `account_log` where `player_id`=? and `status`='unread'", array($player->acc_id));

$logscount = $logcount0->recordcount() + $logcount1->recordcount() + $logcount2->recordcount() + $logcount3->recordcount() + $logcount4->recordcount();

?>
<div id="tudo">

	<div id="topo">
		<center><br /><img src="images/topo.jpg" alt="O Confronto - MMORPG" border="0"></center>
	</div>

	<div id="barra">
		<div id="textoAviso">
			<?php
			if ($player->stat_points > 0) {
				echo "<b><font color=\"red\">Aten��o:</font></b> <i>Voc� tem <b>" . $player->stat_points . "</b> pontos de status dispon�veis! <a href=\"stat_points.php\">Clique aqui para utiliza-los</a>!</i>";
			} else {
				$questa = 0;

				$lembrete = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`<90", array($player->id, 1));
				if ($lembrete->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"promote.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete2 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`<90", array($player->id, 2));
				if ($lembrete2->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest1.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete3 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 3));
				if ($lembrete3->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest2.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete4 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 4));
				if ($lembrete4->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest2.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete5 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 5));
				if ($lembrete5->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest3.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete6 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 6));
				if ($lembrete6->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest3.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete7 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 7));
				if ($lembrete7->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest4.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete8 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 9));
				if ($lembrete8->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest5.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete9 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 12));
				if ($lembrete9->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"promo1.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete102 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 13));
				if ($lembrete102->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest6.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete10 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90 and `quest_status`!=89", array($player->id, 14));
				if ($lembrete10->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest6.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete11 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 15));
				if ($lembrete11->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest7.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete12 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 17));
				if ($lembrete12->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest8.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				$lembrete13 = $db->execute("select * from `quests` where `player_id`=? and `quest_id`=? and `quest_status`!=90", array($player->id, 18));
				if ($lembrete13->recordcount() > 0 and $questa == 0) {
					echo "<i><a href=\"quest9.php\">Clique aqui</a> para continuar sua miss�o!</i>";
					$questa = 1;
				}
				if ($questa == 0) {


					if ($player->level < 100) {
						$tier = 1;
					} elseif (($player->level > 99) and ($player->level < 200)) {
						$tier = 2;
					} elseif (($player->level > 199) and ($player->level < 300)) {
						$tier = 3;
					} elseif (($player->level > 299) and ($player->level < 400)) {
						$tier = 4;
					} elseif (($player->level > 399) and ($player->level < 1000)) {
						$tier = 5;
					}

					$torneiovarificapelotier = "tournament_" . $tier . "_" . $player->serv . "";
					$lottoavisoheader = "lottery_" . $player->serv . "";

					if ($setting->$torneiovarificapelotier == y) {
						echo "<i>O <a href=\"tournament.php\">Torneio</a> come�ou!</i>";
					} else {


						$messaged = 0;

						$sorteia = rand(1, 6);

						if ($setting->$torneiovarificapelotier == t) {
							if ($sorteia == 1) {
								echo "<i>Inscreva-se torneio! <a href=\"tournament.php\">Clique aqui</a>.</i>";
								$messaged = 1;
							}
						}

						if ($setting->promo == t) {
							if ($sorteia == 2) {
								echo "<i>Ganhe 2 milh�es em ouro! <a href=\"promo.php\">Clique aqui</a>.</i>";
								$messaged = 1;
							}
						}

						if ($setting->$lottoavisoheader == t) {
							if ($sorteia == 3) {
								echo "<i>Aposte na loteria! <a href=\"lottery.php\">Clique aqui</a>.</i>";
								$messaged = 1;
							}
						}

						if ($setting->eventoouro > time()) {
							if ($sorteia == 4) {
								echo "<i><b>Evento surpresa!</b> Monstros com ouro em dobro!.</i>";
								$messaged = 1;
							}
						}

						if ($setting->eventoexp > time()) {
							if ($sorteia == 5) {
								echo "<i><b>Evento surpresa!</b> Monstros com experi�ncia em dobro!.</i>";
								$messaged = 1;
							}
						}

						if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {
							if ($sorteia == 6) {
								echo "Seu navegador pode n�o suportar o jogo, se encontrar algum problema, <a href=\"view_topic.php?id=2601\">clique aqui</a>.";
								$messaged = 1;
							}
						}


						if ($messaged == 0) {
							$mensagemespecial = rand(1, 3);
							if ($mensagemespecial == 1) {
								echo "<i>Participe da nossa comunidade no <a href=\"http://www.orkut.com.br/Main#Community.aspx?cmm=73799681\" target=\"_blank\">ORKUT</a>!</i>";
							} elseif ($mensagemespecial == 2) {
								echo "<i><a href=\"view_topic.php?id=5229\">Clique aqui</a> e leia sobre o novo servidor e outras atualiza��es.</i>";
							} else {
								echo "<i>Hotkeys adicionadas no jogo. <a href=\"view_topic.php?id=3295\">Leia mais</a>.</i>";
							}
						}
					}
				}

			}
			?>
		</div>
	</div>

	<div id="conteudo">
		<div id="menus">

			<br />
			<div id="div_avatar" class="avatar">
				<a href="avatar.php"><img src="<?php echo $player->avatar ?>" width="138px" height="138px"
						alt="Editar perfil" border="0"></a>
			</div>
			<font size="1"><b>Usu�rio:</b> <?php echo $player->username ?></font><br />
			<!-- <img src="bargen.php?hp"><br />
			<img src="bargen.php?mana"><br />
			<img src="bargen.php?energy"><br /> -->

			<div class="bg-barras">
				<div class="ic-hp">
					<div class="bg-bar">
						<div id="bar-hp" class="bg-hp"
							style="width:<?php echo ceil(($player->hp * 100) / $player->maxhp); ?>%">
							<span><?php echo $player->hp; ?> / <?php echo $player->maxhp; ?></span>
						</div>
					</div>
				</div>
				<div class="ic-mp">
					<div class="bg-bar">
						<div id="bar-mp" class="bg-mp"
							style="width:<?php echo ceil(($player->mana * 100) / $player->maxmana); ?>%">
							<span><?php echo $player->mana; ?> / <?php echo $player->maxmana; ?></span>
						</div>
					</div>
				</div>
				<div class="ic-en">
					<div class="bg-bar">
						<div id="bar-en" class="bg-en"
							style="width:<?php echo ceil(($player->energy * 100) / $player->maxenergy); ?>%">
							<span><?php echo $player->energy; ?> / <?php echo $player->maxenergy; ?></span>
						</div>
					</div>
				</div>
			</div>

			<br />
			<?php
			include("showit.php");
			?>
			<br />
			<b>N�vel:</b> <?php echo $player->level ?><br />
			<?php
			$percent = intval(100 - (($player->exp / $player->maxexp) * 100));
			?>
			<!-- <div title="header=[Experi�ncia] body=[<b><?php echo $percent ?>%</b> restantes]\">
				<img src="bargen.php?exp">
			</div> -->
			<?php
			$style = 'display: none;';
			$progress = ($player->exp * 100) / ($player->maxexp);
			if (is_numeric($progress) && $progress > 0 && $progress <= 100) {
				$style = "width: " . round($progress) . "%; display: block;";
			}
			?>

			<div id="ex-bg">
				<div id="expbar" style="<?= $style ?>"></div>
				<span id="expbarText"><?php echo ceil($player->exp); ?> / <?php echo ceil($player->maxexp); ?>
					(<?php echo ceil(($player->exp * 100) / $player->maxexp); ?>%)</span>
			</div>

			<br />
			<b>Ouro: <font color="#DFA40F"><?php echo $player->gold ?></font></b><br />
			<br /><br />

			<div id="masterdiv">
				<ul id="verticalmenu" class="glossymenu">
					<div class="menutitle" onclick="SwitchMenu('sub1')">
						<li><a><?php echo $player->username ?></a></li>
					</div>
					<span class="submenu" id="sub1">
						<li><a href="home.php">Principal</a></li>
						<li><a href="log.php">Log [<font color="red"><?php echo $logscount; ?></font>]</a></li>
						<li><a href="inventory.php">Invent�rio</a></li>
						<li><a href="bat.php">Batalhar</a></li>
						<li><a href="work.php">Trabalhar</a></li>
						<li><a href="earn.php">
								<font color="gold">Imagens</font>
							</a></li>
					</span>
				</ul>

				<br />
				<ul id="verticalmenu" class="glossymenu">
					<div class="menutitle" onclick="SwitchMenu('sub2')">
						<li><a>Cidade</a></li>
					</div>
					<span class="submenu" id="sub2">
						<li><a href="bank.php">Banco</a></li>
						<li><a href="shop.php">Ferreiro</a></li>
						<li><a href="market.php">Mercado</a></li>
						<li><a href="hospital.php">Hospital</a></li>
						<li><a href="lottery.php">Loteria</a></li>
						<li><a href="tournament.php">Torneio</a></li>
					</span>
				</ul>

				<br />
				<ul id="verticalmenu" class="glossymenu">
					<div class="menutitle" onclick="SwitchMenu('sub3')">
						<li><a>Comunidade</a></li>
					</div>
					<span class="submenu" id="sub3">
						<li><a href="#"
								onclick="javascript:window.open('chat.php', '_blank','top=100, left=100, height=530, width=700, status=no, menubar=no, resizable=no, scrollbars=yes, toolbar=no, location=no, directories=no');">Chat</a>
						</li>
						<li><a href="select_forum.php">F�rum</a></li>
						<li><a href="guild_listing.php">Cl�s</a></li>
						<li><a href="members.php">Ranking</a></li>
						<li><a href="mail.php">Mensagens [<font color="red"><?php echo $mailcount->recordcount(); ?>
								</font>]</a></li>
						<li><a href="friendlist.php">Amigos</a></li>
					</span>
				</ul>
				<br />
				<ul id="verticalmenu" class="glossymenu">
					<div class="menutitle" onclick="SwitchMenu('sub4')">
						<li><a>Sua conta</a></li>
					</div>
					<span class="submenu" id="sub4">
						<li><a href="editinfo.php">Configura��es</a></li>
						<li><a href="logoutchar.php">Personagens</a></li>
						<li><a href="logout.php">Sair</a></li>
					</span>
				</ul>
				<?php
				if ($setting->promo == t) {
					echo "<br/>";
					echo "<ul id=\"verticalmenu\" class=\"glossymenu\">";
					echo "<div class=\"menutitle\">";
					echo "<li><a href=\"promo.php\"><blink>Promo��o!</blink></a></li>";
					echo "</div>";
					echo "</ul>";
				}
				?>


			</div>


		</div>
		<div id="borda">
			<div id="principal">
				<div id="usr">
					<font size="1">Carregando...</font>
				</div>