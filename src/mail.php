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
define("PAGENAME", "Mensagens");
$player = check_user($secret_key, $db);

$errormsg = "<font color=\"red\">";
$errors = 0;
if ($_POST['sendmail'])
{
	//Process mail info, show success message
	$query = $db->execute("select `id`, `gm_rank` from `players` where `username`=?", [$_POST['to']]);
	if ($query->recordcount() == 0)
	{
		$errormsg .= "Este usuário não existe!<br />";
		$errors = 1;
	}
	$sendto = $query->fetchrow();

	if (!$_POST['body'])
	{
		$errormsg .= "Você precisa digitar uma mensagem!<br />";
		$errors = 1;
	}
	if ($sendto['gm_rank'] > 10 && $player->gm_rank < 2)
	{
		$errormsg .= "Você não pode enviar mensagens diretamente para o administrador!<br />";
		$errormsg .= "Se o assunto for sério mande uma mensagem para um de nossos moderadores:<br/>";
		$query4 = $db->execute("select `username` from `players` where `gm_rank`>2 and `id`!=1 order by rand()");

		while($member1 = $query4->fetchrow())
		{
		$errormsg .= "<a href=\"mail.php?act=compose&to=" . $member1['username'] . "\">";
		$errormsg .= $member1['username'];
		$errormsg .= "</a> | ";
		}
		$errormsg .= "<br />";
		$errors = 1;
	}
	$ignorado = $db->execute("select * from `ignored` where `uid`=? and `bname`=?", [$sendto['id'], $player->username]);
	if ($ignorado->recordcount() > 0)
	{
		$errormsg .= "Você está sendo ignorado por este usuário e não poderá enviar mensagens para ele.<br />";
		$errors = 1;
	}

	
	if ($errors != 1)
	{
		$insert['to'] = $sendto['id'];
		$insert['from'] = $player->id;
		$insert['body'] = $_POST['body'];
		$insert['body'] = htmlentities((string) $_POST['body'], ENT_QUOTES);
		$insert['subject'] = ($_POST['subject'] == "")?"Sem Assunto":$_POST['subject'];
		$insert['time'] = time();
		$query = $db->execute("insert into `mail` (`to`, `from`, `body`, `subject`, `time`) values (?, ?, ?, ?, ?)", [$insert['to'], $insert['from'], $insert['body'], $insert['subject'], $insert['time']]);
		if ($query)
		{
			include(__DIR__ . "/templates/private_header.php");
			echo "<fieldset>";
			echo "<legend><b>Sucesso</b></legend>";
			echo "Sua mensagem foi enviada com sucesso para " . $_POST['to'] . "!<br/><a href=\"mail.php\">Voltar.</a>";
			echo "</fieldset>";
			include(__DIR__ . "/templates/private_footer.php");
			exit;
		}
  $errormsg .= "Erro, a mensagem não pode ser enviada.";
  //Add to admin error log, or whatever, maybe for another version ;)

	}
}
$errormsg .= "</font><br />\n";

include(__DIR__ . "/templates/private_header.php");
?>

<script>
function checkAll() {
	count = document.inbox.elements.length;
    for (i=0; i < count; i++) 
	{
    	if(document.inbox.elements[i].checked == 1)
    		{document.inbox.elements[i].checked = 0; document.inbox.check.checked=0;}
    	else {document.inbox.elements[i].checked = 1; document.inbox.check.checked=1;}
	}
}
</script>


<a href="mail.php">Caixa de entrada</a> | <a href="mail.php?act=enviadas">Mensagens enviadas</a> | <a href="mail.php?act=ignore">Usuários ignorados</a> | <a href="mail.php?act=compose">Escrever mensagem</a>
<br /><br />
<?php
switch($_GET['act'])
{
	case "ignore": //Reading a message
	if($_GET['add']){
	if($_GET['add'] == $player->username){
		echo "Você não pode ignorar você mesmo!<br><a href=\"mail.php?act=ignore\">Voltar</a>.";
	include(__DIR__ . "/templates/private_footer.php");
		exit;
	}
	$quereya = $db->execute("select * from `ignored` where `bname`=? and `uid`=?", [$_GET['add'], $player->id]);
	if ($quereya->recordcount() > 0){
	echo "Você já está ignorando este usuário!<br><a href=\"mail.php?act=ignore\">Voltar</a>.";
	include(__DIR__ . "/templates/private_footer.php");
	exit;
	}
	$quereya = $db->execute("select `username` from `players` where `username`=?", [$_GET['add']]);
	if ($quereya->recordcount() == 0){
	echo "Este usuário não existe!<br><a href=\"mail.php?act=ignore\">Voltar</a>.";
	include(__DIR__ . "/templates/private_footer.php");
	exit;
	}
 $insert['uid'] = $player->id;
 $insert['bname'] = $_GET['add'];
 $query = $db->autoexecute('ignored', $insert, 'INSERT');
 if($query){
				echo "" . $_GET['add'] . " foi ignorado!<br><a href=\"mail.php?act=ignore\">Voltar</a>.";
				include(__DIR__ . "/templates/private_footer.php");
				exit;
			}
 echo "Um erro desconhecido ocorreu!<br><a href=\"mail.php?act=ignore\">Voltar</a>.";
 include(__DIR__ . "/templates/private_footer.php");
 exit;
}
if($_GET['delete']){
$query = $db->execute("delete from `ignored` where `uid`=? and `bname`=?", [$player->id, $_GET['delete']]);
if($query){
echo "Agora " . $_GET['delete'] . " não está mais sendo ignorado!<br><a href=\"mail.php?act=ignore\">Voltar</a>.";
include(__DIR__ . "/templates/private_footer.php");
exit;
}
}

	echo "<fieldset>";
	echo "<legend><b>Usuários ignorados</b></legend>";

$query = $db->execute("select `bname` from `ignored` WHERE `uid`=?", [$player->id]);
if ($query->recordcount() == 0)
{
	echo "Você não está ignorando ninguém.";
}
else
{
	while($friend = $query->fetchrow())
	{
		echo "<table width=\"100%\">\n";
		echo "<tr><td width=\"60%\"><a href=\"profile.php?id=".$friend['bname']."\">" . $friend['bname'] . "</a></td><td><a href=\"mail.php?act=compose&to=".$friend['bname']."\">Mensagem</a> | <a href=\"mail.php?act=ignore&delete=".$friend['bname']."\">Deletar</a></td></tr>";
		echo "</table>";
	}
}

	echo "</fieldset>";

echo "você está ignorando ".$query->recordcount()." usuário(s)";
	echo "<br/><br/>\n";
	echo "<fieldset>\n";
	echo "<legend><b>Ignorar usuário</b></legend>\n";
	echo "<form method=\"get\" action=\"mail.php?act=ignore\">\n";
	echo "<table width=\"100%\">\n";
	echo "<tr>\n<td width=\"30%\">Usuário:</td>\n<td width=\"40%\"><input type=\"hidden\" name=\"act\" value=\"ignore\"/><input type=\"text\" name=\"add\" /></td>";
	echo "<td width=\"30%\"><input type=\"submit\" value=\"Ignorar\" /></td></tr>\n";
	echo "</table>\n";
	echo "</form>\n</fieldset>\n";
break;


	case "read": //Reading a message
		$query = $db->execute("select `id`, `to`, `from`, `subject`, `body`, `time`, `status` from `mail` where `id`=?", [$_GET['id']]);
		if ($query->recordcount() == 1)
		{
			$msg = $query->fetchrow();

  			if ($player->id != $msg['to'] && $player->id != $msg['from']){
			echo "OPS! Parece que um erro ocorreu.";
			break;
			}

			echo "<table width=\"100%\" border=\"0\">\n";
			$to = $db->GetOne("select `username` from `players` where `id`=?", [$msg['to']]);
			echo "<tr><td width=\"20%\"><b>Para:</b></td><td width=\"80%\"><a href=\"profile.php?id=" . $to . "\">" . $to . "</a></td></tr>\n";
			$from = $db->GetOne("select `username` from `players` where `id`=?", [$msg['from']]);
			echo "<tr><td width=\"20%\"><b>De:</b></td><td width=\"80%\"><a href=\"profile.php?id=" . $from . "\">" . $from . "</a></td></tr>\n";
			echo "<tr><td width=\"20%\"><b>Data:</b></td><td width=\"80%\">" . date("F j, Y, g:i a", $msg['time']) . "</td></tr>";
			echo "<tr><td width=\"20%\"><b>Assunto:</b></td><td width=\"80%\">" . stripslashes((string) $msg['subject']) . "</td></tr>";
			echo "<tr><td width=\"20%\"><b>Mensagem:</b></td><td width=\"80%\">" . $bbcode->parse(stripslashes(nl2br((string) $msg['body']))) . "</td></tr>";
			echo "</table>";
  			if ($player->id == $msg['to'] && $msg['status'] == "unread"){
			$query = $db->execute("update `mail` set `status`='read' where `id`=?", [$msg['id']]);
			}
			echo "<br /><br />\n";
			echo "<table width=\"30%\">\n";
			echo "<tr><td width=\"50%\">\n";
  			if ($player->id == $msg['to']){
			echo "<form method=\"post\" action=\"mail.php?act=compose\">\n";
			echo "<input type=\"hidden\" name=\"to\" value=\"" . $from . "\" />\n";
			echo "<input type=\"hidden\" name=\"subject\" value=\"RE: " . stripslashes((string) $msg['subject']) . "\" />\n";
			$reply = explode("\n", (string) $msg['body']);
			foreach($reply as $key=>$value)
			{
				$reply[$key] = ">>" . $value;
			}
			$reply = implode("\n", $reply);
			echo "<input type=\"hidden\" name=\"body\" value=\"\n\n\n" . $reply . "\" />\n";
			echo "<input type=\"submit\" value=\"Responder\" />\n";
			echo "</form>\n";
			echo "</td><td width=\"50%\">\n";
			echo "<form method=\"post\" action=\"mail.php?act=delete\">\n";
			echo "<input type=\"hidden\" name=\"id\" value=\"" . $msg['id'] . "\" />\n";
			echo "<input type=\"submit\" name=\"delone\" value=\"Deletar\" />\n";
			echo "</form>\n";
			}
			echo "</td></tr>\n</table>";
		}else{
			echo "OPS! Parece que um erro ocorreu.2";
		}
		break;
	
	case "compose": //Composing mail (justt he form, processing is at the top of the page)
		echo $errormsg;
		echo "<fieldset>";
		echo "<legend><b>Escrever Mensagem</b></legend>";
		echo "<form method=\"POST\" action=\"mail.php?act=compose\">\n";
		echo "<table width=\"100%\" border=\"0\">\n";
		echo "<tr><td width=\"20%\"><b>Para:</b></td><td width=\"80%\"><input type=\"text\" name=\"to\" value=\"";
		echo ($_POST['to'] != "")?$_POST['to']:$_GET['to'];
		echo "\" /></td></tr>\n";
		echo "<tr><td width=\"20%\"><b>Assunto:</b></td><td width=\"80%\"><input type=\"text\" name=\"subject\" value=\"";
		echo ($_POST['subject'] != "")?stripslashes((string) $_POST['subject']):stripslashes((string) $_GET['subject']);
		echo "\" /></td></tr>\n";
		echo "<tr><td width=\"20%\"><b>Mensagem:</b></td><td width=\"80%\"><textarea name=\"body\" rows=\"15\" cols=\"50\">";
		echo ($_POST['body'] != "")?stripslashes(stripslashes((string) $_POST['body'])):stripslashes(stripslashes((string) $_GET['body']));
		echo "</textarea></td></tr>\n";
		echo "<tr><td></td><td><input type=\"submit\" value=\"Enviar\" name=\"sendmail\" /></td></tr>\n";
		echo "</table>\n";
		echo "</form>\n";
		echo "</fieldset>";
		break;
	
	case "delete":
		if ($_POST['delone']) {
      //Deleting message from viewing page, single delete
      if (!$_POST['id'])
   			{
   				echo "Uma mensagem deve ser selecionada!";
   			}
   			else
   			{
   				$query = $db->getone("select count(*) as `count` from `mail` where `id`=? and `to`=?", [$_POST['id'], $player->id]);
   				if (($query['count'] = 0) !== 0) {
           //In case there are some funny guys out there ;)
           echo "Esta(s) mensagem não pertence a você!";
       } elseif (!$_POST['deltwo']) {
           echo "Você tem certeza que quer deletar esta(s) mensagem(s)?<br /><br />\n";
           echo "<form method=\"post\" action=\"mail.php?act=delete\">\n";
           echo "<input type=\"hidden\" name=\"id\" value=\"" . $_POST['id'] . "\" />\n";
           echo "<input type=\"hidden\" name=\"deltwo\" value=\"1\" />\n";
           echo "<input type=\"submit\" name=\"delone\" value=\"Deletar\" />\n";
           echo "</form>";
       } else
  					{
  						$query = $db->execute("delete from `mail` where `id`=?", [$_POST['id']]);
  						echo "A mensagem foi deletada com sucesso!";
  						//Redirect back to inbox, or show success message
  						//Can be changed in the admin panel
  					}
   			}
  } elseif ($_POST['delmultiple']) {
      //Deleting messages from inbox, multiple selections
      if (!$_POST['id'])
   			{
   				echo "A message must be selected!";
   			}
   			else
   			{
   				foreach($_POST['id'] as $msg)
   				{
   					$query = $db->getone("select count(*) as `count` from `mail` where `id`=? and `to`=?", [$msg, $player->id]);
   					if (($query['count'] = 0) !== 0)
   					{
   						//In case there are some funny guys out there ;)
   						echo "Esta(s) mensagem(s) não pertence a você!";
   						$delerror = 1;
   					}
   				}
   				if (!$delerror)
   				{
   					if (!$_POST['deltwo'])
   					{
   						echo "Você tem certeza que quer deletar esta(s) mensagem(s)?<br /><br />\n";
   						echo "<form method=\"post\" action=\"mail.php?act=delete\">\n";
   						foreach($_POST['id'] as $msg)
   						{
   							echo "<input type=\"hidden\" name=\"id[]\" value=\"" . $msg . "\" />\n";
   						}
   						echo "<input type=\"hidden\" name=\"deltwo\" value=\"1\" />\n";
   						echo "<input type=\"submit\" name=\"delmultiple\" value=\"Deletar\" />\n";
   						echo "</form>";
   					}
   					else
   					{
   						foreach($_POST['id'] as $msg)
   						{
   							$query = $db->execute("delete from `mail` where `id`=?", [$msg]);
   						}
   						echo "A mensagem foi deletada com sucesso!";
   						//Redirect back to inbox, or show success message
   						//Can be changed in the admin panel (TODO)
   					}
   				}
   			}
  }
		break;

	case "enviadas":
		echo "<fieldset>";
		echo "<legend><b>Mensagens Enviadas</b></legend>";
		echo "<table width=\"100%\" border=\"0\">\n";
		echo "<tr>\n";
		echo "<td width=\"20%\"><b>Para</b></td>\n";
		echo "<td width=\"35%\"><b>Assunto</b></td>\n";
		echo "<td width=\"40%\"><b>Data</b></td>\n";
		echo "</tr>\n";
		$query = $db->execute("select `id`, `to`, `subject`, `time` from `mail` where `from`=? order by `time` desc", [$player->id]);
		if ($query->recordcount() > 0)
		{
			$bool = 1;
			while($msg = $query->fetchrow())
			{
				echo "<tr class=\"row" . $bool . "\">\n";
				$to = $db->GetOne("select `username` from `players` where `id`=?", [$msg['to']]);
				$servto = $db->GetOne("select `serv` to `players` where `id`=?", [$msg['to']]);
				$rankto = $db->GetOne("select `gm_rank` to `players` where `id`=?", [$msg['to']]);
				echo "<td width=\"20%\">";
				if ($rankto > 2){
				echo "<a href=\"profile.php?id=" . $to . "\"><font color=\"green\">" . $to . "</font></a>";
				} elseif ($servto != $player->serv){
				echo "<a href=\"profile.php?id=" . $to . "\"><font color=\"red\">" . $to . "</font></a>";
				}else{
				echo "<a href=\"profile.php?id=" . $to . "\">" . $to . "</a>";
				}
				echo "</td>\n";
				echo "<td width=\"40%\">";
				echo "<a href=\"mail.php?act=read&id=" . $msg['id'] . "\">" . stripslashes((string) $msg['subject']) . "</a>";
				echo "</td>\n";
				echo "<td width=\"40%\">" . date("F j, Y, g:i a", $msg['time']) . "</td>\n";
				echo "</tr>\n";
				$bool = ($bool==1)?2:1;
			}
		}
		else
		{
			echo "<tr class=\"row1\">\n";
			echo "<td colspan=\"4\"><b>Sem mensagens</b></td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "</fieldset>";
		break;

	
	default: //Show inbox
		echo "<fieldset>";
		echo "<legend><b>Caixa de Entrada</b></legend>";
		echo "<form method=\"post\" action=\"mail.php?act=delete\" name=\"inbox\">\n";
		echo "<table width=\"100%\" border=\"0\">\n";
		echo "<tr>\n";
		echo "<td width=\"5%\"><input type=\"checkbox\" onclick=\"javascript: checkAll();\" name=\"check\" /></td>\n";
		echo "<td width=\"20%\"><b>De</b></td>\n";
		echo "<td width=\"35%\"><b>Assunto</b></td>\n";
		echo "<td width=\"40%\"><b>Data</b></td>\n";
		echo "</tr>\n";
		$query = $db->execute("select `id`, `from`, `subject`, `time`, `status` from `mail` where `to`=? order by `time` desc limit 25", [$player->id]);
		if ($query->recordcount() > 0)
		{
			$qsfwwwy = $db->execute("select `id`, `from`, `subject`, `time`, `status` from `mail` where `to`=?", [$player->id]);
			if ($qsfwwwy->recordcount() > 25)
			{
			echo "<br/>";
			echo "<center><b>Mostrando 25 mensagens de " . $qsfwwwy->recordcount() . ". Delete mensagens para exibir as outras.</b></center>";
			echo "<br/>";
			}

			$bool = 1;
			while($msg = $query->fetchrow())
			{
				echo "<tr class=\"row" . $bool . "\">\n";
				echo "<td width=\"5%\"><input type=\"checkbox\" name=\"id[]\" value=\"" . $msg['id'] . "\" /></td>\n";
				$from = $db->GetOne("select `username` from `players` where `id`=?", [$msg['from']]);
				$servfrom = $db->GetOne("select `serv` from `players` where `id`=?", [$msg['from']]);
				$rankfrom = $db->GetOne("select `gm_rank` from `players` where `id`=?", [$msg['from']]);
				echo "<td width=\"20%\">";
				echo ($msg['status'] == "unread")?"<b>":"";
				if ($rankfrom > 2){
				echo "<a href=\"profile.php?id=" . $from . "\"><font color=\"green\">" . $from . "</font></a>";
				} elseif ($servfrom != $player->serv){
				echo "<a href=\"profile.php?id=" . $from . "\"><font color=\"red\">" . $from . "</font></a>";
				}else{
				echo "<a href=\"profile.php?id=" . $from . "\">" . $from . "</a>";
				}
				echo ($msg['status'] == "unread")?"</b>":"";
				echo "</td>\n";
				echo "<td width=\"35%\">";
				echo ($msg['status'] == "unread")?"<b>":"";
				echo "<a href=\"mail.php?act=read&id=" . $msg['id'] . "\">" . stripslashes((string) $msg['subject']) . "</a>";
				echo ($msg['status'] == "unread")?"</b>":"";
				echo "</td>\n";
				echo "<td width=\"40%\">" . date("F j, Y, g:i a", $msg['time']) . "</td>\n";
				echo "</tr>";
				$bool = ($bool==1)?2:1;
			}
		}
		else
		{
			echo "<tr class=\"row1\">\n";
			echo "<td colspan=\"4\"><b>Sem mensagens</b></td>\n";
			echo "</tr>\n";
		}
		echo "</table>";
		echo "</fieldset>";
		echo "<input type=\"submit\" name=\"delmultiple\" value=\"Deletar Selecionados\" />\n";
		echo "</form>";
		break;
}

include(__DIR__ . "/templates/private_footer.php");
?>