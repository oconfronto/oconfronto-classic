<?php

$query = $db->execute("select `id` from `guilds` where `pagopor`<?", array(time()));
if ($query->recordcount() > 0)
{
	$query0 = $db->execute("select `id`, `leader`, `name`, `gold` from `guilds` where `pagopor`<?", array(time()));
	 	while($guild = $query0->fetchrow()) {

			$query4 = $db->execute("select `id` from `players` where `guild`=?", array($guild['name']));
				while($member = $query4->fetchrow()) {
				$logmsg = "A gangue " . $guild['name'] . " foi deletada, pois seus administradores deixaram de paga-la.";
				addlog($member['id'], $logmsg, $db);
				}

			$query1 = $db->execute("update `players` set `bank`=`bank`+? where `username`=?", array($guild['gold'], $guild['leader']));
			$query2 = $db->execute("update `players` set `guild`='' where `guild`=?", array($guild['name']));
			$query3 = $db->execute("delete from `guilds` where `name`=?", array($guild['name']));


		}
}


?>