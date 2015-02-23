<?
	$showPubGames = $currentUser->getUsermeta('showPubGames');
	$currentUser->updateUsermeta('showPubGames', (bool) $showPubGames?0:1);

	header("Location: {$_SESSION['lastURL']}");
?>