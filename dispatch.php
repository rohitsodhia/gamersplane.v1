<?php
	require('includes/requires.php');

	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

	/*
	Possible states: standard, maintenance, moving
	*/
	define('STATE', getenv('APP_STATE'));

	if (sizeof(explode('.', $_SERVER['HTTP_HOST'])) != sizeof(explode('.', getenv('APP_URL')))) {
		include('subdomains.php');
		exit;
	}

	$reqPath = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
//	echo $reqPath;

	if (substr($reqPath, -1) == '/') {
		$reqPath = substr($reqPath, 0, -1);
	}
	$reqPathParts = explode('/', $reqPath);
	$pathOptions = array_slice(explode('/', $reqPath), 1);
	$pathAction = $pathOptions[0];
	$reqPath .= '/';

	$pathOptions = array_slice($pathOptions, 1);

//	$reqPath .= strlen($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:'';
	if (!in_array('ajax', $reqPathParts)) {
		if (
			(
				$_SESSION['currentURL'] != $reqPath ||
				$_SESSION['lastURL'] == '' ||
				$_SESSION['currentURL'] == ''
			) &&
			!in_array($pathAction, ['login'])
		) {
			$_SESSION['lastURL'] = $_SESSION['currentURL'];
			$_SESSION['currentURL'] = $reqPath;
		} elseif ($_SESSION['currentURL'] == $reqPath) {
			$sameURL = true;
		}
	}

	if (file_exists(FILEROOT.'/includes/'.$pathAction.'/_section.php')) {
		include(FILEROOT.'/includes/'.$pathAction.'/_section.php');
	}

	// echo $pathAction;
	// print_r($pathOptions);
	// var_dump($_SESSION);
	// var_dump($_COOKIE);

	$requireLoc = '';
	$isAJAX = false;

	if ($pathAction == 'facebook') {
		header('Location: https://www.facebook.com/GamersPlane/');
	} elseif (STATE == 'standard') {
		$moddedPath = $pathAction ? $pathAction : '';
		foreach ($pathOptions as $pathOption) {
			if ($pathOption == 'ajax') $isAJAX = true;

			$moddedPath .= '/';
			if (is_numeric($pathOption)) {
				$moddedPath .= '(###)';
			} elseif (!$isAJAX && $systems->verifySystem($pathOption)) {
				$moddedPath .= '(system)';
			} else {
				$moddedPath .= $pathOption;
			}
		}
		$dispatchInfo = $mysql->prepare('SELECT url, pageID, ngController, file, title, loginReq, fixedGameMenu, bodyClass, modalWidth FROM dispatch WHERE ? LIKE concat(url, "%") ORDER BY LENGTH(url) DESC LIMIT 1');
		$dispatchInfo->execute([$moddedPath . '/']);
		$dispatchInfo = $dispatchInfo->fetch();
		// var_dump($dispatchInfo); exit();
		global $loggedIn;
		$loggedIn = User::checkLogin((bool) $dispatchInfo['loginReq']);
		if (
			($dispatchInfo['pageID'] == 'home' && $moddedPath != '') ||
			!file_exists($dispatchInfo['file'])
		) {
			$dispatchInfo = $mysql->query('SELECT url, pageID, file, title, fixedGameMenu FROM dispatch WHERE url = "404/"');
			$dispatchInfo = $dispatchInfo->fetch();
		}
		if ($dispatchInfo['url'] == '/' && !$loggedIn) {
			$dispatchInfo['ngController'] = 'landing';
		}
		$requireLoc = $dispatchInfo['file'];
		define('PAGE_ID', $dispatchInfo['pageID']);
		$fixedGameMenu = $dispatchInfo['fixedGameMenu'] ? true : false;
	} elseif (STATE == 'maintenance') {
		$dispatchInfo = array(
			'url' => '/',
			'title' => "Undergoing Maintenance",
			'bodyClass' => null,
			'modalWidth' => null
		);
		$requireLoc = 'maintenance.php';
		define('PAGE_ID', 'maintenance');
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
	require($requireLoc);

	$formErrors->clearErrors(true);
	$mysql = null;
?>
