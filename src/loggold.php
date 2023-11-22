<?php
include(__DIR__ . "/lib.php");
$player = check_user($secret_key, $db);
?>
<html>
<head>
<title>O Confronto :: Logs de Ouro</title>
<?php
        $checknocur = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\CURSOR, $player->id]);
if ($checknocur->recordcount() > 0) {
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/style2.css\" />";
} else {
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/style.css\" />";
}
?>
<script type="text/javascript" src="templates/boxover.js"></script>
</head>

<body>


<?php
$read0 = $db->execute("update `user_log` set `status`='read' where `player_id`=? and `status`='unread'", [$player->id]);

echo "<table width=\"100%\">";
echo "<tr><td align=\"center\" bgcolor=\"#E1CBA4\"><b>Logs de Ouro</b></td></tr>";
$query0 = $db->execute("select * from `log_gold` where `player_id`=? order by `time` desc", [$player->id]);
if ($query0->recordcount() > 0) {
    while ($trans = $query0->fetchrow()) {
        $read1 = $db->execute("update `log_gold` set `status`='read' where `player_id`=? and `status`='unread' and `id`=?", [$player->id, $trans['id']]);

        echo "<tr>";

        $auxiliar = $trans['action'] == \ENVIOU ? "para" : "de";

        $valortempo = time() -  $trans['time'];
        if ($valortempo < 60) {
            $valortempo2 = $valortempo;
            $auxiliar2 = "segundo(s) atrás.";
        } elseif ($valortempo < 3600) {
            $valortempo2 = floor($valortempo / 60);
            $auxiliar2 = "minuto(s) atrás.";
        } elseif ($valortempo < 86400) {
            $valortempo2 = floor($valortempo / 3600);
            $auxiliar2 = "hora(s) atrás.";
        } elseif ($valortempo > 86400) {
            $valortempo2 = floor($valortempo / 86400);
            $auxiliar2 = "dia(s) atrás.";
        }

        echo "<td class=\"off\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\"><div title=\"header=[" . $valortempo2 . " " . $auxiliar2 . "] body=[]\">";
        if ($trans['action'] == \DOOU) {
            echo "<font size=\"1\">Você enviou <b>" . $trans['value'] . "</b> de ouro para o clã <b><a href=\"guild_profile.php?id=" . $trans['name2'] . "\">" . $trans['name2'] . "</a></b></font></div></td>";
        } elseif ($trans['action'] == \GANHOU) {
            echo "<font size=\"1\">Você recebeu <b>" . $trans['value'] . "</b> de ouro para o clã <b><a href=\"guild_profile.php?id=" . $trans['name2'] . "\">" . $trans['name2'] . "</a></b></font></div></td>";
        } else {
            echo "<font size=\"1\">Você " . $trans['action'] . " <b>" . $trans['value'] . "</b> de ouro " . $auxiliar . " <b><a href=\"profile.php?id=" . $trans['name2'] . "\">" . $trans['name2'] . "</a></b></font></div></td>";
        }

        echo "</tr>";
    }
} else {
    echo "<tr>";
    echo "<td class=\"off\"><font size=\"1\">Nenhum registro encontrado!</font></td>";
    echo "</tr>";
}
echo "</table>";
echo "<center><font size=\"1\">Exibindo todos os logs dos últimos 14 dias.</font></center>";
echo "</body>";
echo "</html>";
?>