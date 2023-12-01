<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include(__DIR__ . "/lib.php");
define("PAGENAME", "Perfil");
$player = check_user($secret_key, $db);

include(__DIR__ . '/bbcode.php');
$bbcode = new bbcode();

//Check for user ID
if (!$_GET['id']) {
    header("Location: members.php");
} else {
    $query = $db->execute("select * from `players` where `username`=?", [$_GET['id']]);
    if ($query->recordcount() == 0) {
        header("Location: members.php");
    } else {
        $profile = $query->fetchrow();
    }
}

include(__DIR__ . "/templates/private_header.php");
?>
<script type="text/javascript" src="js/simpletabs_1.3.js"></script>
<?php

if ($profile['gm_rank'] > 50) {
    echo "<fieldset>";
    echo "<legend><b>" . $profile['username'] . "</b></legend>";
    echo "O usu�rio " . $profile['username'] . " � um dos administradores do jogo.<br/>";
    echo "Apenas moderadores podem entrar em contato com o administrador. Se precisa falar com a administra��o, clique em contato no parte inferior do site ou mande uma mensagem para algum dos nossos moderadores:<br/>";
    $query4 = $db->execute("select `username` from `players` where `gm_rank`>2 and `id`!=1 order by rand()");
    while($member1 = $query4->fetchrow()) {
        echo "<a href=\"mail.php?act=compose&to=" . $member1['username'] . "\">";
        echo $member1['username'];
        echo "</a> | ";
    }
    echo "</fieldset>";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}


if ($profile['ban'] > time()) {
    echo "<fieldset>";
    echo "<legend><b>" . $profile['username'] . "</b></legend>";
    echo "O usu�rio " . $profile['username'] . " foi banido.<br/>";
    $time = ($profile['ban'] - time());
    $time_remaining = ceil($time / 86400);
    if ($time_remaining > 100) {
        echo "Este usu�rio foi banido permanentemente.";
    } else {
        echo "Faltam " . $time_remaining . " dia(s) para o banimento terminar.";
    }
    echo "</fieldset>";
    echo "<br/><br/>";
    echo "<fieldset>";
    echo "<legend><b>Coment�rios da administra��o</b></legend>";
    $admincomments = $db->execute("select `msg` from `bans` where `player_id`=?", [$profile['id']]);
    if ($admincomments->recordcount() == 0) {
        echo "Sem coment�rios da administra��o.";
    } else {
        $mensagemdoamn = $admincomments->fetchrow();
        echo $mensagemdoamn['msg'];
    }
    echo "</fieldset>";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}

if ($profile['gm_rank'] > 2) {
    echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
    echo "<center><b>Este usu�rio � um moderador do jogo.</b></center>";
    echo "</div>";
} elseif ($profile['serv'] != $player->serv) {
    echo "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
    echo "<center><b>Este usu�rio pertence a outro servidor.</b></center>";
    echo "</div>";
}

?>

  <div id="tabber13" class="simpleTabs">
    <ul class="simpleTabsNavigation">
      <li><a class="current" id="tabber13_a_0" href="#"><?=$profile['username']?></a></li>
      <li><a class="" id="tabber13_a_1" href="#">Coment�rios</a></li>
      <li><a class="" id="tabber13_a_2" href="#">Medalhas</a></li>
      <li><a class="" id="tabber13_a_3" href="#">Amigos</a></li>
      <li><a class="" id="tabber13_a_4" href="#">Estatisticas</a></li>
    </ul>
    <div id="tabber13_div_0" class="simpleTabsContent">

<?php

    echo "<br/>";
echo "<table width=\"120px\" height=\"120px\" align=\"center\"><tr><td>";
echo "<div style=\"position: relative;\">";
echo "<img src=\"" . $profile['avatar'] . "\" width=\"120px\" height=\"120px\" style=\"position: absolute; top: 1; left: 1;\" alt=\"" . $profile['username'] . "\" border=\"1\">";
$checkranknosite = $db->execute("select `time` from `online` where `player_id`=?", [$profile['id']]);
if ($checkranknosite->recordcount() > 0) {
    echo "<img src=\"images/online2.gif\" width=\"120px\" height=\"120px\" style=\"position: absolute; top: 1; left: 1;\" alt=\"" . $profile['username'] . "\" border=\"1\">";
}
echo "</div>";
echo "</td></tr></table>";
echo "<br/>";

?>

<table width="90%">
<tr>
<td width="50%"><b>Usu�rio:</b></td>
<td width="50%"><?=$profile['username']?> (<a href="mail.php?act=compose&to=<?=$profile['username']?>">Mensagem</a>)</td>
</tr>
<tr>
<td><b>N�vel:</b></td>
<td><?=$profile['level']?></td>
</tr>
<tr>
<td><b>Ranking:</b></td>
<td><?php
$sql = "select id from players where gm_rank<10 and serv=" . $profile['serv'] . " order by level desc";
$dados = mysqli_query($db, $sql);
$i = 1;
while($linha = mysqli_fetch_array($dados)) {
    if ($linha['id'] == $profile['id']) {
        echo "$i";
    }
    $i++;
}
echo "�";
?></td>
</tr>
<tr>
<td><b>Voca��o:</b></td>
<td><?php
if ($profile['voc'] == 'archer' && $profile['promoted'] == 'f') {
    echo "Ca�ador";
} elseif ($profile['voc'] == 'knight' && $profile['promoted'] == 'f') {
    echo "Espadachim";
} elseif ($profile['voc'] == 'mage' && $profile['promoted'] == 'f') {
    echo "Bruxo";
} elseif ($profile['voc'] == 'archer' && ($profile['promoted'] == 't' || $profile['promoted'] == 's' || $profile['promoted'] == 'r')) {
    echo "Arqueiro";
} elseif ($profile['voc'] == 'knight' && ($profile['promoted'] == 't' || $profile['promoted'] == 's' || $profile['promoted'] == 'r')) {
    echo "Guerreiro";
} elseif ($profile['voc'] == 'mage' && ($profile['promoted'] == 't' || $profile['promoted'] == 's' || $profile['promoted'] == 'r')) {
    echo "Mago";
} elseif ($profile['voc'] == 'archer' && $profile['promoted'] == 'p') {
    echo "Arqueiro Royal";
} elseif ($profile['voc'] == 'knight' && $profile['promoted'] == 'p') {
    echo "Cavaleiro";
} elseif ($profile['voc'] == 'mage' && $profile['promoted'] == 'p') {
    echo "Arquimago";
}
?></td>
</tr>
<tr>
<td><b>Cl�:</b></td>
<td><?php
if ($profile['guild'] == null || $profile['guild'] == '') {
    echo "[Nenhum]";
} else {
    $profilenomecla = $db->GetOne("select `name` from `guilds` where `id`=?", [$profile['guild']]);
    echo "<b>[</b><a href=\"guild_profile.php?id=" . $profile['guild'] . "\">" . $profilenomecla . "</a><b>]</b>";
}
?></td>
</tr>
<tr>
<td><b>Status:</b></td>
<td><font color="<?=($profile['hp'] == 0) ? "red\">Morto" : "green\">Vivo"?></font></td>
</tr>
<tr><td></td></tr>
<tr>
<td><b>Cadastrado:</b></td>
<td><?=date("j F, Y, g:i a", $profile['registered'])?></td>
</tr>
<tr>
<td><b>Idade no jogo:</b></td>
<?php
$diff = time() - $profile['registered'];
$age = (int) (($diff / 3600) / 24);
?>
<td><?=$age?> dias</td>
</tr>
<tr>
<td><b>�ltima atividade:</b></td>
<?php
        $valortempo = time() -  $profile['last_active'];
if ($valortempo < 60) {
    $valortempo2 = $valortempo;
    $auxiliar2 = "segundo(s) atr�s.";
} elseif ($valortempo < 3600) {
    $valortempo2 = floor($valortempo / 60);
    $auxiliar2 = "minuto(s) atr�s.";
} elseif ($valortempo < 86400) {
    $valortempo2 = floor($valortempo / 3600);
    $auxiliar2 = "hora(s) atr�s.";
} elseif ($valortempo > 86400) {
    $valortempo2 = floor($valortempo / 86400);
    $auxiliar2 = "dia(s) atr�s.";
}
echo "<td>" . $valortempo2 . " " . $auxiliar2 . "</td>";
?>
</tr>
<?php
if ($profile['serv'] != $player->serv) {
    echo "<tr><td><b>Servidor:</b></td><td>" . $profile['serv'] . "</td></tr>";
}

if ($player->gm_rank > 50) {
    echo "<tr><td>Vida M�xima:</td><td>" . $profile['maxhp'] . "</td></tr>";
    echo "<tr><td>Energia M�xima:</td><td>" . $profile['maxenergy'] . "</td></tr>";
    echo "<tr><td>Ouro em m�os:</td><td>" . $profile['gold'] . "</td></tr>";
    echo "<tr><td>Ouro no banco:</td><td>" . $profile['bank'] . "</td></tr>";
    echo "<tr><td>Ouro total:</td><td>" . ($profile['bank'] + $profile['gold']) . "</td></tr>";
    echo "<tr><td>Pontos de Status:</td><td>" . $profile['stat_points'] . "</td></tr>";
    echo "<tr><td>For�a:</td><td>" . $profile['strength'] . "</td></tr>";
    echo "<tr><td>Vitalidade:</td><td>" . $profile['vitality'] . "</td></tr>";
    echo "<tr><td>Agilidade:</td><td>" . $profile['agility'] . "</td></tr>";
    echo "<tr><td>Resist�ncia:</td><td>" . $profile['resistance'] . "</td></tr>";
    echo "<tr><td>Status totais:</td><td>" . ($profile['strength'] + $profile['stat_points'] + $profile['agility'] + $profile['vitality'] + $profile['resistance']) . "</td></tr>";
    echo "<tr><td>Status m�ximos para este n�vel:</td><td>" . (4 + (3 * $profile['level']) + (3 * $profile['buystats'])) . "</td></tr>";
    echo "<tr><td>Senha de transfer�ncia:</td><td>" . $profile['transpass'] . "</td></tr>";
}
?>
</table>
<br /><br />
<center>
<?php
if ($player->gm_rank < 50) {
    echo "<a href=\"battle.php?act=attack&username=" . $profile['username'] . "\">Lutar contra " . $profile['username'] . "</a>";
} else {
    echo "<a href=\"gm/edit_member.php?id=" . $profile['id'] . "\">Editar</a> | <a href=\"gm/ban_member.php?act=ban&id=" . $profile['id'] . "\">Banir</a>";
}

if ($player->gm_rank > 2 && $profile['username'] != 0 && $profile['username'] > 99) {
    echo " | <a href=\"forum_unban.php?player=" . $profile['id'] . "\">Desbanir do F�rum</a>";
}
?>


</center>
</div>


<div id="tabber13_div_1" class="simpleTabsContent">
<table>
<?php
    $procuramengperfil = $db->execute("select `perfil` from `profile` where `player_id`=?", array($profile['id']));
if ($procuramengperfil->recordcount() == 0) {
    $mencomentario = "Sem coment�rios.";
} else {
    $comentdocara = $procuramengperfil->fetchrow();
    $mencomentario = stripslashes($comentdocara['perfil']);
}
?>
<tr><td><b>Nome real:</b></td><td><?php
$nname = $db->GetOne("select `name` from `accounts` where `id`=?", [$profile['acc_id']]);

if ($nname != null) {
    echo $nname;
} else {
    echo "N�o Informado";
}
?></td></tr>
<tr><td><b>Sexo:</b></td><td><?php
$sex = $db->GetOne("select `sex` from `accounts` where `id`=?", [$profile['acc_id']]);

if ($sex == 'm') {
    echo "Masculino";
} elseif ($sex == 'f') {
    echo "Feminino";
} else {
    echo "N�o Informado";
}
?></td></tr>
<tr><td><b>Email:</b></td><td><?php

        $checkshowmmaiele = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\SHOWMAIL, $player->acc_id]);
if ($checkshowmmaiele->recordcount() > 0) {
    $profilemail = $db->GetOne("select `email` from `accounts` where `id`=?", [$player->acc_id]);
    echo $profilemail;
} else {
    echo "Email Oculto";
}
?></td></tr>
<tr><td><b>Coment�rios:</b></td><td><?=$bbcode->parse($mencomentario);?></td></tr>
</table>
</div>

<div id="tabber13_div_2" class="simpleTabsContent">
<?php
$medalha = $db->execute("select * from `medalhas` where `player_id`=?", [$profile['id']]);
if ($medalha->recordcount() == 0) {
    echo "<br/><center><b>" . $profile['username'] . " n�o tem medalhas.</b></center><br/>";
} else {
    echo "<table>";

    while($meda = $medalha->fetchrow()) {
        echo "<tr><td><img src=\"images/itens/medalha.gif\"></td><td><b>" . $meda['medalha'] . ":</b> " . $meda['motivo'] . "</td></tr>";
    }

    echo "</table>";
}
?>
</div>


<div id="tabber13_div_3" class="simpleTabsContent">
<?php
//Select all members ordered by level (highest first, members table also doubles as rankings table)
$querwwq = $db->execute("select `fname` from `friends` where `uid`=? order by `fname` desc", [$profile['id']]);
if ($querwwq->recordcount() == 0) {
    echo "<br/><center><b>" . $profile['username'] . " n�o tem amigos.</b></center><br/>";
} else {
    echo "<table width=\"95%\" border=\"0\">";
    echo "<tr>";
    echo "<th width=\"60%\"><b>Usu�rio</b></td>";
    echo "<th width=\"40%\"><b>Op��es</b></td>";
    echo "</tr>";

    while($friend = $querwwq->fetchrow()) {
        echo "<tr>\n";
        echo "<td><a href=\"profile.php?id=" . $friend['fname'] . "\">" . $friend['fname'] . "</a></td>\n";
        echo "<td><a href=\"mail.php?act=compose&to=" . $friend['fname'] . "\">Mensagem</a> | <a href=\"battle.php?act=attack&username=" . $friend['fname'] . "\">Lutar</a> | <a href=\"friendlist.php?add=" . $friend['fname'] . "\">+ Amigo</a></td>\n";
        echo "</tr>\n";
    }
    echo "</table>";
}
?>
</div>
<div id="tabber13_div_4" class="simpleTabsContent">
<b>Usu�rios assassinados:</b> <?=$profile['kills']?>
<br/><b>Monstros mortos:</b> <?=$profile['monsterkilled']?>
<br/><b>Monstros mortos em grupo:</b> <?=$profile['groupmonsterkilled']?>
<br/><b>Mortes:</b> <?=$profile['deaths']?>
<br/><br/><b>Pontua��o total:</b> <?=ceil(($profile['kills'] * 6) + ($profile['monsterkilled'] / 3) + ($profile['groupmonsterkilled'] / 12) - ($profile['deaths'] * 35))?>
</div>
</div>

<?php
include(__DIR__ . "/templates/private_footer.php");
?>