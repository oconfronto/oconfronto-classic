<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include(__DIR__ . "/lib.php");
define("PAGENAME", "Membros");
$player = check_user($secret_key, $db);

$limit = 10;

$page = ((int) $_GET['page'] == 0)?1:(int) $_GET['page']; //Start on page 1 or $_GET['page']

$begin = ($limit * $page) - $limit; //Starting point for query

$total_players = $db->getone("select count(ID) as `count` from `players` where `serv`=?", [$player->serv]);


include(__DIR__ . "/templates/private_header.php");

echo "<form method=\"get\" action=\"members.php\">\n";
echo "<br><b>Página:</b> <select name=\"page\">";

$numpages = $total_players / $limit;
for ($i = 1; $i <= $numpages; $i++)
{
	//Display page numbers
	echo ($i == $page)?"<option value=\"" . $i . "\" selected=\"" . $i . "\">" . $i . "</option>":"<option value=\"" . $i . "\">" . $i . "</option>";
}

if ($total_players % $limit != 0)
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
} elseif ($_GET['orderby'] == \LEVEL) {
    $ordenarpor = "`level`";
} elseif ($_GET['orderby'] == \GOLD) {
    $ordenarpor = "`gold`+`bank`";
} elseif ($_GET['orderby'] == \KILLS) {
    $ordenarpor = "`kills`";
} elseif ($_GET['orderby'] == \MONSTERKILLED) {
    $ordenarpor = "`monsterkilled`";
}

//Select all members ordered by level (highest first, members table also doubles as rankings table)
$query = $db->execute("select `id`, `username`, `gm_rank`, `level`, `avatar`, `voc`, `promoted` from `players` where `gm_rank`<10 and `serv`=? order by $ordenarpor desc limit ?,?", [$player->serv, $begin, $limit]);

while($member = $query->fetchrow())
{
	echo "<tr>\n";

	echo "<td height=\"64px\"><div style=\"position: relative;\">";
	echo "<img src=\"" . $member['avatar'] . "\" width=\"64px\" height=\"64px\" style=\"position: absolute; top: 1; left: 1;\" alt=\"" . $member['username'] . "\" border=\"0\">";

	$checkranknosite = $db->execute("select `time` from `online` where `player_id`=?", [$member['id']]);
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

include(__DIR__ . "/templates/private_footer.php");
?>