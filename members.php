<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Membros");
$player = check_user($secret_key, $db);

$limit = 10;

$page = (intval($_GET['page']) == 0)?1:intval($_GET['page']); //Start on page 1 or $_GET['page']

$begin = ($limit * $page) - $limit; //Starting point for query

$total_players = $db->getone("select count(ID) as `count` from `players` where `serv`=?", array($player->serv));


include("templates/private_header.php");

echo "<form method=\"get\" action=\"members.php\">\n";
echo "<br><b>Página:</b> <select name=\"page\">";

$numpages = $total_players / $limit;
for ($i = 1; $i <= $numpages; $i++)
{
	//Display page numbers
	echo ($i == $page)?"<option value=\"" . $i . "\" selected=\"" . $i . "\">" . $i . "</option>":"<option value=\"" . $i . "\">" . $i . "</option>";
}

if (($total_players % $limit) != 0)
{
	//Display last page number if there are left-over users in the query
	echo ($i == $page)?"<option value=\"" . $i . "\" selected=\"" . $i . "\">" . $i . "</option>":"<option value=\"" . $i . "\">" . $i . "</option>";
}
echo "</select>&nbsp;&nbsp;<b>Ordenar por:</b>&nbsp;<select name=\"orderby\"><option value=\"level\">Nível</option><option value=\"gold\">Ouro</option><option value=\"kills\">Assassinatos</option><option value=\"monsterkilled\">Monstros mortos</option>&nbsp;<input type=\"submit\" value=\"Ir\"></form>";
$lang['keyword_previous'] = "<b>Página anterior</b>";
$lang['keyword_next'] = "<b>Próxima página</b>";

echo ($page != 1)?"<a href=\"members.php?limit=" . $limit . "&page=" . ($page-1) . "\">" . $lang['keyword_previous'] . "</a> | ":$lang['keyword_previous'] . " | ";

echo (($total_players - ($limit * $page)) > 0)?"<a href=\"members.php?limit=" . $limit . "&page=" . ($page+1) . "\">" . $lang['keyword_next'] . "</a> ":$lang['keyword_next'];

?>
<fieldset>
<legend><b>Membros</b></legend>
<table width="100%" border="0">
<tr>
<th width="15%"><b>Imagem</b></td>
<th width="25%"><b>Usuário</b></td>
<th width="20%"><b>Nivel</b></td>
<th width="20%"><b>Vocação</b></td>
<th width="20%"><b>Opções</b></td>
</tr>
<?php

if (!$_GET['orderby']) {
$ordenarpor = "`level`";
} else if ($_GET['orderby'] == level) {
$ordenarpor = "`level`";
} else if ($_GET['orderby'] == gold) {
$ordenarpor = "`gold`+`bank`";
} else if ($_GET['orderby'] == kills) {
$ordenarpor = "`kills`";
} else if ($_GET['orderby'] == monsterkilled) {
$ordenarpor = "`monsterkilled`";
}

//Select all members ordered by level (highest first, members table also doubles as rankings table)
$query = $db->execute("select `id`, `username`, `gm_rank`, `level`, `avatar`, `voc`, `promoted` from `players` where `gm_rank`<10 and `serv`=? order by $ordenarpor desc limit ?,?", array($player->serv, $begin, $limit));

while($member = $query->fetchrow())
{
	echo "<tr>\n";

	echo "<td height=\"64px\"><div style=\"position: relative;\">";
	echo "<img src=\"" . $member['avatar'] . "\" width=\"64px\" height=\"64px\" style=\"position: absolute; top: 1; left: 1;\" alt=\"" . $member['username'] . "\" border=\"0\">";

	$checkranknosite = $db->execute("select `time` from `online` where `player_id`=?", array($member['id']));
	if ($checkranknosite->recordcount() > 0) {
	echo "<img src=\"images/online1.gif\" width=\"64px\" height=\"64px\" style=\"position: absolute; top: 1; left: 1;\" alt=\"" . $member['username'] . "\" border=\"0\">";
	}

	echo "</div></td>";

	echo "<td><a href=\"profile.php?id=" . $member['username'] . "\">";
	echo ($member['username'] == $player->username)?"<b>":"";
	echo $member['username'];
	echo ($member['username'] == $player->username)?"</b>":"";
	echo "</a></td>\n";
	echo "<td>" . $member['level'] . "</td>\n";
	echo "<td>";
if ($member['voc'] == 'archer' and $member['promoted'] == 'f'){
echo "Caçador";
} else if ($member['voc'] == 'knight' and $member['promoted'] == 'f'){
echo "Espadachim";
} else if ($member['voc'] == 'mage' and $member['promoted'] == 'f'){
echo "Bruxo";
} else if (($member['voc'] == 'archer') and ($member['promoted'] == 't' or $member['promoted'] == 's' or $member['promoted'] == 'r')){
echo "Arqueiro";
} else if (($member['voc'] == 'knight') and ($member['promoted'] == 't' or $member['promoted'] == 's' or $member['promoted'] == 'r')){
echo "Guerreiro";
} else if (($member['voc'] == 'mage') and ($member['promoted'] == 't' or $member['promoted'] == 's' or $member['promoted'] == 'r')){
echo "Mago";
} else if ($member['voc'] == 'archer' and $member['promoted'] == 'p'){
echo "Arqueiro Royal";
} else if ($member['voc'] == 'knight' and $member['promoted'] == 'p'){
echo "Cavaleiro";
} else if ($member['voc'] == 'mage' and $member['promoted'] == 'p'){
echo "Arquimago";
}
	echo "</td>\n";
	echo "<td><font size=\"1\"><a href=\"mail.php?act=compose&to=" . $member['username'] . "\">Mensagem</a><br/><a href=\"battle.php?act=attack&username=" . $member['username'] . "\">Lutar</a><br/>+ <a href=\"friendlist.php?add=".$member['username']."\">Amigo</a></font></td>\n";
	echo "</tr>\n";
}
?>
</table>
</fieldset>
<br/><br/>
<?php
		echo "<fieldset>\n";
		echo "<legend><b>Procurar por usuário</b></legend>\n";
		echo "<form method=\"get\" action=\"profile.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"30%\"><b>Usuário:</b></td>\n<td width=\"40%\"><input type=\"text\" name=\"id\" /></td>";
		echo "<td width=\"30%\"><input type=\"submit\" value=\"Procurar\" /></td></tr>\n";
		echo "</table>\n";
		echo "</form></fieldset>";

include("templates/private_footer.php");
?>