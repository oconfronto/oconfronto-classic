<?php
include(__DIR__ . "/lib.php");
$player = check_user($secret_key, $db);

$deletechecknosite = $db->execute("delete from `online` where `player_id`=?", [$player->id]);

	$_SESSION['userid'] = 0;
	$_SESSION['playerhash'] = 0;

header("Location: characters.php");
exit;
?>