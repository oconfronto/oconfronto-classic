<?php
include("lib.php");
define("PAGENAME", $lang['page_account']);
$acc = check_acc($secret_key, $db);
$error = 0;

if ($_POST['newchar'])
{
	header("Location: newchar.php");
	exit;
}elseif ($_GET['act'] == settings){
	include("includes/header.php");

	echo "<div id=\"topbar\" class=\"transparent\">";
	echo "<div id=\"title\">" . $lang['page_settings'] . "</div>";
	echo "<div id=\"leftnav\"><a href=\"account.php\">" . $lang['page_account'] . "</a></div>";
	echo "</div>";

	echo "<div id=\"content\">";
	echo "<form method=\"post\" action=\"account.php?act=settings\">";
	echo "<ul class=\"pageitem\">";
	echo "<li class=\"button\"><input name=\"changepass\" type=\"submit\" value=\"" . $lang['change_pass'] . "\" /></li>";
	echo "<li class=\"button\"><input name=\"deletechar\" type=\"submit\" value=\"" . $lang['delete_char'] . "\" /></li>";
	echo "</ul><ul class=\"pageitem\">";
	echo "<li class=\"button\"><input name=\"changelang\" type=\"submit\" value=\"" . $lang['change_lang'] . "\" /></li>";
	echo "</ul>";
	echo "</div>";

	include("includes/footer.php");
	exit;
}elseif ($_GET['charinfo']){
	$charexists = $db->execute("select * from `players` where `id`=?", array($_GET['charinfo']));
		if ($charexists->recordcount() > 0){
			$youracc = $db->execute("select * from `players` where `id`=? and `acc_id`=?", array($_GET['charinfo'], $acc->id));
			$loginban = $db->GetOne("select `ban` from `players` where `id`=?", array($_GET['charinfo']));
			if ($youracc->recordcount() != 1){
				$errormsg = $lang['char_notyours'];
				$error = 1;
			}elseif ($loginban > time()){
				$time = $loginban - time();
				$daysleft = ceil($time / 86400);
				$errormsg = sprintf($lang['char_banned'], $daysleft);
				$error = 1;
			}
		}else{
			$errormsg = $lang['char_notfound'];
			$error = 1;
		}
}

include("includes/header.php");
?>

<div id="topbar" class="transparent">
	<div id="title"><?php echo PAGENAME;?></div>
	<div id="leftnav">
		<a href="logout.php"><?php echo $lang['logout']; ?></a>
	</div>
	<div id="rightbutton">
		<a href="account.php?act=settings"><?php echo $lang['page_settings']; ?></a>
	</div>
</div>
<div id="content">
	<form method="post" action="account.php">
	<span class="graytitle"><?php echo $lang['select_char']; ?></span>
		<ul class="pageitem">
		<?php
		$showchars = $db->execute("select `id`, `username`, `avatar`, `ban`, `level`, `voc` from `players` where `acc_id`=? order by `username` asc", array($acc->id));
		while($char = $showchars->fetchrow())
			{
			echo "<li class=\"store\"><a href=\"login.php?id=" . $char['id'] . "\">";
			echo "<span class=\"image\" style=\"background-image: url('"; if ($char['avatar'] == NULL){ echo $setting->avatar; }else{ echo $char['avatar']; } echo "')\"></span>";
			echo "<span class=\"name\">" . $char['username'] . "</span>";
			echo "<span class=\"comment\">" . $lang['level'] . " " . $char['level'] . ", " . ucfirst($char['voc']) . ".</span><span class=\"arrow\"></span>";
			echo "</a></li>";
			}
		?>

		<li class="button"><input name="newchar" type="submit" value="<?php echo $lang['new_char']; ?>" /></li>
		</ul>
	</form>
</div>

<?php
if ($error > 0){
	echo"<script language=\"JavaScript\" type=\"text/javascript\">";
	echo"alert (\"" . $errormsg . "\")";
	echo"</script>";
}

include("includes/footer.php");
?>