	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="/images/favicon.ico" rel="shortcut icon">
	<title><?=strlen($dispatchInfo['title'])?$dispatchInfo['title'].' - ':''?>Gamers Plane</title>
<?	$defaultDesc = "Gamers' Plane is a pen and paper RPG play-by-post site and community. Including tools like integrated dice, character library, and more, Gamers' Plane is a great place to get into a game online, or organize your live ones."; ?>
	<description><?=strlen($dispatchInfo['description'])?$dispatchInfo['description']:$defaultDesc?></description>

	<meta property="og:site_name" content="Gamers' Plane">
	<meta property="og:type" content="website">
	<meta property="og:description" content="<?=strlen($dispatchInfo['description'])?$dispatchInfo['description']:$defaultDesc?>">
	<meta property="og:image" content="http://gamersplane.com/images/logo.jpg">

	<meta http-equiv="cache-control" content="max-age=0">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
	<meta http-equiv="pragma" content="no-cache">