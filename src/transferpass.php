<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Tranferir Ouro");
$player = check_user($secret_key, $db);
include(__DIR__ . "/checkbattle.php");
include(__DIR__ . "/checkwork.php");

$pass1 = strtolower((string) $_POST['pass']);
$pass2 = strtolower((string) $_POST['pass2']);

if (($_POST['pass']) && ($_POST['pass2'])) {

    if ($player->transpass != \F) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Seguran�a</b></legend>";
        echo "Voc� j� possui uma senha de transfer�ncia.";
        echo "</fieldset>";
        echo"<br/><a href=\"home.php\">Voltar</a>.</br>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    if ($pass1 !== $pass2) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Seguran�a</b></legend>";
        echo "Digite as duas senhas corretamente.";
        echo "</fieldset>";
        echo"<br/><a href=\"home.php\">Voltar</a>.</br>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    if (strlen($pass1) > 30) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Seguran�a</b></legend>";
        echo "Sua senha de transfer�ncia n�o pode ter mais de 30 caracteres.";
        echo "</fieldset>";
        echo"<br/><a href=\"home.php\">Voltar</a>.</br>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    if (strlen($pass1) < 4) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Seguran�a</b></legend>";
        echo "Sua senha de transfer�ncia n�o pode ter menos de 4 caracteres.";
        echo "</fieldset>";
        echo"<br/><a href=\"home.php\">Voltar</a>.</br>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    elseif (sha1($pass1) == $player->password) {
        include(__DIR__ . "/templates/private_header.php");
        echo "<fieldset><legend><b>Seguran�a</b></legend>";
        echo "Sua senha de transfer�ncia n�o pode ser igual a senha da sua conta.";
        echo "</fieldset>";
        echo"<br/><a href=\"home.php\">Voltar</a>.</br>";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }
    else {
            $query = $db->execute("update `players` set `transpass`=? where `id`=?", [$pass1, $player->id]);
            include(__DIR__ . "/templates/private_header.php");
		echo "<fieldset><legend><b>Seguran�a</b></legend>";
		echo "Sua senha de transf�rencia foi criada com sucesso.";
		echo "</fieldset>";
		echo"<br/><a href=\"home.php\">Voltar</a>.</br>";
            include(__DIR__ . "/templates/private_footer.php");
            exit;
    }

}
include(__DIR__ . "/templates/private_header.php");
echo "<fieldset><legend><b>Seguran�a</b></legend>";
echo "Voc� precisa preencher todos os campos.";
echo "</fieldset>";
echo"<br/><a href=\"home.php\">Voltar</a>.</br>";
include(__DIR__ . "/templates/private_footer.php");
?>
