<?php
include("lib.php");

$checkduels = $db->execute("select * from `duels` where `active`='t'");
$duel = $checkduels->fetchrow();

if ($duel['time'] < (time() + 45)){
	if ($duel['turn'] == 1){
		$winner = $duel['rival'];
		$loser = $duel['owner'];
	elseif ($duel['turn'] == 2){
		$winner = $duel['owner'];
		$loser = $duel['rival'];
	}

	$db->execute("update `duels` set `active`='f' where `id`=?", array($duel['id']));

	$lossmsg =. "Você perdeu o duelo após ficar 45 segundos sem responder.<br/>";
	$winmsg =. "Você ganhou o duelo após seu oponente ficar 45 segundos sem responder.<br/>";
		
	if ($duel['prize'] > 0){
		$losergold = $db->GetOne("select `bank` from `players` where `id`=?", array($loser));
			if ($losergold < $duel['prize']){
				$winmsg =. "Parece que seu oponente não tinha " . $duel['prize'] . " no banco para lhe pagar, portanto você só receberá " . $losergold . ".<br/>";
				$winmsg =. "Isso é um erro, contate o administrador.";
				$db->execute("update `players` set `bank`=`bank`+? where `id`=?", array($losergold, $winner));
				$db->execute("update `players` set `bank`=`bank`-? where `id`=?", array($losergold, $loser));
				$lossmsg =. "Você perdeu " . $losergold . " de ouro.";
			}else{
				$winmsg =. "" . $duel['prize'] . " de ouro foi adicionado a sua conta bancária.<br/>";
				$db->execute("update `players` set `bank`=`bank`+? where `id`=?", array($duel['prize'], $winner));
				$db->execute("update `players` set `bank`=`bank`-? where `id`=?", array($duel['prize'], $loser));
				$lossmsg =. "Você perdeu " . $duel['prize'] . " de ouro.";
			}
	}

	addlog($loser, $lossmsg, $db);
	addlog($winner, $winmsg, $db);

}

?>