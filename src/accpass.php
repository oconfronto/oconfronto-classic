<?php
	include(__DIR__ . "/lib.php");
	define("PAGENAME", "Alterar Senha");
	include(__DIR__ . "/templates/acc_header.php");

	$acc = check_acc($secret_key, $db);

$sucess1 = 0;
$sucess2 = 0;

	echo "<br/><br/><br/>";

if ($_POST['changepassword']) {
    //Check password
    if (!$_POST['password']) {
        $errmsg .= "Você precisa preencher todos os campos!";
        $error = 1;
    } elseif (!$_POST['password2']) {
        $errmsg .= "Você precisa preencher todos os campos!";
        $error = 1;
    } elseif (!$_POST['oldpassword']) {
        $errmsg .= "Você precisa preencher todos os campos!";
        $error = 1;
    } elseif ($acc->password != sha1((string) $_POST['oldpassword'])) {
        $errmsg .= "Sua senha atual está incorreta!";
        $error = 1;
    } elseif ($_POST['password'] != $_POST['password2']) {
        $errmsg .= "Você não digitou as duas senhas corretamente!";
        $error = 1;
    } elseif (strlen((string) $_POST['password']) < 4) {
        $errmsg .= "Sua senha deve ter mais que 3 caracteres.";
        $error = 1;
    }
    if ($error == 0) {
		$insert['player_id'] = $acc->id;
		$insert['msg'] = "Você alterou a senha de sua conta.";
		$insert['time'] = time();
		$query = $db->autoexecute('account_log', $insert, 'INSERT');

        $query = $db->execute("update `accounts` set `password`=? where `id`=?", [sha1((string) $_POST['password']), $acc->id]);
        $msg .= "Você trocou sua senha.";
	$sucess1 = 1;
    }
}



if ($sucess1 == 0){
?>
<fieldset>
<p /><legend><b>Alterar senha da conta</b></legend><p />
<table width="100%">
<form method="POST" action="accpass.php">
<tr><td width="25%"><b>Senha atual</b>:</td><td><input type="password" name="oldpassword" size="20"/></td></tr>
<tr><td width="25%"><b>Nova senha</b>:</td><td><input type="password" name="password" size="20"/></td></tr>
<tr><td width="25%"><b>Digite novamente</b>:</td><td><input type="password" name="password2" size="20"/></td></tr>
<tr><td colspan="2" align="center"><input type="submit" name="changepassword" value="Alterar"></td></tr>
</table>
</form>
<p /><font color=red><?=$errmsg?></font><p />
</fieldset>
<br/>
<a href="characters.php">Voltar</a>.
<?php
}else{
echo "<center><b>Sucesso</b><br/>" . $msg . " <a href=\"characters.php\">Voltar</a>.</center>";
}

	include(__DIR__ . "/templates/acc_footer.php");

?>