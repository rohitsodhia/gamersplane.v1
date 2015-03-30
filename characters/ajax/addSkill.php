<?
	define('SYSTEM', $_POST['system']);
	if ($systems->verifySystem(SYSTEM)) {
		$charClass = $systems->systemClassName(SYSTEM).'Character';
		require_once(FILEROOT."/includes/packages/{$charClass}.package.php");
		$charClass::skillEditFormat(intval($_POST['key']));
	}
?>