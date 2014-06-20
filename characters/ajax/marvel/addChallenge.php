<?
	require_once(FILEROOT.'/includes/packages/marvelCharacter.package.php');
	if ($character = new marvelCharacter($characterID)) {
		$character->challengeEditFormat($_POST['challengeNum']);
	}
?>