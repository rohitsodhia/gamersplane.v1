<?
	if (checkLogin(0)) {
		require_once(FILEROOT.'/includes/packages/marvelCharacter.package.php');
		marvelCharacter::challengeEditFormat($_POST['key']);
	}
?>