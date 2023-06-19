<?php
include("lib.php");
define("PAGENAME", "Tutorial");
$player = check_user($secret_key, $db);

if ($_GET['skip'] == true)
{
$query = $db->execute("delete from `pending` where `pending_id`=2 and `player_id`=?", array($player->id));
header("Location: home.php");
}

include("templates/private_header.php");

if (!$_GET['act'])
{
$numero = 1;
}else{
$numero = $_GET['act'];
}

	if (($numero > 1) and ($numero < 8)){
	echo"<br/><center><img src=\"images/tutorial/" . $_GET['act'] . ".png\" usemap=\"#tutorial\" border=\"0\"></center>";
	echo"<map name=\"tutorial\">";
	echo"<area shape=\"rect\" coords=\"136,266,286,292\" href=\"tutorial.php?act=" . ($_GET['act'] - 1) . "\">";
	echo"<area shape=\"rect\" coords=\"294,266,445,292\" href=\"tutorial.php?act=" . ($_GET['act'] + 1) . "\">";
	echo"</map>";
	}else if ($numero == 8){
	echo"<br/><center><img src=\"images/tutorial/8.png\" usemap=\"#tutorial\" border=\"0\"></center>";
	echo"<map name=\"tutorial\">";
	echo"<area shape=\"rect\" coords=\"136,266,286,292\" href=\"tutorial.php?act=" . ($_GET['act'] - 1) . "\">";
	echo"<area shape=\"rect\" coords=\"294,266,445,292\" href=\"tutorial.php?skip=true\">";
	echo"</map>";
	}else{
	echo"<br/><center><img src=\"images/tutorial/1.png\" usemap=\"#tutorial\" border=\"0\"></center>";
	echo"<map name=\"tutorial\">";
	echo"<area shape=\"rect\" coords=\"6,266,157,292\" href=\"tutorial.php?skip=true\">";
	echo"<area shape=\"rect\" coords=\"294,266,445,292\" href=\"tutorial.php?act=2\">";
	echo"</map>";
	}

include("templates/private_footer.php");
?>