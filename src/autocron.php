<?php

$fiun = $db->execute("select * from `cron`");

if(!$fiun){
    die("Error executing query: " . $db->ErrorMsg());
}

$cron = [];
while($row = $fiun->fetchrow())
{
	$cron[$row['name']] = $row['value'];
}

$now = time();

$diff = ($now - $cron['reset_last']);
if($diff >= 60)
{
	$atualizacron = $db->execute("update `cron` set `value`=? where `name`=?", [$now, "reset_last"]);

	if ($atualizacron) {
	$timedif = ($diff / 60);
	$addhp = (35 * $timedif);
	$addenergy = (10 * $timedif);
	$addmana = (35 * $timedif);
	$sql = "update `players` set hp = IF((hp + $addhp)>maxhp, maxhp, (hp + $addhp)), mana = IF((mana + $addmana)>maxmana, maxmana, (mana + $addmana)) where hp > 0 and lutando = 0";
	$sql2 = "update `players` set energy = IF((energy + $addenergy)>maxenergy, maxenergy, (energy + $addenergy))";
	$result=mysql_query($sql);
	$result=mysql_query($sql2);
	}
}

$diff = ($now - $cron['interest_last']);
if($diff >= $cron['interest_time'])
{
	$db->execute("update `players` set `died`=0");
	$db->execute("update `players` set `bank`=`bank`+(`bank` / 100)* ? where `bank`+`gold` < ?", [$setting->bank_interest_rate, $setting->bank_limit]);
	$db->execute("update `players` set `alerts`=`alerts`-1 where `alerts`>0 and `alerts`<999");
	$db->execute("update `guilds` set `msgs`=0");

		$db->execute("drop view `guild_scores`");
		$db->execute("CREATE DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `guild_scores` AS select `guilds`.`id` AS `guild_id`,((((`players`.`gold`) + (`players`.`bank`) + (`guilds`.`gold`)) / 90) + ((`players`.`kills`) * 4) + (`players`.`monsterkilled`) + ((`players`.`level`) * 28) - ((`players`.`deaths`) * 10)) AS `score` from (`guilds` join `players`) where (`players`.`guild` = `guilds`.`id`) group by `guilds`.`id` order by ((((`players`.`gold`) + (`players`.`bank`) + (`guilds`.`gold`)) / 90) + ((`players`.`kills`) * 4) + (`players`.`monsterkilled`) + ((`players`.`level`) * 28) - ((`players`.`deaths`) * 10)) desc");

	$db->execute("update `cron` set `value`=? where `name`=?", [$now, "interest_last"]);
}

$diff = isset($cron['oneweek_last']) ? ($now - $cron['oneweek_last']) : 0;
if (isset($cron['oneweek_time']) && $diff >= $cron['oneweek_time'])
{
	$db->execute("update `players` set `totalbet`=0");
	$db->execute("update `cron` set `value`=? where `name`=?", [$now, "oneweek_last"]);
}

$cura = $db->execute("update `players` set `hp`=`maxhp`, `deadtime`=0 where `hp`<1 and `deadtime`<?", [time()]);

$tempoip = ceil(time() - 1800);
$deletaip = $db->execute("delete from `ip` where `time`<?", [$tempoip]);

$tempolog = ceil(time() - 604800);
$db->execute("delete from `mail` where `time`<?", [$tempolog]);
$db->execute("delete from `user_log` where `time`<?", [$tempolog]);
$db->execute("delete from `log_battle` where `time`<?", [$tempolog]);
$db->execute("delete from `logbat` where `time`<?", [$tempolog]);
$db->execute("delete from `revenge` where `time`<?", [$tempolog]);
$db->execute("delete from `work` where `started`<?", [$tempolog]);

$duassemana = ceil(time() - 1_209_600);
$db->execute("delete from `log_gold` where `time`<?", [$duassemana]);
$db->execute("delete from `log_item` where `time`<?", [$duassemana]);
$db->execute("delete from `account_log` where `time`<?", [$duassemana]);
$db->execute("delete from `log_forum` where `time`<?", [$duassemana]);

$updategeralwork = $db->execute("select * from `work` where `status`='t' and (`start`+(`worktime`*3600))<?", [time()]);

if(!$updategeralwork){
    die("Error executing work query: " . $db->ErrorMsg());
}

while($newwork = $updategeralwork->fetchrow())
{
    $updateWorkStatus = $db->execute("update `work` set `status`='f' where `id`=?", [$newwork['id']]);
    if(!$updateWorkStatus){
        die("Error updating work status: " . $db->ErrorMsg());
    }

    $updatePlayers = $db->execute("update `players` set `gold`=`gold`+?, `energy`=`energy`/? where `id`=?", [($newwork['gold'] * $newwork['worktime']), $newwork['worktime'], $newwork['player_id']]);
    if(!$updatePlayers){
        die("Error updating players: " . $db->ErrorMsg());
    }

    $worklog = "Seu trabalho como " . $newwork['worktype'] . " terminou! Voc?recebeu <b>" . ($newwork['gold'] * $newwork['worktime']) . " moedas de ouro</b>.";
    addlog($newwork['player_id'], $worklog, $db);
}

?>
