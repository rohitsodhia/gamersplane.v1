<?
	require('includes/requires.php');
	
	error_reporting(E_ALL & ~E_NOTICE);
	
	if ($_SERVER['SERVER_NAME'] == 'localhost') $reqPath = $_SERVER['REDIRECT_URL'];
	else $reqPath = $_SERVER['SCRIPT_URL'];
	
	if (substr($reqPath, -1) == '/') $reqPath = substr($reqPath, 0, -1);
	$reqPathParts = explode('/', $reqPath);
	$pathOptions = array_slice(explode('/', $reqPath), sizeof(explode('/', SITEROOT)));
	$action = $pathOptions[0];

	$pathOptions = array_slice($pathOptions, 1);
	
	if (($_SESSION['currentURL'] != $reqPath || $_SESSION['lastURL'] == '' || $_SESSION['currentURL'] == '') && !in_array($action, array('login'))) {
		$_SESSION['lastURL'] = $_SESSION['currentURL'];
		$_SESSION['currentURL'] = $reqPath.$_SERVER['QUERY_STRING'];
	} elseif ($_SESSION['currentURL'] == $reqPath) $sameURL = TRUE;
	
//	echo $action;
//	print_r($pathOptions);
//	print_r($_SESSION);
	
	$requireLoc = '';
	
	if ($action == 'facebook') header('Location: http://www.facebook.com/pages/Gamers-Plane/245904792107862');
	else {
		$moddedPath = $action?$action:'';
		foreach ($pathOptions as $pathOption) $moddedPath .= '/'.(is_numeric($pathOption)?'(###)':$pathOption);
		$dispatchInfo = $mysql->prepare('SELECT url, pageID, file, title, fixedGameMenu FROM dispatch WHERE ? LIKE concat(url, "%") ORDER BY LENGTH(url) DESC LIMIT 1');
		$dispatchInfo->execute(array($moddedPath));
		$dispatchInfo = $dispatchInfo->fetch(PDO::FETCH_ASSOC);
//		if ($dispatchInfo['url'] != $moddedPath) $dispatchInfo = $dispatchInfo404;
		$requireLoc = $dispatchInfo['file'];
		define('PAGE_ID', $dispatchInfo['pageID']);
		define('FIXED_GAME_MENU', $dispatchInfo['fixedGameMenu']?TRUE:FALSE);

		require($requireLoc);
	}
	
	$mysql = null;
?>