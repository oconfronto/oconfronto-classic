<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Khashul        */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.bbgamezone.com/     */
/*************************************/

include(__DIR__ . "/lib.php");
include(__DIR__ . '/bbcode.php');
$bbcode = new bbcode();
define("PAGENAME", "Clã");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkguild.php");

$totalgold = 0;
$totalbattles = 0;
$totalmonsters = 0;
$totallevel = 0;
$totaldeaths = 0;


//Check for user ID
if (!$_GET['id']) {
    header("Location: guild_listing.php");
} else {
    //Populates $guild variable
    $query = $db->execute("select * from `guilds` where `id`=?", [$_GET['id']]);
    if ($query->recordcount() == 0) {
        header("Location: guild_listing.php");
    } else {
        $guild = $query->fetchrow();
    }
}

include(__DIR__ . "/templates/private_header.php");
?>
<script type="text/javascript" src="js/simpletabs_1.3.js"></script>


  <div id="tabber13" class="simpleTabs">
    <ul class="simpleTabsNavigation">
<?php
if (strlen((string) $guild['name']) < 17) {
    echo "<li><a class=\"current\" id=\"tabber13_a_0\" href=\"#\">" . $guild['name'] . "</a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_1\" href=\"#\">Membros</a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_2\" href=\"#\">Aliados</a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_3\" href=\"#\">Inimigos</a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_4\" href=\"#\">Guerras</a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_5\" href=\"#\">Estatisticas</a></li>";
} else {
    echo "<li><a class=\"current\" id=\"tabber13_a_0\" href=\"#\"><font size=\"1\">" . $guild['name'] . "</font></a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_1\" href=\"#\"><font size=\"1\">Membros</font></a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_2\" href=\"#\"><font size=\"1\">Aliados</font></a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_3\" href=\"#\"><font size=\"1\">Inimigos</font></a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_4\" href=\"#\"><font size=\"1\">Guerras</font></a></li>";
    echo "<li><a class=\"\" id=\"tabber13_a_5\" href=\"#\"><font size=\"1\">Estatisticas</font></a></li>";
}
?>
    </ul>
    <div id="tabber13_div_0" class="simpleTabsContent">

<table width="100%">
<tr>
<td width="100%">
<table width="100%">
<tr>
<td width="30%">
<center><img src="<?=$guild['img']?>" width="120px" height="120px" border="1"></center>
</td>
<td width="70%">
<table width="100%">
<tr>
<td width="30%"><b>Nome:</b></td>
<td width="70%"><?=$guild['name']?> [<?=$guild['tag']?>]</td>
</tr>
<tr>
<td><b>Logo:</b></td>
<td>"<?=$guild['motd']?>"</td>
</tr>
<tr>
<td><b>Lider:</b></td>
<td><a href="profile.php?id=<?=$guild['leader']?>"><?=$guild['leader']?></a></td>
</tr>
<tr>
<td><b>Vice-Lider:</b></td>
<td>
<?php
if ($guild['vice'] == null || $guild['vice'] == '') {
    echo "Ninguém";
} else {
    echo "<a href=\"profile.php?id=" . $guild['vice'] . "\">" . $guild['vice'] . "</a></td>";
}
?>
</tr>
<tr>
<td><b>Membros:</b></td>
<td><?=$guild['members']?></td>
</tr>
<tr>
<td><b>Dinheiro:</b></td>
<td><?=$guild['gold']?></td>
</tr>
</table>

</td>
</tr>
</table>
</td>
</tr>
<tr>
<td width="100%">
<?php
echo "<b>Descrição:</b> ";
if ($guild['blurb'] == null || $guild['blurb'] == '') {
    echo "Sem descrição.";
} else {
    $descrikon = stripslashes((string) $guild['blurb']);
    echo $bbcode->parse($descrikon);
}
?>
</td>
</tr>
</table>
</div>

<div id="tabber13_div_1" class="simpleTabsContent">
<table width="100%" border="0">
<tr>
<th width="30%"><b>Usuário</b></td>
<th width="15%"><b>Nivel</b></td>
<th width="25%"><b>Vocação</b></td>
<th width="15%"><b>Status</b></td>
<th width="20%"><b>Opções</b></td>
</tr>
<?php
//Select all members ordered by level (highest first, members table also doubles as rankings table)
$query = $db->execute("select `id`, `username`, `level`, `voc`, `promoted`, `gold`, `bank`, `hp`, `kills`, `monsterkilled`, `deaths` from `players` where `guild`=? order by `level` desc", [$guild['id']]);

while($member = $query->fetchrow()) {
    echo "<tr>\n";
    echo "<td><a href=\"profile.php?id=" . $member['username'] . "\">";
    echo ($member['username'] == $player->username) ? "<b>" : "";
    echo $member['username'];
    echo ($member['username'] == $player->username) ? "</b>" : "";
    echo "</a></td>\n";
    echo "<td>" . $member['level'] . "</td>\n";

    echo "<td>";
    if ($member['voc'] == 'archer' && $member['promoted'] == 'f') {
        echo "Caçador";
    } elseif ($member['voc'] == 'knight' && $member['promoted'] == 'f') {
        echo "Espadachim";
    } elseif ($member['voc'] == 'mage' && $member['promoted'] == 'f') {
        echo "Bruxo";
    } elseif ($member['voc'] == 'archer' && ($member['promoted'] == 't' || $member['promoted'] == 's' || $member['promoted'] == 'r')) {
        echo "Arqueiro";
    } elseif ($member['voc'] == 'knight' && ($member['promoted'] == 't' || $member['promoted'] == 's' || $member['promoted'] == 'r')) {
        echo "Guerreiro";
    } elseif ($member['voc'] == 'mage' && ($member['promoted'] == 't' || $member['promoted'] == 's' || $member['promoted'] == 'r')) {
        echo "Mago";
    } elseif ($member['voc'] == 'archer' && $member['promoted'] == 'p') {
        echo "Arqueiro Royal";
    } elseif ($member['voc'] == 'knight' && $member['promoted'] == 'p') {
        echo "Cavaleiro";
    } elseif ($member['voc'] == 'mage' && $member['promoted'] == 'p') {
        echo "Arquimago";
    }
    echo "</td>\n";


    echo "<td>";
    if ($member['hp'] < 1) {
        echo "<font color=\"red\">Morto</font>";
    } else {
        echo "<font color=\"green\">Vivo</font>";
    }
    echo "</td>\n";
    echo "<td><font size=\"1\"><a href=\"mail.php?act=compose&to=" . $member['username'] . "\">Mensagem</a><br/><a href=\"battle.php?act=attack&username=" . $member['username'] . "\">Lutar</a></font></td>\n";
    echo "</tr>\n";

    $totalgold += ($member['gold'] + $member['bank'])  / $guild['members']; //Add to total gold
    $totalbattles += $member['kills'] / $guild['members']; //Add to total battles
    $totalmonsters += $member['monsterkilled'] / $guild['members']; //Add to total monsters
    $totallevel += $member['level'] / $guild['members']; //Add to total level
    $totaldeaths += $member['deaths'] / $guild['members']; //Add to total deaths
}
?>
</table>
</div>


<div id="tabber13_div_2" class="simpleTabsContent">
<?php
echo "<br/>";

$alyquery = $db->execute("select `aled_na` from `guild_aliance` where `guild_na`=?", [$guild['id']]);
if ($alyquery->recordcount() < 1) {
    echo "<center><b>O clã " . $guild['name'] . " não tem alianças.</b></center><br/>";
} else {
    while($aly = $alyquery->fetchrow()) {
        $allyname = $db->GetOne("select `name` from `guilds` where `id`=?", [$aly['aled_na']]);
        echo "<center><b>O clã " . $guild['name'] . " possui alianças com o clã <a href=\"guild_profile.php?id=" . $aly['aled_na'] . "\">" . $allyname . "</a>.</b></center><br/>";
    }
}
?>
</div>

<div id="tabber13_div_3" class="simpleTabsContent">
<?php

$enyquery = $db->execute("select `enemy_na` from `guild_enemy` where `guild_na`=?", [$guild['id']]);

if ($enyquery->recordcount() < 1) {
    echo "<br/><center><b>O clã " . $guild['name'] . " não tem inimigos.</b></center><br/>";
} else {
    while($eny = $enyquery->fetchrow()) {
        $ennyname = $db->GetOne("select `name` from `guilds` where `id`=?", [$eny['enemy_na']]);
        echo "<br/><center><b>O clã " . $guild['name'] . " é inimigo do clã <a href=\"guild_profile.php?id=" . $eny['enemy_na'] . "\">" . $ennyname . "</a>.</b></center><br/>";
    }
}
?>
</div>


<div id="tabber13_div_4" class="simpleTabsContent">
<br/><center><b>Sistema de guerras em desenvolvimento.</b></center><br/>
</div>

<div id="tabber13_div_5" class="simpleTabsContent">
<?php
if ($guild['members'] < 9) {
    echo "<br/><font size=\"1\"><center><b>As estátisticas dos membros só estão disponiveis à gangues com 10 membros ou mais.</b></center></font><br/>";
} else {
    ?>
<table width="100%" border="0">
<tr><td><b>Média de nivel:</b></td><td><?=ceil($totallevel)?></td></tr>
<tr><td><b>Média de ouro:</b></td><td><?=ceil($totalgold)?></td></tr>
<tr><td><b>Média de usuários mortos:</b></td><td><?=ceil($totalbattles)?></td></tr>
<tr><td><b>Média de monstros mortos:</b></td><td><?=ceil($totalmonsters)?></td></tr>
</table>
<br />
<?php
    $totalpoints = ((($totalgold + $guild['gold']) / 90) + ($totalbattles * 4) + ($totalmonsters) + ($totallevel * 28) - ($totaldeaths * 10));
    ?>
<center><b>Pontuação total:</b> <?=ceil($totalpoints)?></center>
<?php
}
echo "</div></div>";
include(__DIR__ . "/templates/private_footer.php");
?>