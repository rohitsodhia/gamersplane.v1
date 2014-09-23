<?
	if (checkLogin(0)) {
		$userID = intval($_SESSION['userID']);
		define('SYSTEM', $_POST['system']);
		if ($systems->getSystemID(SYSTEM)) {
			require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
			$charClass = SYSTEM.'Character';
			$charClass::skillEditFormat(intval($_POST['key']));
		}
	}
?>