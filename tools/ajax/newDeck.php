<?
	$deckInfo = newGlobalDeck($_POST['newDeck']);
	echo "{$deckInfo[0]}~{$deckInfo[1]}~{$deckInfo[2]}";
?>