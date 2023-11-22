<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Khashul        */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.bbgamezone.com/     */
/*************************************/

include(__DIR__ . "/lib.php");
define("PAGENAME", "Administração do Clã");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkguild.php");

$error = 0;

//Populates $guild variable
$guildquery = $db->execute("select * from `guilds` where `id`=?", [$player->guild]);

if ($guildquery->recordcount() == 0) {
    header("Location: home.php");
} else {
    $guild = $guildquery->fetchrow();
}

include(__DIR__ . "/templates/private_header.php");

//Guild Leader Admin check
if ($player->username != $guild['leader']) {
    echo "<p />Você não pode acessar esta página.<p />";
    echo "<a href=\"home.php\">Principal</a><p />";
} else {

if ($_GET['remove']) {
if ($guild['vice'] == NULL || $guild['vice'] == ''){
$msg .= "Seu clã não possui um vice lider.";
}else{
$msg .= "Você removeu os privilégios de vice lider de " . $guild['vice'] . ".<br/><a href=\"guild_admin.php\">Voltar</a>.";
$query = $db->execute("update `guilds` set `vice`='' where `id`=?", [$guild['id']]);
}
}


if (isset($_POST['username']) && ($_POST['submit'])) {

	$username = $_POST['username'];
	$query = $db->execute("select `id`, `username`, `guild` from `players` where `username`=? and `serv`=?", [$username, $guild['serv']]);

    if ($query->recordcount() == 0) {
        $errmsg .= "Este usuário não existe!<p />";
        $error = 1;
    } elseif ($username == $guild['leader']) {
        $errmsg .= "Este usuário é o lider do clã!<p />";
        $error = 1;
    } elseif ($username == $guild['vice']) {
        $errmsg .= "Este usuário é o vice-lider do clã!<p />";
        $error = 1;
    } else {
   		$member = $query->fetchrow();
	   		if ($member['guild'] != $guild['id']) {
    			$errmsg .= "O usuário $username não faz parte do clã: " . $member['guild'] ."!<p />";
    			$error = 1;
    		} else {
			if ($guild['vice'] == NULL || $guild['vice'] == ''){
    			$msg .= "Você nomeou $username como vice-lider do clã.<br/><a href=\"guild_admin.php\">Voltar</a>.";
			}else{
    			$msg .= "Você nomeou $username como vice-lider do clã. O antigo vice-lider, " . $guild['vice'] . "  agora é um membro comum.<br/><a href=\"guild_admin.php\">Voltar</a>.";
			}

    			$query = $db->execute("update `guilds` set `vice`=? where `id`=?", [$username, $guild['id']]);
    			$logmsg = "Você foi nomeado vice-lider do clã: ". $guild['name'] .".";
				addlog($member['id'], $logmsg, $db);
    		}
    	}
	}


if ($guild['vice'] == NULL || $guild['vice'] == ''){
$viceatual1 = "Ninguém.";
}else{
$viceatual1 = $guild['vice'];
$viceatual2 = "<a href=\"guild_admin_vice.php?remove=" . $guild['vice'] . "\">Remover Vice-Liderança de " . $guild['vice'] . "</a>.";
}
?>

<fieldset>
<legend><b><?=$guild['name']?> :: Nomear Vice-Lider</b></legend>
<b>Vice-Lider atual:</b> <?=$viceatual1?> <?=$viceatual2?><br/><br/>
<form method="POST" action="guild_admin_vice.php">
<b>Usuário:</b> <?php $query = $db->execute("select `id`, `username` from `players` where `guild`=?", [$guild['id']]);
echo "<select name=\"username\"><option value=''>Selecione</option>";
while($result = $query->fetchrow()){
echo "<option value=\"$result[username]\">$result[username]</option>";
}
echo "</select>"; ?> <input type="submit" name="submit" value="Nomear Vice-Lider"><p />
</form>
<b>ATENÇÃO:</b> Um vice-lider tem todas as funções do administrador do clã, porem não pode desfazer o mesmo e nem nomear novos vice lideres.<p /><?=$msg?><font color=red><?=$errmsg?></font>
</fieldset>

<?php
}
include(__DIR__ . "/templates/private_footer.php");
?>