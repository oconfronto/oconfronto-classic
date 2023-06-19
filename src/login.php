<?php
include("lib.php");
define("PAGENAME", "Login");
$acc = check_acc($secret_key, $db);

if (!$_GET['id'])
{
	header("Location: characters.php");
	exit;
}else{
	$loginban = $db->GetOne("select `ban` from `players` where `id`=?", array($_GET['id']));
	$youracc = $db->execute("select * from `players` where `id`=? and `acc_id`=?", array($_GET['id'], $acc->id));

	if ($loginban > time()){
		include("templates/acc_header.php");
		$time = $loginban - time();
		$days_remaining = $time / 86400;
		echo "<br/><br/><br/><center>" . $loginto . " foi banido do jogo. O banimento acabará em " . round($days_remaining) . " dia(s). <a href=\"characters.php\">Voltar</a>.</center><br/>";
		include("templates/acc_footer.php");
		exit;
	}elseif ($youracc->recordcount() != 1){
		include("templates/acc_header.php");
		echo "<br/><br/><br/><center>Este usuário não pertence a sua conta ou não foi encontrado. <a href=\"characters.php\">Voltar</a>.</center><br/>";
		include("templates/acc_footer.php");
		exit;
	}else{
		$_SESSION['userid'] = $_GET['id'];
		$_SESSION['playerhash'] = sha1($acc->password . $_GET['id'] . $acc->id . $_SERVER['REMOTE_ADDR'] . $secret_key);
		header("Location: home.php");
		exit;
	}
}
?>