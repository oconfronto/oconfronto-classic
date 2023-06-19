<?php
include("lib.php");
define("PAGENAME", "" . $lang['page_home'] . "");

$ip = $_SERVER['REMOTE_ADDR'];

if ($_POST['login'])
{
	$tentativas = $db->GetOne("select `tries` from `ip` where `ip`=?", array($ip));

	if ((!$_POST['accountname']) or (!$_POST['password']))
	{
		$errormsg = $lang['error_missingfields'];
		$error = 1;
	}
	elseif ($tentativas > 9)
	{
		$errormsg = $lang['error_locked'];
		$error = 1;
	}

	else if ($error == 0)
	{
		$checkpass = $db->execute("select `id` from `accounts` where `conta`=? and `password`=?", array($_POST['accountname'], sha1($pass_encode . $_POST['password'])));
		if ($checkpass->recordcount() == 0)
		{
			$restantes = ceil(10 - $tentativas);
			$errormsg = sprintf($lang['error_password'], $restantes);

			$bloqueiaip = $db->execute("select `tries` from `ip` where `ip`=?", array($ip));
			if ($bloqueiaip->recordcount() == 0) {
			$insert['ip'] = $ip;
			$insert['tries'] = 1;
			$insert['time'] = time();
			$db->autoexecute('ip', $insert, 'INSERT');
			}elseif ($bloqueiaip->recordcount() > 0) {
			$db->execute("update `ip` set `tries`=`tries`+1 where `ip`=?", array($ip));
			}

			$error = 1;
			
			session_unset();
			session_destroy();
		}
		else
		{
		
			$acc = $checkpass->fetchrow();
			
				$db->execute("update `accounts` set `last_ip`=? where `id`=?", array($ip, $acc['id']));

				$hash = sha1($acc['id'] . $ip . $secret_key);
				$_SESSION['accid'] = $acc['id'];
				$_SESSION['hash'] = $hash;
				header("Location: account.php");
		}
	}

}elseif ($_POST['register']){
	header("Location: register.php");
	exit;
}

include("includes/header.php");
?>

<div id="topbar" class="transparent">
	<div id="title"><?php echo PAGENAME;?></div>
	<div id="rightbutton"><a href="about.php"><?php echo $lang['link_about'];?></a></div>
</div>
<div id="content">
	<form method="post" action="index.php">
		<span class="graytitle"><?php echo $lang['login']; ?></span>
		<ul class="pageitem">
			<li class="bigfield"><input placeholder="<?php echo $lang['acc_name']; ?>" type="text" name="accountname" value="<?php if ($_GET['acc']){ echo $_GET['acc']; } else { echo $_POST['accountname']; } ?>"/></li>
			<li class="bigfield"><input placeholder="<?php echo $lang['password']; ?>" type="password" name="password" /></li>
		</ul>

		<ul class="pageitem">
			<li class="button"><input name="login" type="submit" value="<?php echo $lang['login']; ?>" /></li>
			<li class="button"><input name="register" type="submit" value="<?php echo $lang['register']; ?>" /></li>
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