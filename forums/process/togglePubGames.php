<?
	$showPubGames = $currentUser->getUsermeta('showPubGames');
	$currentUser->updateUsermeta('showPubGames', $showPubGames?0:1);

	header("Location: {$_SESSION['lastURL']}");
?>