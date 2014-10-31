<?
	if ($_POST['numCards']) {
		$cardsLeft = 0;
		for ($count = 1; $count <= sizeof($_SESSION['deck']); $count++) if ($_SESSION['deck'][$count] == 1) $cardsLeft += 1;
		if ($cardsLeft != 0) {
			$cardsLeft -= intval($_POST['numCards']);
			$cardsDrawn = array();
			$totalNumCards = sizeof($_SESSION['deck']);
			for ($count = 0; $count < intval($_POST['numCards']); $count++) {
				if (in_array(1, $_SESSION['deck'])) {
					do {
						$rndCard = mt_rand(1, $totalNumCards);
					} while ($_SESSION['deck'][$rndCard] == 0);
					$_SESSION['deck'][$rndCard] = 0;

					if (isset($_POST['ajax'])) echo getCardImg($rndCard, $_SESSION['deckShort'], isset($_POST['size']) && strlen($_POST['size']) > 0?$_POST['size']:'')."\n";
					else $cardsDrawn[] = $rndCard;
				}
			}
			if (!isset($_POST['ajax'])) $_SESSION['cardsDrawn'] = $cardsDrawn;
		}
	}
	
	if (!isset($_POST['ajax'])) header('Location: /tools/cards');
?>