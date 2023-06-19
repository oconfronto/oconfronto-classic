<?php
include("lib.php");
define("PAGENAME", "Novo Personagem");
$acc = check_acc($secret_key, $db);

$querynumplayers = $db->execute("select `id` from `players` where `acc_id`=?", array($acc->id));

if ($querynumplayers->recordcount() > 5)
{
include("templates/acc_header.php");
echo "<br/><br/><br/><br/><center>Você já atingiu o número máximo de personagens por conta, cinco.<br/>Você não pode mais criar personagens nesta conta. <a href=\"characters.php\">Voltar</a>.</center><br/>";
include("templates/acc_footer.php");
}else{


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


	$check1 = $db->execute("select `id` from `players` where `username`=?", array($nomedeusuari0));

	if ((!$_POST['username']) or (!$_POST['voc'])) {
		$errormsg = "Você precisa preencher todos os campos";
		$error = 1;
	}

	elseif (strlen($nomedeusuari0) < 3)
	{
		$errormsg = "Seu nome de usuário deve ter mais que 2 caracteres!";
		$error = 1;
	}
	else if (strlen($nomedeusuari0) > 20)
	{
		$errormsg = "Seu nome de usuário deve ser de 20 caracteres ou menos!";
		$error = 1;
	}
	else if (!preg_match("/^[A-Za-z[:space:]\-]+$/", $_POST['username']))
	{
		$errormsg = "Seu nome de usuário não pode conter <b>números</b> ou <b>caracteres especiais</b>!";
		$error = 1;
	}
	else if ($check1->recordcount() > 0)
	{
		$errormsg = "Este nome de usuário já está sendo usado!";
		$error = 1;
	}

	else if ($_POST['voc'] == none)
	{
		$errormsg = "Você precisa escolher uma vocação!";
		$error = 1;
	}

	else if (($_POST['voc'] != 'archer') and ($_POST['voc'] != 'knight') and ($_POST['voc'] != 'mage') and ($_POST['voc'] != 'none')){
		$errormsg = "Você precisa escolher uma vocação!";
		$error = 1;
	}
	
	if ($error == 0)
	{
		$insert['acc_id'] = $acc->id;
		$insert['username'] = $nomedeusuari0;
		$insert['registered'] = time();
		$insert['last_active'] = time();
		$insert['ip'] = $_SERVER['REMOTE_ADDR'];
		$insert['voc'] = $_POST['voc'];
		$insert['serv'] = 1;
		$addplayer = $db->autoexecute('players', $insert, 'INSERT');

		$playerid = $db->execute("select `id` from `players` where `username`=?", array($nomedeusuari0));
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
		

		if (!$addplayer)
		{
			$errormsg = "Ocorreu um erro em nosso servidor, tente novamente.";
			$error = 1;
		}
		else
		{		
			header("Location: account.php");
  			exit;
		}
	}
}


include("includes/header.php");

?>

<Script Language=JavaScript>
var nText = new Array()
nText[0] = "Choose your vocation.";
nText[1] = "Archers have a great atack but a poor defense.";
nText[2] = "Knights have a great defense but a poor attack.";
nText[3] = "Mages have atack and defense balanced."
function swapText(isList){
txtIndex = isList.selectedIndex;
document.getElementById('textDiv').innerHTML = nText[txtIndex];
}

</Script>

<div id="topbar" class="transparent">
	<div id="title"><?php echo PAGENAME;?></div>
	<div id="leftnav"><a href="account.php">Account</a></div>
	</div>
<div id="content">
	<form method="post" action="newchar.php">
		<span class="graytitle">Character Name</span>
		<ul class="pageitem">
			<li class="bigfield"><input placeholder="<?php echo $lang['username']; ?>" type="text" name="username" value="<?php echo $_POST['username']; ?>"/></li>
		</ul>

		<span class="graytitle">Vocation</span>
		<ul class="pageitem">
			<li class="select"><select name="voc" onchange="swapText(this)">
			<option value="none">Select</option>
			<option value="archer">Archer</option>
			<option value="knight">Knight</option>
			<option value="mage">Mage</option>
			</select><span class="arrow"></span></li>
			<li class="textbox"><div id="textDiv" align="center">Choose your vocation.</div></li>
		</ul>

		<ul class="pageitem">
		<li class="button">
			<input name="register" type="submit" value="<?php echo $lang['submit'];?>" /></li>
		</ul>
	</form>
</div>

<?php 
if ($error > 0){
	echo"<script language=\"JavaScript\" type=\"text/javascript\">";
	echo"alert (\"" . $errormsg . "\")";
	echo"</script>";
}

include("includes/footer.php");
}

?>