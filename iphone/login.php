<?php
include("lib.php");
$acc = check_acc($secret_key, $db);

if (!$_GET['id'])
{
	header("Location: account.php");
	exit;
}else{
	$loginban = $db->GetOne("select `ban` from `players` where `id`=?", array($_GET['id']));
	$youracc = $db->execute("select * from `players` where `id`=? and `acc_id`=?", array($_GET['id'], $acc->id));

	if ($loginban > time()){
		header("Location: account.php?charinfo=" . $_GET['id'] . "");
		exit;
	}elseif ($youracc->recordcount() != 1){
		header("Location: account.php?charinfo=" . $_GET['id'] . "");
		exit;
	}else{
		$_SESSION['userid'] = $_GET['id'];
		$_SESSION['playerhash'] = sha1($acc->password . $_GET['id'] . $acc->id . $_SERVER['REMOTE_ADDR'] . $secret_key);
		header("Location: home.php");
		exit;
	}
}
?>