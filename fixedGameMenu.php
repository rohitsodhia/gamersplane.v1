
<?
	if (($gameID || $pathAction == 'characters') && !isset($_GET['modal'])) {
		$userID = intval($_SESSION['userID']);
?>
<div id="fixedMenu"><div id="fixedMenu_window">
<?
		if ($gameID) {
			$gameInfo = $mysql->query("SELECT g.gameID, s.shortName system, g.forumID, p.isGM FROM games g INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN players p ON g.gameID = p.gameID AND p.userID = $userID WHERE g.gameID = {$gameID}");
			$gameInfo = $gameInfo->fetch();
?>
	<ul class="rightCol">
<!--		<li><a href="<?='/chat/'.$gameID?>" class="menuLink">Chat</a></li>-->
		<li><a href="<?='/games/'.$gameID?>" class="menuLink">Game Details</a></li>
	</ul>
<?		} ?>
	<ul class="leftCol">
<?		if ($gameInfo['isGM'] || $pathAction == 'characters') { ?>
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
			$characters = $mysql->query("SELECT c.characterID, c.label, c.approved, u.userID, u.username FROM characters c, users u WHERE".($gameInfo['isGM']?'':" c.userID = $userID AND")." c.gameID = $gameID AND u.userID = c.userID ORDER BY c.approved DESC, u.username ASC, c.label ASC");
			if ($characters->rowCount() && $pathAction != 'characters') {
?>
		<li id="fm_characters">
			<a href="" class="menuLink">Characters</a>
			<ul class="submenu<?=$gameInfo['isGM']?' isGM':''?>" data-menu-group="characters">
<?
				$currentUserID = 0;
				foreach ($characters as $charInfo) {
					if ($currentUserID != $charInfo['userID']) {
						if ($currentUserID != 0) echo "				</li>\n";
						$currentUserID = $charInfo['userID'];
						echo "				<li>\n";
						if ($gameInfo['isGM']) {
?>
					<p class="username"><a href="/ucp/<?=$charInfo['userID']?>" class="username"><?=$charInfo['username']?></a></p>
<?
						}
					}
?>
					<p class="charName"><a href="/characters/<?=$gameInfo['system']?>/<?=$charInfo['characterID']?>/"><?=$charInfo['label']?></a></p>
<?				} ?>
				</li>
			</ul>
		</li>
<?
			}
		}
?>
<?=$gameID && $pathAction != 'forums'?"			<li><a href=\"/forums/{$gameInfo['forumID']}\" target=\"_blank\" class=\"menuLink\">Forum</a></li>\n":''?>
	</ul>
</div></div>
<? } ?>