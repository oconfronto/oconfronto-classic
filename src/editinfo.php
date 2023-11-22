<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Informações Pessoais");
$acc = check_acc($secret_key, $db);

include(__DIR__ . "/templates/acc_header.php");

$error = 0;
$checknocur = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\CURSOR, $acc->id]);
$checkshowmail = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\SHOWMAIL, $acc->id]);

if ($_POST['submit']) {
    if ((!$_POST['rlname'] | !$_POST['showmail'] | !$_POST['showcur'] | !$_POST['remember'] | !$_POST['sex']) !== 0) {
        $errmsg .= "Por favor preencha todos os campos!";
        $error = 1;
    } elseif (strlen((string) $_POST['rlname']) < 3) {
        $errmsg .= "Seu nome deve ter mais que três caracteres!";
        $error = 1;
    } elseif ($_POST['showmail'] != 1 && $_POST['showmail'] != 2) {
        $errmsg .= "Um erro desconhecido ocorreu.";
        $error = 1;
    } elseif ($_POST['showcur'] != 1 && $_POST['showcur'] != 2) {
        $errmsg .= "Um erro desconhecido ocorreu.";
        $error = 1;
    } elseif ($_POST['remember'] != 1 && $_POST['remember'] != 2) {
        $errmsg .= "Um erro desconhecido ocorreu.";
        $error = 1;
    } elseif ($_POST['sex'] != 1 && $_POST['sex'] != 2 && $_POST['sex'] != 3) {
        $errmsg .= "Um erro desconhecido ocorreu.";
        $error = 1;
    }
    if ($error == 0) {

        if ($_POST['sex'] == 2) {
            $sexx = "m";
        } elseif ($_POST['sex'] == 3) {
            $sexx = "f";
        } else {
            $sexx = "n";
        }

        $rememberr = $_POST['remember'] == 2 ? "t" : "f";

        if ($_POST['showmail'] == 2) {
            if ($checkshowmail->recordcount() < 1) {
                $insert['player_id'] = $acc->id;
                $insert['value'] = \SHOWMAIL;
                $insertchecknocur = $db->autoexecute('other', $insert, 'INSERT');
            }
        } else {
            $deletechecknocur = $db->execute("delete from `other` where `value`=? and `player_id`=?", [\SHOWMAIL, $acc->id]);
        }

        if ($_POST['showcur'] == 2) {
            if ($checknocur->recordcount() < 1) {
                $insert['player_id'] = $acc->id;
                $insert['value'] = \CURSOR;
                $insertchecknocur = $db->autoexecute('other', $insert, 'INSERT');
            }
        } else {
            $deletechecknocur = $db->execute("delete from `other` where `value`=? and `player_id`=?", [\CURSOR, $acc->id]);
        }

        $query = $db->execute("update `accounts` set `name`=?, `sex`=?, `remember`=? where `id`=?", [$_POST['rlname'], $sexx, $rememberr, $acc->id]);
        $msg .= "Informações pessoais alteradas com sucesso!";
    }
}

$acc = check_acc($secret_key, $db);
$checknocur = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\CURSOR, $acc->id]);
$checkshowmail = $db->execute("select * from `other` where `value`=? and `player_id`=?", [\SHOWMAIL, $acc->id]);
?>
<br/><br/><br/>
<fieldset>
<p /><legend><b>Informações pessoais</b></legend><p />
<table width="100%">
<form method="POST" action="editinfo.php">
<tr><td width="40%"><b>Nome real</b>:</td><td><input type="text" name="rlname" value="<?=$acc->name?>" size="20"/></td></tr>
<?php
if ($acc->sex == "m") {
    echo "<tr><td width=\"40%\"><b>Sexo</b>:</td><td><select name=\"sex\"><option value=\"1\">Selecione</option><option value=\"2\" selected=\"selected\">Masculino</option><option value=\"3\">Feminino</option></select></td></tr>";
} elseif ($acc->sex == "f") {
    echo "<tr><td width=\"40%\"><b>Sexo</b>:</td><td><select name=\"sex\"><option value=\"1\">Selecione</option><option value=\"2\">Masculino</option><option value=\"3\" selected=\"selected\">Feminino</option></select></td></tr>";
} else {
    echo "<tr><td width=\"40%\"><b>Sexo</b>:</td><td><select name=\"sex\"><option value=\"1\" selected=\"selected\">Selecione</option><option value=\"2\">Masculino</option><option value=\"3\">Feminino</option></select></td></tr>";
}

if ($checkshowmail->recordcount() < 1) {
    echo "<tr><td width=\"40%\"><b>Mostrar email</b>:</td><td><select name=\"showmail\"><option value=\"1\" selected=\"selected\">Não</option><option value=\"2\">Sim</option></select> <font size=\"1\">" . $acc->email . " - <a href=\"changemail.php\">Alterar Email</a></font></td></tr>";
} else {
    echo "<tr><td width=\"40%\"><b>Mostrar email</b>:</td><td><select name=\"showmail\"><option value=\"1\">Não</option><option value=\"2\" selected=\"selected\">Sim</option></select> <font size=\"1\">" . $acc->email . " - <a href=\"changemail.php\">Alterar Email</a></font></td></tr>";
}

if ($acc->remember != \T) {
    echo "<tr><td width=\"40%\"><b>Lembrar Senha</b>:</td><td><select name=\"remember\"><option value=\"1\" selected=\"selected\">Não</option><option value=\"2\">Sim</option></select> <font size=\"1\">(entrar automatiamente ao visitar o jogo).</font></td></tr>";
} else {
    echo "<tr><td width=\"40%\"><b>Lembrar Senha</b>:</td><td><select name=\"remember\"><option value=\"1\">Não</option><option value=\"2\" selected=\"selected\">Sim</option></select> <font size=\"1\">(entrar automatiamente ao visitar o jogo).</font></td></tr>";
}

if ($checknocur->recordcount() < 1) {
    echo "<tr><td width=\"40%\"><b>Cursor customisado</b>:</td><td><select name=\"showcur\"><option value=\"1\" selected=\"selected\">Não</option><option value=\"2\">Sim</option></select></td></tr>";
} else {
    echo "<tr><td width=\"40%\"><b>Cursor customisado</b>:</td><td><select name=\"showcur\"><option value=\"1\">Não</option><option value=\"2\" selected=\"selected\">Sim</option></select></td></tr>";
}
?>
<tr><td colspan="2" align="center"><input type="submit" name="submit" value="Enviar"></td></tr>
</table>
</form>
<p /><font color=green><?=$msg?></font><p />
<p /><font color=red><?=$errmsg?></font><p />
</fieldset>
<br/>
<a href="characters.php">Voltar</a>.

<?php
    include(__DIR__ . "/templates/acc_footer.php");
?>