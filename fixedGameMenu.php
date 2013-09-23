<?
	if (($gameID || $action == 'characters') && !isset($_GET['modal'])) {
		$userID = intval($_SESSION['userID']);
		$fixedMenu = TRUE;
?>
	<div id="fixedMenu"><div id="fixedMenu_window">
<?
		if ($gameID) {
			$gameInfo = $mysql->query("SELECT g.gameID, s.shortName system, g.forumID, p.isGM FROM games g INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN players p ON g.gameID = p.gameID AND p.userID = $userID WHERE g.gameID = $gameID");
			$gameInfo = $gameInfo->fetch();
?>
		<ul class="rightCol">
			<li><a href="<?=SITEROOT.'/tools/maps/?gameID='.$gameID?>" class="right">Maps</a></li>
			<li><a href="<?=SITEROOT.'/chat/'.$gameID?>" class="right">Chat</a></li>
			<li><a href="<?=SITEROOT.'/games/'.$gameID?>" class="right">Game Details</a></li>
		</ul>
<?		} ?>
		<ul class="leftCol">
<?		if ($gameInfo['isGM'] || $action == 'characters') { ?>
			<li id="fixedMenu_tools">
				<a href="<?=SITEROOT?>/tools" class="left">Tools</a>
				<ul class="submenu" data-menu-group="tools">
					<li id="fixedMenu_diceRoller">
						<a href="<?=SITEROOT?>/tools/dice">Dice Roller</a>
						<ul class="submenu">
							<li>
								<div class="floatLeft">
									<div id="customDiceRoll">
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
							</li>
						</ul>
					</li>
					<li id="fixedMenu_cards">
						<a href="<?=SITEROOT?>/tools/cards">Cards</a>
						<ul class="submenu">
<?php $cardCount = array_count_values($_SESSION['deck']); ?>
							<li class="cardControls">
								<p class="deckName"><?=$_SESSION['deckName']?></p>
								<p>Cards Left: <span class="cardsLeft"><?=$cardCount[1]?></span></p>
								<div>Draw <input type="text" name="numCards" maxlength="2" value="<?=!isset($cardsDrawn)?'':(sizeof($cardsDrawn) > $cardCount[0]?$cardCount[0]:sizeof($cardsDrawn))?>" autocomplete="off" class="numCards"> Cards</div>
								<div><button type="submit" name="drawCards" class="drawCards btn_drawCards"></button></div>
								<a href="?newDeck=1" class="newDeckLink"><img src="<?=SITEROOT?>/images/buttons/newDeck.jpg"></a>
							</li>
							
							<li id="fm_dispArea">
								<div class="newDeck<?=$cardCount[0] > 0?' hideDiv':''?>">
									<h2>New Deck</h2>
									
									<div class="deckType"><a id="newDeck_pcwj" href="?newDeck=pcwj">Playing Cards w/ Jokers</a></div>
									<div class="deckType last"><a id="newDeck_pcwoj" href="?newDeck=pcwoj">Playing Cards w/o Jokers</a></div>
								</div>

								<div class="cardSpace">
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</li>
<?		} ?>
<?
		$characters = $mysql->query("SELECT c.characterID, c.label, c.approved, u.userID, u.username FROM characters c, users u WHERE".($gameInfo['isGM']?" c.userID = $userID AND":'')." c.gameID = $gameID AND u.userID = c.userID ORDER BY c.approved DESC, c.label ASC");
		if ($characters->rowCount() && $action != 'characters') {
?>
			<li id="fixedMenu_characters">
				<a href="" data-menu-group="characters">Characters</a>
				<ul class="submenu">
<?
			foreach ($characters as $charInfo) {
?>
					<li>
						<a href="<?=SITEROOT?>/characters/<?=$gameInfo['system']?>/sheet/<?=$charInfo['characterID']?>"><?=$charInfo['label']?></a><br>
						<a href="<?=SITEROOT?>/ucp/<?=$charInfo['userID']?>" class="username"><?=$charInfo['username']?></a>
					</li>
<?			} ?>
				</ul>
			</li>
<?		} ?>
<?=$gameID && $action != 'forums'?"			<li><a href=\"".SITEROOT."/forums/{$gameInfo['forumID']}\" target=\"_blank\">Forum</a></li>\n":''?>
		</ul>
	</div></div>
<? } ?>