<?php
include(__DIR__ . "/lib.php");
define("PAGENAME", "Ganhar Ouro");
$player = check_user($secret_key, $db);
include(__DIR__ . "/templates/private_header.php");
?>
<fieldset>
<legend><b>Ajudante</b></legend>
<i>Olá, está precisando de dinheiro?</i><br />
Posso te ajudar, a cada amigo que você convidar para o game você receberá 2500 de ouro! <font size="1">(Seu amigo deve atingir o nível <?php echo $setting->activate_level?> para você receber sua recompensa).</font><br /><br />
Para convidar seus amigos mande para eles o seguinte link:
<br />
<b>Link para convidar amigos:</b> <a href="http://oconfronto.freehostia.com?r=<?php echo $player->id?>">http://oconfronto.freehostia.com?r=<?php echo $player->id?></a><br />
<b>Amigos convidados:</b> <?php echo $player->ref?>
</fieldset>
<br /><br />
<b>*BETA* Você pode usar esta assinatura:</b> <a href="http://oconfronto.freehostia.com?r=<?php echo $player->id?>"><img src="imprime.php?id=<?php echo $player->id?>" alt="Jogue o confronto! é de graça!" border="0"></a><br />
  
    <table border="0">
        <tbody><tr>
            <td>
         <textarea style="overflow: hidden; width: 500px; height: 60px;" size="70">[URL=http://oconfronto.freehostia.com?r=<?php echo $player->id?>][IMG]http://oconfronto.freehostia.comimprime.php?id=<?php echo $player->id?>[/IMG][/URL]</textarea>
            </td> 
            <td valign="top">

         Código para Fórum (1)
            </td>
        </tr>
    </tbody></table>
    <table border="0">
        <tbody><tr>
            <td>
          <textarea style="overflow: hidden; width: 500px; height: 50px;" size="70">[url=http://oconfronto.freehostia.com?r=<?php echo $player->id?>][img=http://oconfronto.freehostia.comimprime.php?id=<?php echo $player->id?>][/url]</textarea>

            </td> 
            <td valign="top">
          Código para Fórum (2)
            </td>
        </tr>
    </tbody></table>
<br/><br/>
<b>Ou você pode usar esta bar:</b> <a href="http://oconfronto.freehostia.com?r=<?php echo $player->id?>"><img src="http://img824.imageshack.us/img824/6083/bart.png" alt="Jogue o confronto! é de graça!" width="360" height="21" border="0"></a><br />
  
    <table border="0">
        <tbody><tr>
            <td>
         <textarea style="overflow: hidden; width: 500px; height: 60px;" size="70">[URL=http://oconfronto.freehostia.com?r=<?php echo $player->id?>][IMG]http://img824.imageshack.us/img824/6083/bart.png[/IMG][/URL]</textarea>
            </td> 
            <td valign="top">

         Código para Fórum (1)
            </td>
        </tr>
    </tbody></table>
    <table border="0">
        <tbody><tr>
            <td>
          <textarea style="overflow: hidden; width: 500px; height: 50px;" size="70">[url=http://oconfronto.freehostia.com?r=<?php echo $player->id?>][http://img824.imageshack.us/img824/6083/bart.png][/url]</textarea>

            </td> 
            <td valign="top">
          Código para Fórum (2)
            </td>
        </tr>
    </tbody></table>



<?php
    include(__DIR__ . "/templates/private_footer.php");
?>