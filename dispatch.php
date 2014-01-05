<?
	require('includes/requires.php');
	
	error_reporting(E_ALL & ~E_NOTICE);
//	error_reporting(E_ALL);
	
	if ($_SERVER['SERVER_NAME'] == 'localhost') $reqPath = $_SERVER['REDIRECT_URL'];
	else $reqPath = $_SERVER['SCRIPT_URL'];

//	echo $reqPath;
	
	if (substr($reqPath, -1) == '/') $reqPath = substr($reqPath, 0, -1);
	$reqPathParts = explode('/', $reqPath);
	$pathOptions = array_slice(explode('/', $reqPath), sizeof(explode('/', SITEROOT)));
	$pathAction = $pathOptions[0];

	$pathOptions = array_slice($pathOptions, 1);
	
//	$reqPath .= strlen($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:'';
	if (!in_array('ajax', $reqPathParts)) {
		if (($_SESSION['currentURL'] != $reqPath || $_SESSION['lastURL'] == '' || $_SESSION['currentURL'] == '') && !in_array($pathAction, array('login'))) {
			$_SESSION['lastURL'] = $_SESSION['currentURL'];
			$_SESSION['currentURL'] = $reqPath;
		} elseif ($_SESSION['currentURL'] == $reqPath) $sameURL = TRUE;
	}
	
	if (file_exists(FILEROOT.'/includes/'.$pathAction.'/_section.php')) include(FILEROOT.'/includes/'.$pathAction.'/_section.php');
	
//	echo $pathAction;
//	print_r($pathOptions);
//	print_r($_SESSION);
	
	$requireLoc = '';
	
	if ($pathAction == 'facebook') header('Location: http://www.facebook.com/pages/Gamers-Plane/245904792107862');
	else {
		$moddedPath = $pathAction?$pathAction:'';
		foreach ($pathOptions as $pathOption) $moddedPath .= '/'.(is_numeric($pathOption)?'(###)':$pathOption);
//		echo $moddedPath;
		$dispatchInfo = $mysql->prepare('SELECT url, pageID, file, title, fixedGameMenu, bodyClass, modalWidth FROM dispatch WHERE ? LIKE concat(url, "%") ORDER BY LENGTH(url) DESC LIMIT 1');
		$dispatchInfo->execute(array($moddedPath.'/'));
		$dispatchInfo = $dispatchInfo->fetch();
		if ($dispatchInfo['pageID'] == 'home' && $moddedPath != '') {
			$dispatchInfo = $mysql->query('SELECT url, pageID, file, title, fixedGameMenu FROM dispatch WHERE url = "404"');
			$dispatchInfo = $dispatchInfo->fetch();
		}
		$requireLoc = $dispatchInfo['file'];
		define('PAGE_ID', $dispatchInfo['pageID']);
		$fixedGameMenu = $dispatchInfo['fixedGameMenu']?TRUE:FALSE;

		require($requireLoc);
	}
	
	$mysql = null;
?>