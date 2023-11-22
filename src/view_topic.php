<?php
include(__DIR__ . "/lib.php");
include(__DIR__ . '/bbcode.php');
$bbcode = new bbcode();
define("PAGENAME", "Fórum");
$player = check_user($secret_key, $db);

include(__DIR__ . "/checkforum.php");
include(__DIR__ . '/ps_pagination.php');

if (!$_GET['id']) {
    header("Location: select_forum.php");
    exit;
}
include(__DIR__ . "/templates/private_header.php");
$foruminfo = $db->execute("select * from `forum_question` where `id`=?", [$_GET['id']]);
if ($foruminfo->recordcount() != 1) {
    echo "Este tópico não existe. <a href=\"select_forum.php\">Voltar</a>.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}
$rows = $foruminfo->fetchrow();
$id = $_GET['id'];

if (($rows['category'] == 'gangues' || $rows['category'] == 'trade') && $player->serv != $rows['serv']) {
    echo "<fieldset><legend><b>Erro</b></legend>Você não pode visualizar este tópico.<BR>";
    echo "<a href='select_forum.php'>Voltar</a></fieldset>";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}
if ($_GET['up']) {
    $jaupou = $db->execute("select * from `thumb` where `topic_id`=? and `player_id`=?", [$_GET['id'], $player->id]);
    if ($jaupou->recordcount() > 0) {
        echo "Você já votou neste tópico! <a href=\"view_topic.php?id=" . $_GET['id'] . "\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    $insert['player_id'] = $player->id;
    $insert['topic_id'] = $_GET['id'];
    $upar0 = $db->autoexecute('thumb', $insert, 'INSERT');
    $upar1 = $db->execute("update `forum_question` set `up`=`up`+1 where `id`=?", [$_GET['id']]);
    echo "Obrigado por votar! <a href=\"view_topic.php?id=" . $_GET['id'] . "\">Voltar</a>.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}


if ($_GET['down']) {
    $jadown = $db->execute("select * from `thumb` where `topic_id`=? and `player_id`=?", [$_GET['id'], $player->id]);
    if ($jadown->recordcount() > 0) {
        echo "Você já votou neste tópico! <a href=\"view_topic.php?id=" . $_GET['id'] . "\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    $insert['player_id'] = $player->id;
    $insert['topic_id'] = $_GET['id'];
    $down0 = $db->autoexecute('thumb', $insert, 'INSERT');
    $down1 = $db->execute("update `forum_question` set `down`=`down`+1 where `id`=?", [$_GET['id']]);
    echo "Obrigado por votar! <a href=\"view_topic.php?id=" . $_GET['id'] . "\">Voltar</a>.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}


// get value of id that sent from address bar

if ($rows['category'] == 'gangues') {
    $categoria = "Clãs";
} elseif ($rows['category'] == 'trade') {
    $categoria = "Compro/Vendo";
} elseif ($rows['category'] == 'noticias') {
    $categoria = "Notícias";
} elseif ($rows['category'] == 'sugestoes') {
    $categoria = "Sugestões";
} elseif ($rows['category'] == 'duvidas') {
    $categoria = "Dúvidas";
} elseif ($rows['category'] == 'fan') {
    $categoria = "Fanwork";
} elseif ($rows['category'] == 'off') {
    $categoria = "Off-Topic";
} else {
    $categoria = $rows['category'];
}

echo "<b><font size=\"1\"><a href=\"select_forum.php\">Fóruns</a> -> <a href=\"main_forum.php?cat=" . $rows['category'] . "\">" . ucfirst((string) $categoria) . "</a> -> <a href=\"view_topic.php?id=" . $rows['id'] . "\">" . ucfirst((string) $rows['topic']) . "</a></font></b>";

$query = $db->execute("select `id`, `username`, `avatar`, `posts`, `ban`, `alerts`, `gm_rank`, `serv` from `players` where `id`=?", [$rows['user_id']]);
$topicouser = $query->fetchrow();
?>


<table width="560px" bgcolor="#f2e1ce">
  <tr>
    <td width="110px" bgcolor="#E1CBA4"><center><img src="<?php echo $topicouser['avatar']; ?>" width="100px" height="100px" border="0"></center><br/><center><font size="1"><b><a href="profile.php?id=<?php echo $topicouser['username']; ?>"><?php if($topicouser['gm_rank'] > 2) {
        echo "<font color=\"green\">" . $topicouser['username'] . "</font>";
    } elseif ($player->serv != $topicouser['serv']) {
        echo "<font color=\"red\">" . $topicouser['username'] . "</font>";
    } else {
        echo $topicouser['username'];
    } ?></a></b><br/><b>Posts:</b> <?php echo $topicouser['posts']; ?>
<br/><?php
if ($topicouser['alerts'] != 0 && $topicouser['alerts'] < 100 && $topicouser['ban'] < time()) {
    echo "<b>Alerta:</b> " . $topicouser['alerts'] . "%</br>";
} elseif ($topicouser['ban'] > time()) {
    echo "Banido</br>";
} elseif ($topicouser['alerts'] == 'forever' || $topicouser['alerts'] > 99) {
    echo "Banido do Fórum</br>";
}
if ($player->gm_rank > 2) {
    if ($player->gm_rank > 10) {
        echo "<br/><a href=\"forum_ban.php?player=" . $topicouser['id'] . "\">Banir</a> | <a href=\"forum_alert.php?player=" . $topicouser['id'] . "\">Alertar</a><br/><a href=\"delete_all.php?player=" . $topicouser['id'] . "\">Apagar todos Posts</a><br/>";
    } else {
        echo "<br/><a href=\"forum_ban.php?player=" . $topicouser['id'] . "\">Banir</a> | <a href=\"forum_alert.php?player=" . $topicouser['id'] . "\">Alertar</a><br/>";
    }
}

?>
</font></center></th>
    <td width="435px"><table width="100%">
      <tr>
        <td width="100%"><table width="100%">
          <tr>
            <td width="70%" bgcolor="#E1CBA4"><b><?php echo $rows['topic']; ?></b></td>
            <td width="30%" bgcolor="#E1CBA4"><font size="1"><center><b><?php echo $rows['datetime']; ?></b></center><font></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td width="100%" bgcolor="#f2e1ce">
<div class=\"scroll\" style="width : 435px; overflow : auto; ">
<?php
if ($player->username == $topicouser['username'] && $player->gm_rank < 3) {
    echo "&nbsp;&nbsp;&nbsp;<a href=\"edit_topic.php?topic=" . $rows['id'] . "\">Editar</a> | <a href=\"delete_topic.php?topic=" . $rows['id'] . "\">Deletar</a><br/>";
} elseif ($player->gm_rank > 2) {
    echo "&nbsp;&nbsp;&nbsp;<a href=\"edit_topic.php?topic=" . $rows['id'] . "\">Editar</a> | <a href=\"move_topic.php?topic=" . $rows['id'] . "\">Mover</a> | <a href=\"delete_topic.php?topic=" . $rows['id'] . "\">Deletar</a><br/>";
}

$topiko = stripslashes((string) $rows['detail']);
echo $bbcode->parse($topiko);
?></div>
        </td>
      </tr>
    </table></th>
  </tr>
</table>



<?php
if ($rows['vota'] == \T) {
    $total = $rows['up'] + $rows['down'];
    if ($total > 0) {
        $porcentoup = (int) ($rows['up'] / $total * 100);
        $porcentodown = (int) ($rows['down'] / $total * 100);
    } else {
        $porcentoup = 0;
        $porcentodown = 0;
    }

    echo "<b><font size=\"1\">De sua nota: <a href=\"view_topic.php?id=" . $_GET['id'] . "&up=true\"><img src=\"images/thumb_up.png\" border=\"0\"></a>" . $porcentoup . "%&nbsp;&nbsp;&nbsp;<a href=\"view_topic.php?id=" . $_GET['id'] . "&down=true\"><img src=\"images/thumb_down.png\" border=\"0\"></a>" . $porcentodown . "%</b> (" . $total . " Votos)</font>";
}
?>
<BR><BR>
<?php
$conta = $db->execute("select `a_id` from `forum_answer` where `question_id`=?", [$_GET['id']]);
if ($conta->recordcount() > 0) {

    $sql2 = "SELECT * FROM forum_answer WHERE question_id=$id order by a_id asc";
    $pager = new PS_Pagination($sql2, 5, 15, "id=" . $_GET['id'] . "");


    $rs = $pager->paginate();
    //Loop through the result set
    while($rows = mysql_fetch_assoc($rs)) {

        $query = $db->execute("select `id`, `username`, `avatar`, `posts`, `ban`, `alerts`, `gm_rank`, `serv` from `players` where `id`=?", [$rows['a_user_id']]);
        $user = $query->fetchrow();

        ?>
<table width="560" bgcolor="#f2e1ce">
  <tr>
    <td width="110" bgcolor="#E1CBA4"><center><img src="<?php echo $user['avatar']; ?>" width="100px" height="100px" border="0"></center><br/><center><font size="1"><b><a href="profile.php?id=<?php echo $user['username']; ?>"><?php if($user['gm_rank'] > 2) {
        echo "<font color=\"green\">" . $user['username'] . "</font>";
    } elseif ($player->serv != $user['serv']) {
        echo "<font color=\"red\">" . $user['username'] . "</font>";
    } else {
        echo $user['username'];
    } ?></a></b><br/><b>Posts:</b> <?php echo $user['posts']; ?>
<br/><?php
if ($user['alerts'] != 0 && $user['alerts'] < 100 && $user['ban'] < time()) {
    echo "<b>Alerta:</b> " . $user['alerts'] . "%</br>";
} elseif ($user['ban'] > time()) {
    echo "Banido</br>";
} elseif ($user['alerts'] == 'forever' || $user['alerts'] > 99) {
    echo "Banido do Fórum</br>";
}
if ($player->gm_rank > 2) {
    if ($player->gm_rank > 10) {
        echo "<br/><a href=\"forum_ban.php?player=" . $user['id'] . "\">Banir</a> | <a href=\"forum_alert.php?player=" . $user['id'] . "\">Alertar</a><br/><a href=\"delete_all.php?player=" . $user['id'] . "\">Apagar todos Posts</a><br/>";
    } else {
        echo "<br/><a href=\"forum_ban.php?player=" . $user['id'] . "\">Banir</a> | <a href=\"forum_alert.php?player=" . $user['id'] . "\">Alertar</a><br/>";
    }
}

        ?>
</font></center></td>
    <td width="435" bgcolor="#f2e1ce">
<?php
if ($player->username == $user['username']) {
    echo "<a href=\"edit_answer.php?topic=" . $rows['question_id'] . "&a=" . $rows['a_id'] . "\">Editar</a> | <a href=\"delete_answer.php?topic=" . $rows['question_id'] . "&a=" . $rows['a_id'] . "\">Deletar</a><br>";
} elseif ($player->gm_rank > 2) {
    echo "<a href=\"edit_answer.php?topic=" . $rows['question_id'] . "&a=" . $rows['a_id'] . "\">Editar</a> | <a href=\"delete_answer.php?topic=" . $rows['question_id'] . "&a=" . $rows['a_id'] . "\">Deletar</a><br>";
}
        ?><?php
        $respoxtak = stripslashes((string) $rows['a_answer']);
        echo $bbcode->parse($respoxtak);
        ?>
    </td>
  </tr>
</table>
<br/>
<?php
    }
    //Display the navigation
    echo "<center><font size=1>" . $pager->renderFullNav() . "</font></center>";
}

$viewcount = $db->execute("update `forum_question` set `view`=`view`+1 where `id`=?", [$id]);

$fecxhado = $db->GetOne("select `closed` from `forum_question` where `id`=?", [$id]);
if ($fecxhado['closed'] != 't') {
    ?>
<BR><BR>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#f2e1ce">
<tr>
<form name="form1" method="post" action="add_answer.php">
<td>
<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#f2e1ce">
<tr>
<td colspan="3" bgcolor="#E1CBA4"><strong>Responder</strong> </td>
</tr>
<tr>
<td><input name="id" type="hidden" value="<?php echo $id; ?>"><script>edToolbar('a_answer'); </script><textarea name="a_answer" rows="6" id="a_answer" class="ed"></textarea></td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Enviar" />&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:window.open('example.html', '_blank','top=100, left=100, height=400, width=400, status=no, menubar=no, resizable=no, scrollbars=yes, toolbar=no, location=no, directories=no');">Dicas de formatação</a></td>
</tr>
</table>
</td>
</form>
</tr>
</table>
<?php
} else {
    echo "<br/><center><b>Tópico fechado.</b></center>";
}
include(__DIR__ . "/templates/private_footer.php");
?>
