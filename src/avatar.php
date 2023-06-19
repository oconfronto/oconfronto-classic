<?php
include("lib.php");
define("PAGENAME", "Editar perfil");
$player = check_user($secret_key, $db);

$error = 0;

include("templates/private_header.php");

if ($_POST['submit']) {
    if (!$_POST['avatar']) {
        $errmsg .= "Por favor preencha todos os campos!";
        $error = 1;
	}

	else if (($_POST['avatar']) and (!@GetImageSize($_POST['avatar']))) {
        $errmsg .= "O endereço desta imagem não é válido!";
        $error = 1;
	}

    if ($error == 0) {

	if (!$_POST['avatar']){
	$avat = "anonimo.gif";
	}else{
	$avat = $_POST['avatar'];
	}

        $query = $db->execute("update `players` set `avatar`=? where `id`=?", array($avat, $player->id));
        $msg .= "Você alterou seu avatar com sucesso!";
    }
}

?>

<fieldset>
<legend><b>Editar perfil</b></legend>
<table width="100%">
<form method="POST" action="avatar.php">
<tr><td width="30%"><b>Comentários</b>:</td><td><a href="edit_comment.php">Clique aqui para editar seus comentários.</a></td></tr>
<tr><td width="30%"><b>Avatar</b>:</td><td><input type="text" name="avatar" value="<?=$player->avatar?>" size="45"/></td></tr>
<tr><td colspan="2" align="center"><input type="submit" name="submit" value="Salvar Dados"></td></tr>
</table>
</form>
<p /><font color=green><?=$msg?></font><p />
<p /><font color=red><?=$errmsg?></font><p />
</fieldset>

<?php include("templates/private_footer.php");
?>