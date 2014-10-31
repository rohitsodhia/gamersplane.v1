<?
	if ($loggedIn) {
		require_once(FILEROOT.'/includes/packages/marvelCharacter.package.php');
		marvelCharacter::modifierEditFormat($_POST['key']);
	}
?>