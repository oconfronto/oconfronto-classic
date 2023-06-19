<?php

/*************************************/
/*           ezRPG script            */
/*         Written by Khashul        */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.bbgamezone.com/     */
/*************************************/

include("lib.php");
define("PAGENAME", "Tesouro do Clã");
$player = check_user($secret_key, $db);
include("checkbattle.php");

include("checkguild.php");


//Populates $guild variable
$query = $db->execute("select * from `guilds` where `id`=?", array($player->guild));

if ($query->recordcount() == 0) {
    header("Location: home.php");
} else {
    $guild = $query->fetchrow();
}

if (isset($_POST['amount']) && ($_POST['submit'])) {

$amount = floor($_POST['amount']);

    if ($player->gold < $amount ) {
        include("templates/private_header.php");
        echo "Você não pode enviar esta quantia de ouro!<br />";
        echo "<a href=\"guild_treasury.php\">Tentar Novamente</a>.";
        include("templates/private_footer.php");
        exit;
    } else if ($amount < 1) {
        include("templates/private_header.php");
        echo "Você não pode enviar esta quantia de ouro!<br />";
        echo "<a href=\"guild_treasury.php\">Tentar Novamente</a>.";
        include("templates/private_footer.php");
        exit;
    } else if (!is_numeric($amount)) {
        include("templates/private_header.php");
        echo "Você não pode enviar esta quantia de ouro!<br />";
        echo "<a href=\"guild_treasury.php\">Tentar Novamente</a>.";
        include("templates/private_footer.php");
        exit;        
    } else {     
            $quety0 = $db->execute("select `id` from `players` where `username`=?", array($guild['leader']));
            $query = $db->execute("update `guilds` set `gold`=? where `id`=?", array($guild['gold'] + $amount, $player->guild));
            $query1 = $db->execute("update `players` set `gold`=? where `id`=?", array($player->gold - $amount, $player->id));
            include("templates/private_header.php");

            echo "Você transferiu <b>$amount de gold</b> para o clã: " . $guild['name'] . ".<br /><a href=\"guild_home.php\">Voltar</a>.";

    		$lider = $quety0->fetchrow();
    		$logmsg = "<b>$player->username</b> transferiu <b>$amount de gold</b> para o clã.";
		addlog($lider['id'], $logmsg, $db);

		$insert['player_id'] = $player->id;
		$insert['name1'] = $player->username;
		$insert['name2'] = $guild['name'];
		$insert['action'] = "doou";
		$insert['value'] = $amount;
		$insert['aditional'] = "gangue";
		$insert['time'] = time();
		$query = $db->autoexecute('log_gold', $insert, 'INSERT');

		if (($guild['vice'] != '') and ($guild['vice'] != NULL)){
		$quety00 = $db->execute("select `id` from `players` where `username`=?", array($guild['vice']));
    		$vice = $quety00->fetchrow();
    		$logmsg = "<b>$player->username</b> transferiu <b>$amount de gold</b> para o clã.";
		addlog($vice['id'], $logmsg, $db);
		}


            include("templates/private_footer.php");
            exit;
    }
}

include("templates/private_header.php");

?>

<fieldset>
<legend><b><?=$guild['name']?> :: Tranferir Ouro</b></legend>
<form method="POST" action="guild_treasury.php">
<b>Desejo Enviar:</b> <input type="text" name="amount" value="0" size="15"/>
<input type="submit" name="submit" value="Enviar para o Clã"><p />
</form>
<b>Seu clã possui <?=$guild['gold']?> de ouro.</b> O tesouro do seu clã poderá ser usado pelos lideres para adicionar mais vagas no clã, pagas as taixas ou até mesmo para fazer doações aos demais membros do clã.
</fieldset>
<br/>
<a href="guild_home.php">Voltar</a>.

<?php include("templates/private_footer.php");
?>