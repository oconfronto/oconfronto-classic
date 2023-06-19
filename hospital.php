<?php
	include("lib.php");
	define("PAGENAME", "Hospital");
	$player = check_user($secret_key, $db);
	include("checkbattle.php");
	include("checkwork.php");
	include("templates/private_header.php");
?>
<fieldset>
<legend><b>Hospital</b></legend>
<b>Enfermeira:</b> <i>Olá, aqui eu posso te <a href="hospt.php">curar</a>, ou estender sua <a href="energy.php">energia</a>.</i><br />
</fieldset>
<?php
	include("templates/private_footer.php");
?>