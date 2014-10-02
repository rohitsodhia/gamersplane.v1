<?
	if (checkLogin(0)) {
		define('SYSTEM', $_POST['system']);
		if ($systems->getSystemID(SYSTEM)) {
			require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
			$charClass = SYSTEM.'Character';
			$charClass::skillEditFormat(intval($_POST['key']));
		}
	}
?>