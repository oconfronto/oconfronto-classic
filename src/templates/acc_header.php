<html>
<head>
<title>O Confronto :: <?=PAGENAME?></title>
<meta name="keywords" content="confronto, confronto medieval, confrontos medievais, mmorpg, rpg online, webgame, jogo online, jogos online, diversão online, medieval, batalhas, jogo medieval, jogos medievais" />
<meta name="description" content="Venha confrontar monstros e jogadores, neste incrivel e maravilhoso game medieval, sobreviva a este desafio online e totalmente gratuito! Jogue Online." />
<meta name="RATING" content="GENERAL" />
<meta name="audience" content="all" />
<meta name="LANGUAGE" content="Portuguese" />
<meta name="ROBOTS" content="index,follow" />
<meta name="GOOGLEBOT" content="index,follow" />
<meta name="revisit-after" content="3 Days" />
<?php
		$checknocur000 = $db->execute("select * from `other` where `value`=? and `player_id`=?", array(cursor, $acc->id));
		if ($checknocur000->recordcount() > 0) {
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/acc_style_2.css\" />";
		}else{
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/acc_style_1.css\" />";
		}
?>

<Script Language=JavaScript>
var nText = new Array()
nText[0] = "<font size=\"1\">Escolha sua vocação.</font>";
nText[1] = "<font size=\"1\">Os Cavaleiros possuem uma grande defesa mas um baixo ataque.</font>";
nText[2] = "<font size=\"1\">Os Magos são nivelados em ataque e defesa.</font>";
nText[3] = "<font size=\"1\">Os Arqueiros possuem um bom ataque mas uma defesa fraca.</font>"
function swapText(isList){
txtIndex = isList.selectedIndex;
document.getElementById('textDiv').innerHTML = nText[txtIndex];
}

</Script>

<?php
$online1 = $db->execute("select * from `online` where `serv`=1");
$registrados1 = $db->execute("select `id` from `players` where `serv`=1");
$top1 = $db->GetOne("select `level` from `players` where `serv`=1 and `gm_rank`<5 order by `level` desc");

$online2 = $db->execute("select * from `online` where `serv`=2");
$registrados2 = $db->execute("select `id` from `players` where `serv`=2");
$top2 = $db->GetOne("select `level` from `players` where `serv`=2 and `gm_rank`<5 order by `level` desc");
?>
<Script Language=JavaScript>
var nServ = new Array()
nServ[0] = "<font size=\"1\">Selecione um servidor para obter informações.</font>";
nServ[1] = "<table><tr><td width=\"90px\"><font size=\"2\"><b>Servidor I</b></font></td><td><font size=\"1\"><b>Usuários Online:</b> <?=$online1->recordcount();?></font><br/><font size=\"1\"><b>Usuários Registrados:</b> <?=$registrados1->recordcount();?></font><br/><font size=\"1\"><b>Maior Nível:</b> <?=$top1;?></font></td></tr></table>";
nServ[2] = "<table><tr><td width=\"90px\"><font size=\"2\"><b>Servidor II</b></font></td><td><font size=\"1\"><b>Usuários Online:</b> <?=$online2->recordcount();?></font><br/><font size=\"1\"><b>Usuários Registrados:</b> <?=$registrados2->recordcount();?></font><br/><font size=\"1\"><b>Maior Nível:</b> <?=$top2;?></font></td></tr></table>";
function mudaText(isList){
txtIndex = isList.selectedIndex;
document.getElementById('servDiv').innerHTML = nServ[txtIndex];
}

</Script>


</head>


<body>

<table width="760" align="center">
<tr><td>
<div id="footer">
<div id="baixo-text"></div>
</div>
<div id="wrapper">
<center><img src="images/topo.jpg" alt="O Confronto - MMORPG" border="0"></center>
<?php
if ($escolheper == 55){
echo "<div id=\"footer-text\"><div id=\"textoAviso\"><i>Escolha seu Personagem</i></div></div>";
} elseif ($escolheper == 44){
echo "<div id=\"footer-text\"><div id=\"textoAviso\"><i>Criar novo Personagem</i></div></div>";
} else {
echo "<div id=\"footer-text\"><div id=\"textoAviso\"><i>Sua Conta</i></div></div>";
}
?>

<div id="acccontent">