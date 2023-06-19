<?php
include("lib.php");
define("PAGENAME", "Recuperar senha");
$email1 = $_POST['email1']; 
include("templates/header.php");

if(isset($_POST['submit']))
{

if ((!$_POST['username']) or (!$_POST['email'])){
print"Preencha todos os campos. <a href='forgot.php'>Voltar</a>.";
include("templates/footer.php");
exit;
}
   
	$query = $db->execute("select * from `accounts` where `email`=? and `conta`=?", array($_POST['email'], $_POST['username']));
	if ($query->recordcount() != 1) {
	print"Os dados digitados não conferem. <a href='forgot.php'>Voltar</a>.";
	include("templates/footer.php");
	exit;
	}

	$recu = $query->fetchrow();

	$subject = "Recuperar senha - O Confronto";
	$message = "Você solicitou uma nova senha no Confronto.\nSe quiser uma nova senha, acesse: http://www.oconfronto.kinghost.net/newpass.php?email=" . $recu['email'] . "&string=" . $recu['validkey'] . "\n\n Caso contrário ignore este email.\n\n -> oconfronto.kinghost.net";
	$headers = "From: no-reply@oconfronto.kinghost.net";
      	mail( $recu['email'], $subject, $message, $headers );
      print "Sua senha foi enviada ao seu email. <a href='index.php'>Voltar</a>.";

}
else
{
  print "<fieldset><legend><b>Recuperar senha</b></legend>\n";
  print "<table><form action='forgot.php' method='post'>"; 
  print "<tr><td><b>Conta:</b></td><td><input type='text' name='username' size='20'></td></tr>";
  print "<tr><td><b>Email:</b></td><td><input type='text' name='email' size='25'></td></tr>";
  print "<tr><td></td><td><input type='submit' name='submit' value='Enviar nova senha'></form></td></tr></table>";
  print "</fieldset>";
}
include("templates/footer.php");
?>