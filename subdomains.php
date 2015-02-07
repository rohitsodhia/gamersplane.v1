<?
	$subdomainParts = explode($__SERVER['HTTP_HOST']);
	$subdomain = $subdomainParts[0];
	if ($subdomain == 'www') $redirect = 'http://gamersplane.com';
	elseif ($subdomain == 'amazon') $redirect = 'http://smile.amazon.com/?_encoding=UTF8&camp=1789&creative=9325&linkCode=ur2&tag=gampla0e6-20&linkId=7RQR4I66XH6Z2U4B';
	elseif ($subdomain == 'dtrpg') $redirect = 'http://rpg.drivethrustuff.com/browse.php?affiliate_id=739399';
	header('Location: '.$redirect);
?>