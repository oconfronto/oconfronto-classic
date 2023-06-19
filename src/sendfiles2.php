<?php
	include("lib.php");
	define("PAGENAME", "Evento");
	$player = check_user($secret_key, $db);
	include("checkwork.php");
	include("templates/private_header.php");
?>
<b>ATENÇÃO:</b><br/>
Você, jogador de oconfronto, já deve ter percebido que estamos precisando de imagens para armaduras, elmos, botas, etc. Por isso, estamos realizando um concurço.
<br/><br/>

<fieldset>
<legend><b>Como funciona</b></legend>
<b>1º</b> - <i>Visite o ferreiro e procure um item que está sem imagem.</i><br />
<b>2º</b> - <i>Desenhe o seu item em tamanho 32x32, e salve-o em qualidade Bitmap (BMP), sem fundo transparente.</i><br />
<b>3º</b> - <i>Iremos analizar seu iten, ele será aprovado ou não, mas nem sempre vamos responder falando sobre nossa decisão.</i><br />
<b>4º</b> - <i>Se seu item for aprovado você receberá o item no jogo e/ou uma recompensa.</i><br />
</fieldset>
<br/><br/>

<fieldset>
<legend><b>LEMBRE-SE</b></legend>
<b>1º</b> - <i><b>O item vai ter que ser desenhado, apenas por você, caso contrário você poderá ser banido.</b></i><br />
<b>2º</b> - <i>Seu item deve seguir o padrão do jogo, caso contrário não será aceito. Exemplos:</i><br />
<center><img src="http://www.oconfronto.kinghost.net/images/itens/doubleaxe.gif"> <img src="http://www.oconfronto.kinghost.net/images/itens/goldensword.gif"> <img src="http://www.oconfronto.kinghost.net/images/itens/crusarmor.gif"> <img src="http://www.oconfronto.kinghost.net/images/itens/sainthelmet.gif"></center>
</fieldset>
<br />
<center><font color="red">Atingimos o numero máximo de espadas, e apartir de agora não iremos mais aprova-las.</font></center>
<br />
<fieldset>
<legend><b>Enviar Imagen</b></legend>
<form action="sendfiles2.php" method="post" enctype="multipart/form-data">
<input type="file" name="foto" size="30"><input type="submit" name="upload" value="Enviar">
</form>
</fieldset>
<font size=\"1\">Ao enviar permito que minha imagem seja usada no jogo.</font><br/><br/>
<?php

if ($_POST['upload'])
{
$erro = $config = array();

// Prepara a variável do arquivo
$arquivo = isset($_FILES["foto"]) ? $_FILES["foto"] : FALSE;

// Tamanho máximo do arquivo (em bytes)
$config["tamanho"] = 1000000;
// Largura máxima (pixels)
$config["largura"] = 500;
// Altura máxima (pixels)
$config["altura"] = 500;

// Formulário postado... executa as ações
if($arquivo)
{
// Verifica se o mime-type do arquivo é de imagem
if(!eregi("^image\/(pjpeg|jpeg|gif|bmp)$", $arquivo["type"]))
{
$erro[] = "<span style=\"color: white; border: solid 1px ; background: red;\">Arquivo em formato inválido!</span><br/>- A imagem deve ser bmp.";
}
else
{
// Verifica tamanho do arquivo
if($arquivo["size"] > $config["tamanho"])
{
$erro[] = "<span style=\"color: white; border: solid 1px ; background: red;\">Arquivo em tamanho muito grande!</span><br>- A imagem deve ser de no máximo " . $config["tamanho"] . " bytes.";
}

// Para verificar as dimensões da imagem
$tamanhos = getimagesize($arquivo["tmp_name"]);

// Verifica largura
if($tamanhos[0] > $config["largura"])
{
$erro[] = "Largura da imagem não deve ultrapassar " . $config["largura"] . " pixels";
}

// Verifica altura
if($tamanhos[1] > $config["altura"])
{
$erro[] = "Altura da imagem não deve ultrapassar " . $config["altura"] . " pixels";
}
}

// Imprime as mensagens de erro
if(sizeof($erro))
{
foreach($erro as $err)
{
echo " - " . $err . "<BR>";
}
}

// Verificação de dados OK, nenhum erro ocorrido, executa então o upload...
else
{
// Pega extensão do arquivo
preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $arquivo["name"], $ext);


// Gera um nome único para a imagem
$imagem_nome = md5(uniqid(time())) . "." . $ext[1];

// Caminho de onde a imagem ficará
$imagem_dir = "concur/" . $imagem_nome;

// Faz o upload da imagem
move_uploaded_file($arquivo["tmp_name"], $imagem_dir);

	$endereco = "http://www.oconfronto.kinghost.net/concur/".$imagem_nome."";
		$insert['player'] = $player->username;
		$insert['img'] = $endereco;
		$query = $db->autoexecute('concur', $insert, 'INSERT');

echo "<span style=\"color: white; border: solid 1px; background: green;\">Sua imagem foi enviada com sucesso!</span><br/>";
echo "<font size=\"1\">Iremos avalia-la, e se for aceita será adicionada ao jogo e você reconpensado.</font>";
}
}
}


	include("templates/private_footer.php");
?>