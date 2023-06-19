<?php
include("lib.php");
$ipp = $_SERVER['REMOTE_ADDR'];

if ($_SESSION['userid'] > 0)
{
$player = check_user($secret_key, $db);

$checknosite = $db->execute("select `time` from `online` where `ip`=?", array($ipp));

if ($checknosite->recordcount() < 1) {
	$insert['player_id'] = $player->id;
	$insert['ip'] = $ipp;
	$insert['time'] = time();
	$insert['login'] = time();
	$insert['serv'] = $player->serv;
	$insertchecknosite = $db->autoexecute('online', $insert, 'INSERT');
} else {
	$updatechecknosite1 = $db->execute("update `online` set `time`=? where `player_id`=?", array(time(), $player->id));
	$updatechecknosite2 = $db->execute("update `login` set `time`=? where `friendid`=?", array(time(), $player->id));
}

$deletechecknosite1 = $db->execute("delete from `online` where `time`<?", array((time() - 20)));
$deletechecknosite2 = $db->execute("delete from `login` where `time`<?", array((time() - 20)));

$mailcount = $db->execute("select `id` from `mail` where `to`=? and `status`='unread'", array($player->id));
if ($mailcount->recordcount() > 0){
	echo "<div style=\"background-color:#FFFDE0; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\" align=\"center\">";
	echo "<p>Voc&ecirc; tem " . $mailcount->recordcount() . " <a href=\"mail.php\">mensagem(s)</a> n&atilde;o lida(s)!</p>";
	echo "</div>";
}

$queryloginfriend = $db->execute("select `fname` from `friends` where `uid`=?", array($player->id));

while($loginfriend = $queryloginfriend->fetchrow())
{
	$frienddeide = $db->GetOne("select `id` from `players` where `username`=?", array($loginfriend['fname']));
	$veruserfrindlogin1 = $db->execute("select `ip` from `online` where `player_id`=?", array($frienddeide));
	$veruserfrindlogin2 = $db->execute("select `time` from `login` where `friendid`=? and `myid`=?", array($frienddeide, $player->id));
   	if (($veruserfrindlogin1->recordcount() == 1) and ($veruserfrindlogin2->recordcount() == 0)){
		$insert['myid'] = $player->id;
		$insert['friendid'] = $frienddeide;
		$insert['time'] = time();
		$firndlogedadded = $db->autoexecute('login', $insert, 'INSERT');

	echo "<div style=\"background-color:#45E61D; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
	echo "<center>Seu(a) amigo(a) <b>" . $loginfriend['fname'] . "</b> acabou de entrar.</center>";
	echo "</div>";
	}
}

$queryduelos = $db->execute("select * from `duels` where (`owner`=? or `rival`=?)", array($player->id, $player->id));

while($duinfo = $queryduelos->fetchrow())
{
	$owname = $db->GetOne("select `username` from `players` where `id`=?", array($duinfo['owner']));
	$riname = $db->GetOne("select `username` from `players` where `id`=?", array($duinfo['rival']));

	$rivalonline = $db->execute("select * from `online` where `player_id`=? and `serv`=?", array($duinfo['rival'], $player->serv));
	if ($rivalonline->recordcount() > 0){
	$rionline = 1;
	}else{
	$rionline = 0;
	}

	$owneronline = $db->execute("select * from `online` where `player_id`=? and `serv`=?", array($duinfo['owner'], $player->serv));
	if ($owneronline->recordcount() > 0){
	$owonline = 1;
	}else{
	$owonline = 0;
	}

	if (($duinfo['owner'] == $player->id) and ($rionline == 1)){
		echo "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
		echo "<center><a href=\"profile.php?id=" . $riname . "\">" . $riname . "</a> est?online. Aguardando <a href=\"profile.php?id=" . $riname . "\">" . $riname . "</a> aceitar a proposta de duelo.</center><br/>";
		echo "<center><a href=\"duel.php?accept=" . $duinfo['id'] . "\">Cancelar proposta.</a></center><br/>";
		echo "</div>";
	}

	if (($duinfo['rival'] == $player->id) and ($owonline == 1)){
		echo "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
		echo "<center><a href=\"profile.php?id=" . $owname . "\">" . $owname . "</a> est?online. Aguardando <a href=\"profile.php?id=" . $owname . "\">" . $owname . "</a> aceitar a proposta de duelo.</center><br/>";
		echo "<center><a href=\"duel.php?info=" . $duinfo['id'] . "\">Detalhes do duelo.</a></center><br/>";
		echo "</div>";
	}
}


}else{
$deletechecknosite = $db->execute("delete from `online` where `ip`=?", array($ipp));
echo "<div style=\"background-color:#EEA2A2; padding:5px; border: 1px solid #DEDEDE; margin-bottom:10px\">";
echo "<center><b>Conex&atilde;o perdida com o servidor.</b></center>";
echo "</div>";
}

?>