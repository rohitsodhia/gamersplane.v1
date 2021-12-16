
<?php	if (($gameID || $pathAction == 'characters') && !isset($_GET['modal'])) { ?>
<div id="fixedMenu"><div id="fixedMenu_window">
<?php
		$gameID = (int) $gameID;
		$isUserGm=false;
		if ($gameID) {
			$game = $mongo->games->findOne(
				[
					'gameID' => (int) $gameID
				],
				[
					'system' => true,
					'forumID' => true,
					'public' => true,
					'players.$' => true
				]
			);
			$isGM = $game && $game['players'][0]['isGM'] ? true : false;
?>
<ul style="display:none" id="playerList">
<?php if($gameID){
	foreach ($game['players'] as $player){
		if($player['isGM'] && $player['user']['userID']==$currentUser->userID){
			$isUserGm=true;
		}
		if($player['approved']){?>
	<li><?= $player['user']['username']?></li>
<?php }
	}
} if($gameID){ ?>
	<script type="application/json" id="gameOptions">
	<?= $game["gameOptions"] ?>
	</script>

<?php }?>
</ul>
	<ul class="rightCol">
		<li><a href="<?='/games/'.$gameID?>" class="menuLink">Game Details</a></li>
	</ul>
<?php		} ?>
	<ul class="leftCol">
<?php		if ($isGM || $pathAction == 'characters') { ?>
		<li id="fm_tools" class="mob-hide">
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
<?php		$cardCount = isset($_SESSION['deck']) && count($_SESSION['deck']) ? array_count_values($_SESSION['deck']) : [0, 0]; ?>
						<div class="cardControls">
							<p class="deckName"><?=$_SESSION['deckName']?></p>
							<p>Cards Left: <span class="cardsLeft"><?=$cardCount[1]?></span></p>
							<div>Draw <input type="text" name="numCards" maxlength="2" value="<?=!isset($cardsDrawn) ? '' : (sizeof($cardsDrawn) > $cardCount[0] ? $cardCount[0] : sizeof($cardsDrawn))?>" autocomplete="off" class="numCards alignCenter"> Cards</div>
							<button type="submit" name="drawCards" class="drawCards">Draw Cards</button>
							<a href="?newDeck=1" class="newDeckLink button">New Deck</a>
						</div>
						<div id="fm_dispArea">
							<div class="newDeck<?=$cardCount[0] > 0 ? ' hideDiv' : ''?>">
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
<?php		} ?>
<?php
		if ($gameID) {
			$charConds = ['game.gameID' => $gameID, 'game.approved' => true];
			if (!$isGM) {
				$charConds['user.userID'] = $currentUser->userID;
			}
			$characters = $mongo->characters->find(
				$charConds,
				[
					'projection' => [
						'characterID' => true,
						'system' => true,
						'label' => true,
						'user' => true,
					],
					'sort' => ['user.username' => 1, 'label' => 1]
				]
			)->toArray();
			if (count($characters) && $pathAction != 'characters') {
?>
		<li id="fm_characters">
			<a href="" class="menuLink">Characters</a>
			<ul class="submenu<?=$isUserGm ? ' isGM' : ''?>" data-menu-group="characters">
<?php
				$currentUserID = 0;
				foreach ($characters as $charInfo) {
					if ($currentUserID != $charInfo['user']['userID']) {
						if ($currentUserID != 0) {
							echo "				</li>\n";
						}
						$currentUserID = $charInfo['user']['userID'];
						echo "				<li".($currentUser->userID==$currentUserID?" class='thisUser'":"").">\n";
						if ($isGM) {
?>
					<p class="username"><i class="ra ra-quill-ink"></i> <a href="/user/<?=$charInfo['user']['userID']?>" class="username"><?=$charInfo['user']['username']?></a></p>
<?php
						}
					}
?>
					<p class="charName"><i class="ra ra-quill-ink"></i> <a href="/characters/<?=$charInfo['system']?>/<?=$charInfo['characterID']?>/" class="charid-<?=$charInfo['characterID']?>"><?=$charInfo['label']?></a></p>
<?php				} ?>
				</li>
			</ul>
		</li>
<?php
			}
		}
		if ($gameID && $pathAction != 'forums' && ($game['players'][0]['approved'] || $game['public'])) {
?>
			<li><a href="/forums/<?=$game['forumID']?>/" class="menuLink">Forum</a></li>
<?php	} ?>
	</ul>
</div></div>
<?php } ?>
