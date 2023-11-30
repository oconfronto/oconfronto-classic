<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Recuperar senha");

$email1 = isset($_POST['email1']) ? $_POST['email1'] : '';

include(__DIR__ . "/templates/header.php");

if (isset($_POST['submit'])) {
    if (empty($_POST['username']) || empty($_POST['email'])) {
        echo "Preencha todos os campos. <a href='forgot.php'>Voltar</a>.";
        include(__DIR__ . "/templates/footer.php");
        exit;
    }

    $query = $db->execute("SELECT * FROM `accounts` WHERE `email` = ? AND `conta` = ?", [$_POST['email'], $_POST['username']]);
    if ($query->recordcount() != 1) {
        echo "Os dados digitados não conferem. <a href='forgot.php'>Voltar</a>.";
        include(__DIR__ . "/templates/footer.php");
        exit;
    }

    $recu = $query->fetchrow();

    $subject = "Recuperar senha - O Confronto";
    $message = "Você solicitou uma nova senha no Confronto.\nSe quiser uma nova senha, acesse: http://www.oconfronto.net/newpass.php?email=" . $recu['email'] . "&string=" . $recu['validkey'] . "\n\n Caso contrário ignore este email.\n\n -> oconfronto.net";
    $headers = "From: no-reply@oconfronto.net";

    mail((string) $recu['email'], $subject, $message, $headers);
    echo "Sua senha foi enviada ao seu email. <a href='index.php'>Voltar</a>.";
} else {
    echo "<fieldset><legend><b>Recuperar senha</b></legend>\n";
    echo "<table><form action='forgot.php' method='post'>";
    echo "<tr><td><b>Conta:</b></td><td><input type='text' name='username' size='20'></td></tr>";
    echo "<tr><td><b>Email:</b></td><td><input type='text' name='email' size='25'></td></tr>";
    echo "<tr><td></td><td><input type='submit' name='submit' value='Enviar nova senha'></form></td></tr></table>";
    echo "</fieldset>";
}
include(__DIR__ . "/templates/footer.php");
?>
