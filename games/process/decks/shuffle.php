<?
	$gameID = intval($_POST['gameID']);
	$deckID = intval($_POST['deckID']);
	$gmCheck = $mysql->query("SELECT p.primaryGM FROM players p INNER JOIN decks d ON p.gameID = d.gameID WHERE p.isGM = 1 AND p.gameID = {$gameID} AND d.deckID = {$deckID} AND p.userID = {$currentUser->userID}");
	if (isset($_POST['shuffle']) && $gmCheck->rowCount()) {
		$deckID = intval($_POST['deckID']);
		$deckSize = $mysql->query("SELECT t.deckSize FROM deckTypes t INNER JOIN decks d ON t.short = d.type WHERE d.deckID = {$deckID}");
		$deckSize = $deckSize->fetch(PDO::FETCH_COLUMN);
		$deck = array();
		for ($count = 1; $count <= $deckSize; $count++) 
			$deck[] = $count;
		shuffle($deck);
		$deck = implode('~', $deck);
		$mysql->query("UPDATE decks SET position = 1, deck = '{$deck}', lastShuffle = '".gmdate('Y-m-d H:i:s')."' WHERE deckID = {$deckID}");
		
		$hl_deckShuffled = new HistoryLogger('deckShuffled');
		$hl_deckShuffled->addDeck($deckID)->addUser($currentUser->userID)->save();
		
		displayJSON(array('success' => true, 'deckID' => (int) $deckID, 'deckSize' => (int) $deckSize));
	} else 
		displayJSON(array('failed' => true));
?>