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

	//iOS uses smart quotes.  Replace those as they might be tag attributes.
	$text = str_replace('“', '"', $text);
	$text = str_replace('”', '"', $text);

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
					 "/[\r\n]*\[linebreak\][\r\n]*/",
					 '/\[img\](.*?)\[\/img\]/ms',
					 '/\[email\](.*?)\[\/email\]/ms',
					 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
					 '/\[url\](.*?)\[\/url\]/ms',
					 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
					 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
//					 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
//					 '/\[list\](.*?)\[\/list\]/ms',
//					 '/\[\*\]\s?(.*?)\n/ms',
					 "/[\r\n]*\[ooc\](.*?)\[\/ooc\][\r\n]*/ms",
					 "/[\r\n]*\[spoiler=\"?(.*?)\"?\](.*?)\[\/spoiler\][\r\n]*/ms",
					 "/[\r\n]*\[spoiler\](.*?)\[\/spoiler\][\r\n]*/ms",
					 "/\[youtube\]https:\/\/youtu.be\/(.*?)\[\/youtube\]/ms",
					 "/[\r\n]*\[2column\][ \t\r\n]*(.*?)[ \t\r\n]*\[\/2column\][\r\n]*/ms",
					 "/[\r\n]*\[col\][ \t\r\n]*(.*?)[ \t\r\n]*\[\/col\][\r\n]*/ms",
					 "/[\r\n]*\[style\](.*?)\[\/style\][\r\n]*/ms",
	);
	// And replace them by...
	$out = array(	 '<strong>\1</strong>',
					 '<em>\1</em>',
					 '<u>\1</u>',
					 '<span style="text-decoration:line-through">\1</span>',
					 '<hr>',
					 '<img src="\1" alt="\1" class="usrImg">',
					 '<a href="mailto:\1">\1</a>',
					 '<a href="\1" target="_blank" rel="nofollow">\2</a>',
					 '<a href="\1" target="_blank" rel="nofollow">\1</a>',
					 '<span style="font-size:\1%">\2</span>',
					 '<span style="color:\1">\2</span>',
//					 '<ol start="\1">\2</ol>',
//					 '<ul>\1</ul>',
//					 '<li>\1</li>',
					 '<blockquote class="oocText"><div>OOC:</div>\1</blockquote>',
					 '<blockquote class="spoiler closed"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] \1</div><div class="hidden">\2</div></blockquote>',
					 '<blockquote class="spoiler closed"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] Spoiler</div><div class="hidden">\1</div></blockquote>',
					 '<iframe width="560" height="315" src="https://www.youtube.com/embed/\1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
					 '<div class="layout-columns-2">\1</div>',
					 '<div class="layout-column">\1</div>',
					 '<div class="style" style="display:none;">\1</div>',
	);
	$text = preg_replace($in, $out, $text);
	while (preg_match("/\[quote(?:=\"([\w\.]+?)\")?\](.*?)\[\/quote\]/sm", $text))
		$text = preg_replace("/([\r\n]?)[\r\n]*\[quote(?:=\"([\w\.]+?)\")?\](.*?)\[\/quote\]\s*/s", '\1<blockquote class="quote"><div class="quotee">\2 says:</div>\3</blockquote>', $text);
	$text = str_replace('<div class="quotee"> says:</div>', '<div class="quotee">Quote:</div>', $text);

	//map
	$matches = null;
	$text=preg_replace_callback("/\[map\](.*?)\[\/map\]/ms", function($matches){
			$mapLink=preg_replace('/\s+/', '',$matches[1]);
			return '<a class="mapLink" target="_blank" href="'.$mapLink.'"><img class="usrImg" src="'.$mapLink.'"/></a>';
	}, $text);
	//end map

	//spotify
	$matches = null;
	$text=preg_replace_callback("/\[spotify\]https:\/\/open.spotify.com\/([a-​zA-Z]*)\/([a-​zA-Z0-9_]*)\??([\=a-​zA-Z0-9_]*)\[\/spotify\]/ms", function($matches){
		$height=80;
		if($matches[1]=="episode" || $matches[1]=="show"){
			$height=152;
		}
		return "<iframe src='https://open.spotify.com/embed/".$matches[1]."/".$matches[2]."?theme=0' width='100%' height='".$height."' frameBorder='0' allowtransparency='true' allow='encrypted-media'></iframe>";

	}, $text);
	//end spotify

	//tables
	$matches = null;
	$text=preg_replace_callback("/\[table(=([\"a-zA-Z])*)?\](.*?)\[\/table\]/ms", function($matches){
			$tableType=strtolower(trim(str_replace("=","",str_replace("\"","",$matches[1]))));
			$tableClass="";
			if($tableType=="center"||$tableType=="centre"){
				$tableClass=" bbTableCenter";
			} else if($tableType=="right"){
				$tableClass=" bbTableRight";
			} else if($tableType=="stats"){
				$tableClass=" bbTableStats";
			} else if($tableType=="ht" || $tableType=="htl" || $tableType=="hl"){
				$tableClass=" bbTable-".$tableType;
			} else if($tableType=="rolls"){
				$tableClass=" bbTableRolls";
			}


			$tableRows = explode("\n", trim(str_replace("<br />","",$matches[3])));

			//simple code to split cells on pipes
			$ret="<table class='bbTable".$tableClass."'>";
			foreach ($tableRows as $tableRow){
				$ret=$ret."<tr><td>".str_replace("|","</td><td>",$tableRow)."</td></tr>";
			}


			//more involved implementation which uses colspans to distribute cells across the table
			//I think it's too clever for its own good and prefer the naive implementation above
			/*
			$maxCols=0;
			foreach ($tableRows as $tableRow){
				$maxCols=max(substr_count($tableRow,"|")+1,$maxCols);
			}

			foreach ($tableRows as $tableRow){
				$ret=$ret."<tr>";
				$cells=explode("|",$tableRow);
				$colsPerCell=$maxCols/count($cells);
				$colNum=1;
				$colsAdded=0;
				foreach ($cells as $cell){
					$colSpan=floor($colNum*$colsPerCell)-$colsAdded;
					$ret=$ret.(($colSpan==1)?"<td>":"<td colspan='".$colSpan."'>");
					$colsAdded+=$colSpan;
					$colNum++;
					$ret=$ret.$cell;
					$ret=$ret."</td>";
				}
	
				$ret=$ret."</tr>";
			}*/

			$ret=$ret."</table>";

			return $ret;
	}, $text);
	//end tables
	
	$matches = null;
	global $currentUser, $isGM, $post;
	if ($post) {
		$display = false;

		$text = preg_replace('/\[note="?(\w[\w\. +;,]+?)"?](.*?)\[\/note\]\s*/s', '<aside class="note"><div>Note to \1</div>\2</aside>', $text);
		if (strpos($text, 'aside class="note"') !== false && !$isGM && $post->getAuthor('userID') != $currentUser->userID && preg_match_all('/\<aside class="note"\>\<div\>Note to (.*?)\<\/div\>.*?\<\/aside\>/ms', $text, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$noteTo = array_map('strtolower', preg_split('/[^\w\.]+/', $match[1]));
				if (!in_array(strtolower($currentUser->username), $noteTo)) {
					$text = str_replace($match[0], '<aside class="note"><div>'.$post->getAuthor('username').' sent a note to '.$match[1].'</div></aside>', $text);
				}
			}
		}

		$text = preg_replace('/\[private="?(\w[\w\. +;,]+?)"?](.*?)\[\/private\]\s*/s', '<aside class="private"><div>Privately: \1</div>\2</aside>', $text);
		if (strpos($text, 'aside class="private"') !== false && !$isGM && $post->getAuthor('userID') != $currentUser->userID && preg_match_all('/\<aside class="private"\>\<div\>Privately: (.*?)\<\/div\>(.*?)\<\/aside\>/ms', $text, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$noteTo = array_map('strtolower', preg_split('/[^\w\.]+/', $match[1]));
				if (!in_array(strtolower($currentUser->username), $noteTo)) {
					$text = str_replace($match[0], '', $text);
				}
				else{
					$text = str_replace($match[0], $match[2].'<br/>', $text);
				}
			}
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