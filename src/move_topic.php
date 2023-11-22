<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Fórum");
$player = check_user($secret_key, $db);

include(__DIR__ . "/templates/private_header.php");

if (!$_GET['topic']) {
    echo "Um erro desconhecido ocorreu! <a href=\"main_forum.php\">Voltar</a>.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}
if ($player->gm_rank > 2) {
    $procuramensagem = $db->execute("select `topic` from `forum_question` where `id`=?", [$_GET['topic']]);
} else {
    echo "Você não tem permisões para mover este tópico! <a href=\"view_topic.php?id=" . $_GET['topic'] . "\">Voltar</a>.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}

if ($procuramensagem->recordcount() == 0) {
    echo "Um erro desconhecido ocorreu! <a href=\"main_forum.php\">Voltar</a>.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}
$nome = $procuramensagem->fetchrow();

if(isset($_POST['submit'])) {

    if (!$_POST['detail']) {
        echo "Você precisa preencher todos os campos! <a href=\"move_topic.php?topic=" . $_GET['topic'] . "\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    if ($_POST['detail'] == 'none') {
        echo "Você precisa preencher todos os campos! <a href=\"move_topic.php?topic=" . $_GET['topic'] . "\">Voltar</a>.";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }


    if ($_POST['detail'] == 'gangues') {
        $categoria = "Clãs";
    } elseif ($_POST['detail'] == 'trade') {
        $categoria = "Compro/Vendo";
    } elseif ($_POST['detail'] == 'noticias') {
        $categoria = "Notícias";
    } elseif ($_POST['detail'] == 'sugestoes') {
        $categoria = "Sugestões";
    } elseif ($_POST['detail'] == 'duvidas') {
        $categoria = "Dúvidas";
    } elseif ($_POST['detail'] == 'fan') {
        $categoria = "Fanwork";
    } elseif ($_POST['detail'] == 'off') {
        $categoria = "Off-Topic";
    } else {
        $categoria = $_POST['detail'];
    }


    $logalert2 = "O tópico " . $nome['topic'] . " foi movido para a sessão " . $categoria . " pelo moderador <b>" . $player->username . "</b>";
    forumlog($logalert2, $db);


    $real = $db->execute("update `forum_question` set `category`=? where `id`=?", [$_POST['detail'], $_GET['topic']]);
    echo "Postagem editada com sucesso! <a href=\"view_topic.php?id=" . $_GET['topic'] . "\">Voltar</a>.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}

?>

<table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
<tr>
<form method="POST" action="move_topic.php?topic=<?=$_GET['topic']?>">
<td>
<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
<tr>
<td colspan="3" bgcolor="#E6E6E6"><strong>Mover Tópico</strong> </td>
</tr>
<tr>
<td>Para onde deseja mover o tópico: <b><?=$nome['topic']?></b> ?<br/>
<select name="detail">
<option value="none" selected="selected">Selecione</option>
<option value="noticias">Notícias</option>
<option value="equipe">Equipe</option>
<option value="sugestoes">Sugestões</option>
<option value="gangues">Clãs</option>
<option value="trade">Compro/Vendo</option>
<option value="duvidas">Duvidas</option>
<option value="fan">Fanwork</option>
<option value="outros">Outros</option>
<option value="off">Off-Topic</option></td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Mover" /></td>
</tr>
</table>
</td>
</form>
</tr>
</table>
<?php
include(__DIR__ . "/templates/private_footer.php");
?>
