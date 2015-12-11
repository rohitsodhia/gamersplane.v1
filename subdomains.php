<?
	$subdomainParts = explode('.', $_SERVER['HTTP_HOST']);
	$subdomain = $subdomainParts[0];
	$redirect = '';
	if ($subdomain == 'www') 
		$redirect = 'http://gamersplane.com';
	elseif ($subdomain == 'amazon') 
		$redirect = 'http://smile.amazon.com/?_encoding=UTF8&camp=1789&creative=9325&linkCode=ur2&tag=gampla0e6-20&linkId=7RQR4I66XH6Z2U4B';
	elseif ($subdomain == 'dtrpg') 
		$redirect = 'http://rpg.drivethrustuff.com/browse.php?affiliate_id=739399';
	if ($subdomain == 'edr') 
		$redirect = 'http://www.shareasale.com/r.cfm?B=751134&U=1218073&M=60247&urllink=';
	if ($redirect != '') 
		header('Location: '.$redirect);
?>