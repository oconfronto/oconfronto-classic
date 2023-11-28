<?php

include(__DIR__ . "/lib.php");
include(__DIR__ . '/bbcode.php');
$bbcode = new bbcode();
define("PAGENAME", "Principal");

function redirectToCharacterPageIfNeeded($db, $secret_key) {
    if (!isset($_SESSION['accid'], $_SESSION['hash']) || $_SESSION['accid'] <= 0 || !$_SESSION['hash']) {
        return;
    }

    $check = sha1($_SESSION['accid'] . $_SERVER['REMOTE_ADDR'] . $secret_key);
    if ($check != $_SESSION['hash']) {
        return;
    }

    $rematual = $db->GetOne("SELECT `remember` FROM `accounts` WHERE `id` = ?", [$_SESSION['accid']]);
    if ($rematual == 't') {
        header("Location: characters.php");
        exit;
    }
}

function attemptLogin($db, $secret_key, &$error, &$errormsg) {
    if (empty($_POST['username'])) {
        $errormsg .= "Por favor digite sua conta.";
        $error = 1;
        return;
    }

    if (empty($_POST['password'])) {
        $errormsg .= "Por favor digite sua senha.";
        $error = 1;
        return;
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $tentativas = $db->GetOne("SELECT `tries` FROM `ip` WHERE `ip` = ?", [$ip]);

    if ($tentativas > 9) {
        $errormsg .= "Você errou sua senha 10 vezes seguidas. Aguarde 30 minutos para poder tentar novamente.";
        $error = 1;
        return;
    }

    $acc = $db->GetRow("SELECT `id`, `conta` FROM `accounts` WHERE `conta` = ? AND `password` = ?", [$_POST['username'], sha1($_POST['password'])]);

    if (empty($acc)) {
        $restantes = ceil(10 - $tentativas);
        $errormsg .= "Conta ou senha incorreta! (" . $restantes . " tentativas restantes).";
        updateLoginAttempts($db, $ip);
        session_unset();
        session_destroy();
        $error = 1;
        return;
    }

    $db->Execute("UPDATE `accounts` SET `last_ip` = ? WHERE `id` = ?", [$ip, $acc['id']]);
    $_SESSION['accid'] = $acc['id'];
    $_SESSION['hash'] = sha1($acc['id'] . $ip . $secret_key);
    header("Location: characters.php");
}

function updateLoginAttempts($db, $ip) {
    $bloqueiaip = $db->GetRow("SELECT `tries` FROM `ip` WHERE `ip` = ?", [$ip]);

    if (empty($bloqueiaip)) {
        $insert = ['ip' => $ip, 'tries' => 1, 'time' => time()];
        $db->AutoExecute('ip', $insert, 'INSERT');
    } else {
        $db->Execute("UPDATE `ip` SET `tries` = `tries` + 1 WHERE `ip` = ?", [$ip]);
    }
}

function displayNews($db, $bbcode) {
    $newsRows = $db->GetAll("SELECT `topic`, `detail`, `user_id`, `datetime` FROM `forum_question` WHERE `category` = 'noticias' ORDER BY `postado` DESC LIMIT 5");

    if (!$newsRows) {
        echo "<tr><td>No news available.</td></tr>"; // Display a message if no news is available
        return;
    }

    $noticiaid = 1;
    foreach ($newsRows as $news) {
        $username = $db->GetOne("SELECT `username` FROM `players` WHERE `id` = ?", [$news['user_id']]);
        
        // Ensure $username is not empty
        $username = $username ?: 'Unknown';

        echo "<tr>";
        echo "<td class=\"off\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\" onclick=\"ex_news{$noticiaid}();\"><b>{$news['topic']}</b></td>";
        echo "</tr>";
        echo "<tr><td bgcolor=\"#f2e1ce\"><div style=\"display: none;\" id=\"news{$noticiaid}\">";
        echo $bbcode->parse($news['detail']);
        echo "<br/><b><font size=\"1\">Notícia publicada por {$username} em {$news['datetime']}.</font></b></td></tr></div>";
        $noticiaid++;
    }
}

redirectToCharacterPageIfNeeded($db, $secret_key);

$error = 0;
$errormsg = "<font color=\"red\">";

if (isset($_POST['login'])) {
    attemptLogin($db, $secret_key, $error, $errormsg);
}

$errormsg .= "</font>";

include(__DIR__ . "/templates/header.php");
?>

<script type="text/javascript" src="js/inventariojquery.js"></script>
<script type="text/javascript" src="js/inventario.js"></script>

<form method="POST" action="index.php">
    <table align="center">
    <tr>
        <td><b>Conta:</b> <input type="text" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : '' ?>" size="18"/></td>
        <td><b>Senha:</b> <input type="password" name="password" size="17"/></td>
        <td><input name="login" type="submit" value="Entrar" /></td>
    </tr>
    </table>
    <font size="1"><a href="forgot.php">Esqueceu a senha?</a> <?php echo ($error == 1) ? $errormsg : ""?></font>
</form>
<table width="100%">
    <tr>
        <td align="center" bgcolor="#E1CBA4"><b>Últimas Notícias</b></td>
    </tr>
    <?php displayNews($db, $bbcode); ?>
</table>

<table width="100%">
    <tr>
        <td><center><a href="http://naruto.ativoforum.com" target="_blank"><img src="http://i48.servimg.com/u/f48/12/18/57/45/banner19.gif" width="88" height="31" border="0"></a></center></td>
        <td><center><a href="http://www.freedomain.co.nr/" target="_blank"><img src="./images/conr.gif" width="88" height="31" border="0" alt="Free Domains Forwarding" /></a></center></td>
    </tr>
</table>

<?php include(__DIR__ . "/templates/footer.php"); ?>
