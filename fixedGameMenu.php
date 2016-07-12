
<?	if (($gameID || $pathAction == 'characters') && !isset($_GET['modal'])) { ?>
<div id="fixedMenu"><div id="fixedMenu_window">
<?
		$gameID = (int) $gameID;
		if ($gameID) {
			$game = $mongo->games->findOne(array(
				'gameID' => (int) $gameID,
				'players.user.userID' => $currentUser->userID
			), array(
				'system' => true,
				'forumID' => true,
				'public' => true,
				'players.$' => true
			));
			$isGM = $game && $game['players'][0]['isGM']?true:false;
?>
	<ul class="rightCol">
		<li><a href="<?='/games/'.$gameID?>" class="menuLink" target="_blank">Game Details</a></li>
	</ul>
<?		} ?>
	<ul class="leftCol">
<?		if ($isGM || $pathAction == 'characters') { ?>
		<li id="fm_tools">
			<a href="/tools" class="menuLink">Tools</a>
			<ul class="submenu" data-menu-group="tools">
				<li id="fm_diceRoller">
					<a href="/tools/dice" class="menuLink">Dice Roller</a>
					<div class="subwindow">
						<div class="floatLeft">
							<div id="fm_customDiceRoll">
								<input type="text"><button id="fm_roll">Roll</button>
							</div>
							<div><button name="d4" class="diceBtn">d4</button></div>
							<div><button name="d6" class="diceBtn">d6</button></div>
							<div><button name="d8" class="diceBtn">d8</button></div>
							<div><button name="d10" class="diceBtn">d10</button></div>
							<div><button name="d12" class="diceBtn">d12</button></div>
							<div><button name="d20" class="diceBtn">d20</button></div>
							<div><button name="d100" class="diceBtn">d100</button></div>
						</div>
						<div class="floatRight"></div>
					</div>
				</li>
				<li id="fm_cards">
					<a href="/tools/cards" class="menuLink">Cards</a>
					<div class="subwindow">
<?		$cardCount = isset($_SESSION['deck']) && count($_SESSION['deck'])?array_count_values($_SESSION['deck']):array(0, 0); ?>
						<div class="cardControls">
							<p class="deckName"><?=$_SESSION['deckName']?></p>
							<p>Cards Left: <span class="cardsLeft"><?=$cardCount[1]?></span></p>
							<div>Draw <input type="text" name="numCards" maxlength="2" value="<?=!isset($cardsDrawn)?'':(sizeof($cardsDrawn) > $cardCount[0]?$cardCount[0]:sizeof($cardsDrawn))?>" autocomplete="off" class="numCards alignCenter"> Cards</div>
							<button type="submit" name="drawCards" class="drawCards">Draw Cards</button>
							<a href="?newDeck=1" class="newDeckLink button">New Deck</a>
						</div>
						<div id="fm_dispArea">
							<div class="newDeck<?=$cardCount[0] > 0?' hideDiv':''?>">
								<h3>New Deck</h3>

								<div class="deckType"><a id="newDeck_pcwj" href="?newDeck=pcwj">Playing Cards w/ Jokers</a></div>
								<div class="deckType last"><a id="newDeck_pcwoj" href="?newDeck=pcwoj">Playing Cards w/o Jokers</a></div>
							</div>

							<div class="cardSpace"><div>
							</div></div>

							<div class="alignCenter">
								<a id="fm_upArrow" href="" class="arrow hideArrow"></a>
								<a id="fm_downArrow" href="" class="arrow hideArrow"></a>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</li>
<?		} ?>
<?
		if ($gameID) {
			$charConds = array('game.gameID' => $gameID, 'game.approved' => true);
			if (!$isGM)
				$charConds['user.userID'] = $currentUser->userID;
			$characters = $mongo->characters->find($charConds, array(
				'characterID' => true,
				'system' => true,
				'label' => true,
				'user' => true,
			))->sort(array('user.username' => 1, 'label' => 1));
			if ($characters && $pathAction != 'characters') {
?>
		<li id="fm_characters">
			<a href="" class="menuLink">Characters</a>
			<ul class="submenu<?=$isGM?' isGM':''?>" data-menu-group="characters">
<?
				$currentUserID = 0;
				foreach ($characters as $charInfo) {
					if ($currentUserID != $charInfo['user']['userID']) {
						if ($currentUserID != 0) echo "				</li>\n";
						$currentUserID = $charInfo['user']['userID'];
						echo "				<li>\n";
						if ($isGM) {
?>
					<p class="username"><a href="/user/<?=$charInfo['user']['userID']?>" class="username"><?=$charInfo['user']['username']?></a></p>
<?
						}
					}
?>
					<p class="charName"><a href="/characters/<?=$game['system']?>/<?=$charInfo['characterID']?>/"><?=$charInfo['label']?></a></p>
<?				} ?>
				</li>
			</ul>
		</li>
<?
			}
		}
		if ($gameID && $pathAction != 'forums' && ($game['players'][0]['approved'] || $game['public'])) {
?>
			<li><a href="/forums/<?=$game['forumID']?>/" target="_blank" class="menuLink">Forum</a></li>
<?		} ?>
	</ul>
</div></div>
<? } ?>
