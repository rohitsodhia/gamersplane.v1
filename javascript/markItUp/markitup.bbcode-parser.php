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
function splitByHeader($title, $text, $cssClass, $collectionData=''){
	$ret="<div class='".$cssClass." ddCollection'".$collectionData.">";
	if($title!=''){
		$ret=$ret.'<h2 class="headerbar hbDark">'.$title.'</h2>';
	}

	$abilityLines = explode("\n", trim($text));

	$abilityOpen=true;
	$abilityRaw="";
	$ret=$ret.'<div class="ability"><div class="abilityNotes">';
	foreach ($abilityLines as $abilityLine){

		if(substr($abilityLine,0,1)=='#'){
			if($abilityOpen){
				$ret=$ret.'</div><div style="display:none" class="abilityBBCode">'.$abilityRaw.'</div></div>'; //close open ability notes and ability
			}

			$ret=$ret.'<div class="ability"><span class="abilityName">'.substr($abilityLine,1).'</span><a href="" class="ability_notesLink">Notes</a><div class="abilityNotes notes">';
			$abilityOpen=true;
			$abilityRaw="";
		}
		else {
			$ret=$ret.$abilityLine."\n";
			$abilityRaw=$abilityRaw.str_replace(array("[","]"), array("&#91;", "&#93;"),  $abilityLine)."&#10;";
		}
	}

	if($abilityOpen){
		$ret=$ret.'</div><div style="display:none" class="abilityBBCode">'.$abilityRaw.'</div></div>'; //close open ability notes and ability
	}

	$ret=$ret."</div>";  //close abilities

	return $ret;
}

/**
 * BBCode Replace function which supports nested tags
 * $text - the text to search
 * $openTagNeedle - regex for the opening tag to search for
 * $closeTagNeedle - the closing tag.  Not regex
 * $callback - function to perform the replacement.  This is passed the tag found, the inner text, and the matches
 */
function nestedReplace($text, $openTagNeedle, $closeTagNeedle, $callback, $options){
    $replacementMade=true;
    $searchFromPos=0;
    $closeTagLen=strlen($closeTagNeedle);

    do{
        $replacementMade=false;
        if(preg_match($openTagNeedle, $text, $matches, PREG_OFFSET_CAPTURE,$searchFromPos)) {
            $foundTag = $matches[0][0];
            $openTagStart = $matches[0][1];
			$openTagEnd=$openTagStart+strlen($foundTag);
            $nextOpenTagPos=-1;

            if(preg_match($openTagNeedle, $text, $matchesNext, PREG_OFFSET_CAPTURE,$openTagEnd)){
                $nextOpenTagPos=$matchesNext[0][1];		//find the next opening tag
            }
            $nextCloseTag=strpos($text,$closeTagNeedle,$openTagEnd);

            if($nextCloseTag!==false){
                if($nextCloseTag<$nextOpenTagPos || $nextOpenTagPos==-1){
					//found a closing tag before the next opening tag
					$text=substr($text,0,$openTagStart).$callback($matches,substr($text,$openTagEnd,$nextCloseTag-$openTagEnd),$options).substr($text,$nextCloseTag+$closeTagLen);
                    $replacementMade=true;
                    $searchFromPos=$openTagEnd;
                } else {
					//found an opening tag before the next closing tag - nesting
					$restOfText=substr($text,$openTagEnd);
                    $text=substr($text,0,$openTagEnd).nestedReplace($restOfText,$openTagNeedle, $closeTagNeedle, $callback, $options);
                    $replacementMade=true;
                }
            }
        }
    }while($replacementMade);

    return $text;
}

function gpClassFormatter($matches,$innerText,$tag){
	$styles=array();
	$classes=array_map(function($className) use(&$styles)
		{
			if(strpos($className,':')!==false){
				$styles[]=$className;
				return "";
			}
			return 'gpFormat-'.strtolower($className);
		},explode(' ',$matches[1][0]));

	if(count($styles)>0){
		return "<{$tag} class=\"".implode(' ',$classes)."\" style=\"".implode(';',$styles)."\">{$innerText}</{$tag}>";
	}

	return "<{$tag} class=\"".implode(' ',$classes)."\">{$innerText}</{$tag}>";
}

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
			//$code = htmlspecialchars($code);
			//$code = str_replace("\\", "\\\\", $code);
			$code = str_replace("[", "&#91;", $code);
			$code = str_replace("]", "&#93;", $code);
			$code = str_replace("\r\n", "\n", $code);
			$code = str_replace("\n", "&#10;", $code);  //prevent adding BR
			return '<pre><code>'.$code.'</code></pre>';
		}
	}
	$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', "escape", $text);

	//char sheets
	$matches = null;
	$charsheet=0;
	$text=preg_replace_callback("/[\r\n]*\[charsheet=\"?(.*?)\"?\](.*?)\[\/charsheet\][\r\n]*/ms", function($matches) use (&$charsheet){
			$escapedSnipped=str_replace("[", "&#91;", $matches[2]);
			$escapedSnipped=str_replace("]", "&#93;", $escapedSnipped);
			$escapedSnipped=str_replace("\r\n", "\n", $escapedSnipped);
			$escapedSnipped=str_replace("\n", "&#10;", $escapedSnipped);
			return '<blockquote class="spoiler closed charsheet" data-charsheet="'.($charsheet++).'"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] <span class="snippetName">'.$matches[1].'</span></div><div class="hidden"><div class="createSheet"><span class="createSheetButton">Create character</span></div>'.$matches[2].'</div><div style="display:none;" class="snippetBBCode">'.$escapedSnipped.'</div></blockquote>';
	}, $text);
	//end char sheet


	//editable block
	$matches = null;
	$formField=0;
	$text=preg_replace_callback("/[\r\n]*\[#=\"?(.*?)\"?\](.*?)\[\/#\][\r\n]*/ms", function($matches) use (&$formField){
			$escapedSnipped=str_replace("[", "&#91;", $matches[2]);
			$escapedSnipped=str_replace("]", "&#93;", $escapedSnipped);
			$escapedSnipped=str_replace("\r\n", "\n", $escapedSnipped);
			$escapedSnipped=str_replace("\n", "&#10;", $escapedSnipped);
			return '<div class="formBlock" data-blockfieldidx="'.($formField++).'"><h2 class="headerbar hbDark"><i class="ra ra-quill-ink"></i> '.$matches[1].'</h2><div class="formBlockRendered">'.$matches[2].'</div><div style="display:none;" class="formBlockBBCode">'.$escapedSnipped.'</div></div>';
	}, $text);
	//end editable block

	//ability sections
	$matches = null;
	$formField=0;
	$text=preg_replace_callback("/\[abilities=\"?(.*?)\"?\](.*?)\[\/abilities\]/ms", function($matches) use (&$formField){
		return splitByHeader('<i class="ra ra-quill-ink"></i> '.$matches[1],$matches[2],'abilities',(" data-abilitiesfieldidx='".($formField++)."'"));
	}, $text);
	//end ability sections

	//snippet group sections
	$matches = null;
	$text=preg_replace_callback("/\[snippets=\"?(.*?)\"?\](.*?)\[\/snippets\]/ms", function($matches){
		return splitByHeader($matches[1],$matches[2],"snippets");
	}, $text);
	//end ability sections

	//snippets
	$matches = null;
	$snippetCount=0;
	$text=preg_replace_callback("/[\r\n]*\[snippet=\"?(.*?)\"?\](.*?)\[\/snippet\][\r\n]*/ms", function($matches) use (&$snippetCount){
			$escapedSnipped=str_replace("[", "&#91;", $matches[2]);
			$escapedSnipped=str_replace("]", "&#93;", $escapedSnipped);
			$escapedSnipped=str_replace("\r\n", "\n", $escapedSnipped);
			$escapedSnipped=str_replace("\n", "&#10;", $escapedSnipped);
			return '<blockquote class="spoiler closed snippet" data-snippetidx="'.($snippetCount++).'"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] <span class="snippetName">'.$matches[1].'</span></div><div class="hidden">'.$matches[2].'</div><div style="display:none;" class="snippetBBCode">'.$escapedSnipped.'</div></blockquote>';
	}, $text);
	//end snippets

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
					 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
					 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
					 '/\[zoommap\="?(.*?)"?\](.*?)\[\/zoommap\]/ms',
//					 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
//					 '/\[list\](.*?)\[\/list\]/ms',
//					 '/\[\*\]\s?(.*?)\n/ms',
					"/[\r\n]*\[ooc\](.*?)\[\/ooc\][\r\n]*/ms",
					"/[\r\n]*\[spoiler=\"?(.*?)\"?\](.*?)\[\/spoiler\][\r\n]*/ms",
					"/[\r\n]*\[spoiler\](.*?)\[\/spoiler\][\r\n]*/ms",
					"/\[youtube\]https:\/\/youtu.be\/(.*?)\[\/youtube\]/ms",
					 "/[\r\n]*\[2column\][ \t\r\n]*(.*?)[ \t\r\n]*\[\/2column\][\r\n]*/ms",
					 "/[\r\n]*\[3column\][ \t\r\n]*(.*?)[ \t\r\n]*\[\/3column\][\r\n]*/ms",
					 "/[\r\n]*\[col\][ \t\r\n]*(.*?)[ \t\r\n]*\[\/col\][\r\n]*/ms",
					 "/[\r\n]*\[style\](.*?)\[\/style\][\r\n]*/ms",
					 "/\[npc=\"?(.*?)\"?\](.*?)\[\/npc\]*/ms",
	);
	// And replace them by...
	$out = array(	 '<strong>\1</strong>',
					 '<em>\1</em>',
					 '<u>\1</u>',
					 '<span style="text-decoration:line-through">\1</span>',
					 '<hr>',
					 '<img src="\1" alt="\1" class="usrImg">',
					 '<a href="mailto:\1">\1</a>',
					 '<span class="userSize" style="font-size:\1%">\2</span>',
					 '<span class="userColor" style="color:\1">\2</span>',
					 '<div class="zoommap" data-mapimage="\1" style="display:none">\2</div>',
//					 '<ol start="\1">\2</ol>',
//					 '<ul>\1</ul>',
//					 '<li>\1</li>',
					 '<blockquote class="oocText"><div>OOC:</div>\1</blockquote>',
					 '<blockquote class="spoiler closed"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] \1</div><div class="hidden">\2</div></blockquote>',
					 '<blockquote class="spoiler closed"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] Spoiler</div><div class="hidden">\1</div></blockquote>',
					 '<div class="youtube_bb"><iframe src="https://www.youtube.com/embed/\1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>',
					 '<div class="layout-columns-2">\1</div>',
					 '<div class="layout-columns-3">\1</div>',
					 '<div class="layout-column">\1</div>',
					 '<div class="style" style="display:none;">\1</div>',
					 '<div class="inlineNpcPrefix"></div><div class="inlineNpc"><img class="inlineNpcAvatar" src="\2"/><div class="inlineNpcName">\1</div></div>',
	);

	$text = preg_replace($in, $out, $text);

	while (preg_match("/\[quote(?:=\"?([^\"\]]+?)\"?)?\](.*?)\[\/quote\]/sm", $text))
		$text = preg_replace("/([\r\n]?)[\r\n]*\[quote(?:=\"?([^\"\]]+?)\"?)?\](.*?)\[\/quote\]\s*/s", '\1<blockquote class="quote"><div class="quotee">\2 says:</div>\3</blockquote>', $text);
	$text = str_replace('<div class="quotee"> says:</div>', '<div class="quotee">Quote:</div>', $text);

	//form characters
	$matches = null;
	$formField=0;
	$text=preg_replace_callback('/\[\_(([\w\_\$]*)\=)?([^\]]*)\]/', function($matches) use (&$formField){
			$formVarName=$matches[2];
			$formVal=$matches[3]; //todo strip tags
			if($formVarName && substr( $formVarName, -1 )=='$'){
				$isCalc=true;
				$formVarName=rtrim($formVarName,"$");
			}
			else{
				$isCalc=false;
			}
			preg_match('/(\d+)\/(\d+)/',$formVal,$splitVal);
			$valAsInt=intval($splitVal[1]);
			$outOf=intval($splitVal[2]);
			$valHtml='';
			$spanClasses='formVal'.($isCalc?" formCalc":'').($formVarName?" formVar":"");
			if($outOf<=20 && $outOf>0 && $valAsInt<=$outOf && $valAsInt>=0){
				$spanClasses.=' formCheck';
				$valHtml.=str_repeat('<input class="notPretty" type="checkbox" checked/>',$valAsInt);
				$valHtml.=str_repeat('<input class="notPretty" type="checkbox"/>',$outOf-$valAsInt);
			}
			else{
				$spanClasses.=($isCalc?'':' formText');
				$valHtml = ($isCalc?'':$formVal);
			}
			return '<span class="'.$spanClasses.'" data-varname="'.$formVarName.'" data-varcalc="'.($isCalc?$formVal:"").'" data-formfieldidx="'.($formField++).'">'.$valHtml.'</span>';
	}, $text);
	//end form characters

	//format
	$text=nestedReplace($text,'/\[f=?"?\s*([^\"\]]*)\s*"?\]/ms','[/f]','gpClassFormatter',"span");
	$text=nestedReplace($text,'/\[b=?"?\s*([^\"\]]*)\s*"?\]/ms','[/b]','gpClassFormatter',"strong");
	//end format

	//map
	$matches = null;
	$text=preg_replace_callback("/\[map\](.*?)\[\/map\]/ms", function($matches){
			$mapLink=preg_replace('/^[\s]*(--).*?$/ms', '',$matches[1]); 	//remove -- comments
			$mapLink=preg_replace('/\s+/', '',$mapLink);					//remove spaces
			return '<a class="mapLink" target="_blank" href="'.$mapLink.'"><img class="usrImg" src="'.$mapLink.'"/></a>';
	}, $text);
	//end map

	//urls
	$matches = null;
	$text=preg_replace_callback(array('/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms','/\[url\](.*?)\[\/url\]/ms'), function($matches){
		$url=$matches[1];
		$target=' target="_blank"';
		if ( substr($url, 0, 1) === "/" || preg_match("/^https?:\/\/".getenv('APP_URL')."/", strtolower($url))){
			$target='';
		}
		$linkText=$url;
		if($matches[2]){
			$linkText=$matches[2];
		}

		return '<a href="'.$url.'" '.$target.' rel="nofollow">'.$linkText.'</a>';
	}, $text);
	//end urls

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
	$text=preg_replace_callback("/\[table(=([\"a-zA-Z0-9\- ])*)?\](.*?)\[\/table\]/ms", function($matches){
			$tableType=strtolower(trim(str_replace("=","",str_replace("\"","",$matches[1]))));
			$tableClass="";
			if(strpos($tableType,"center")!==false || strpos($tableType,"centre")!==false){
				$tableClass .= " bbTableCenter";
			}
			if(strpos($tableType,"right")!==false){
				$tableClass .= " bbTableRight";
			}
			if(strpos($tableType,"stats")!==false){
				$tableClass .= " bbTableStats";
			}

			if(strpos($tableType,"htl")!==false){
				$tableClass .= " bbTable-htl";
			} else if(strpos($tableType,"ht")!==false){
				$tableClass .= " bbTable-ht";
			} else if(strpos($tableType,"hl")!==false){
				$tableClass .= " bbTable-hl";
			}

			if(strpos($tableType,"rolls")!==false){
				$tableClass .= " bbTableRolls";
			}

			if(strpos($tableType,"d20")!==false){
				$tableClass.=" bbTableD20";
			}

			if(strpos($tableType,"compact")!==false){
				$tableClass.=" bbTableCompact";
			}

			if(strpos($tableType,"dnd5e")!==false){
				$tableClass.=" bbTableDnd5e";
			}

			if(strpos($tableType,"grid")!==false || strpos($tableType,"lines")!==false){
				$tableClass .= " bbTableGrid";
			}

			if(strpos($tableType,"zebra")!==false){
				$tableClass .= " bbTableZebra";
			}

			if(strpos($tableType,"pool")!==false){
				$tableClass .= " bbTablePool";
			}

			if(strpos($tableType,"pool-add")!==false){
				$tableClass .= " bbTablePoolAdd";
			}

			$tableRows = explode("\n", trim(str_replace("<br />","",$matches[3])));

			//simple code to split cells on pipes
			$ret="<div class='bbTableWrapper'><table class='bbTable".$tableClass."'>";
			foreach ($tableRows as $tableRow){
				$ret=$ret."<tr><td>".str_replace("|","</td><td>",$tableRow)."</td></tr>";
			}

			$ret=$ret."</table></div>";

			return $ret;
	}, $text);
	//end tables

	//npc list
	$matches = null;
	$text=preg_replace_callback("/\[npcs=\"?(.*?)\"?\](.*?)\[\/npcs\]/ms", function($matches){
			$npcTitle=trim(str_replace("=","",str_replace("\"","",$matches[1])));
			$lines=$matches[2];
			$lines=str_replace("<br />","",$lines);
			$lines=str_replace("\r","",$lines);
			$npcRows = explode("\n", trim($lines));

			$ret = "<div class='npcs'>";
			if($npcTitle){
				$ret = $ret."<h3>".$npcTitle."</h3>";
			}else{
				$ret = $ret."<h3>NPCs</h3>";
			}

			$ret = $ret."<div class='npcList'>";
			foreach ($npcRows as $npcRow){
				$npcElements=explode('|',$npcRow,2);
				if(count($npcElements) == 2){
					$ret=$ret."<div class='npcList_item'><img class='npcList_itemAvatar' data-avatar='".$npcElements[1]."' src='".$npcElements[1]."'/><div class='npcList_itemName'>".$npcElements[0]."</div></div>";
				}
			}

			$ret=$ret."</div></div>";

			return $ret;
	}, $text);
	//end npc list

	//notes and private
	$matches = null;
	global $currentUser, $isGM, $post, $postAuthor;

	$postAuthor=false;
	$postAuthorName="";

	if($post){
		$postAuthor=$post->getAuthor('userID') == $currentUser->userID;
		$postAuthorName=$post->getAuthor('username');
	}

	$text = preg_replace('/\[note="?(\w[\w\. +;,]+?)"?](.*?)\[\/note\]\s*/s', '<aside class="note"><div>Note to \1</div>\2</aside>', $text);
	if (strpos($text, 'aside class="note"') !== false && !$isGM && !$postAuthor && preg_match_all('/\<aside class="note"\>\<div\>Note to (.*?)\<\/div\>.*?\<\/aside\>/ms', $text, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$noteTo = array_map('strtolower', preg_split('/[^\w\.]+/', $match[1]));
			if (!in_array(strtolower($currentUser->username), $noteTo)) {
				$text = str_replace($match[0], '<aside class="note"><div>'.$postAuthorName.' sent a note to '.$match[1].'</div></aside>', $text);
			}
		}
	}

	$text = preg_replace('/\[private="?(\w[\w\. +;,]+?)"?](.*?)\[\/private\]\s*/s', '<aside class="private"><div>Privately: \1</div>\2</aside>', $text);
	if (strpos($text, 'aside class="private"') !== false && !$isGM && !$postAuthor && preg_match_all('/\<aside class="private"\>\<div\>Privately: (.*?)\<\/div\>(.*?)\<\/aside\>/ms', $text, $matches, PREG_SET_ORDER)) {
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
	//end notes and private

	//start polls
	if($post){
		$matches = null;
		$text=preg_replace_callback("/\[poll=\"?(.*?)?\"([^\]]*)\](.*?)\[\/poll\]/ms", function($matches){
			global $post, $postAuthor, $isGM;

			$pollResults=$post->getPollResults();

			$pollTitle=$matches[1];

			$pollQuestions = explode("\n", trim(str_replace("<br />","",$matches[3])));

			$multipleVotes = (stripos($matches[2],'multi')!==false);
			$showBeforeVote = (stripos($matches[2],'show')!==false);
			$publicVote = (stripos($matches[2],'public')!==false);

			$ret = "<div class='postPoll".($multipleVotes?" pollAllowMulti":"").($publicVote?" pollPublic":"")."' data-postid='".$post->getPostID()."'>";
			$ret .= "<h3>".$pollTitle.($multipleVotes?" <span class='badge badge-pollMulti'>Multi</span>":"").($publicVote?" <span class='badge badge-pollPublic'>Public</span>":"")."</h3>";

			$ret .= "<div class='pollQuestions'>";
			$answerNumber=1;
			foreach ($pollQuestions as $pollQuestion){
				$pollQuestion=trim($pollQuestion);
				if(strlen($pollQuestion)>0){
					$ret .= "<div class='pollQuestion ".($pollResults['votes'][$answerNumber]["me"]?" pollMyVote":"")."' data-q='".$answerNumber."'><div class='pollQuestionLabel'>".$pollQuestion."</div>";
					$ret .= "<div class='pollQuestionResults'>";
					if($pollResults['voted'] || $showBeforeVote || $postAuthor || $isGM){
						$ret .= $pollResults['votes'][$answerNumber]['html'];
					}
					else{
						$ret .='<div class="voteToView">Vote to view results.</div>';
					}
					$ret .= "</div></div>";
					$answerNumber++;
				}
			}

			$ret.="</div></div>";

			return $ret;


		}, $text, $limit=1);
	}
	//end polls

	//start ffg destiny
	if($post){
		$matches = null;
		$text=preg_replace_callback("/\[fliptokens=\"?(\d*)\"?\]/ms", function($matches){
			global $post, $postAuthor, $isGM;
			$ffgTokens=(int)$matches[1];
			$ffgResults=$post->getFfgDestinyResults($ffgTokens);
			return $ffgResults['html'];
		}, $text, $limit=1);
	}
	//end ffg destiny

	$text = preg_replace_callback('/(\@[0-9a-zA-Z\-\.\_]+[0-9a-zA-Z\-\_])/', function($matches){
		global $currentUser;
		if('@'.strtolower($currentUser->username)==strtolower($matches[1]))
			return '<span class="atHighlight">'.$matches[1].'</span>';
		else
			return $matches[1];
	}, $text);


	//blockquotes back to divs
	$text = str_replace("<blockquote","<div",$text);
	$text = str_replace("</blockquote","</div",$text);
	$text = str_replace("<aside","<div",$text);
	$text = str_replace("</aside","</div",$text);


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