<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Principal");
$player = check_user($secret_key, $db);

include(__DIR__ . "/checkforum.php");
include(__DIR__ . "/templates/private_header.php");
$tbl_name = "forum_answer"; // Table name

// Get value of id that sent from hidden field
$id = $_POST['id'];

if (!$_POST['a_answer']) {
    echo "<fieldset><legend><b>Erro</b></legend>Você precisa preencher todos os campos!<BR>";
    echo "<a href='view_topic.php?id=".$id."'>Voltar</a></fieldset>";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}

$fecxhado = $db->GetOne("select `closed` from `forum_question` where `id`=?", [$id]);
if ($fecxhado['closed'] == 't') {
    echo "<fieldset><legend><b>Erro</b></legend>Este tópico está fechado.<BR>";
    echo "<a href='view_topic.php?id=".$id."'>Voltar</a></fieldset>";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}

$categoryae = $db->GetOne("select `category` from `forum_question` where `id`=?", [$id]);
$servae = $db->GetOne("select `serv` from `forum_question` where `id`=?", [$id]);
if (($categoryae == 'gangues' || $categoryae == 'trade') && $player->serv != $servae) {
    echo "<fieldset><legend><b>Erro</b></legend>Você não pode postar aqui.<BR>";
    echo "<a href='select_forum.php'>Voltar</a></fieldset>";
    include(__DIR__ . "/templates/private_footer.php");
    exit;
}

// Find highest answer number.
$sql = "SELECT MAX(a_id) AS Maxa_id FROM $tbl_name WHERE question_id='$id'";
$result = mysql_query($sql);
$rows = mysql_fetch_array($result);

// add + 1 to highest answer number and keep it in variable name "$Max_id". if there no answer yet set it = 1
$Max_id = $rows ? $rows['Maxa_id'] + 1 : 1;


// get values that sent from form
$a_answer = $_POST['a_answer'];

$notavelreply = strip_tags((string) $a_answer);
$texto = nl2br($notavelreply);

$listaExtensao = ['JPG' => 1, 'jpg' => 2, 'PNG' => 3, 'png' => 4, 'BMP' => 5, 'bmp' => 6, 'GIF' => 7, 'gif' => 8];
$aux = " " . $texto . "";


while(true) {
    $inicioImg = 0;

    $inicioImg = strpos($aux, '[img]');

    if($inicioImg < 1) {
        break;
    }

    $fimImg = strpos($aux, '[/img]');
    $tamanho = strlen($aux);
    $parteAnterior = substr($aux, 0, $inicioImg);
    $partePosterior = substr($aux, $fimImg + 6, $tamanho - 1);
    $parteLink = substr($aux, $inicioImg, $fimImg - $inicioImg + 6);
    $extensao = substr($parteLink, strlen($parteLink) - 9, 3);

    if(!array_key_exists($extensao, $listaExtensao)) {
        $parteLink  = '[IMG REMOVIDA]';
    }
    $textoFinal = $textoFinal.$parteAnterior.$parteLink;
    $aux = $partePosterior;


}
$mostraimg = $textoFinal . "" . $aux;
$mostraimg = substr($mostraimg, 1);


$time = time();
$datetime = date("d/m/y H:i:s");

// Insert answer
$sql2 = "INSERT INTO $tbl_name(question_id, a_id, a_user_id, a_answer, a_datetime)VALUES('$id', '$Max_id', '$player->id', '$mostraimg', '$time')";
$sql4 = $db->execute("update `forum_question` set `last_post`=?, `last_post_date`=? where `id`=?", [time(), $datetime, $id]);
$sql5 = $db->execute("update `players` set `posts`=`posts`+1 where `id`=?", [$player->id]);
$result2 = mysql_query($sql2);

if($result2) {
    echo "<fieldset><legend><b>Sucesso</b></legend>Mensagem enviada com sucesso!<BR>";
    echo "<a href='view_topic.php?id=".$id."'>Visualizar sua mensagem</a></fieldset>";

    // If added new answer, add value +1 in reply column
    $tbl_name2 = "forum_question";
    $sql3 = "UPDATE $tbl_name2 SET reply='$Max_id' WHERE id='$id'";
    $result3 = mysql_query($sql3);

} else {
    echo "<fieldset><legend><b>Erro</b></legend>Um erro inesperado ocorreu.<BR>";
    echo "<a href=select_forum.php>Voltar</a></fieldset>";
}

mysql_close();
?>
<?php
include(__DIR__ . "/templates/private_footer.php");
?>