<?php
/*************************************/
/*           ezRPG Script            */
/*         Written by Khashul        */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.bbgamezone.com/     */
/*************************************/
  
include("lib.php");
define("PAGENAME", "Abandonar Clã");
$player = check_user($secret_key, $db);
include("checkbattle.php");
include("checkguild.php");

//Populates $guild variable
$query = $db->execute("select * from `guilds` where `id`=?", array($player->guild));
if ($query->recordcount() == 0) {
      header("Location: home.php");
}else{ 
$guild = $query->fetchrow();
}

include("templates/private_header.php");
  
if ($_GET['act'] == "go") {
	$leader = $db->GetOne("select `leader` from `guilds` where `id`=?", array($player->guild));
	$vice = $db->GetOne("select `vice` from `guilds` where `id`=?", array($player->guild));
      
    if (($player->username != $leader) and ($player->username != $vice)) {
    	$query = $db->execute("update `guilds` set `members`=? where `id`=?", array($guild['members'] - 1, $player->guild));
        $query = $db->execute("update `players` set `guild`=? where `username`=?", array(NULL, $player->username));
        echo "Você abandonou seu clã com sucesso.<br />";
        echo "<a href=\"home.php\">Principal</a>.";
    } else {
      	echo "Você não pode abandonar este clã. Se você for o lider dele, deverá desfaze-lo primeiro. Se for o vice-lider, abandone seu cargo primeiro.<br />";
        echo "<a href=\"home.php\">Principal</a>.";
    }
} else {
      echo "Você tem certeza que quer abandonar seu clã?<br />";
      echo "<a href=\"guild_leave.php?act=go\">Abandonar</a> | <a href=\"home.php\">Voltar</a>.";
}

include("templates/private_footer.php");
?>