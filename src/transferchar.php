<?php

	include(__DIR__ . "/lib.php");
	define("PAGENAME", "Login");
	$acc = check_acc($secret_key, $db);

if ($_GET['cancel'])
{
$cancel0 = $db->execute("select * from `pending` where `pending_id`=4 and `pending_other`=?", [$acc->id]);
	if ($cancel0->recordcount() > 0){
	$dileti = $db->execute("delete from `pending` where `pending_id`=4 and `pending_other`=?", [$acc->id]);
	include(__DIR__ . "/templates/acc_header.php");
	echo "<br/><br/><br/><center>Você cancelou a solicitação de transferência de personagem. <a href=\"characters.php\">Voltar</a>.</center><br/>";
	include(__DIR__ . "/templates/acc_footer.php");
	exit;
	}
 include(__DIR__ . "/templates/acc_header.php");
 echo "<br/><br/><br/><center>Nenhuma solicitação de transferência encontrada. <a href=\"characters.php\">Voltar</a>.</center><br/>";
 include(__DIR__ . "/templates/acc_footer.php");
 exit;
}

		$error = 0;

$querynumplayers = $db->execute("select `id` from `players` where `acc_id`=?", [$acc->id]);
if ($querynumplayers->recordcount() > 19)
{
include(__DIR__ . "/templates/acc_header.php");
echo "<br/><br/><br/><br/><center>Você já atingiu o número máximo de personagens por conta, vinte.<br/>Você não pode mais adicionar personagens nesta conta. <a href=\"characters.php\">Voltar</a>.</center><br/>";
include(__DIR__ . "/templates/acc_footer.php");
exit;
}

if (!$_GET['id'])
{
	include(__DIR__ . "/templates/acc_header.php");
	echo "<center>Digite o nome do personagem que você deseja transferir para sua conta.</center><br/><br/>";
	echo "<form method=\"get\" action=\"transferchar.php\"><center><b>Personagem: <input type=\"text\" name=\"id\" size=\"25\"/> <input type=\"submit\" name=\"submit\" value=\"Enviar\"></b></center></form>";
	include(__DIR__ . "/templates/acc_footer.php");
	exit;
}
$query0 = $db->execute("select * from `players` where `username`=?", [$_GET['id']]);
$query1 = $db->execute("select * from `pending` where `pending_id`=4 and `pending_status`=?", [$_GET['id']]);
$query2 = $db->execute("select * from `pending` where `pending_id`=4 and `pending_other`=?", [$char['acc_id']]);
$query3 = $db->execute("select * from `pending` where `pending_id`=4 and `player_id`=?", [$acc->id]);
if ($query0->recordcount() != 1){
	include(__DIR__ . "/templates/acc_header.php");
	echo "<br/><br/><br/><center>Personagem não encontrado. <a href=\"transferchar.php\">Voltar</a>.</center><br/>";
	include(__DIR__ . "/templates/acc_footer.php");
	exit;
	}
$char = $query0->fetchrow();
if ($char['acc_id'] == $acc->id){
	include(__DIR__ . "/templates/acc_header.php");
	echo "<br/><br/><br/><center>Este personagem já pertence a sua conta. <a href=\"characters.php\">Voltar</a>.</center><br/>";
	include(__DIR__ . "/templates/acc_footer.php");
	exit;
	}
if ($query1->recordcount() > 0){
	include(__DIR__ . "/templates/acc_header.php");
	echo "<br/><br/><br/><center>Já existe uma solicitação de transferência pendente com este personagem. <a href=\"characters.php\">Voltar</a>.</center><br/>";
	include(__DIR__ . "/templates/acc_footer.php");
	exit;
	}
if ($query2->recordcount() > 0){
	include(__DIR__ . "/templates/acc_header.php");
	echo "<br/><br/><br/><center>Já existe uma solicitação de transferência pendente com a conta deste personagem. <a href=\"characters.php\">Voltar</a>.</center><br/>";
	include(__DIR__ . "/templates/acc_footer.php");
	exit;
	}
if ($query3->recordcount() > 0){
	include(__DIR__ . "/templates/acc_header.php");
	echo "<br/><br/><br/><center>Já existe uma solicitação de transferência pendente com sua conta. <a href=\"characters.php\">Voltar</a>.</center><br/>";
	include(__DIR__ . "/templates/acc_footer.php");
	exit;
	}
if ($_POST['submit']){
		$cconta = $db->GetOne("select `conta` from `accounts` where `id`=?", [$char['acc_id']]);
		$ccontappassss = $db->GetOne("select `password` from `accounts` where `id`=?", [$char['acc_id']]);

		if (!$_POST['conta'] || !$_POST['senhadaconta']) {
     $errmsg .= "Preencha todos os campos<br/>";
     $error = 1;
 } elseif (!$_POST['transferpass'] && $char['transpass'] != \F) {
     $errmsg .= "Preencha todos os campos<br/>";
     $error = 1;
 } elseif ($_POST['conta'] != $cconta) {
     $errmsg .= "Algum dado preenchido não confere.<br/>";
     $error = 1;
 } elseif (sha1((string) $_POST['senhadaconta']) != $ccontappassss) {
     $errmsg .= "Algum dado preenchido não confere.<br/>";
     $error = 1;
 } elseif ($_POST['transferpass'] != $char['transpass'] && $char['transpass'] != \F) {
     $errmsg .= "Algum dado preenchido não confere.<br/>";
     $error = 1;
 }

		if ($error == 0){

		$insert['player_id'] = $acc->id;
		$insert['pending_id'] = 4;   	  
		$insert['pending_status'] = $char['username'];
		$insert['pending_time'] = (time() + 1_296_000);
		$insert['pending_other'] = $char['acc_id'];
		$query = $db->autoexecute('pending', $insert, 'INSERT');

		include(__DIR__ . "/templates/acc_header.php");
		echo "<br/><br/><br/><center>Você solicitou a tranferência de " . $char['username'] . " para sua conta.<br/>Por motivos de segurança, você terá que aguardar 14 dias para ver " . $char['username'] . " em sua conta. <a href=\"characters.php\">Voltar</a>.</center><br/>";
		include(__DIR__ . "/templates/acc_footer.php");
		exit;
		}

	}
include(__DIR__ . "/templates/acc_header.php");
echo "<br/><br/><br/>";

<fieldset>
<legend><b>Digite as seguintes informações de 
<?=$char['username'];
</b></legend>
<form method="POST" action="transferchar.php?id=
<?=$_GET['id'];
">
<table>
<tr><td width="40%"><b>Conta</b>:</td><td><input type="password" name="conta" value="
<?=$_POST['conta'];
" size="20"/></td></tr>
<tr><td width="40%"><b>Senha da conta</b>:</td><td><input type="password" name="senhadaconta" value="
<?=$_POST['senhadaconta'];
" size="20"/></td></tr>

if ($char['transpass'] != \F){
echo "<tr><td width=\"40%\"><b>Senha de Tranferência</b>:</td><td><input type=\"password\" name=\"transferpass\" value=\"" . $_POST['transferpass'] . "\" size=\"20\"/></td></tr>";
}
<tr><td colspan="2" align="center"><input type="submit" name="submit" value="Transferir 
<?=$char['username'];
 para minha conta"></td></tr>
</table>
</form>
<p /><font color=red>
<?=$errmsg?>
</font><p />
</fieldset>
<br/>
<a href="characters.php">Voltar</a>.


include(__DIR__ . "/templates/acc_footer.php");
exit;
?>
