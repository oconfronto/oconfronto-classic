<?php
//    Filename: bbcode.php
//    Version:     0.1
//

class bbcode {


    function code_box($text)
    {
        $output = "<blockquote><b><font size=1>Código:</font></b><br/>\\1</blockquote>";
    
    return $output;
    
    }

    function quote($text)
    {
        $output = "<blockquote><b><font size=1>Citação:</font></b><br/>\\1</blockquote>";
    
    return $output;
    
    }
    
    function htmlout($text)
    {
        $text = stripslashes($text);
        $text = htmlspecialchars($text);    
        $text = nl2br($text);
                
        return $text;
    }


    function parse($text)  
    {
        // First: If there isn't a "[" and a "]" in the message, don't bother.
        $text = " " . $text;
        {
	
	
   	    $text = eregi_replace("\\[img]([^\\[]*)\\[/img\\]","<img style=\"max-width:460px; width: expression(this.width > 460 ? 460: true);\" src=\"\\1\">",$text); 
   	    $text = eregi_replace("\\[youtube]([^\\[]*)\\[/youtube\\]","<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1&hl=pt-br&fs=1&\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/\\1&hl=pt-br&fs=1&\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"425\" height=\"344\"></embed></object>",$text); 
   	    $text = preg_replace("/\\[b\\](.+?)\[\/b\]/is",'<b>\1</b>', $text);
            $text = preg_replace("/\\[i\\](.+?)\[\/i\]/is",'<i>\1</i>', $text);
            $text = preg_replace("/\\[u\\](.+?)\[\/u\]/is",'<u>\1</u>', $text);
            $text = preg_replace("/\\[center\\](.+?)\[\/center\]/is",'<center>\1</center>', $text);
            $text = preg_replace("/\\[left\\](.+?)\[\/left\]/is",'<div align=left>\1</div>', $text);
            $text = preg_replace("/\\[right\\](.+?)\[\/right\]/is",'<div align=right>\1</div>', $text);
            $text = preg_replace("/\[s\](.+?)\[\/s\]/is",'<s>\1</s>', $text);
            $text = preg_replace("/\[small\](.+?)\[\/small\]/is",'<font size=\"1px\">\1</font>', $text);
            $text = preg_replace("/\[big\](.+?)\[\/big\]/is",'<font size=\"5px\">\1</font>', $text);

            $text = preg_replace("/\\[li\\](.+?)\[\/li\]/is",'<li>\1</li>', $text);
            $text = preg_replace("/\\[order\\](.+?)\[\/order\]/is",'<ol>\1</ol>', $text);
            $text = preg_replace("/\\[list\\](.+?)\[\/list\]/is",'<ul>\1</ul>', $text);
            $text = preg_replace("/\\[url\\](.+?)\[\/url\]/is",'<a>\1</a>', $text);
            
            $text = preg_replace("/\[code\](.+?)\[\/code\]/is","".$this->code_box('\\1')."", $text);
            $text = preg_replace("/\[quote\](.+?)\[\/quote\]/is","".$this->quote('\\1')."", $text);
    
            $text = eregi_replace("\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]","<font color=\"\\1\">\\2</font>",$text);
            $text = eregi_replace("\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=\"_blank\" border=\"0px\">\\2</a>",$text);    
 
            $text = eregi_replace(":o","<img src=\"images/smiles/1.gif\">",$text); 
            $text = eregi_replace(";)","<img src=\"images/smiles/2.gif\">",$text); 
            $text = eregi_replace(":D","<img src=\"images/smiles/3.gif\">",$text); 
            $text = eregi_replace("8)","<img src=\"images/smiles/4.gif\">",$text); 
            $text = eregi_replace(":)","<img src=\"images/smiles/5.gif\">",$text);
            $text = eregi_replace(":sad:","<img src=\"images/smiles/12.gif\">",$text);

            $text = eregi_replace("fdp","<s>Censurado</s>",$text);
            $text = eregi_replace("caralho","<s>Censurado</s>",$text);
            $text = eregi_replace("buceta","<s>Censurado</s>",$text);
            $text = eregi_replace("vsf","<s>Censurado</s>",$text);
            $text = eregi_replace("fude","<s>Censurado</s>",$text);
            $text = eregi_replace("cacete","<s>Censurado</s>",$text);
            $text = eregi_replace("filho da puta","<s>Censurado</s>",$text);
            $text = eregi_replace("porra","<s>Censurado</s>",$text);
            $text = eregi_replace("fuck","<s>Censurado</s>",$text);
            $text = eregi_replace("puto","<s>Censurado</s>",$text);

            return $text;
        }    
}
}

?>