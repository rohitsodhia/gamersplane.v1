<?
	checkLogin(1);
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($_POST['gameID']);
	$deckID = intval($_POST['deckID']);
	$gmCheck = $mysql->query("SELECT p.primaryGM FROM players p, decks d WHERE p.isGM = 1 AND p.gameID = $gameID AND d.gameID = p.gameID AND d.deckID = $deckID AND p.userID = $userID");
	if (isset($_POST['shuffle']) && $gmCheck->rowCount()) {
		$deckID = intval($_POST['deckID']);
		$deckInfo = $mysql->query('SELECT t.deckSize FROM deckTypes t, decks d WHERE t.short = d.type AND d.deckID = '.$deckID);
		$deckInfo = $deckInfo->fetch();
		$deck = array();
		for ($count = 1; $count <= $deckInfo['deckSize']; $count++) $deck[] = $count;
		shuffle($deck);
		$deck = implode('~', $deck);
		$mysql->query('UPDATE decks SET position = 1, deck = "'.$deck.'", lastShuffle = "'.gmdate('Y-m-d H:i:s').'" WHERE deckID = '.$deckID);
		
		addGameHistory($gameID, 'deckShuffled', $userID, 'NOW()', 'deck', $deckID);
		
		echo 1;
	} else echo 0;
?>