<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Fórum");
$player = check_user($secret_key, $db);

include(__DIR__ . "/checkforum.php");
include(__DIR__ . "/templates/private_header.php");
?>
<script type="text/javascript" src="bbeditor/ed.js"></script>

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
<tr>
<form id="form1" name="form1" method="post" action="add_topic.php">
<td>
<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
<tr>
<td colspan="3" bgcolor="#E6E6E6"><strong>Criar novo Tópico</strong> </td>
</tr>
<tr>
<td width="14%"><strong>Titulo</strong></td>
<td width="2%">:</td>
<td width="84%"><input name="topic" type="text" id="topic" size="27" />&nbsp;&nbsp;&nbsp;<select name="category">
<?php
echo "<option value=\"none\" selected=\"selected\">Selecione</option>";
if ($player->gm_rank > 2) {
echo "<option value=\"noticias\">Notícias</option>";
echo "<option value=\"equipe\">Equipe</option>";
}
echo "<option value=\"sugestoes\">Sugestões</option>";
echo "<option value=\"gangues\">Clãs</option>";
echo "<option value=\"trade\">Compro/Vendo</option>";
echo "<option value=\"duvidas\">Duvidas</option>";
echo "<option value=\"fan\">Fanwork</option>";
echo "<option value=\"outros\">Outros</option>";
echo "<option value=\"off\">Off-Topic</option>";
?>
</select></td>
</tr>
<tr>
<td valign="top"><strong>Mensagem</strong></td>
<td valign="top">:</td>
<td>
<script>edToolbar('detail'); </script>  
<textarea name="detail" rows="12" id="detail" class="ed"></textarea></td>
</tr>
<tr>
<input type="hidden" name="user_id" value="<?=$player->id?>">
<input type="hidden" name="name" value="<?=$player->username?>">
<input type="hidden" name="avatar" value="<?=$player->avatar?>">
<td>&nbsp;</td>
<td>&nbsp;</td>
<td><input type="submit" name="Submit" value="Enviar" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="vota" value="yes"> Ativar Votação</td>
</tr>
</table>
</td>
</form>
</tr>
</table>
<?php
include(__DIR__ . "/templates/private_footer.php");
?>