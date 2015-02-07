<?
	require('includes/requires.php');
	
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

	define('STATE', 'standard');
//	define('STATE', 'maintainance');
//	define('STATE', 'moving');

	if (sizeof(explode('.', $_SERVER['HTTP_HOST'])) != 2) {
		include('subdomains.php');
		exit;
	}

	$reqPath = str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
//	echo $reqPath;
	
	if (substr($reqPath, -1) == '/') $reqPath = substr($reqPath, 0, -1);
	$reqPathParts = explode('/', $reqPath);
	$pathOptions = array_slice(explode('/', $reqPath), 1);
	$pathAction = $pathOptions[0];

	$pathOptions = array_slice($pathOptions, 1);

//	$reqPath .= strlen($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:'';
	if (!in_array('ajax', $reqPathParts)) {
		if (($_SESSION['currentURL'] != $reqPath || $_SESSION['lastURL'] == '' || $_SESSION['currentURL'] == '') && !in_array($pathAction, array('login'))) {
			$_SESSION['lastURL'] = $_SESSION['currentURL'];
			$_SESSION['currentURL'] = $reqPath;
		} elseif ($_SESSION['currentURL'] == $reqPath) $sameURL = true;
	}
	
	if (file_exists(FILEROOT.'/includes/'.$pathAction.'/_section.php')) include(FILEROOT.'/includes/'.$pathAction.'/_section.php');
	
//	echo $pathAction;
//	print_r($pathOptions);
//	print_r($_SESSION);
//	var_dump($_COOKIE);

	$requireLoc = '';
	$isAJAX = false;
	
	if ($pathAction == 'facebook') header('Location: http://www.facebook.com/pages/Gamers-Plane/245904792107862');
	elseif (STATE == 'standard') {
		$moddedPath = $pathAction?$pathAction:'';
		foreach ($pathOptions as $pathOption) {
			if ($pathOption == 'ajax') $isAJAX = true;

			$moddedPath .= '/';
			if (is_numeric($pathOption)) $moddedPath .= '(###)';
			elseif (!$isAJAX && $systems->getSystemID($pathOption)) $moddedPath .= '(system)';
			else $moddedPath .= $pathOption;
		}
//		echo $moddedPath;
		$dispatchInfo = $mysql->prepare('SELECT url, pageID, file, title, loginReq, fixedGameMenu, bodyClass, modalWidth FROM dispatch WHERE ? LIKE concat(url, "%") ORDER BY LENGTH(url) DESC LIMIT 1');
		$dispatchInfo->execute(array($moddedPath.'/'));
		$dispatchInfo = $dispatchInfo->fetch();
		global $loggedIn;
		$loggedIn = checkLogin($dispatchInfo['loginReq']);
		if (($dispatchInfo['pageID'] == 'home' && $moddedPath != '') || !file_exists($dispatchInfo['file'])) {
			$dispatchInfo = $mysql->query('SELECT url, pageID, file, title, fixedGameMenu FROM dispatch WHERE url = "404/"');
			$dispatchInfo = $dispatchInfo->fetch();
		}
		$requireLoc = $dispatchInfo['file'];
		define('PAGE_ID', $dispatchInfo['pageID']);
		$fixedGameMenu = $dispatchInfo['fixedGameMenu']?true:false;

		require($requireLoc);
	} elseif (STATE == 'maintainance') {
		$dispatchInfo = array(
			'url' => '/',
			'title' => "Undergoing Maintaince",
			'bodyClass' => null,
			'modalWidth' => null
		);
		$requireLoc = 'maintainance.php';
		define('PAGE_ID', 'maintainance');
		$fixedGameMenu = false;
	} elseif (STATE == 'moving') {
		$dispatchInfo = array(
			'url' => '/',
			'title' => "We're moving",
			'bodyClass' => null,
			'modalWidth' => null
		);
		$requireLoc = 'moving.php';
		define('PAGE_ID', 'moving');
		$fixedGameMenu = false;
	}
	
	$formErrors->clearErrors(true); 
	$mysql = null;
?>