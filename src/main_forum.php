<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Fórum");
$player = check_user($secret_key, $db);

include(__DIR__ . "/checkforum.php");
include(__DIR__ . "/templates/private_header.php");

include(__DIR__ . '/ps_pagination.php');

if (!$_GET['cat']) {
    echo "Nenhuma categoria foi selecionada! <a href=\"select_forum.php\">Voltar</a>.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}

$cate = $_GET['cat'];

if ($cate == 'gangues') {
    $categoria = "Clãs";
} elseif ($cate == 'trade') {
    $categoria = "Compro/Vendo";
} elseif ($cate == 'noticias') {
    $categoria = "Notícias";
} elseif ($cate == 'sugestoes') {
    $categoria = "Sugestões";
} elseif ($cate == 'duvidas') {
    $categoria = "Dúvidas";
} elseif ($cate == 'fan') {
    $categoria = "Fanwork";
} elseif ($cate == 'off') {
    $categoria = "Off-Topic";
} else {
    $categoria = $cate;
}

echo "<b><font size=\"1\"><a href=\"select_forum.php\">Fóruns</a> -> <a href=\"main_forum.php?cat=" . $cate . "\">" . ucfirst((string) $categoria) . "</a></font></b>";
?>

<table width="95%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">
<tr>
<td width="55%" align="center" bgcolor="#E1CBA4"><strong>Tópico</strong></td>
<td width="15%" align="center" bgcolor="#E1CBA4"><strong>Visitas</strong></td>
<td width="15%" align="center" bgcolor="#E1CBA4"><strong>Respostas</strong></td>
<td width="15%" align="center" bgcolor="#E1CBA4"><strong><font size=1>Ùltima Postagem</font></strong></td>
</tr>

<?php
    if ($cate == 'gangues' || $cate == 'trade') {
        $total_players = $db->getone("select count(ID) as `count` from `forum_question` WHERE category='$cate' and serv='$player->serv'");
        $sql = "SELECT * FROM forum_question WHERE category='$cate' and serv='$player->serv' ORDER BY fixo ASC, last_post DESC";
    } else {
        $total_players = $db->getone("select count(ID) as `count` from `forum_question` WHERE category='$cate'");
    }

if ($total_players == 0) {
    echo "<tr><td align=\"center\" bgcolor=\"#FFFFFF\"><b>Nenhum tópico encontrado.</b></td><td align=\"center\" bgcolor=\"#FFFFFF\">#</td><td align=\"center\" bgcolor=\"#FFFFFF\">#</td><td align=\"center\" bgcolor=\"#FFFFFF\">#</td></tr>";
} else {

    if ($cate == 'gangues' || $cate == 'trade') {
        $sql = "SELECT * FROM forum_question WHERE category='$cate' and serv='$player->serv' ORDER BY fixo ASC, last_post DESC";
    } else {
        $sql = "SELECT * FROM forum_question WHERE category='$cate' ORDER BY fixo ASC, last_post DESC";
    }

    $pager = new PS_Pagination($sql, 20, 20, "cat=" . $cate . "");


    $rs = $pager->paginate();
    //Loop through the result set
    while($rows = mysql_fetch_assoc($rs)) {

        $query = $db->execute("select `username` from `players` where `id`=?", [$rows['user_id']]);
        $user = $query->fetchrow();

        echo "<tr><td bgcolor=\"#FFFFFF\">";
        if ($rows['fixo'] == 't') {
            echo "<b>Fixo:</b> ";
        } elseif ($rows['closed'] == 't') {
            echo "<b>Fechado:</b> ";
        }
        echo "<b><a href=\"view_topic.php?id=" . $rows['id'] . "\">" . $rows['topic'] . "</a></b><br />";

        if ($rows['reply'] > 0) {
            $lastpostid = $db->GetOne("select `a_user_id` from `forum_answer` where `question_id`=? order by `a_datetime` DESC", [$rows['id']]);
            $lastpostname = $db->GetOne("select `username` from `players` where `id`=?", [$lastpostid]);
            echo "<font size=\"1\">Último post por <a href=\"profile.php?id=" . $lastpostname . "\">" . $lastpostname . "</a></font></td>";
        } else {
            echo "<font size=\"1\">Iniciado por <a href=\"profile.php?id=" . $user['username'] . "\">" . $user['username'] . "</a></font></td>";
        }

        echo "<td align=\"center\" bgcolor=\"#FFFFFF\">" . $rows['view'] . "</td>";
        echo "<td align=\"center\" bgcolor=\"#FFFFFF\">" . $rows['reply'] . "</td>";
        echo "<td align=\"center\" bgcolor=\"#FFFFFF\">" . $rows['last_post_date'] . "</td>";
        echo "</tr>";
    }

    //Display the navigation
    $paginaciones = "<br/><center><font size=1>" . $pager->renderFullNav() . "</font></center>";
}

?>
<tr>
<td colspan="5" align="right" bgcolor="#E1CBA4"><a href="create_topic.php"><strong>Criar novo Tópico</strong> </a></td>
</tr>
</table>
<?php
echo $paginaciones;
include(__DIR__ . "/templates/private_footer.php");
?>