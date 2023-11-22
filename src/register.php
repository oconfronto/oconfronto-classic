<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include(__DIR__ . "/config.php");
include(__DIR__ . "/functions.php");
define("PAGENAME", "Cadastro");
mt_srand((float)microtime() * 1_000_000);  //sets random seed
$string = md5(random_int(0, 1_000_000));

$usaar = $_GET['r'] ?: "1";

$msg1 = "<b><font color=\"red\" size=\"1\">";
$msg2 = "<b><font color=\"red\" size=\"1\">";
$msg3 = "<b><font color=\"red\" size=\"1\">";
$msg4 = "<b><font color=\"red\" size=\"1\">";
$msg5 = "<b><font color=\"red\" size=\"1\">";
$msg7 = "<b><font color=\"red\" size=\"1\">";
$msg8 = "<b><font color=\"red\" size=\"1\">";
$error = 0;


if ($_POST['register']) {

    //Check if username has already been used
    $query = $db->execute("select `id` from `accounts` where `conta`=?", [$_POST['username']]);
    //Check username
    if (!$_POST['username']) { //If username isn't filled in...
        $msg1 .= "Você precisa digitar o nome da conta desejada.<br />\n"; //Add to error message
        $error = 1; //Set error check
    } elseif (strlen((string) $_POST['username']) < 3) { //If username is too short...
        $msg1 .= "Sua conta não pode ter menos de 3 caracteres!<br />\n"; //Add to error message
        $error = 1; //Set error check
    } elseif (strlen((string) $_POST['username']) > 20) {
        //If username is too short...
        $msg1 .= "Seu nome de usuário deve ser de 20 caracteres ou menos!<br />\n";
        //Add to error message
        $error = 1;
    //Set error check
    } elseif (!preg_match("/^[-_a-zA-Z0-9]+$/", (string) $_POST['username'])) {
        //If username contains illegal characters...
        $msg1 .= "Sua conta não pode conter <b>caracteres especiais</b>!<br />\n";
        //Add to error message
        $error = 1;
    //Set error check
    } elseif ($query->recordcount() > 0) {
        $msg1 .= "Esta conta já está sendo usuada!<br />\n";
        $error = 1;
        //Set error check
    }

    //Check password
    if (!$_POST['password']) {
        //If password isn't filled in...
        $msg2 .= "Você precisa digitar uma senha!<br />\n";
        //Add to error message
        $error = 1;
    //Set error check
    } elseif ($_POST['password'] != $_POST['password2']) {
        $msg3 .= "<br/>Você não digitou as duas senhas corretamente!<br />\n";
        $error = 1;
    } elseif (strlen((string) $_POST['password']) < 4) {
        //If password is too short...
        $msg2 .= "Sua senha deve ser maior que 3 caracteres!<br />\n";
        //Add to error message
        $error = 1;
        //Set error check
    }


    //Check email
    if (!$_POST['email']) {
        //If email address isn't filled in...
        $msg4 .= "Você precisa digitar um email!<br />\n";
        //Add to error message
        $error = 1;
    //Set error check
    } elseif ($_POST['email'] != $_POST['email2']) {
        $msg5 .= "<br/>Você não digitou os dois emails corretamente!";
        $error = 1;
    } elseif (strlen((string) $_POST['email']) < 5) {
        //If email is too short...
        $msg4 .= "O seu endereço de email deve conter mais de 5 caracteres.<br />\n";
        //Add to error message
        $error = 1;
    //Set error check
    } elseif (!preg_match("/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i", (string) $_POST['email'])) {
        $msg4 .= "O formato do seu email é inválido!<br />\n";
        //Add to error message
        $error = 1;
    //Set error check
    } else {
        //Check if email has already been used
        $query = $db->execute("select `id` from `accounts` where `email`=?", [$_POST['email']]);
        $query2 = $db->execute("select * from `pending` where `pending_id`=1 and `pending_status`=?", [$_POST['email']]);
        if ($query->recordcount() > 0) {
            $msg4 .= "Este email já está sendo usado por outra conta!<br />\n";
            $error = 1;
        //Set error check
        } elseif ($query2->recordcount() > 0) {
            $msg4 .= "Este email já está em uso!<br />\n";
            $error = 1;
            //Set error check
        }
    }


    if (!$_POST['rules']) {
        //If email address isn't filled in...
        $msg7 .= "Você precisa ler e aceitar as regras!";
        $error = 1;
    //Set error check
    } elseif ($_POST['rules'] != 'yes') {
        $msg7 .= "Você precisa ler e aceitar as regras!";
        $error = 1;
        //Set error check
    }

    if (!$_POST['age']) {
        //If email address isn't filled in...
        $msg8 .= "<br/>Você precisa ter 14 anos ou mais para jogar!<br/>";
        $error = 1;
    //Set error check
    } elseif ($_POST['age'] != 'yes') {
        $msg8 .= "<br/>Você precisa ter 14 anos ou mais para jogar!<br/>";
        $error = 1;
        //Set error check
    }


    if ($error == 0) {
        $insert['conta'] = $_POST['username'];
        $insert['password'] = sha1((string) $_POST['password']);
        $insert['email'] = $_POST['email'];
        $insert['registered'] = time();
        $insert['last_active'] = time();
        $insert['ip'] = $_SERVER['REMOTE_ADDR'];
        $insert['last_ip'] = $_SERVER['REMOTE_ADDR'];
        $insert['validkey'] = $string;
        $insert['ref'] = $usaar;
        $query = $db->autoexecute('accounts', $insert, 'INSERT');

        $playerid = $db->execute("select `id` from `accounts` where `conta`=?", [$_POST['username']]);
        $player = $playerid->fetchrow();

        $playerip = $db->execute("select `id` from `accounts` where `last_ip`=?", [$_SERVER['REMOTE_ADDR']]);
        if ($playerip->recordcount() > 1 && $usaar != 1) {
            $alerta1 = "<b>Atenção</b>:  Muitas contas já foram cadastradas nesse computador, e o usuário que te convidou não ganhará nenhum bônus.<br/>";
            $db->execute("update `accounts` set `ref`=? where `id`=?", [1, $player['id']]);
        }



        if (!$query) {
            $could_not_register = "Desculpe, ocorreu um erro desconhecido! Contate o administrador!<br /><br />";
        } else {
            session_unset();
            session_start();
            $hash = sha1($player['id'] . $ip . $secret_key);
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
<tr><td width="35%"><b>Conta</b>:</td><td><input type="text" name="username" id="conta" value="<?php echo $_POST['username'];?>" /><span id="msgbox4" style="display:none"></span></td></tr>
<tr><td colspan="2"><font size="1">Será usada para acessar sua lista de personagens.<br/><u>Caracteres especiais</u> não são permitidos.<br /></font><?php echo $msg1;?><br /></td></tr>

<tr><td width="35%"><b>Senha</b>:</td><td><input type="password" name="password" id="passwordbox" value="<?php echo $_POST['password'];?>" /><span id="msgbox3" style="display:none"></span></td></tr>
<tr><td width="35%"><b>Repita a senha</b>:</td><td><input type="password" name="password2" value="<?php echo $_POST['password2'];?>" /></td></tr>
<tr><td colspan="2"><?php echo $msg2;?><?php echo $msg3;?><br /></td></tr>

<tr><td width="35%"><b>Email</b>:</td><td><input type="text" name="email" id="emailbox" value="<?php echo $_POST['email'];?>" size="25"/><span id="msgbox2" style="display:none"></span></td></tr>
<tr><td width="35%"><b>Repita o Email</b>:</td><td><input type="text" name="email2" value="<?php echo $_POST['email2'];?>" size="25"/></td></tr>
<tr><td colspan="2"><font size="1">Seu email lhe dá direito a <u>recuperar sua conta</u> e outras <u>vantagens</u>.</font><br /><?php echo $msg4;?><?php echo $msg5;?><br /></td></tr>

<tr><td width="35%"><b>Idade</b>:</td><td><input type="checkbox" name="age" VALUE="yes" checked> Possuo mais de 14 anos.</td></tr>
<tr><td width="35%"><b>Regras</b>:</td><td><input type="checkbox" name="rules" VALUE="yes" checked> Declaro que li e aceito as <a href="regras2.php" target="_blank">regras</a> do jogo.</td></tr>
<tr><td colspan="2"><?php echo $msg7;?><?php echo $msg8;?><br /></td></tr>

<tr><td colspan="2" align="center"><input type="submit" name="register" value="Cadastrar"></td></tr>
</table>
</form>


<?php
include(__DIR__ . "/templates/footer.php");
?>