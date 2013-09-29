<?
	$loggedIn = checkLogin(0);
	
	if (isset($_GET['newDeck'])) newDeck($_GET['newDeck']);
	if (!isset($_SESSION['deck'])) $_SESSION['deck'] = array();
	
	$cardCount = array_count_values($_SESSION['deck']);
	if (isset($_SESSION['cardsDrawn'])) {
		$cardsDrawn = $_SESSION['cardsDrawn'];
		unset($_SESSION['cardsDrawn']);
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Card Dealer</h1>
        
		<form method="post" action="<?=SITEROOT?>/tools/process/cards" class="cardControls<?=isset($cardCount[0]) && $cardCount[0] == 0?' hideDiv':''?>">
			<p class="deckName"><?=$_SESSION['deckName']?></p>
			<p>Cards Left: <span class="cardsLeft"><?=$cardCount[1]?></span></p>
			<div>Draw <input type="text" name="numCards" maxlength="2" value="<?=!isset($cardsDrawn)?'':(sizeof($cardsDrawn) > $cardCount[0]?$cardCount[0]:sizeof($cardsDrawn))?>" autocomplete="off" class="numCards"> Cards</div>
			<div class="drawCards"><button type="submit" name="drawCards" class="fancyButton">Draw Cards</button></div>
			<a href="?newDeck=1" class="newDeckLink fancyButton">New Deck</a>
		</form>
		
		<div id="dispArea">
			<div class="newDeck<?=isset($cardCount[1]) && $cardCount[1] > 0?' hideDiv':''?>">
				<h2>New Deck</h2>
				
				<div class="deckType"><a id="newDeck_pcwj" href="?newDeck=pcwj">Playing Cards w/ Jokers</a></div>
				<div class="deckType last"><a id="newDeck_pcwoj" href="?newDeck=pcwoj">Playing Cards w/o Jokers</a></div>
			</div>

			<div class="cardSpace"><div>
<?
	if (isset($cardsDrawn)) {
		$deckShort = $_SESSION['deckShort'];
		$totalNumCards = sizeof($_SESSION['deck']);
		foreach ($cardsDrawn as $card)  echo "\t\t\t".getCardImg($card, $deckShort)."\n";
	} elseif (isset($deckShort)) echo "\t\t\t".'<p id="deckAnnouncement">Deck shuffled!</p>'."\n";
	elseif ($cardCount[0] > 0) echo "\t\t\t".'<p id="deckAnnouncement">Draw cards on the left</p>'."\n";
	else echo "\t\t\t".'<p id="deckAnnouncement">Deck empty. Please select a new deck from above.</p>'."\n";
?>
			</div></div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>