<?php
include("lib.php");
define("PAGENAME", "Chat");
$player = check_user($secret_key, $db);

include("templates/chat_header.php");
?>
<center>
<embed src="http://www.xatech.com/web_gear/chat/chat.swf" quality="high" width="700" height="490" name="chat" FlashVars="id=64980752&rl=Brazilian" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://xat.com/update_flash.shtml" />
</center>
</body>
</html>