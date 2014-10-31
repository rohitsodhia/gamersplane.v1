<?
	if ($loggedIn) {
		require_once(FILEROOT.'/includes/packages/marvelCharacter.package.php');
		marvelCharacter::challengeEditFormat($_POST['key']);
	}
?>