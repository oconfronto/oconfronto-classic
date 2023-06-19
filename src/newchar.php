<?php
include("lib.php");
define("PAGENAME", "Novo Personagem");
$acc = check_acc($secret_key, $db);
$escolheper = 44;

$querynumplayers = $db->execute("select `id` from `players` where `acc_id`=?", array($acc->id));

if ($querynumplayers->recordcount() > 19)
{
include("templates/acc_header.php");
echo "<br/><br/><br/><br/><center>Você já atingiu o número máximo de personagens por conta, vinte.<br/>Você não pode mais criar usuários nesta conta. <a href=\"characters.php\">Voltar</a>.</center><br/>";
include("templates/acc_footer.php");
}else{


$msg1 = "<b><font color=\"red\" size=\"1\">";
$msg2 = "<b><font color=\"red\" size=\"1\">";
$msg3 = "<b><font color=\"red\" size=\"1\">";
$error = 0;


if ($_POST['register'])
{

		$pat[0] = "/^\s+/";
		$pat[1] = "/\s{2,}/";
		$pat[2] = "/\s+\$/";
		$rep[0] = "";
		$rep[1] = " ";
		$rep[2] = "";
		$nomedeusuari0 = ucwords(preg_replace($pat,$rep,$_POST['username']));

	//Check if username has already been used
	$query = $db->execute("select `id` from `players` where `username`=?", array($_POST['username']));
	//Check username
	if (!$_POST['username']) { //If username isn't filled in...
		$msg1 .= "Você precisa digitar um nome de usuário!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}

	elseif (strlen($nomedeusuari0) < 3)
	{ //If username is too short...
		$msg1 .= "Seu nome de usuário deve ter mais que 2 caracteres!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if (strlen($nomedeusuari0) > 20)
	{ //If username is too short...
		$msg1 .= "Seu nome de usuário deve ser de 20 caracteres ou menos!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if (!preg_match("/^[A-Za-z[:space:]\-]+$/", $_POST['username']))
	{ //If username contains illegal characters...
		$msg1 .= "Seu nome de usuário não pode conter <b>números</b> ou <b>caracteres especiais</b>!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if ($query->recordcount() > 0)
	{
		$msg1 .= "Este nome de usuário já está sendo usado!<br />\n";
		$error = 1; //Set error check
	}

	if ($_POST['voc'] == none)
	{
		$msg2 .= "<br/>Você precisa escolher uma vocação!";
		$error = 1;
	}

	if (($_POST['voc'] != 'archer') and ($_POST['voc'] != 'knight') and ($_POST['voc'] != 'mage') and ($_POST['voc'] != 'none')){
	$could_not_register = "<center><font size=\"1\">Desculpe, ocorreu um erro desconhecido! Tente novamente.</font></center><br /><br />";
	$error = 1; //Set error check
	}

	if ($_POST['serv'] == none)
	{
		$msg3 .= "<br/>Você precisa escolher um servidor!";
		$error = 1;
	}

	if (($_POST['serv'] != 1) and ($_POST['serv'] != 2) and ($_POST['serv'] != 'none')){
	$could_not_register = "<center><font size=\"1\">Desculpe, ocorreu um erro desconhecido! Tente novamente.</font></center><br /><br />";
	$error = 1; //Set error check
	}

	
	if ($error == 0)
	{
		$pat[0] = "/^\s+/";
		$pat[1] = "/\s{2,}/";
		$pat[2] = "/\s+\$/";
		$rep[0] = "";
		$rep[1] = " ";
		$rep[2] = "";
		$nomedeusuario = ucwords(preg_replace($pat,$rep,$_POST['username']));
		$nomedeusuario2 = ucwords($_POST['username']);

		$insert['acc_id'] = $acc->id;
		$insert['username'] = $nomedeusuario;
		$insert['registered'] = time();
		$insert['last_active'] = time();
		$insert['ip'] = $_SERVER['REMOTE_ADDR'];
		$insert['voc'] = $_POST['voc'];
		$insert['serv'] = $_POST['serv'];
		$query = $db->autoexecute('players', $insert, 'INSERT');

		$playerid = $db->execute("select `id` from `players` where `username`=?", array($nomedeusuario));
		$player = $playerid->fetchrow();

		if ($_POST['voc'] == 'archer'){
		$insert['player_id'] = $player['id'];
		$insert['item_id'] = 81;
		$insert['status'] = equipped;
		$query = $db->autoexecute('items', $insert, 'INSERT');
		}
		elseif ($_POST['voc'] == 'knight'){
		$insert['player_id'] = $player['id'];
		$insert['item_id'] = 8;
		$insert['status'] = equipped;
		$query = $db->autoexecute('items', $insert, 'INSERT');
		}
		elseif ($_POST['voc'] == 'mage'){
		$insert['player_id'] = $player['id'];
		$insert['item_id'] = 92;
		$insert['status'] = equipped;
		$query = $db->autoexecute('items', $insert, 'INSERT');
		}
		
		$numpots = 5;
		$playerpots = $player['id'];
		$addpots = "INSERT INTO items (player_id, item_id) VALUES";
			for ($i = 0; $i < $numpots; $i++)
			{
				$addpots .= "($playerpots, 136)" . (($i == $numpots - 1) ? "" : ", ");
			}
		$result = mysql_query($addpots);

		$insert['player_id'] = $player['id'];
		$insert['magia_id'] = 4;
		$query = $db->autoexecute('magias', $insert, 'INSERT');

		if (!$query)
		{
			$could_not_register = "<center><font size=\"1\">Desculpe, ocorreu um erro desconhecido! Tente novamente.</font></center><br /><br />";
		}
		else
		{		
			include("templates/acc_header.php");
			echo "<br/><br/><br/>";
			if ($nomedeusuario != $nomedeusuario2){
			$alerta2 = "<b>Atenção</b>: Seu nome de usuário foi alterado para " . $nomedeusuario . ".<br/><br/>";
			}

			echo "<center>Parabéns! Seu personagem foi criado com sucesso!<br />";
			echo "<a href=\"login.php?id=" . $player['id'] . "\">Clique aqui</a> e começe a jogar com " . $nomedeusuario . ".</center>";
			include("templates/acc_footer.php");
  			exit;
		}
	}
	}

$msg1 .= "</font></b>";
$msg2 .= "</font></b>";
$msg3 .= "</font></b>";

include("templates/acc_header.php");
echo "<br/><br/><br/>";

?>
<?include("box.php");?>
<?=$could_not_register?>
<form method="POST" action="newchar.php">
<table width="90%" align="center" border=\"0\">
<tr><td width="10%"><b>Nome</b>:</td><td width="45%"><input type="text" name="username" id="username" value="<?=$_POST['username'];?>" size=\"20\"/><span id="msgbox" style="display:none"></span></td><td width="15%"><b>Vocação</b>:</td><td width="30%"><select name="voc" onchange="swapText(this)"><option value="none" selected="selected">Selecione</option><option value="knight">Guerreiro</option><option value="mage">Mago</option><option value="archer">Arqueiro</option></select></td></tr>
<tr><td colspan="2"><font size="1"><u>Números</u> e <u>caracteres especiais</u> não são permitidos.<br /></font><?=$msg1;?><br /></td><td colspan="2"><font size="1"><div id="textDiv">Escolha sua vocação.</font><?=$msg2;?></div></td></tr></table>
<table width="90%" align="center" border=\"0\">
<tr><td width="55%"><div id="servDiv"><font size=\"1\">Selecione um servidor para obter informações.</font></div></td><td width="45%"><table width="90%"><tr><td width="15%"><b>Servidor</b>:</td><td width="30%"><select name="serv" onchange="mudaText(this)"><option value="none" selected="selected">Selecione</option><option value="1">Servidor I</option><option value="2">Servidor II</option></select></td></tr><tr><td colspan="2"><font size="1">Escolha um servidor.</font><?=$msg3;?></div></td></tr></table></td></tr>
</table>
<br/>
<center><input type="submit" name="register" value="Criar Personagem"></center>
</form>


<?php
include("templates/acc_footer.php");
}
?>