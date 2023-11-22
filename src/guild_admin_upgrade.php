<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Administração do Clã");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkguild.php");

$price = 950000;
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
if ($player->username != $guild['leader'] && $player->username != $guild['vice']) {
    echo "<p />Você não pode acessar esta página.<p />";
    echo "<a href=\"home.php\">Principal</a><p />";
} else {

    if ($_GET['upgrade'] == \MAXPLAYERS) {
        if ($guild['maxmembers'] > 49) {
            $errmsg .= "<center><b>Você não pode adicionar mais vagas para o clã.</b></center>";
            $error = 1;
        } elseif ($price > $guild['gold']) {
            $errmsg .= "<center><b>Seu clã não possui ouro suficiente. (Preço: " . $price . ")</b></center>";
            $error = 1;
        }
        if ($error == 0) {
            $query = $db->execute("update `guilds` set `maxmembers`=?, `gold`=? where `id`=?", [$guild['maxmembers'] + 10, $guild['gold'] - $price, $guild['id']]);
            $msg .= "<center><b>Agora seu clã pode possuir " . ($guild['maxmembers'] + 10) . " membros.</b></center>";
        }
    }

    ?>
<?=$msg?><font color=red><?=$errmsg?></font>
<fieldset>
<legend><b><?=$guild['name']?> :: Melhorias</b></legend>
<br/>
<center><input type="button" VALUE="Adicionar mais 10 vagas para o clã." ONCLICK="window.location.href='guild_admin_upgrade.php?upgrade=maxplayers'"> <b>(Preço: <?=$price?>)</b></center>
<br/>
</fieldset>
<a href="guild_admin.php">Voltar</a>.
<?php
}
include(__DIR__ . "/templates/private_footer.php");
?>