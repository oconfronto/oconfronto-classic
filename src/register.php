<?php

include(__DIR__ . "/config.php");
include(__DIR__ . "/functions.php");
define("PAGENAME", "Cadastro");
$string = md5(random_int(0, 1_000_000));

$usaar = isset($_GET['r']) ? $_GET['r'] : "1";
$could_not_register = '';

$msg1 = $msg2 = $msg3 = $msg4 = $msg5 = $msg7 = $msg8 = "<b><font color=\"red\" size=\"1\">";
$error = 0;

if (isset($_POST['register'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $email = $_POST['email'] ?? '';
    $email2 = $_POST['email2'] ?? '';
    $age = isset($_POST['age']) ? $_POST['age'] : '';
    $rules = isset($_POST['rules']) ? $_POST['rules'] : '';

    //Check if username has already been used
    $query = $db->execute("select `id` from `accounts` where `conta`=?", [$username]);
    
    // Username validation
    if (!$username) {
        $msg1 .= "Você precisa digitar o nome da conta desejada.<br />\n";
        $error = 1;
    } elseif (strlen($username) < 3) {
        $msg1 .= "Sua conta não pode ter menos de 3 caracteres!<br />\n";
        $error = 1;
    } elseif (strlen($username) > 20) {
        $msg1 .= "Seu nome de usuário deve ser de 20 caracteres ou menos!<br />\n";
        $error = 1;
    } elseif (!preg_match("/^[-_a-zA-Z0-9]+$/", $username)) {
        $msg1 .= "Sua conta não pode conter <b>caracteres especiais</b>!<br />\n";
        $error = 1;
    } elseif ($query->recordcount() > 0) {
        $msg1 .= "Esta conta já está sendo usada!<br />\n";
        $error = 1;
    }

    // Password validation
    if (!$password) {
        $msg2 .= "Você precisa digitar uma senha!<br />\n";
        $error = 1;
    } elseif ($password != $password2) {
        $msg3 .= "<br/>Você não digitou as duas senhas corretamente!<br />\n";
        $error = 1;
    } elseif (strlen($password) < 4) {
        $msg2 .= "Sua senha deve ser maior que 3 caracteres!<br />\n";
        $error = 1;
    }

    // Email validation
    if (!$email) {
        $msg4 .= "Você precisa digitar um email!<br />\n";
        $error = 1;
    } elseif ($email != $email2) {
        $msg5 .= "<br/>Você não digitou os dois emails corretamente!<br />\n";
        $error = 1;
    } elseif (strlen($email) < 5) {
        $msg4 .= "O seu endereço de email deve conter mais de 5 caracteres.<br />\n";
        $error = 1;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg4 .= "O formato do seu email é inválido!<br />\n";
        $error = 1;
    } else {
        // Check if email has already been used
        $queryEmail = $db->execute("select `id` from `accounts` where `email`=?", [$email]);
        if ($queryEmail->recordcount() > 0) {
            $msg4 .= "Este email já está sendo usado por outra conta!<br />\n";
            $error = 1;
        }
    }

    // Age validation
    if ($age != 'yes') {
        $msg8 .= "<br/>Você precisa ter 14 anos ou mais para jogar!<br/>";
        $error = 1;
    }

    // Rules acceptance check
    if ($rules != 'yes') {
        $msg7 .= "Você precisa ler e aceitar as regras!";
        $error = 1;
    }

    // If no errors, proceed with registration
    if ($error == 0) {
        $insert = [
            'conta' => $username,
            'password' => sha1($password),
            'email' => $email,
            'registered' => time(),
            'last_active' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'last_ip' => $_SERVER['REMOTE_ADDR'],
            'validkey' => $string,
            'ref' => $usaar
        ];
        $query = $db->autoexecute('accounts', $insert, 'INSERT');

        if (!$query) {
            $could_not_register = "Desculpe, ocorreu um erro desconhecido! Contate o administrador!<br /><br />";
        } else {
            session_unset();
            session_start();
            $playerid = $db->execute("select `id` from `accounts` where `conta`=?", [$username]);
            $player = $playerid->fetchrow();
            $hash = sha1($player['id'] . $_SERVER['REMOTE_ADDR'] . $secret_key);
            $_SESSION['accid'] = $player['id'];
            $_SESSION['hash'] = $hash;

            include(__DIR__ . "/templates/header.php");
            echo "Parabéns! Você foi cadastrado com sucesso!<br />";
            echo "Agora você pode entrar no jogo. <a href=\"characters.php\">Clique aqui.</a>";
            include(__DIR__ . "/templates/footer.php");
            exit;
        }
    }
}

$msg1 .= "</font></b>";
$msg2 .= "</font></b>";
$msg3 .= "</font></b>";
$msg4 .= "</font></b>";
$msg5 .= "</font></b>";
$msg7 .= "</font></b>";
$msg8 .= "</font></b>";

include(__DIR__ . "/templates/header.php");
?>

<?include("box.php");?>
<?php echo $could_not_register?>

<form method="POST" action="register.php?r=<?php echo $usaar;?>">
    <table width="100%">
    <tr>
        <td width="35%">
            <b>Conta</b>:
        </td>
        <td>
            <input type="text" name="username" id="conta" value="<?php echo $_POST['username'] ?? '';?>" />
            <span id="msgbox4" style="display:none"></span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <font size="1">Será usada para acessar sua lista de personagens.<br/>
            <u>Caracteres especiais</u> não são permitidos.<br />
            </font><?php echo $msg1;?><br />
        </td>
    </tr>
    <tr>
        <td width="35%">
            <b>Senha</b>:
        </td>
        <td>
            <input type="password" name="password" id="passwordbox" value="<?php echo $_POST['password'] ?? '';?>" />
            <span id="msgbox3" style="display:none"></span>
        </td>
    </tr>
    <tr>
        <td width="35%">
            <b>Repita a senha</b>:
        </td>
        <td>
            <input type="password" name="password2" value="<?php echo $_POST['password2'] ?? '';?>" />
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo $msg2;?><?php echo $msg3;?><br /></td>
    </tr>
    <tr>
        <td width="35%">
            <b>Email</b>:
        </td>
        <td>
            <input type="text" name="email" id="emailbox" value="<?php echo $_POST['email'] ?? '';?>" size="25"/>
            <span id="msgbox2" style="display:none"></span>
        </td>
    </tr>
    <tr>
        <td width="35%">
            <b>Repita o Email</b>:
        </td>
        <td>
            <input type="text" name="email2" value="<?php echo $_POST['email2'] ?? '';?>" size="25"/>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <font size="1">Seu email lhe dá direito a <u>recuperar sua conta</u> e outras <u>vantagens</u>.</font><br />
            <?php echo $msg4;?><?php echo $msg5;?><br />
        </td>
    </tr>
    <tr>
        <td width="35%">
            <b>Idade</b>:
        </td>
        <td>
            <input type="checkbox" name="age" VALUE="yes" checked> Possuo mais de 14 anos.
        </td>
    </tr>
    <tr>
        <td width="35%">
            <b>Regras</b>:
        </td>
        <td>
            <input type="checkbox" name="rules" VALUE="yes" checked> Declaro que li e aceito as <a href="regras2.php" target="_blank">regras</a> do jogo.
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <?php echo $msg7;?><?php echo $msg8;?><br />
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <input type="submit" name="register" value="Cadastrar">
        </td>
    </tr>
    </table>
</form>

<?php
include(__DIR__ . "/templates/footer.php");
?>
