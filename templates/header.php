<html>
<head>
<title>O Confronto :: <?php echo PAGENAME?></title>
<meta name="keywords" content="confronto, confronto medieval, confrontos medievais, mmorpg, rpg online, webgame, jogo online, jogos online, diversão online, medieval, batalhas, jogo medieval, jogos medievais, tibia" />
<meta name="description" content="Venha confrontar monstros e jogadores, neste incrivel e maravilhoso game medieval, sobreviva a este desafio online e totalmente gratuito! Jogue Online." />
<meta name="RATING" content="GENERAL" />
<meta name="audience" content="all" />
<meta name="LANGUAGE" content="Portuguese" />
<meta name="ROBOTS" content="index,follow" />
<meta name="GOOGLEBOT" content="index,follow" />
<meta name="revisit-after" content="3 Days" />
<link rel="stylesheet" type="text/css" href="./css/header.css" />
<link rel="stylesheet" type="text/css" href="./css/cssverticalmenu.css" />
<link rel="stylesheet" href="./css/lightbox.css" type="text/css" media="screen" />

<script type="text/javascript" src="./templates/cssverticalmenu.js"></script>
<script type="text/javascript" src="./js/lightbox/prototype.js"></script>
<script type="text/javascript" src="./js/lightbox/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="./js/lightbox/lightbox.js"></script>

</head>
<body>
<?php
if ($_GET['r'])
{
$usaar = $_GET['r'];
}
else
{
$usaar = "1";
}

$deletechecknosite1 = $db->execute("delete from `online` where `time`<?", array((time() - 20)));
$deletechecknosite2 = $db->execute("delete from `login` where `time`<?", array((time() - 20)));
?>

<table width="760" align="center">
<tr><td>
<div id="footer">
<div id="baixo-text"></div>
</div>
<div id="wrapper">
<center><img src="./images/topo.jpg" alt="O Confronto - MMORPG" border="0"></center>
<div id="footer-text"><div id="textoAviso">Bem-vindo visitante, <a href="register.php?r=<?php echo $usaar?>">clique aqui</a> para se cadastrar e começar a jogar!</div></div>
<div id="left">
<div class="left-section">
<br/>

<ul id="verticalmenu" class="glossymenu">
<li><a href="index.php?r=<?php echo $usaar?>">Principal</a></li>
<li><a href="register.php?r=<?php echo $usaar?>">Cadastro</a></li>
</ul>
<br/><br/>
<ul id="verticalmenu" class="glossymenu">
<li><a href="sobre.php?r=<?php echo $usaar?>">Sobre o Jogo</a></li>
<li><a href="images.php?r=<?php echo $usaar?>">Imagens</a></li>
<li><a href="fansites.php?r=<?php echo $usaar?>">Fansites</a></li>
</ul>

<br />
<center><a href="http://www.otserv.com.br" target="_blank"><img src="images/parceirot.jpg" width="120" height="60" border="0"></a></center>
<br />
<center>
<font size="1"><a href="#" onclick="javascript:window.open('parceria.html', '_blank','top=100, left=100, height=400, width=400, status=no, menubar=no, resizable=no, scrollbars=yes, toolbar=no, location=no, directories=no');">Seja nosso Parceiro</a></font>
</center>
</div>
</div>

<div id="right">
<div id="content">