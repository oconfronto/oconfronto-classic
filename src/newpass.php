<?php
include("lib.php");
define("PAGENAME", "Recuperar senha");


$email=$_GET['email'];
$string=$_GET['string'];
$email=trim($email); //trims whitespace
$email=strip_tags($email); //strips out possible HTML
$string=trim($string);
$string=strip_tags($string);

srand((double)microtime()*1000000);  //sets random seed
$newstring = md5(rand(0,1000000));


		$real = $db->execute("select `id`, `validkey` from `accounts` where `email`=? and `validkey`=?", array($email, $string));
		if ($real->recordcount() == 0)
		{
			include("templates/header.php");
			echo "Endereço inválido ou antigo. <a href=\"index.php\">Voltar</a>.";
			include("templates/footer.php");
			exit;
		}
			
		if (!$_GET['email'] or !$_GET['string'] or !$_GET['email'] & !$_GET['string'])
		{
			include("templates/header.php");
			echo "Endereço inválido ou antigo. <a href=\"index.php\">Voltar</a>.";
			include("templates/footer.php");
			exit;
                }
		else
		{	
			include("templates/header.php");

			$newpassword=rand(10000, 100000);
			$newpasswordcoded=sha1($newpassword);

			$query = $db->execute("update `accounts` set `password`=?, `validkey`=? where `email`=? and `validkey`=?", array($newpasswordcoded, $newstring, $email, $string));

		$memberto = $real->fetchrow();

		$insert['player_id'] = $memberto['id'];
		$insert['msg'] = "Você recuperou a senha de sua conta pelo seu email.";
		$insert['time'] = time();
		$query = $db->autoexecute('account_log', $insert, 'INSERT');

	$subject = "Sua nova senha - O Confronto";
	$message = "Sua nova senha foi gerada com sucesso, ela é: $newpassword\n\n -> oconfronto.kinghost.net";
	$headers = "From: no-reply@oconfronto.kinghost.net";
      	mail( $email, $subject, $message, $headers );

			echo "Sua nova senha foi gerada com sucesso, ela é: " . $newpassword . ".<br/>";
			echo "Para que você não se esqueça novamente, sua nova senha foi enviada para seu email. <a href=\"index.php\">Voltar</a>.";
			include("templates/footer.php");
			exit;	
		}
	

?>