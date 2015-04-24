<?
	require('../includes/connect.php');
	define('PVAR', 'xU3Fh9XLo21mlHuk6H31');
	define('PAGINATE_PER_PAGE', 20);
	require('functions.php');
	define('FILEROOT', $_SERVER['DOCUMENT_ROOT']);
	$ext = explode('.', $_SERVER['HTTP_HOST']);
	$ext = end($ext);
	define('COOKIE_DOMAIN', '.gamersplane.'.$ext);
	startSession();
	require('../includes/User.class.php');
	
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
	
	$moddedPath = $pathAction?$pathAction:'';

	header("Access-Control-Allow-Origin: http://".substr(COOKIE_DOMAIN, 1));
	header('Access-Control-Allow-Credentials: true');
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) 
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) 
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		exit(0);
	}

	global $loggedIn;
	if (sizeof($_POST) == 0) 
		$_POST = (array) json_decode(file_get_contents("php://input"));
	$loggedIn = User::checkLogin(false);

	if (file_exists(FILEROOT.'/'.$pathAction.'.class.php')) {
		require(FILEROOT.'/'.$pathAction.'.class.php');
		$controller = new $pathAction();
	}

	$mysql = null;
?>