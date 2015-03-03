<?
	require('../includes/connect.php');
	require('functions.php');
	startSession();
	require('../includes/User.class.php');
	define('FILEROOT', $_SERVER['DOCUMENT_ROOT']);
	
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

	$reqPath = str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
//	echo $reqPath;

	if ($reqPath == '/') exit;

	if (substr($reqPath, -1) == '/') $reqPath = substr($reqPath, 0, -1);
	$reqPathParts = explode('/', $reqPath);
	$pathOptions = array_slice(explode('/', $reqPath), 1);
	$pathAction = $pathOptions[0];
	$reqPath .= '/';

	$pathOptions = array_slice($pathOptions, 1);

//	$reqPath .= strlen($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:'';
	
	if (file_exists(FILEROOT.'/includes/'.$pathAction.'/_section.php')) include(FILEROOT.'/includes/'.$pathAction.'/_section.php');
	
//	echo $pathAction;
//	print_r($pathOptions);
//	print_r($_SESSION);
//	var_dump($_COOKIE);

	$requireLoc = '';
	
	$moddedPath = $pathAction?$pathAction:'';

	global $loggedIn;
	$loggedIn = User::checkLogin(false);

	if(file_exists(FILEROOT.'/'.$pathAction.'/')) {
		foreach(glob(FILEROOT.'/'.$pathAction.'/*.php') as $file) 
			require($file);
		$controllerName = $pathAction.'Controller';
		$controller = new $controllerName();

	}

	$mysql = null;
?>