<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include(__DIR__ . "/lib.php");
include(__DIR__ . '/bbcode.php');
$bbcode = new bbcode;
define("PAGENAME", "Principal");

if (isset($_SESSION['accid']) && isset($_SESSION['hash']) && $_SESSION['accid'] > 0 && $_SESSION['hash']) {

		$check = sha1($_SESSION['accid'] . $_SERVER['REMOTE_ADDR'] . $secret_key);
		if ($check == $_SESSION['hash'])
		{
			$rematual = $db->GetOne("select `remember` from `accounts` where `id`=?", [$_SESSION['accid']]);
			if ($rematual == 't'){
			header("Location: characters.php");
			exit;
			}
		}
}


//Begin checking if user has tried to login
$error = 0; //Error count
$errormsg = "<font color=\"red\">"; //Error message to be displayed in case of error (modified below depending on error)


if (isset($_POST['login']))
{
	$tentativas = $db->GetOne("select `tries` from `ip` where `ip`=?", [$ip]);

	if (!$_POST['username'])
	{
		$errormsg .= "Por favor digite sua conta.";
		$error = 1;
	}
	elseif (!$_POST['password'])
	{
		$errormsg .= "Por favor digite sua senha.";
		$error = 1;
	}
	elseif ($tentativas > 9)
	{
		$errormsg .= "Voc?errou sua senha 10 vezes seguidas. Aguarde 30 minutos para poder tentar novamente.";
		$error = 1;
	} elseif ($error === 0) {
     $query = $db->execute("select `id`, `conta` from `accounts` where `conta`=? and `password`=?", [$_POST['username'], sha1((string) $_POST['password'])]);
     if ($query->recordcount() == 0)
   		{
   			$restantes = ceil(10 - $tentativas);
   			$errormsg .= "Conta ou senha incorreta! (" . $restantes . " tentativas restantes).";
   
   			$bloqueiaip = $db->execute("select `tries` from `ip` where `ip`=?", [$ip]);
   			if ($bloqueiaip->recordcount() == 0) {
   			$insert['ip'] = $ip;
   			$insert['tries'] = 1;
   			$insert['time'] = time();
   			$query = $db->autoexecute('ip', $insert, 'INSERT');
   			}elseif ($bloqueiaip->recordcount() > 0) {
   			$query = $db->execute("update `ip` set `tries`=`tries`+1 where `ip`=?", [$ip]);
   			}
   
   			$error = 1;
   			
   			//Clear user's session data
   			session_unset();
   			session_destroy();
   		}
   		else
   		{
   		
   			$acc = $query->fetchrow();
   			
   				$query = $db->execute("update `accounts` set `last_ip`=? where `id`=?", [$ip, $acc['id']]);
   
   				$hash = sha1($acc['id'] . $ip . $secret_key);
   				$_SESSION['accid'] = $acc['id'];
   				$_SESSION['hash'] = $hash;
   				header("Location: characters.php");
   		}
 }

}$errormsg .= "</font>";


include(__DIR__ . "/templates/header.php");
?>
<script type="text/javascript" src="js/inventariojquery.js"></script>
<script type="text/javascript" src="js/inventario.js"></script>

<form method="POST" action="index.php">
<table align="center">
<tr>
<td><b>Conta:</b> <input type="text" name="username" value="<?php echo $_POST['username']?>" size="18"/></td>
<td><b>Senha:</b> <input type="password" name="password" size="17"/></td>
<td><input name="login" type="submit" value="Entrar" /></td>
</tr>
</table>
<font size="1"><a href="forgot.php">Esqueceu a senha?</a> <?php echo ($error==1)?$errormsg:""?></font>
</form>
<table width="100%">
<tr><td align="center" bgcolor="#E1CBA4"><b>�ltimas Noticias</b></td></tr>

<?php
$noticiaid = 1;
$query55 = $db->execute("select `topic`, `detail`, `user_id`, `datetime` from `forum_question` where `category`='noticias' order by `postado` desc limit 0,5");

while($news = $query55->fetchrow())
{
$query = $db->execute("select `username` from `players` where `id`=?", [$news['user_id']]);
$user = $query->fetchrow();


		echo "<tr>";
		echo "<td class=\"off\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\" onclick=\"ex_news" . $noticiaid . "();\"><b>" . $news['topic'] . "</b></td>";
		echo "</tr>";
		echo "<tr><td bgcolor=\"#f2e1ce\"><div style=\"display: none;\" id=\"news" . $noticiaid . "\">";
		$noticia = $news['detail'];
		echo $bbcode->parse($noticia);
		echo "<br/><b><font size=\"1\">Not�cia publicada por " . $user['username'] . " em " . $news['datetime'] . ".</font></b></td></tr></div>";
		$noticiaid ++;
}

echo "</table>";
echo "<table width=\"100%\"><tr>";
echo "<td><center><a href=\"http://naruto.ativoforum.com\" target=\"_blank\"><img src=\"http://i48.servimg.com/u/f48/12/18/57/45/banner19.gif\" width=\"88\" height=\"31\" border=\"0\"></a></center></td>";
echo "<td><center><a href=\"http://www.freedomain.co.nr/\" target=\"_blank\"><img src=\"./images/conr.gif\" width=\"88\" height=\"31\" border=\"0\" alt=\"Free Domains Forwarding\" /></a></center></td>";
echo "</tr></table>";


include(__DIR__ . "/templates/footer.php");
?>
