<?php
	require_once('../vendor/autoload.php');

	$envs = explode("\n", file_get_contents('../.env'));
	foreach ($envs as $env) {
		$env = trim($env);
		if (strlen($env)) {
			putenv($env);
		}
	}

	require('../includes/connect.php');
	define('ENVIRONMENT', getenv('ENVIRONMENT'));
	define('PVAR', getenv('PVAR'));
	define('PAGINATE_PER_PAGE', (int) getenv('PAGINATE_PER_PAGE'));
	require('functions.php');
	define('FILEROOT', getenv('APP_FILEROOT'));
	define('APIROOT', getenv('APP_API_FILEROOT'));
	$permissionTypes = [
		'read' => 'Read',
		'write' => 'Write',
		'editPost' => 'Edit Post',
		'deletePost' => 'Delete Post',
		'createThread' => 'Create Thread',
		'deleteThread' => 'Delete Thread',
		'addRolls' => 'Add Rolls',
		'addDraws' => 'Add Draws',
		'moderate' => 'Moderate'
	];
	define('COOKIE_DOMAIN', '.'.getenv('APP_URL'));
	startSession();
	require('../includes/User.class.php');
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	require_once('../includes/HistoryLogger.class.php');
	require_once('../includes/HistoryCache.class.php');

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

//	if (file_exists(FILEROOT.'/includes/'.$pathAction.'/_section.php'))
//		include(FILEROOT.'/includes/'.$pathAction.'/_section.php');

//	echo $pathAction;
//	print_r($pathOptions);
//	print_r($_SESSION);
//	var_dump($_COOKIE);

	$requireLoc = '';

	$moddedPath = $pathAction ? $pathAction : '';

	header("Access-Control-Allow-Origin: http".(ENVIRONMENT != 'dev'?'s':'')."://".substr(COOKIE_DOMAIN, 1));
	header('Access-Control-Allow-Credentials: true');
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
		}
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		}
		exit(0);
	}

	global $loggedIn;
	if (sizeof($_POST) == 0) {
		$_POST = (array) json_decode(file_get_contents("php://input"));
	}
	$loggedIn = User::checkLogin(false);

	if (file_exists(APIROOT.'/'.$pathAction.'.class.php')) {
		require(APIROOT.'/'.$pathAction.'.class.php');
		$controller = new $pathAction();
	}

	$mysql = null;
?>
