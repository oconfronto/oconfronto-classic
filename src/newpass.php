<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Recuperar senha");


$email=$_GET['email'];
$string=$_GET['string'];
$email=trim((string) $email); //trims whitespace
$email=strip_tags($email); //strips out possible HTML
$string=trim((string) $string);
$string=strip_tags($string);

mt_srand((double)microtime()*1_000_000);  //sets random seed
$newstring = md5(random_int(0,1_000_000));


		$real = $db->execute("select `id`, `validkey` from `accounts` where `email`=? and `validkey`=?", [$email, $string]);
		if ($real->recordcount() == 0)
		{
			include(__DIR__ . "/templates/header.php");
			echo "Endereço inválido ou antigo. <a href=\"index.php\">Voltar</a>.";
			include(__DIR__ . "/templates/footer.php");
			exit;
		}
			
		if (!$_GET['email'] || !$_GET['string'] || !$_GET['email'] & !$_GET['string'])
		{
			include(__DIR__ . "/templates/header.php");
			echo "Endereço inválido ou antigo. <a href=\"index.php\">Voltar</a>.";
			include(__DIR__ . "/templates/footer.php");
			exit;
                }
  include(__DIR__ . "/templates/header.php");
  $newpassword=random_int(10000, 100000);
  $newpasswordcoded=sha1((string) $newpassword);
  $query = $db->execute("update `accounts` set `password`=?, `validkey`=? where `email`=? and `validkey`=?", [$newpasswordcoded, $newstring, $email, $string]);
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
  include(__DIR__ . "/templates/footer.php");
  exit;
	

?>
