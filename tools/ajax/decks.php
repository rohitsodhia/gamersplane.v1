<?
	if (intval($_POST['numCards'])) {
		$deckShort = $_SESSION['deckType'];
		$totalNumCards = sizeof($_SESSION['deck']);
		for ($count = 0; $count < intval($_POST['numCards']); $count++) {
			if (in_array(1, $_POST['deck'])) {
				do {
					$rndCard = mt_rand(1, $totalNumCards);
				} while ($_SESSION['deck'][$rndCard] == 0);
				$_SESSION['deck'][$rndCard] = 0;
				
				echo "\t\t\t".getCardImg($rndCard, $deckShort, $_POST['mini']?TRUE:FALSE)."\n";	
			} else { echo "\t\t\t".'<p id="deckAnnouncement">Deck Empty</p>'."\n"; break; }
		}
	} elseif (isset($deckShort)) 
		echo "\t\t\t".'<p id="deckAnnouncement">Deck Shuffled!</p>'."\n";
?>