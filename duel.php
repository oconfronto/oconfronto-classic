<?php
include("lib.php");
define("PAGENAME", "Duelos");
$player = check_user($secret_key, $db);
include("checkbattle.php");
include("checkhp.php");
include("checkwork.php");
$customprice = 0;

if($_GET['add']){

} elseif($_GET['deny']) {
	$denyduel = $db->execute("select * from `duels` where `id`=?", array($_GET['deny']));
	if ($denyduel->recordcount() == 0){
        	include("templates/private_header.php");
        	echo "<fieldset><legend><b>Duelo</b></legend>";
        	echo "Convite de duelo não encontrado.";
        	echo "</fieldset>";
		echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        	include("templates/private_footer.php");
		exit;
	}else{
	$denyrow = $denyduel->fetchrow();
	}
		if (($denyrow['owner'] != $player->id) and ($denyrow['rival'] != $player->id)){
        		include("templates/private_header.php");
        		echo "<fieldset><legend><b>Duelo</b></legend>";
        		echo "Convite de duelo não encontrado.";
        		echo "</fieldset>";
			echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        		include("templates/private_footer.php");
			exit;
		}else if ($denyrow['owner'] == $player->id){
			$db->execute("delete from `duels` where `id`=? and `owner`=?", array($_GET['deny'], $player->id));
			$denyacao = "cancelar";
		}else if ($denyrow['rival'] == $player->id){
			$db->execute("update `duels` set `active`='d' where `id`=? and `rival`=?", array($_GET['deny'], $player->id));
			$denyacao = "recusar";
		}

        	include("templates/private_header.php");
		echo "<fieldset><legend><b>Duelo</b></legend>";
        	echo "Você acaba de " . $denyacao . " o duelo.";
        	echo "</fieldset>";
		echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        	include("templates/private_footer.php");
		exit;

} elseif($_POST['submit']){
$futurorival = $db->execute("select `id`, `username`, `bank`, `serv` from `players` where `username`=?", array($_POST['rival']));
$rival = $futurorival->fetchrow();

	if ((!$_POST['rival']) or ($futurorival->recordcount() == 0)){
        include("templates/private_header.php");
        	echo "<fieldset><legend><b>Duelo</b></legend>";
        	echo "Preencha um nome de usuário válido.";
        	echo "</fieldset>";
		echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        	include("templates/private_footer.php");
		exit;
	}

	if ($_POST['prize']){
		if (!is_numeric($_POST['prize'])){
        		include("templates/private_header.php");
        		echo "<fieldset><legend><b>Duelo</b></legend>";
        		echo "O valor da aposta não é válido.";
        		echo "</fieldset>";
			echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        		include("templates/private_footer.php");
        		exit;
		}
		if ($_POST['prize'] < 0){
        		include("templates/private_header.php");
        		echo "<fieldset><legend><b>Duelo</b></legend>";
        		echo "O valor da aposta não é válido.";
        		echo "</fieldset>";
			echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        		include("templates/private_footer.php");
        		exit;
		}
		if ($_POST['prize'] > $player->bank){
        		include("templates/private_header.php");
        		echo "<fieldset><legend><b>Duelo</b></legend>";
        		echo "Você não possui " . $_POST['prize'] . " no banco.";
        		echo "</fieldset>";
			echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        		include("templates/private_footer.php");
        		exit;
		}
		if ($_POST['prize'] > $rival['bank']){
        		include("templates/private_header.php");
        		echo "<fieldset><legend><b>Duelo</b></legend>";
        		echo "Seu rival não possui " . $_POST['prize'] . " no banco.";
        		echo "</fieldset>";
			echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        		include("templates/private_footer.php");
        		exit;
		}
		$customprice = 1;
	}

	if ($player->serv != $rival['serv']){
        	include("templates/private_header.php");
        	echo "<fieldset><legend><b>Duelo</b></legend>";
        	echo "Esse usuário pertence a outro servidor.";
        	echo "</fieldset>";
		echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        	include("templates/private_footer.php");
        	exit;
	}

	if ($player->username == $rival['username']){
        	include("templates/private_header.php");
        	echo "<fieldset><legend><b>Duelo</b></legend>";
        	echo "Você não pode duelar contra você mesmo.";
        	echo "</fieldset>";
		echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        	include("templates/private_footer.php");
        	exit;
	}

	$addjahexists = $db->execute("select `id` from `duels` where ((`owner`=? and `rival`=?) or (`owner`=? and `rival`=?))", array($player->id, $rival['id'], $rival['id'], $player->id));
	if ($addjahexists->recordcount() > 0){
        	include("templates/private_header.php");
        	echo "<fieldset><legend><b>Duelo</b></legend>";
        	echo "Já existe um convite de duelo entre você e " . $rival['username'] . ".";
        	echo "</fieldset>";
		echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
        	include("templates/private_footer.php");
        	exit;
	}

		$insert['owner'] = $player->id;
		$insert['rival'] = $rival['id'];
		if ($customprice == 1){
		$insert['prize'] = $_POST['prize'];
		}
		$insert['time'] = time();
		$insert['active'] = 'w';
		$query = $db->autoexecute('duels', $insert, 'INSERT');

	include("templates/private_header.php");
	echo "<fieldset><legend><b>Duelo</b></legend>";
	echo "Você desafiou " . $rival['username'] . " para um duelo.<br/>";
	echo "Seu rival apenas poderá aceitar o duelo quando ambos estiverem online.<br/>";
	echo "Você não poderá retirar do banco o ouro que apostou até que vença o duelo ou cancele-o.";
	echo "</fieldset>";
	echo"<br/><a href=\"duel.php\">Voltar</a>.</br>";
	include("templates/private_footer.php");
	exit;
}


include("templates/private_header.php");

	echo "<fieldset>\n";
	echo "<legend><b>Duelos</b></legend>\n";
	$procuraseusduelos = $db->execute("select * from `duels` where `owner`=?", array($player->id));
		if ($procuraseusduelos->recordcount() == 0)
		{
		echo "<br/><center><b><font size=\"1\">Você não desafiou ninguém no momento.</font></b></center><br/>";
		}else{

		echo "<table width=\"100%\" border=\"0\">";
		echo "<tr>";
		echo "<th width=\"25%\"><b>Usuário</b></td>";
		echo "<th width=\"10%\"><b>Nível</b></td>";
		echo "<th width=\"30%\"><b>Info</b></td>";
		echo "<th width=\"15%\"><b>Status</b></td>";
		echo "<th width=\"20%\"><b>Opções</b></td>";
		echo "</tr>";

			while($rivalid = $procuraseusduelos->fetchrow())
			{
			$getrivalinfo = $db->execute("select `username`, `level` from `players` where `id`=?", array($rivalid['rival']));
			$rivalinfo = $getrivalinfo->fetchrow();

			echo "<tr>";
			echo "<td><a href=\"profile.php?id=" . $rivalinfo['username'] . "\">" . $rivalinfo['username'] . "</a></td>";
			echo "<td>" . $rivalinfo['level'] . "</td>";

			if ($rivalid['active'] == 'd'){
			echo "<td><font size=\"1\">Convite recusado.</font></td>";
			}else{
			echo "<td><font size=\"1\">Aguardando aceitar convite.</font></td>";
			}

				$checkrivalonline = $db->execute("select * from `online` where `player_id`=?", array($rivalid['rival']));
				if ($checkrivalonline->recordcount() > 0) {
				echo "<td><font size=\"1\">Online</font></td>";
				}else{
				echo "<td><font size=\"1\">Offline</font></td>";
				}

			echo "<td><font size=\"1\"><a href=\"mail.php?act=compose&to=". $rivalinfo['username'] ."\">Mensagem</a><br/><a href=\"duel.php?deny=". $rivalid['id'] ."\">Cancelar Duelo</a></font></td>";
			echo "</tr>";
			}

		echo "</table>";
		}
	echo "</fieldset>";

	echo "<br/><br/>";
	echo "<fieldset>";
	echo "<legend><b>Desafiar Usuário</b></legend>";
	echo "<form method=\"POST\" action=\"duel.php\">";
	echo "<table width=\"100%\">";
	echo "<tr>";
	echo "<td width=\"20%\"><b><font size=\"1\">Usuário:</font></b></td>";
	echo "<td width=\"80%\"><input type=\"text\" name=\"rival\" /></td>";
	echo "</tr><tr>";
	echo "<td width=\"20%\"><b><font size=\"1\">Aposta:</font></b></td>";
	echo "<td width=\"40%\"><input type=\"text\" name=\"prize\" value=\"0\"/></td>";
	echo "<td width=\"40%\"><input type=\"submit\" name=\"submit\" value=\"Desafiar\" /></td></tr>";
	echo "</table>";
	echo "</form></fieldset>";

include("templates/private_footer.php");
?>