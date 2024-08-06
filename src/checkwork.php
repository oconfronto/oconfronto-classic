<?php

if ($player->ban > time()) {
    $newlast = (time() - 210);
    $query = $db->execute("update `players` set `last_active`=? where `id`=?", array($newlast, $player->id));
    session_unset();
    session_destroy();
    echo "você foi banido. As vezes usuários são banidos automaticamente por algum erro em suas contas. Se você acha que foi banido injustamente, ou se tiver algum erro para reportar, crie outra conta e entre em contato com o [GOD]. Assim seu banimento poderá ser removido.";
    include ("templates/private_footer.php");
    exit;
}

$statusgeralwork = $db->execute("select * from `work` where `status`='t'");
$statuswork = $statusgeralwork->fetchrow();
$statususerwork = $db->execute("select * from `players` where `id`=?", array($statuswork['player_id']));

$restahora = date("d-M-Y H:i:s", ($statuswork['start'] + ($statuswork['worktime'] * 3600)));
$restahora2 = date("d-M-Y H:i:s", time());
$atual = new DateTime($restahora2);
$fim = new DateTime($restahora);
$dteDiff = $atual->diff($fim);
$diferenca = $dteDiff->format("%H:%I:%S");
// $conta = $statusgeralwork->recordcount();
// echo"$conta";
// exit;
//AQUI VERIFICA SE O PLAYER ESTÁ TRABALHANDO
if ($statusgeralwork->recordcount() > 0) {
    $profissao = $statuswork['worktype'];
    include ("templates/private_header.php");
    echo "<fieldset>";
    echo "<legend><b>Trabalhar</b></legend>";
    echo "No momento você está trabalhando como <b>{$profissao}</b>, aguarde o fim do trabalho em: <b>{$diferenca}</b> ou você pode abandona-lo.";
    echo "</fieldset><br /><a style='padding-right:30px' href=\"home.php\">Principal</a>";
    echo "<a href=\"work.php?act=cancel\">Abandonar</a>";
    include ("templates/private_footer.php");
    exit;
}