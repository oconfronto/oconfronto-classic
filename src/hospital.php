<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Hospital");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkwork.php");
include(__DIR__ . "/templates/private_header.php");
?>
<fieldset>
<legend><b>Hospital</b></legend>
<b>Enfermeira:</b> <i>Olá, aqui eu posso te <a href="hospt.php">curar</a>, ou estender sua <a href="energy.php">energia</a>.</i><br />
</fieldset>
<?php
    include(__DIR__ . "/templates/private_footer.php");
?>