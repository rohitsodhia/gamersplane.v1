<?
	define('SYSTEM', $_POST['system']);
	if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT."/includes/packages/".SYSTEM."Character.package.php");
		$charClass = $systems->systemClassName(SYSTEM).'Character';
		$charClass::skillEditFormat(intval($_POST['key']));
	}
?>