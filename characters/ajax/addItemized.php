<?
	define('SYSTEM', $_POST['system']);
	if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
		$charClass = SYSTEM.'Character';
		$function = $_POST['type'].'EditFormat';
		$charClass::$function($_POST['key']);
	}
?>