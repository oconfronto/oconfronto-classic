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
$guildquery = $db->execute("select `id`, `name`, `leader`, `vice`, `members` from `guilds` where `id`=?", [$player->guild]);

if ($guildquery->recordcount() == 0) {
    header("Location: home.php");
} else {
    $guild = $guildquery->fetchrow();
}

include(__DIR__ . "/templates/private_header.php");

//Guild Leader Admin check
if ($player->username != $guild['leader'] && $player->username != $guild['vice']) {
    echo "Você não pode acessar esta página.";
    echo "<br/><a href=\"home.php\">Voltar</a>.";
} else {

    if ($_GET['unenemy'] && $_GET['enemy_na']) {

        $acheckcla = $db->execute("select `id` from `guilds` where `id`=?", [$_GET['enemy_na']]);
        $ccheckjaaly = $db->execute("select `id` from `guild_enemy` where `guild_na`=? and `enemy_na`=?", [$guild['id'], $_GET['enemy_na']]);

        if ($acheckcla->recordcount() != 1) {
            $errmsg .= "Este clã não existe!";
            $errorb = 1;
        } elseif ($ccheckjaaly->recordcount() < 1) {
            $errmsg .= "Este clã não é um clã inimigo!";
            $errorb = 1;
        } elseif ($errorb == 0) {
            $log1 = $db->execute("select `id` from `players` where `guild`=?", [$_GET['enemy_na']]);
            while($p1 = $log1->fetchrow()) {
                $logmsg1 = "O clã <a href=\"guild_profile.php?id=". $guild['id'] ."\">". $guild['name'] ."</a> não é mais um clã inimigo.";
                addlog($p1['id'], $logmsg1, $db);
            }
            $msglog2guild = $db->GetOne("select `name` from `guilds` where `id`=?", [$_GET['enemy_na']]);
            $log2 = $db->execute("select `id` from `players` where `guild`=?", [$guild['id']]);
            while($p2 = $log2->fetchrow()) {
                $logmsg2 = "O clã <a href=\"guild_profile.php?id=". $_GET['enemy_na'] ."\">". $msglog2guild ."</a> não é mais um clã inimigo.";
                addlog($p2['id'], $logmsg2, $db);
            }
            $query = $db->execute("delete from `guild_enemy` where `guild_na`=? and `enemy_na`=?", [$guild['id'], $_GET['enemy_na']]);
            $query = $db->execute("delete from `guild_enemy` where `guild_na`=? and `enemy_na`=?", [$_GET['enemy_na'], $guild['id']]);
            $msg .= "O clã " . $msglog2guild . " foi removido da lista de inimigos.";
        }

    } elseif (isset($_POST['gname']) && ($_POST['submit'])) {

        $checkcla = $db->execute("select `id`, `leader`, `vice`, `name` from `guilds` where `id`=?", [$_POST['gname']]);
        $checkjaeny0 = $db->execute("select `id` from `guild_enemy` where `guild_na`=? and `enemy_na`=?", [$guild['id'], $_POST['gname']]);
        $checkjaeny1 = $db->execute("select `id` from `guild_aliance` where `guild_na`=?", [$guild['id']]);

        if ($checkcla->recordcount() == 0) {
            $errmsg .= "Este clã não existe!";
            $error = 1;
        } elseif ($checkjaeny0->recordcount() > 0) {
            $errmsg .= "Este clã já está marcado como inimigo!";
            $error = 1;
        } elseif ($checkjaeny1->recordcount() > 0) {
            $errmsg .= "Este clã é um clã aliado!";
            $error = 1;
        } elseif ($error === 0) {
            $enyguild = $checkcla->fetchrow();
            $insert['guild_na'] = $guild['id'];
            $insert['enemy_na'] = $enyguild['id'];
            $insert['time'] = time();
            $query = $db->autoexecute('guild_enemy', $insert, 'INSERT');
            $insert['guild_na'] = $enyguild['id'];
            $insert['enemy_na'] = $guild['id'];
            $insert['time'] = time();
            $query = $db->autoexecute('guild_enemy', $insert, 'INSERT');
            $msg .= "O clã " . $enyguild['name'] . " foi marcado como inimigo.";
            $log1 = $db->execute("select `id` from `players` where `guild`=?", [$guild['id']]);
            while($p1 = $log1->fetchrow()) {
                $logmsg1 = "O clã <a href=\"guild_profile.php?id=". $enyguild['id'] ."\">". $enyguild['name'] ."</a> foi marcado como clã inimigo.";
                addlog($p1['id'], $logmsg1, $db);
            }
            $log2 = $db->execute("select `id` from `players` where `guild`=?", [$enyguild['id']]);
            while($p2 = $log2->fetchrow()) {
                $logmsg2 = "O clã <a href=\"guild_profile.php?id=". $guild['id'] ."\">". $guild['name'] ."</a> foi marcado como clã inimigo.";
                addlog($p2['id'], $logmsg2, $db);
            }
        } else {
            $errmsg .= "Um erro desconhecido ocorreu.";
            $error = 1;
        }
    }
    ?>

<fieldset>
<legend><b><?=$guild['name']?> :: Clãs Inimigos</b></legend>
<p />
<form method="POST" action="guild_admin_enemy.php">
<b>Adicionar o clã:</b> <?php $query = $db->execute("select `id`, `name` from `guilds` where `id`!=?", [$player->guild]);
    echo "<select name=\"gname\"><option value=''>Selecione</option>";
    while($result = $query->fetchrow()) {
        echo "<option value=\"$result[id]\">$result[name]</option>";
    }
    echo "</select>"; ?> <input type="submit" name="submit" value="Adicionar Inimigo">
</form>
</fieldset>
<center><p /><font color=green><?=$msg?></font><p /></center>
<center><p /><font color=red><?=$errmsg?></font><p /></center>
<br/>
<fieldset>
<legend><b>Gerenciar Inimigos</b></legend>
<?php
    $query0000 = $db->execute("select `enemy_na` from `guild_enemy` where `guild_na`=? order by `enemy_na` asc", [$guild['id']]);

    if ($query0000->recordcount() < 1) {
        echo "<p /><center><b>Seu clã não possui inimigos.</b></center><p />";
    } else {
        echo "<p />";
        while($ali = $query0000->fetchrow()) {
            $whileechoname = $db->GetOne("select `name` from `guilds` where `id`=?", [$ali[\ENEMY_NA]]);
            echo "<b>Clã: <a href=\"guild_profile.php?id=" . $ali[\ENEMY_NA] . "\">" . $whileechoname . "</b></a> - <a href=\"guild_admin_enemy.php?unenemy=true&enemy_na=" . $ali[\ENEMY_NA] . "\">Promover Paz</a> - <s>Proclamar Guerra</s>(em breve)";
        }
        echo "<p />";
    }
    ?>
</fieldset>
<?php
}

include(__DIR__ . "/templates/private_footer.php");
?>