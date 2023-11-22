<?php
include(__DIR__ . "/lib.php");

if ($_SESSION['userid'] > 0){
$player = check_user($secret_key, $db);
$deletechecknosite1 = $db->execute("delete from `online` where `player_id`=?", [$player->id]);
$deletechecknosite2 = $db->execute("delete from `login` where `friendid`=?", [$player->id]);
}

session_unset();
session_destroy();

header("Location: index.php");
exit;
?>