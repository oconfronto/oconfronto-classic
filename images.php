<?php

include("lib.php");
define("PAGENAME", "Imagens");

include("templates/header.php");

?>
<style>

td.off {
background: #FFECCC;
}

td.on {
background: #FFFDE0;
}

</style>

<table width="100%" border="0">
  <tr>
    <td class="off" onmouseover="this.className='on'" onmouseout="this.className='off'" height="200"><div align="center">
      <a href="images/ss/ss1.png" rel="lightbox[screens]"><img src="images/ss/ss1.png" width="180" height="160" border="2px" alt="Fórum" /></a><br />
      <br /><strong>Fórum</strong></div></td>
    <td class="off" onmouseover="this.className='on'" onmouseout="this.className='off'" height="200"><div align="center"><a href="images/ss/ss2.png" rel="lightbox[screens]"><img src="images/ss/ss2.png" width="180" height="160" border="2px" alt="Inventário" /></a><br />
        <br /><strong>Inventário</strong></div></td>
    <td class="off" onmouseover="this.className='on'" onmouseout="this.className='off'" height="200"><div align="center"><a href="images/ss/ss3.png" rel="lightbox[screens]"><img src="images/ss/ss3.png" width="180" height="160" border="2px" alt="Monstros" /></a><br />
        <br /><strong>Monstros</strong></div></td>
  </tr>
</table>
<p>&nbsp;</p>
<table width="100%" border="0">
  <tr>
    <td class="off" onmouseover="this.className='on'" onmouseout="this.className='off'" height="200"><div align="center"><a href="images/ss/ss4.png" rel="lightbox[screens]"><img src="images/ss/ss4.png" width="180" height="160" border="2px" alt="Ferreiro" /></a><br />
        <br /><strong>Ferreiro</strong></div></td>
    <td class="off" onmouseover="this.className='on'" onmouseout="this.className='off'" height="200"><div align="center"><a href="images/ss/ss5.png" rel="lightbox[screens]"><img src="images/ss/ss5.png" width="180" height="160" border="2px" alt="Banco" /></a><br />
      <br /><strong>Banco</strong></div></td>
    <td class="off" onmouseover="this.className='on'" onmouseout="this.className='off'" height="200"><div align="center"><a href="images/ss/ss6.png" rel="lightbox[screens]"><img src="images/ss/ss6.png" width="180" height="160" border="2px" alt="Perfil" /></a><br />
      <br /><strong>Perfil</strong></div></td>
  </tr>
</table>

<?php
include("templates/footer.php");
?>