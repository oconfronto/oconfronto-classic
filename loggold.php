<?php
	include("lib.php");
	$player = check_user($secret_key, $db);
?>
<html>
<head>
<title>O Confronto :: Logs de Ouro</title>
<?php
		$checknocur = $db->execute("select * from `other` where `value`=? and `player_id`=?", array(cursor, $player->id));
		if ($checknocur->recordcount() > 0) {
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/style2.css\" />";
		}else{
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/style.css\" />";
		}
?>
<script type="text/javascript" src="templates/boxover.js"></script>
</head>

<body>


<?php
$read0 = $db->execute("update `user_log` set `status`='read' where `player_id`=? and `status`='unread'", array($player->id));

echo "<table width=\"100%\">";
echo "<tr><td align=\"center\" bgcolor=\"#E1CBA4\"><b>Logs de Ouro</b></td></tr>";
$query0 = $db->execute("select * from `log_gold` where `player_id`=? order by `time` desc", array($player->id));
if ($query0->recordcount() > 0)
{
	while ($trans = $query0->fetchrow())
	{
		$read1 = $db->execute("update `log_gold` set `status`='read' where `player_id`=? and `status`='unread' and `id`=?", array($player->id, $trans['id']));

		echo "<tr>";

		if ($trans['action'] == enviou){
		$auxiliar = "para";
		}else{
		$auxiliar = "de";
		}

		$valortempo = time() -  $trans['time'];
		if ($valortempo < 60){
		$valortempo2 = $valortempo;
		$auxiliar2 = "segundo(s) atr�s.";
		}else if($valortempo < 3600){
		$valortempo2 = floor($valortempo / 60);
		$auxiliar2 = "minuto(s) atr�s.";
		}else if($valortempo < 86400){
		$valortempo2 = floor($valortempo / 3600);
		$auxiliar2 = "hora(s) atr�s.";
		}else if($valortempo > 86400){
		$valortempo2 = floor($valortempo / 86400);
		$auxiliar2 = "dia(s) atr�s.";
		}

		echo "<td class=\"off\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\"><div title=\"header=[" . $valortempo2 . " " . $auxiliar2 . "] body=[]\">";
		if ($trans['action'] == doou){
		echo "<font size=\"1\">Voc� enviou <b>" . $trans['value'] . "</b> de ouro para o cl� <b><a href=\"guild_profile.php?id=" . $trans['name2'] . "\">" . $trans['name2'] . "</a></b></font></div></td>";
		}elseif ($trans['action'] == ganhou){
		echo "<font size=\"1\">Voc� recebeu <b>" . $trans['value'] . "</b> de ouro para o cl� <b><a href=\"guild_profile.php?id=" . $trans['name2'] . "\">" . $trans['name2'] . "</a></b></font></div></td>";
		}else{
		echo "<font size=\"1\">Voc� " . $trans['action'] . " <b>" . $trans['value'] . "</b> de ouro " . $auxiliar . " <b><a href=\"profile.php?id=" . $trans['name2'] . "\">" . $trans['name2'] . "</a></b></font></div></td>";
		}

		echo "</tr>";
	}
}
else
{
	echo "<tr>";
	echo "<td class=\"off\"><font size=\"1\">Nenhum registro encontrado!</font></td>";
	echo "</tr>";
}
echo "</table>";
echo "<center><font size=\"1\">Exibindo todos os logs dos �ltimos 14 dias.</font></center>";
echo "</body>";
echo "</html>";
?>