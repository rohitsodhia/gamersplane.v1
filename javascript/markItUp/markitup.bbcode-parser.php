<?
// ----------------------------------------------------------------------------
// markItUp! BBCode Parser
// v 1.0.5
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2009 Jay Salvat
// http://www.jaysalvat.com/
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------
// Thanks to Arialdo Martini, Mustafa Dindar for feedbacks.
// ----------------------------------------------------------------------------

//define ("EMOTICONS_DIR", "/images/emoticons/");

function BBCode2Html($text) {
	$text = trim($text);

	// BBCode [code]
	if (!function_exists('escape')) {
		function escape($s) {
			global $text;
			$text = strip_tags($text);
			$code = $s[1];
			$code = htmlspecialchars($code);
			$code = str_replace("[", "&#91;", $code);
			$code = str_replace("]", "&#93;", $code);
			return '<pre><code>'.$code.'</code></pre>';
		}	
	}
	$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', "escape", $text);

	// Smileys to find...
/*	$in = array( 	 ':)', 	
					 ':D',
					 ':o',
					 ':p',
					 ':(',
					 ';)'
	);
	// And replace them by...
	$out = array(	 '<img alt=":)" src="'.EMOTICONS_DIR.'emoticon-happy.png" />',
					 '<img alt=":D" src="'.EMOTICONS_DIR.'emoticon-smile.png" />',
					 '<img alt=":o" src="'.EMOTICONS_DIR.'emoticon-surprised.png" />',
					 '<img alt=":p" src="'.EMOTICONS_DIR.'emoticon-tongue.png" />',
					 '<img alt=":(" src="'.EMOTICONS_DIR.'emoticon-unhappy.png" />',
					 '<img alt=";)" src="'.EMOTICONS_DIR.'emoticon-wink.png" />'
	);
	$text = str_replace($in, $out, $text);
*/	
	// BBCode to find...
	$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',	
					 '/\[i\](.*?)\[\/i\]/ms',
					 '/\[u\](.*?)\[\/u\]/ms',
					 '/\[s\](.*?)\[\/s\]/ms',
					 '/\[img\](.*?)\[\/img\]/ms',
					 '/\[email\](.*?)\[\/email\]/ms',
					 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
					 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
					 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
//					 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
//					 '/\[list\](.*?)\[\/list\]/ms',
//					 '/\[\*\]\s?(.*?)\n/ms'
	);
	// And replace them by...
	$out = array(	 '<strong>\1</strong>',
					 '<em>\1</em>',
					 '<u>\1</u>',
					 '<span style="text-decoration:line-through">\1</span>',
					 '<img src="\1" alt="\1" class="usrImg">',
					 '<a href="mailto:\1">\1</a>',
					 '<a href="\1" target="_blank">\2</a>',
					 '<span style="font-size:\1%">\2</span>',
					 '<span style="color:\1">\2</span>',
//					 '<ol start="\1">\2</ol>',
//					 '<ul>\1</ul>',
//					 '<li>\1</li>'
	);
	$text = preg_replace($in, $out, $text);
	while (preg_match("/\[quote(?:=\"(\w+?)\")?]((?!\[quote).*?)\[\/quote\]/sm", $text)) $text = preg_replace("/([\r\n]?)[\r\n]*\[quote(?:=\"(\w+?)\")?]((?!\[quote).*?)\[\/quote\][\r\n]*/sm", '\1<blockquote class="quote"><div class="quotee">\2 says:</div>\3</blockquote>', $text);
	$text = str_replace('<div class="quotee"> says:</div>', '<div class="quotee">Quote:</div>', $text);
	
	$matches = NULL;
	global $isGM;
	global $postInfo;
	$display = FALSE;

	$text = preg_replace('/\[note="?(\w[\w +;,]+)"?](.*?)\[\/note\][\n\r]*/ms', '<blockquote class="note"><div>Note to \1</div>\2</blockquote>', $text);
	if (!$isGM && $postInfo['userID'] != $_SESSION['userID'] && preg_match_all('/\<blockquote class="note"\>\<div\>Note to (.*?)\<\/div\>.*?\<\/blockquote\>/ms', $text, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$noteTo = preg_split('/[^\w]+/', $match[1]);
			if (!in_array($_SESSION['username'], $noteTo)) $text = str_replace($match[0], '', $text);
		}
	}

// paragraphs
//	$text = str_replace("\r", "", $text);
//	$text = "<p>".preg_replace("/(\n){2,}/", "</p><p>", $text)."</p>";
//	$text = nl2br($text);
	
	// clean some tags to remain strict
	// not very elegant, but it works. No time to do better ;)
/*	if (!function_exists('removeBr')) {
		function removeBr($s) {
			return str_replace("<br />", "", $s[0]);
		}
	}	
	$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "removeBr", $text);
	$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);
	
	$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', "removeBr", $text);
	$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);*/
	
	return $text;
}
?>