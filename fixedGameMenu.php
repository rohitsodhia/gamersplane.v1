<?
	if (($gameID || $action == 'characters') && !isset($_GET['modal'])) {
		$fixedMenu = TRUE;
?>
	<div id="fixedMenu"><div id="fixedMenu_window">
<?
		if ($gameID) {
			$gameInfo = $mysql->query("SELECT games.gameID, systems.shortName system, games.forumID, gms.gameID IS NOT NULL isGM FROM games INNER JOIN systems ON games.systemID = systems.systemID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE gms.userID = $userID) gms ON games.gameID = gms.gameID WHERE games.gameID = $gameID");
			$gameInfo = $gameInfo->fetch();
			if (!$gameInfo['isGM']) {
				$charInfo = $mysql->query("SELECT characterID FROM characters WHERE userID = $userID AND gameID = $gameID");
				$characterID = $charInfo->fetchColumn();
			} else $characterID = 0;
?>
		<a href="<?=SITEROOT.'/games/'.$gameID?>" class="right">Game Details</a>
		<a href="<?=SITEROOT.'/games/'.$gameID.'/decks'?>" class="right">Decks</a>
		<a href="<?=SITEROOT.'/chat/'.$gameID?>" class="right">Chat</a>
		<a href="<?=SITEROOT.'/tools/maps/?gameID='.$gameID?>" class="right">Maps</a>
<?
		}
		if ($gameInfo['isGM'] || $action == 'characters') {
?>
		<div id="fixedMenu_tools" class="left subMenu">
			<a href="<?=SITEROOT?>/tools" class="left" data-menu-group="tools">Tools</a>
			<div class="fixedMenu_window">
				<div id="fixedMenu_diceRoller" class="subRootMenu">
					<a href="<?=SITEROOT?>/tools/dice" data-menu-group="tools">Dice Roller</a>
					<div class="fixedMenu_window">
						<div class="floatLeft">
							<div id="customDiceRoll">
								<input type="text"><button id="fm_roll" class="btn_roll"></button>
							</div>
							<p class="indivDice">
								<button name="d4" class="btn_d4 diceBtn"></button>
								<button name="d6" class="btn_d6 diceBtn"></button>
							</p>
							<p class="indivDice">
								<button name="d8" class="btn_d8 diceBtn"></button>
								<button name="d10" class="btn_d10 diceBtn"></button>
							</p>
							<p class="indivDice">
								<button name="d12" class="btn_d12 diceBtn"></button>
								<button name="d20" class="btn_d20 diceBtn"></button>
							</p>
							<p class="indivDice">
								<button name="d100" class="btn_d100 diceBtn"></button>
							</p>
						</div>
						<div class="floatRight">
						</div>
					</div>
				</div>
				<div id="fixedMenu_cards" class="subRootMenu">
					<a href="<?=SITEROOT?>/tools/cards" data-menu-group="tools">Cards</a>
					<div class="fixedMenu_window">
<?php $cardCount = array_count_values($_SESSION['deck']); ?>
						<div class="cardControls">
							<p class="deckName"><?=$_SESSION['deckName']?></p>
							<p>Cards Left: <span class="cardsLeft"><?=$cardCount[1]?></span></p>
							<div>Draw <input type="text" name="numCards" maxlength="2" value="<?=!isset($cardsDrawn)?'':(sizeof($cardsDrawn) > $cardCount[0]?$cardCount[0]:sizeof($cardsDrawn))?>" autocomplete="off" class="numCards"> Cards</div>
							<div><button type="submit" name="drawCards" class="drawCards btn_drawCards"></button></div>
							<a href="?newDeck=1" class="newDeckLink"><img src="<?=SITEROOT?>/images/buttons/newDeck.jpg"></a>
						</div>
						
						<div id="fm_dispArea">
							<div class="newDeck<?=$cardCount[0] > 0?' hideDiv':''?>">
								<h2>New Deck</h2>
								
								<div class="deckType"><a id="newDeck_pcwj" href="?newDeck=pcwj">Playing Cards w/ Jokers</a></div>
								<div class="deckType last"><a id="newDeck_pcwoj" href="?newDeck=pcwoj">Playing Cards w/o Jokers</a></div>
							</div>

							<div class="cardSpace">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<?		} ?>
<?
		if (!$gameInfo['isGM'] && $characterID && $action != 'characters') echo "		<a href=\"".SITEROOT."/characters/{$gameInfo['system']}/sheet/{$characterID}\" target=\"_blank\" class=\"left\">Character Sheet</a>\n";
		elseif ($gameInfo['isGM']) {
?>
		<div id="fixedMenu_characters" class="left subMenu">
			<a href="" data-menu-group="characters">Characters</a>
			<ul class="fixedMenu_window">
<?
			$allChars = $mysql->query("SELECT characters.characterID, characters.label, users.userID, users.username FROM characters, users WHERE characters.gameID = $gameID AND users.userID = characters.userID AND characters.approved = 1 ORDER BY characters.approved DESC, characters.label ASC");
			if ($mysql->rowCount()) { foreach ($allChars as $charInfo) {
?>
				<li>
					<a href="<?=SITEROOT?>/characters/<?=$gameInfo['system']?>/sheet/<?=$charInfo['characterID']?>"><?=$charInfo['label']?></a><br>
					<a href="<?=SITEROOT?>/ucp/<?=$charInfo['userID']?>" class="username"><?=$charInfo['username']?></a>
				</li>
<?			} } else echo "\t\t\t\t<li id=\"noCharacters\">No Characters</li>\n"; ?>
			</ul>
		</div>
<?		} ?>
<?=$gameID && $action != 'forums'?"		<a href=\"".SITEROOT."/forums/{$gameInfo['forumID']}\" target=\"_blank\" class=\"left\">Forum</a>\n":''?>
	</div></div>
<? } ?>