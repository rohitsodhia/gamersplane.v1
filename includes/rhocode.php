<?
	function rhocodeConversion($string) {
		$matches = array(
			'/\[quote="(.*?)"\](?:\s)*(.*?)(?:\s)*\[\/quote\](?:\s)*/is',
			'/\[quote\](?:\s)*(.*?)(?:\s)*\[\/quote\](?:\s)*/is',
			'/\[b\](.*?)\[\/b\]/i',
			'/\[u\](.*?)\[\/u\]/i',
			'/\[i\](.*?)\[\/i\]/i',
			'/\[img\](.*?)\[\/img\]/i',
			'/\[url=(.*?)\](.*?)\[\/url\]/i',
			'/\[url\](.*?)\[\/url\]/i',
			'/\[nlist\](?:\s)*(.*?)(?:\s)*\[\/nlist\](?:\s)*/is',
			'/\[blist\](?:\s)*(.*?)(?:\s)*\[\/blist\](?:\s)*/is',
			'/\[item\](?:\s)*(.*?)(?:\s)*\[\/item\](?:\s)*/is',
			'/(?:\n\r|\r\n|\r|\n)/i',
			'/(?:\[quote=".*?"\]|\[quote\]|\[\/quote\]|\[b\]|\[\/b\]|\[u\]|\[\/u\]|\[i\]|\[\/i\]|\[img\]|\[\/img\]|\[url=.*?\]|\[url\]|\[\/url\]|\[nlist\]|\[\/nlist\]|\[blist\]|\[\/blist\])/i'
		);
		$replacements = array(
			'<div class="quote"><div class="quoteTop">Quote: $1</div>$2</div>',
			'<div class="quote"><div class="quoteTop">Quote</div>$1</div>',
			'<b>$1</b>',
			'<u>$1</u>',
			'<i>$1</i>',
			'<img src="$1">',
			'<a href="$1" target="_blank" rel="nofollow">$2</a>',
			'<a href="$1" target="_blank" rel="nofollow">$1</a>',
			'<ol>$1</ol>',
			'<ul>$1</ul>',
			'<li>$1</li>',
			'<br>',
			''
		);
		$string = preg_replace($matches, $replacements, $string);
		
		return $string;
	}
?>