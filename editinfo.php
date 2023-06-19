<?php
	include("lib.php");
	define("PAGENAME", "Informações Pessoais");
	$acc = check_acc($secret_key, $db);

	include("templates/acc_header.php");

$error = 0;
$checknocur = $db->execute("select * from `other` where `value`=? and `player_id`=?", array(cursor, $acc->id));
$checkshowmail = $db->execute("select * from `other` where `value`=? and `player_id`=?", array(showmail, $acc->id));

if ($_POST['submit']) {
    if (!$_POST['rlname'] | !$_POST['showmail'] | !$_POST['showcur'] | !$_POST['remember'] | !$_POST['sex']) {
        $errmsg .= "Por favor preencha todos os campos!";
        $error = 1;
	}

	else if (strlen($_POST['rlname']) < 3) {
        $errmsg .= "Seu nome deve ter mais que três caracteres!";
        $error = 1;
	}

	else if (($_POST['showmail'] != 1) and ($_POST['showmail'] != 2)) {
        $errmsg .= "Um erro desconhecido ocorreu.";
        $error = 1;
	}

	else if (($_POST['showcur'] != 1) and ($_POST['showcur'] != 2)) {
        $errmsg .= "Um erro desconhecido ocorreu.";
        $error = 1;
	}

	else if (($_POST['remember'] != 1) and ($_POST['remember'] != 2)) {
        $errmsg .= "Um erro desconhecido ocorreu.";
        $error = 1;
	}

	else if (($_POST['sex'] != 1) and ($_POST['sex'] != 2) and ($_POST['sex'] != 3)) {
        $errmsg .= "Um erro desconhecido ocorreu.";
        $error = 1;

    }
    if ($error == 0) {

	if ($_POST['sex'] == 2){
	$sexx = "m";
	}elseif ($_POST['sex'] == 3){
	$sexx = "f";
	}else{
	$sexx = "n";
	}

	if ($_POST['remember'] == 2){
	$rememberr = "t";
	}else{
	$rememberr = "f";
	}

	if ($_POST['showmail'] == 2){
		if ($checkshowmail->recordcount() < 1) {
		$insert['player_id'] = $acc->id;
		$insert['value'] = showmail;
		$insertchecknocur = $db->autoexecute('other', $insert, 'INSERT');
		}
	}else{
	$deletechecknocur = $db->execute("delete from `other` where `value`=? and `player_id`=?", array(showmail, $acc->id));
	}

	if ($_POST['showcur'] == 2){
		if ($checknocur->recordcount() < 1) {
		$insert['player_id'] = $acc->id;
		$insert['value'] = cursor;
		$insertchecknocur = $db->autoexecute('other', $insert, 'INSERT');
		}
	}else{
	$deletechecknocur = $db->execute("delete from `other` where `value`=? and `player_id`=?", array(cursor, $acc->id));
	}

        $query = $db->execute("update `accounts` set `name`=?, `sex`=?, `remember`=? where `id`=?", array($_POST['rlname'], $sexx, $rememberr, $acc->id));
        $msg .= "Informações pessoais alteradas com sucesso!";
    }
}

$acc = check_acc($secret_key, $db);
$checknocur = $db->execute("select * from `other` where `value`=? and `player_id`=?", array(cursor, $acc->id));
$checkshowmail = $db->execute("select * from `other` where `value`=? and `player_id`=?", array(showmail, $acc->id));
?>
<br/><br/><br/>
<fieldset>
<p /><legend><b>Informações pessoais</b></legend><p />
<table width="100%">
<form method="POST" action="editinfo.php">
<tr><td width="40%"><b>Nome real</b>:</td><td><input type="text" name="rlname" value="<?=$acc->name?>" size="20"/></td></tr>
<?php
if ($acc->sex == "m"){
echo "<tr><td width=\"40%\"><b>Sexo</b>:</td><td><select name=\"sex\"><option value=\"1\">Selecione</option><option value=\"2\" selected=\"selected\">Masculino</option><option value=\"3\">Feminino</option></select></td></tr>";
}elseif ($acc->sex == "f"){
echo "<tr><td width=\"40%\"><b>Sexo</b>:</td><td><select name=\"sex\"><option value=\"1\">Selecione</option><option value=\"2\">Masculino</option><option value=\"3\" selected=\"selected\">Feminino</option></select></td></tr>";
}else{
echo "<tr><td width=\"40%\"><b>Sexo</b>:</td><td><select name=\"sex\"><option value=\"1\" selected=\"selected\">Selecione</option><option value=\"2\">Masculino</option><option value=\"3\">Feminino</option></select></td></tr>";
}

if ($checkshowmail->recordcount() < 1){
echo "<tr><td width=\"40%\"><b>Mostrar email</b>:</td><td><select name=\"showmail\"><option value=\"1\" selected=\"selected\">Não</option><option value=\"2\">Sim</option></select> <font size=\"1\">" . $acc->email . " - <a href=\"changemail.php\">Alterar Email</a></font></td></tr>";
}else{
echo "<tr><td width=\"40%\"><b>Mostrar email</b>:</td><td><select name=\"showmail\"><option value=\"1\">Não</option><option value=\"2\" selected=\"selected\">Sim</option></select> <font size=\"1\">" . $acc->email . " - <a href=\"changemail.php\">Alterar Email</a></font></td></tr>";
}

if ($acc->remember != t){
echo "<tr><td width=\"40%\"><b>Lembrar Senha</b>:</td><td><select name=\"remember\"><option value=\"1\" selected=\"selected\">Não</option><option value=\"2\">Sim</option></select> <font size=\"1\">(entrar automatiamente ao visitar o jogo).</font></td></tr>";
}else{
echo "<tr><td width=\"40%\"><b>Lembrar Senha</b>:</td><td><select name=\"remember\"><option value=\"1\">Não</option><option value=\"2\" selected=\"selected\">Sim</option></select> <font size=\"1\">(entrar automatiamente ao visitar o jogo).</font></td></tr>";
}

if ($checknocur->recordcount() < 1){
echo "<tr><td width=\"40%\"><b>Cursor customisado</b>:</td><td><select name=\"showcur\"><option value=\"1\" selected=\"selected\">Não</option><option value=\"2\">Sim</option></select></td></tr>";
}else{
echo "<tr><td width=\"40%\"><b>Cursor customisado</b>:</td><td><select name=\"showcur\"><option value=\"1\">Não</option><option value=\"2\" selected=\"selected\">Sim</option></select></td></tr>";
}
?>
<tr><td colspan="2" align="center"><input type="submit" name="submit" value="Enviar"></td></tr>
</table>
</form>
<p /><font color=green><?=$msg?></font><p />
<p /><font color=red><?=$errmsg?></font><p />
</fieldset>
<br/>
<a href="characters.php">Voltar</a>.

<?php
	include("templates/acc_footer.php");
?>