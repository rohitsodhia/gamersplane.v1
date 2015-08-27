<?
	define('SYSTEM', $_POST['system']);
	if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT."/includes/packages/".SYSTEM."Character.package.php");
		$charClass = Systems::systemClassName(SYSTEM).'Character';
		$charClass::featEditFormat(intval($_POST['key']));
	}
?>