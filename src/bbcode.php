<?php

class bbcode
{
    public function code_box($text)
    {
        return "<blockquote><b><font size=1>Código:</font></b><br/>" . $text . "</blockquote>";
    }

    public function quote($text)
    {
        return "<blockquote><b><font size=1>Citação:</font></b><br/>" . $text . "</blockquote>";
    }

    public function htmlout($text)
    {
        $text = stripslashes($text);
        $text = htmlspecialchars($text);
        $text = nl2br($text);

        return $text;
    }

    public function parse($text)
    {
        $text = " " . $text;

        // Pattern and replacements
        $patternsAndReplacements = [
            "/\\[b\\](.+?)\[\/b\]/is" => '<b>\1</b>',
            "/\\[i\\](.+?)\[\/i\]/is" => '<i>\1</i>',
            "/\\[u\\](.+?)\[\/u\]/is" => '<u>\1</u>',
            "/\\[center\\](.+?)\[\/center\]/is" => '<center>\1</center>',
            "/\\[left\\](.+?)\[\/left\]/is" => '<div align=left>\1</div>',
            "/\\[right\\](.+?)\[\/right\]/is" => '<div align=right>\1</div>',
            "/\[s\](.+?)\[\/s\]/is" => '<s>\1</s>',
            "/\[small\](.+?)\[\/small\]/is" => '<font size="1px">\1</font>',
            "/\[big\](.+?)\[\/big\]/is" => '<font size="5px">\1</font>',
            "/\\[li\\](.+?)\[\/li\]/is" => '<li>\1</li>',
            "/\\[order\\](.+?)\[\/order\]/is" => '<ol>\1</ol>',
            "/\\[list\\](.+?)\[\/list\]/is" => '<ul>\1</ul>',
            "/\\[img\\]([^\\[]*)\\[/img\\]/i" => '<img style="max-width:460px; width: expression(this.width > 460 ? 460: true);" src="\1">',
            "/\\[url\\]([^\\[]*)\\[/url\\]/i" => '<a>\1</a>',
            "/\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]/i" => '<font color="\1">\2</font>',
            "/\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]/i" => '<a href="\1" target="_blank" border="0px">\2</a>',
        ];

        foreach ($patternsAndReplacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }

        // Special cases with callbacks
        $text = preg_replace_callback("/\[code\](.+?)\[\/code\]/is", function($matches) { return $this->code_box($matches[1]); }, $text);
        $text = preg_replace_callback("/\[quote\](.+?)\[\/quote\]/is", function($matches) { return $this->quote($matches[1]); }, $text);

        // Smilies
        $smilies = [
            ':o' => '<img src="images/smiles/1.gif">',
            ';)' => '<img src="images/smiles/2.gif">',
            ':D' => '<img src="images/smiles/3.gif">',
            '8)' => '<img src="images/smiles/4.gif">',
            ':)' => '<img src="images/smiles/5.gif">',
            ':sad:' => '<img src="images/smiles/12.gif">',
        ];

        foreach ($smilies as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        // Censored words
        $censoredWords = ['fdp', 'caralho', 'buceta', 'vsf', 'fude', 'cacete', 'filho da puta', 'porra', 'fuck', 'puto'];
        foreach ($censoredWords as $word) {
            $text = str_replace($word, '<s>Censurado</s>', $text);
        }

        return $text;
    }
}

?>
