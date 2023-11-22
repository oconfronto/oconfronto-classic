<?php
	include(__DIR__ . "/lib.php");
	define("PAGENAME", "Opções da conta");
	$acc = check_acc($secret_key, $db);

	include(__DIR__ . "/templates/acc_header.php");

	echo "<br/><br/><br/>";
	echo "<center><a href=\"transferchar.php\">Transferir personagem para esta conta.</a><br/><br/></center>";

	echo "<center><a href=\"accpass.php\">Alterar senha desta conta.</a><br/></center>";
	echo "<center><a href=\"changemail.php\">Alterar email desta conta.</a><br/>";
	echo "<center><a href=\"editinfo.php\">Alterar configurações pessoais.</a><br/><br/>";

	echo "<font size=\"1\"><a href=\"characters.php\"><b>Voltar</b></a> - <font size=\"1\"><a href=\"#\" onclick=\"javascript:window.open('accountlog.php', '_blank','top=100, left=100, height=350, width=520, status=no, menubar=no, resizable=no, scrollbars=yes, toolbar=no, location=no, directories=no');\">Exibir logs da conta</a></font></font><br/></center>";


	include(__DIR__ . "/templates/acc_footer.php");
?>