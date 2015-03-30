<?
	define('SYSTEM', $_POST['system']);
	if ($systems->verifySystem(SYSTEM)) {
		$charClass = $systems->systemClassName(SYSTEM).'Character';
		require_once(FILEROOT."/includes/packages/{$charClass}.package.php");
		$function = $_POST['type'].'EditFormat';
		$charClass::$function($_POST['key']);
	}
?>