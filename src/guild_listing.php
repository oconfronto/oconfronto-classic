<?php

/*************************************/
/*           ezRPG script            */
/*        Written by Alliehj         */
/*    modded by Die4me + Khashul     */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.bbgamezone.com/     */
/*************************************/

include("lib.php");
define("PAGENAME", "Clãs");
$player = check_user($secret_key, $db);
include("checkbattle.php");
include("checkguild.php");

if ($player->hp <= 0)
{
	include("templates/private_header.php");
	echo "<fieldset>";
	echo "<legend><b>Você está morto!</b></legend>\n";
	echo "Vá ao <a href=\"hospt.php\">hospital</a> ou espere 30 minutos.";
	include("templates/private_footer.php");
	exit;
}

$total_guilds = $db->getone("select count(ID) as `count` from `guilds`");

include("templates/private_header.php");
?>

<table width="100%" border="0">
<tr>
<th width="15%"><b>Imagem</b></td>
<th width="30%"><b>Nome</b></td>
<th width="20%"><b>Membros</b></td>
<th width="15%"><b>Pontos</b></td>
<th width="5%"><b>Preço</b></td>
<th width="15%"></td>
</tr>
<?php

$query = $db->execute("select guilds.id, guilds.name, guilds.leader, guilds.vice, guilds.motd, guilds.members, guilds.price, guilds.img, guild_scores.score from `guilds`, `guild_scores` where guilds.id=guild_scores.guild_id and guilds.serv=? order by guild_scores.score desc", array($player->serv));

while ($guild = $query->fetchrow()) {
    echo "<tr>\n";
    echo "<td>";
	if($player->guild == $guild['name']){
	echo "<a href=\"guild_home.php\">";
	}else{
	echo "<a href=\"guild_profile.php?id=" . $guild['id'] . "\">";
	}
    	echo "<center><img src=\"" . $guild['img'] . "\" alt=\"" . $guild['name'] . "" . $result['username'] . "\"  width=\"64\" height=\"64\" border=\"0\"></center></a></td>\n";

	echo "<td>";
	if($player->guild == $guild['name']){
	echo "<a href=\"guild_home.php\">";
	}else{
	echo "<a href=\"guild_profile.php?id=" . $guild['id'] . "\">";
	}
	echo $guild['name'];
	echo "</a><br/><font size=\"1\">" . $guild['motd'] . "</font></td>\n";
   	echo "<td>" . $guild['members'] . "</td>\n";

    echo "<td>" . ceil($guild['score']) . "</td>\n";

    echo "<td>" . $guild['price'] . "</td>\n";
    $checkquery = $db->execute("select count(*) inv_count from guild_invites where player_id =? and guild_id =?", array($player->id, $guild['id']));
	$check = $checkquery->fetchrow();
	echo "<td>";
	if ($check['inv_count'] > 0) {
    	echo "<center><a href=\"guild_join.php?id=" . $guild['id'] . "\">Entrar</a></center>";
	}else if(($player->guild == $guild['id']) and ($player->username != $guild['leader']) and ($player->username != $guild['vice'])){
	echo "<center><a href=\"guild_leave.php\">Abandonar</a></center>";
	}
	echo "</td>";
    echo "</tr>\n";
}
?>
</table>
<font size=\"1\">Pontos atualizados diariamente.</font>
<br><br>
<center><input type="button" VALUE="Criar Clã" ONCLICK="window.location.href='guild_register.php'"></center>

<?php
include("templates/private_footer.php");
?>