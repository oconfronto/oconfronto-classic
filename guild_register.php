<?php

/*************************************/
/*           ezRPG script            */
/*      Written by Zen + Khashul     */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Criar Clã");
$player = check_user($secret_key, $db);
include("checkbattle.php");
include("checkguild.php");

$error = 0;
$goldcost = 200000;


if ($player->guild != NULL) {
    include("templates/private_header.php");
    echo "Você não pode ter mais de um clã.";
    echo "<br/><a href=\"home.php\">Voltar</a>.";
    include("templates/private_footer.php");
    exit;
}

if ($player->gold < $goldcost) {
    include("templates/private_header.php");
    echo "Você não tem ouro suficiente para criar um clã. Você precisa de " . $goldcost . " de ouro.";
    echo "<br/><a href=\"home.php\">Voltar</a>.";
    include("templates/private_footer.php");
    exit;
}

if ($_POST['register']) {

$msg1 = "<font color=\"red\">";
$msg2 = "<font color=\"red\">";
$msg3 = "<font color=\"red\">";
$msg4 = "<font color=\"red\">";

    $query = $db->execute("select `id` from `guilds` where `name`=? and `serv`=?", array($_POST['name'], $player->serv));

		$pat[0] = "/^\s+/";
		$pat[1] = "/\s{2,}/";
		$pat[2] = "/\s+\$/";
		$rep[0] = "";
		$rep[1] = " ";
		$rep[2] = "";
		$nomedecla = ucwords(preg_replace($pat,$rep,$_POST['name']));

    $query2 = $db->execute("select `id` from `guilds` where `name`=? and `serv`=?", array($nomedecla, $player->serv));

    if (!$_POST['name']) {
        //Add to error message
        $msg1 .= "Você precisa digitar um nome para o clã!<br />\n";
        $error = 1;
    } else if (strlen($_POST['name']) < 3) {
        //Add to error message
        $msg1 .= "O nome do seu clã deve ser maior que 3 caracteres!<br />\n";
        $error = 1;
    } else if (strlen($_POST['name']) > 25) {
        //Add to error message
        $msg1 .= "O nome do seu clã não pode ser maior que 25 caracteres!<br />\n";
        $error = 1; 
    } else if (!preg_match("/^[A-Za-z[:space:]\-]+$/", $_POST['name']))
	{
	$msg1 .= "O nome de seu clã não pode conter <b>caracteres especiais!<br />\n";
	$error = 1; //Set error check
    } else if ($query->recordcount() > 0) {
        $msg1 .= "Este nome já está sendo usado.<br />\n";
        //Set error check
        $error = 1;
    } else if ($query2->recordcount() > 0) {
        $msg1 .= "Este nome já está sendo usado.<br />\n";
        //Set error check
        $error = 1;
    }

    if (!$_POST['tag']) {
        //Add to error message
        $msg2 .= "Você precisa digitar uma tag para o clã!<br />\n";
        $error = 1;
    } else if (strlen($_POST['tag']) < 2) {
        $msg2 .= "A tag do seu clã deve conter de 2 à 4 caracteres!<br />\n";
        //Set error check
        $error = 1;
	} else if (strlen($_POST['tag']) > 4) {
        $msg2 .= "A tag do seu clã deve conter de 2 à 4 caracteres!<br />\n";
        //Set error check
        $error = 1;  
    } else if (!preg_match("/^[-_a-zA-Z0-9]+$/", $_POST['tag'])) {
	$msg2 .= "A tag do seu clã não pode conter <b>caracteres especiais!<br />\n";
	$error = 1; //Set error check
    }

    if (!$_POST['motd']) {
        //Add to error message
        $msg3 .= "Você precisa digitar uma mensagem para o clã!<br />\n";
        $error = 1;
    } else if (strlen($_POST['motd']) < 5) {
        $msg3 .= "A mensagem do seu clã deve conter de 5 à 50 caracteres!<br />\n";
        //Set error check
        $error = 1;
	} else if (strlen($_POST['motd']) > 50) {
        $msg3 .= "A mensagem do seu clã deve conter de 5 à 50 caracteres!<br />\n";
        //Set error check
        $error = 1;  
    }

    if (!$_POST['blurb']) {
        //Add to error message
        $msg4 .= "Você precisa digitar uma descrição para o clã!<br />\n";
        $error = 1;
    } else if (strlen($_POST['tag']) > 5000) {
        $msg4 .= "A descrição do seu clã passou de 5000 caracteres!<br />\n";
        //Set error check
        $error = 1;  
    }

if ($error == 0) {
		$pat[0] = "/^\s+/";
		$pat[1] = "/\s{2,}/";
		$pat[2] = "/\s+\$/";
		$rep[0] = "";
		$rep[1] = " ";
		$rep[2] = "";
		$nomedecla = ucwords(preg_replace($pat,$rep,$_POST['name']));

    $removehtmlmtd=strip_tags($_POST['motd']);
    $insert['name'] = $nomedecla;
    $insert['tag'] = $_POST['tag'];
    $insert['leader'] = $player->username;
    $insert['motd'] = ($removehtmlmtd);
    $tirahtmldades=strip_tags($_POST['blurb']);
    $texto=nl2br($tirahtmldades);
$listaExtensao = array('JPG' => 1, 'jpg' => 2, 'PNG' => 3, 'png' => 4, 'BMP' => 5, 'bmp' => 6, 'GIF' => 7, 'gif' => 8);
$aux = " " . $texto . "";


	while(true){
	$inicioImg = 0;

	$inicioImg = strpos($aux ,'[img]');
	
	if($inicioImg < 1) {
		break;
	}
	
	$fimImg = strpos($aux ,'[/img]');
	$tamanho = strlen($aux);
	$parteAnterior = substr($aux , 0, $inicioImg);
	$partePosterior = substr($aux , $fimImg+6, $tamanho-1);
	$parteLink = substr($aux ,  $inicioImg, $fimImg-$inicioImg+6);
	$extensao = substr($parteLink, strlen($parteLink)-9,3);

	if(!array_key_exists($extensao, $listaExtensao)){
		$parteLink  = '[IMG REMOVIDA]';
	}
	$textoFinal = $textoFinal.$parteAnterior.$parteLink;
	$aux = $partePosterior;


	}
	$mostraimg = $textoFinal . "" . $aux;
	$mostraimg = substr($mostraimg, 1);


    $insert['blurb'] = $mostraimg;
    $insert['pagopor'] = (time() + 950400);
    $insert['serv'] = $player->serv;
    $query = $db->autoexecute('guilds', $insert, 'INSERT');
    
        $insertid = $db->Insert_ID();
        $query = $db->execute("update `players` set `guild`=?, `gold`=? where `id`=?", array($insertid, $player->gold - $goldcost, $player->id));
        
	include("templates/private_header.php");
	echo "Parabéns! Você acaba de criar um novo clã!";
	echo "<br/><a href=\"home.php\">Voltar</a>.";
	include("templates/private_footer.php");
	exit;
	}

//Username error
$msg1 .= "</font>";
$msg2 .= "</font>";
$msg3 .= "</font>";
$msg4 .= "</font>";

}


include("templates/private_header.php");
?>
<script type="text/javascript" src="bbeditor/ed.js"></script>

<form method="POST" action="guild_register.php">
<center><b>Atenção:</b> Criar um clã custa <?=$goldcost?> de ouro.</center>
<table width="100%">
<tr><td width="30%"><span class="style1"><b>Nome do clã</b>:</span></td>
<td><input name="name" type="text" value="<?=$_POST['name'];?>" /></td>
</tr>
<tr><td colspan="2"><span class="style1">Insira o nome desejado para o clã.<br />
<?=$msg1;?>
<br />
</span></td>
</tr>

<tr><td width="30%"><span class="style1"><b>Tag do clã</b>:</span></td>
<td><input name="tag" type="text" value="<?=$_POST['tag'];?>" /></td>
</tr>
<tr><td colspan="2"><span class="style1">A sigla de seu clã.<br />
<?=$msg2;?>
<br />
</span></td>
</tr>

<input name="leader" type="hidden" value="<?=$player->username?>" /></td>

<tr><td width="30%"><span class="style1"><b>Mensagem do clã</b>:</span></td>
<td><input name="motd" type="motd" value="<?=$_POST['motd'];?>" /></td>
</tr>
<tr><td colspan="2"><span class="style1">Digite a mensagem desejada para seu clã.<br />
<?=$msg3;?>
<br />
</span></td>
</tr>

<tr><td width="30%"><span class="style1"><b>Descrição do clã</b>:</span></td>
<td><script>edToolbar('blurb'); </script><textarea name="blurb" id="blurb" rows="12" class="ed"><?=$_POST['blurb'];?></textarea><br>Máximo 5000 caracteres.<br />
<?=$msg4;?>
<br />
</td>
</tr>

<tr>
<td colspan="2" align="center"><span class="style2">
<br><input type="submit" name="register" value="Criar Clã"></td>
</span><br />
</tr>
</table>
</form>
<p />

<?php include("templates/private_footer.php");?>