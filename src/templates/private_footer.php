</div>
</div>
<div class="clear"></div>
</div>
<div id="rodape"><a href="regras.php">
                <font color="black"><b>Regras</b></font>
        </a> - <a href="bugs.php">
                <font color="black">Contato</font>
        </a> - <a href="sendfiles.php">
                <font color="black">Upload de Imagens</font>
        </a> - <a href="hack.php">
                <font color="black">Foi Hackeado?</font>
        </a> - <a href="creditos.php">
                <font color="black"><b>Cr�ditos</b></font>
        </a><br />
        <font size="1">Copyright � 2008-2009 OC Productions</font>
</div>
</div>

<script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
        try {
                var pageTracker = _gat._getTracker("UA-6607673-1");
                pageTracker._trackPageview();
        } catch (err) { }</script>

<script>
    // Verifica se a página atual é a página específica
    if (window.location.pathname === '/monster.php') {
        // Função para armazenar a posição de rolagem antes de recarregar a página
        window.addEventListener('beforeunload', function () {
            localStorage.setItem('scrollPosition', window.scrollY);
        });

        // Função para restaurar a posição de rolagem ao carregar a página
        window.addEventListener('load', function () {
            var scrollPosition = localStorage.getItem('scrollPosition');
            if (scrollPosition) {
                window.scrollTo(0, parseInt(scrollPosition, 10));
                localStorage.removeItem('scrollPosition'); // Limpa a posição após usá-la
            }
        });
    }
</script>


</body>

</html>