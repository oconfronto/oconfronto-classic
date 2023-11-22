<?php

include(__DIR__ . "/lib.php");
define("PAGENAME", "Suporte");
$player = check_user($secret_key, $db);
include(__DIR__ . "/templates/private_header.php");

if ($player->gm_rank > 75) {
    if ($_GET['ignore']) {
        $db->execute("update `players` set `hack`='f' where `id`=?", [$_GET['ignore']]);
        echo "" . $_GET['name'] . " marcado como não hackeado.";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }

    $query = $db->execute("select `id`, `username`, `gold`, `bank`, `level` from `players` where `hack`='t' order by `level` desc");

    if($query->recordcount() == 0) {
        echo "Ninguém Hackeado =)";
        include(__DIR__ . "/templates/private_footer.php");
        exit;
    }

    while($member = $query->fetchrow()) {
        echo "<b>Nome:</b> " . $member['username'] . " | <b>Nível:</b> " . $member['level'] . " | <b>Ouro:</b> " . ($member['gold'] + $member['bank']) . " | <a href=\"hack.php?ignore=" . $member['id'] . "&name=" . $member['username'] . "\">Ignorar</a> - <a href=\"backitens.php?from=" . $member['username'] . "\">Procurar Itens</a><br/>";
    }

    include(__DIR__ . "/templates/private_footer.php");
    exit;
}
if ($_POST['submit']) {
    $db->execute("update `players` set `hack`='t' where `username`=?", [$player->username]);
    echo "Ok, agora sabemos que você precisa de ajuda, mas não podemos prometer que você terá seus itens devolta.";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}
echo "Esta página é destinada à usuários que tiveram <b>itens ou ouro roubados nos ùltimos 10 dias</b>.<br/>";
echo "Se você <b>não passou sua conta</b> para ninguém, e quer seus itens devolta, clique em \"Fui hackeado\" abaixo.<br/>";
echo "<u>Lembre-se, isto não é brincadeira, se você deu sua senha para alguem ou não teve seus itens roubados, será marcado como mentiroso e será punido.</u><br/><br/>";
echo "<form action=\"hack.php\" method=\"post\"><center><input type=\"submit\" name=\"submit\" value=\"Fui hackeado\" /></center><br/><br/><b>OBS:</b> Se faz mais de 10 dias que você foi hackeado, não clique em \"Fui Hackeado\", pois os nossos logs se apagão depois deste tempo e você só vai gastar o tempo da nossa equipe.</form>";
include(__DIR__ . "/templates/private_footer.php");
