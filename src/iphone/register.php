<?php
include("lib.php");
define("PAGENAME", "" . $lang['page_register'] . "");
$error = 0;

if (!$_GET['r']){
$usaar = "1";
}else{
$usaar = $_GET['r'];
}

if ($_POST['register'])
{
	$check1 = $db->execute("select `id` from `accounts` where `conta`=?", array($_POST['acc_name']));
	$check2 = $db->execute("select `id` from `accounts` where `email`=?", array($_POST['email']));
	if ((!$_POST['acc_name']) or (!$_POST['password']) or (!$_POST['email'])) {
		$errormsg = $lang['error_missingfields'];
		$error = 1;
	}

	elseif (strlen($_POST['acc_name']) < 3)
	{
		$errormsg = $lang['acc_short'];
		$error = 1;
	}

	elseif (strlen($_POST['acc_name']) > 20)
	{
		$errormsg = $lang['acc_long'];
		$error = 1;
	}

	elseif (!preg_match("/^[-_a-zA-Z0-9]+$/", $_POST['acc_name']))
	{
		$errormsg = $lang['error_specialchars'];
		$error = 1;
	}

	elseif ($check1->recordcount() > 0)
	{
		$errormsg = $lang['acc_exists'];
		$error = 1;
	}

	elseif (strlen($_POST['password']) < 3)
	{
		$errormsg = $lang['password_short'];
		$error = 1;
	}

	elseif (strlen($_POST['password']) > 50)
	{
		$errormsg = $lang['password_long'];
		$error = 1;
	}

	elseif (strlen($_POST['email']) > 200)
	{
		$errormsg = $lang['email_long'];
		$error = 1;
	}

	elseif (!preg_match("/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i", $_POST['email']))
	{
		$errormsg = $lang['error_email'];
		$error = 1;
	}

	elseif ($check2->recordcount() > 0)
	{
		$errormsg = $lang['email_exists'];
		$error = 1;
	}

		if ($error == 0){

		$insert['conta'] = $_POST['acc_name'];
		$insert['password'] = sha1("" . $pass_encode . "" . $_POST['password'] . "");
		$insert['email'] = $_POST['email'];
		$insert['registered'] = time();
		$insert['last_active'] = time();
		$insert['ip'] = $_SERVER['REMOTE_ADDR'];
		$insert['ref'] = $usaar;
		$query = $db->autoexecute('accounts', $insert, 'INSERT');


		include("includes/header.php");

		echo "<div id=\"topbar\" class=\"transparent\">";
		echo "<div id=\"title\">" . $lang['page_register'] . "</div>";
		echo "<div id=\"leftnav\"><a href=\"index.php\">" . $lang['page_home'] . "</a></div>";
		echo "</div>";

		echo "<div id=\"content\">";
		echo "<ul class=\"pageitem\">";
		echo "<li class=\"textbox\"><center>" . $lang['register_sucess'] . "</center></li>";
		echo "<li class=\"textbox\"><center><a href=\"index.php?acc=" . $_POST['acc_name'] . "\"><b>" . $lang['login'] . "</b></a></center></li>";
		echo "</ul>";
		echo "</div>";

		include("includes/footer.php");
		exit;
		}

}

include("includes/header.php");
?>

<div id="topbar" class="transparent">
	<div id="title"><?php echo PAGENAME;?></div>
	<div id="leftnav"><a href="index.php"><?php echo $lang['page_home'];?></a></div>
	</div>
<div id="content">
	<form method="post" action="register.php">
		<span class="graytitle"><?php echo $lang['acc_info'];?></span>
		<ul class="pageitem">
			<li class="smallfield"><span class="name"><?php echo $lang['acc_name'];?></span><input type="text" name="acc_name" value="<?php echo $_POST['acc_name']; ?>"/></li>
			<li class="smallfield"><span class="name"><?php echo $lang['password'];?></span><input type="text" name="password" value="<?php echo $_POST['password']; ?>"/></li>
			<li class="smallfield"><span class="name"><?php echo $lang['email'];?></span><input placeholder="example@email.com" type="email" name="email" value="<?php echo $_POST['email']; ?>"/></li>
		</ul>

		<ul class="pageitem">
		<li class="button">
			<input name="register" type="submit" value="<?php echo $lang['submit'];?>" /></li>
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