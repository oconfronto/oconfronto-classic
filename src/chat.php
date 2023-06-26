<?php
include("lib.php");
define("PAGENAME", "Chat");
$player = check_user($secret_key, $db);

include("templates/chat_header.php");
?>
<iframe style="position:fixed; top:0; left:0; right:0; bottom:0; width: 100%; height: 100%" src="https://xat.com/embed/chat.php#id=220522347&gn=oconfrontorpg1234" width="540" height="405" frameborder="0" scrolling="no"></iframe>
</body>
</html>
