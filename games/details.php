<?
	$gameID = intval($pathOptions[0]);
	
	$gameInfo = $mysql->query("SELECT g.gameID, g.open, g.title, g.system, g.created, g.postFrequency, g.numPlayers, g.charsPerPlayer, g.description, g.charGenInfo, g.forumID, g.start, g.gmID, u.username, gms.isGM IS NOT NULL isGM, gms.primaryGM IS NOT NULL primaryGM FROM games g INNER JOIN users u ON g.gmID = u.userID LEFT JOIN (SELECT gameID, isGM, primaryGM FROM players WHERE isGM = 1 AND userID = {$currentUser->userID}) gms ON g.gameID = gms.gameID WHERE g.gameID = $gameID");
	if ($gameInfo->rowCount() == 0) { header('Location: /games/list'); exit; }
	$gameInfo = $gameInfo->fetch();

	$postFrequency = explode('/', $gameInfo['postFrequency']);
	$isGM = $gameInfo['isGM']?true:false;
	
	if (!$isGM) {
		$userCheck = $mysql->query('SELECT approved FROM players WHERE gameID = '.$gameInfo['gameID'].' AND userID = '.$currentUser->userID);
		if ($userCheck->rowCount()) {
			$inGame = true;
			$approved = $userCheck->fetchColumn();
		} else 
			$inGame = false;
	} else {
		$inGame = true;
		$approved = true;
	}
	
	$approvedPlayers = $mysql->query("SELECT u.userID, u.username, p.isGM, p.primaryGM FROM users u, players p WHERE p.gameID = $gameID AND u.userID = p.userID AND p.approved = 1 ORDER BY u.username ASC");

	$characters = array();
	foreach ($mysql->query('SELECT characterID, userID, label, approved FROM characters WHERE gameID = '.$gameID) as $character) 
		$characters[$character['userID']][] = $character;
	$playerApprovedChars = $mysql->query("SELECT COUNT(characterID) numChars FROM characters WHERE gameID = {$gameID} AND userID = {$currentUser->userID} AND approved = 1");
	$playerApprovedChars = $playerApprovedChars->fetchColumn();
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Game Details<?=$gameInfo['isGM']?' <a href="/games/'.$gameInfo['gameID'].'/edit">[ EDIT ]</a>':''?></h1>
		
<?	if ($_GET['submitted'] || $_GET['wrongSystem'] || $_GET['approveError']) { ?>
		<div class="alertBox_error"><ul>
<?
		if ($_GET['submitted']) { echo "\t\t\t<li>You already submitted that character to a game.</li>\n"; }
		if ($_GET['wrongSystem']) { echo "\t\t\t<li>That character isn't made for this game.</li>\n"; }
		if ($_GET['approveError']) { echo "\t\t\t<li>There was an issue approving the character.</li>\n"; }
?>
		</ul></div>
<?	} if ($_GET['removed'] || $_GET['gmAdded'] || $_GET['gmRemoved']) { ?>
		<div class="alertBox_success"><ul>
<?
		if ($_GET['gmAdded']) echo "\t\t\t<li>GM successfully added.</li>\n";
		if ($_GET['gmRemoved']) echo "\t\t\t<li>GM successfully removed.</li>\n";
		if ($_GET['removed']) echo "\t\t\t<li>Character successfully removed from game.</li>\n";
?>
		</ul></div>
<?	} ?>
		
		<input id="gameID" type="hidden" value="<?=$gameInfo['gameID']?>">
		<div id="details">
			<div class="tr clearfix">
				<label>Game Status</label>
<?	if ($gameInfo['primaryGM']) { ?>
				<div><?=$gameInfo['open']?'Open':'Closed'?> <a id="changeStatus" href="<?='/games/changeStatus/'.$gameID?>">[ Change ]</a></div>
<?	} else { ?>
				<div><?=$gameInfo['open']?'Open':'Closed'?></div>
<?	} ?>
			</div>
			<div class="tr clearfix">
				<label>Game Title</label>
				<div><?=printReady($gameInfo['title'])?></div>
			</div>
			<div class="tr clearfix">
				<label>System</label>
				<div><?=printReady($systems->getFullName($gameInfo['system']))?></div>
			</div>
			<div class="tr clearfix">
				<label>Game Master</label>
				<div><a href="<?='/user/'.$gameInfo['gmID']?>" class="username"><?=$gameInfo['username']?></a></div>
			</div>
			<div class="tr clearfix">
				<label>Created</label>
				<div class="convertTZ"><?=date('F j, Y g:i a', strtotime($gameInfo['created']))?></div>
			</div>
			<div class="tr clearfix">
				<label>Post Frequency</label>
				<div><?=$postFrequency[0].' post'.($postFrequency[0] == 1?'':'s').' per '.($postFrequency[1] == 'd'?'day':'week')?></div>
			</div>
			<div class="tr clearfix">
				<label>Number of Players</label>
				<div><?=($approvedPlayers->rowCount() - 1).' / '.$gameInfo['numPlayers']?></div>
			</div>
			<div class="tr clearfix">
				<label>Number of Characters per Player</label>
				<div><?=$gameInfo['charsPerPlayer']?></div>
			</div>
			<div class="tr clearfix">
				<label>Description</label>
				<div><?=$gameInfo['description']?printReady($gameInfo['description']):'None Provided'?></div>
			</div>
			<div class="tr clearfix">
				<label>Character Generation Info</label>
				<div><?=$gameInfo['charGenInfo']?printReady($gameInfo['charGenInfo']):'None Provided'?></div>
			</div>
<?
	$forumStatus = $mysql->query('SELECT `read` FROM forums_permissions_general WHERE forumID = '.$gameInfo['forumID']);
	$forumStatus = $forumStatus->fetchColumn();
?>
			<div class="tr clearfix">
				<label>Game Forums are:</label>
<?	if ($gameInfo['primaryGM']) { ?>
				<div><span><?=$forumStatus == 1?'Public':'Private'?></span> <a id="toggleForumVisibility" href="">[ Make game <?=$forumStatus != 1?'Public':'Private'?> ]</a></div>
<?	} else { ?>
				<div><?=$forumStatus == 1?'Public':'Private'?></div>
<?	} ?>
			</div>
		</div>

<?
	if ($loggedIn) {
		$hasRightCol = false;
?>
		<div id="playerDetails" class="clearfix">
<?
		if ($inGame && $approved) {
			$hasRightCol = true;
?>
			<div class="rightCol">
				<div id="submitChar">
					<h2 class="headerbar hbDark">Submit a Character</h2>
<?
			if ($playerApprovedChars < $gameInfo['charsPerPlayer']) {
				$readyChars = $mysql->query("SELECT characterID, label FROM characters WHERE userID = '{$currentUser->userID}' AND system = '{$gameInfo['system']}' AND ISNULL(gameID)");
				if ($readyChars->rowCount()) {
?>
					<form method="post" action="/games/process/addCharacter/" class="hbdMargined">
						<input type="hidden" name="gameID" value="<?=$gameID?>">
						<select name="characterID">
<?					foreach ($readyChars as $charInfo) { ?>
							<option value="<?=$charInfo['characterID']?>"><?=$charInfo['label']?></option>
<?					} ?>
						</select>
						<div><button type="submit" name="submitCharacter" class="fancyButton">Submit</button></div>
					</form>
<?	 			} else { ?>
					<p class="hbdMargined notice">You have no characters you can submit at this time</p>
<?
	 			}
	 		} else {
?>
					<p class="hbdMargined notice">You cannot submit any more characters to this game</p>
<?			} ?>
				</div>
			</div>
<?
		} elseif ($inGame && !$approved) {
			$hasRightCol = true;
?>
			<div class="rightCol">
				<div id="applyToGame">
					<h2 class="headerbar hbDark">Join Game</h2>
					<p class="hbdMargined notice">Your request to join this game is awaiting approval</p>
				</div>
			</div>
<?
		} elseif (!$inGame && $loggedIn && $approvedPlayers->rowCount() - 1 < $gameInfo['numPlayers']) {
			$hasRightCol = true;
?>
			<div class="rightCol">
				<div id="applyToGame">
					<h2 class="headerbar hbDark">Join Game</h2>
					<form method="post" action="/games/process/join/" class="alignCenter">
						<input type="hidden" name="gameID" value="<?=$gameID?>">
						<button type="submit" name="apply" class="fancyButton">Apply to Game</button>
					</form>
				</div>
			</div>
<?
		} elseif (!$inGame && $loggedIn && $approvedPlayers->rowCount() - 1 == $gameInfo['numPlayers']) {
			$hasRightCol = true;
?>
			<div class="rightCol">
				<div id="applyToGame">
					<h2 class="headerbar hbDark">Game Full</h2>
					<p class="hbdMargined notice">This game is currently full</p>
				</div>
			</div>
<?		} ?>
			
			<div<?=$hasRightCol?' class="leftCol"':''?>>
				<h2 class="headerbar hbDark hb_hasList">Players in Game</h2>
				<ul id="playersInGame" class="hbdMargined hbAttachedList">
<?	foreach ($approvedPlayers as $playerInfo) { ?>
					<li id="userID_<?=$playerInfo['userID']?>"<?=sizeof($characters[$playerInfo['userID']])?' class="hasChars"':''?>>
						<div class="playerInfo clearfix">
							<div class="player"><a href="<?='/user/'.$playerInfo['userID']?>/" class="username"><?=$playerInfo['username']?></a><?=$playerInfo['isGM']?' <img src="/images/gm_icon.png">':''?></div>
							<div class="actionLinks">
<?		if ($isGM && !$playerInfo['primaryGM']) { ?>
								<a href="<?='/games/'.$gameID.'/removePlayer/'.$playerInfo['userID']?>/" class="removePlayer">Remove player from Game</a>
								<a href="<?='/games/'.$gameID.'/toggleGM/'.$playerInfo['userID']?>/" class="toggleGM"><?=$playerInfo['isGM']?'Remove as GM':'Make GM'?></a>
<?		} elseif ($playerInfo['userID'] == $currentUser->userID && !$playerInfo['primaryGM']) { ?>
								<a href="<?='/games/'.$gameID.'/leaveGame/'.$playerInfo['userID']?>" class="leaveGame">Leave Game</a>
<?		} ?>
							</div>
						</div>
<?		if (sizeof($characters[$playerInfo['userID']])) { ?>
						<ul class="characters">
<?			foreach ($characters[$playerInfo['userID']] as $character) { ?>
							<li class="clearfix">
								<div class="charLabel"><?=(($isGM || $currentUser->userID == $playerInfo['userID'])?'<a href="/characters/'.$gameInfo['system'].'/'.$character['characterID'].'/sheet"':'<div').'>'.$character['label'].(($isGM || $currentUser->userID == $playerInfo['userID'])?"</a>\n":"</div>\n"); ?></div>
								<div class="actionLinks">
<?				if ($isGM && !$character['approved']) { ?>
									<a href="<?='/games/'.$gameID.'/approveChar/'.$character['characterID']?>" class="approveChar">Approve Character</a>
<?
				}
				if ($isGM || $character['userID'] == $currentUser->userID) {
?>
									<a href="<?='/games/'.$gameID.'/removeChar/'.$character['characterID']?>" class="removeChar"><?=$character['userID'] == $currentUser->userID?'Withdraw':(!$character['approved']?'Reject':'Remove')?> Character</a>
<?				} ?>
								</div>
							</li>
<?			} ?>
						</ul>
<?		} ?>
					</li>
<?	} ?>
				</ul>

<?
	if ($isGM) {
		$waitingPlayers = $mysql->query("SELECT u.userID, u.username FROM users u, players p WHERE p.gameID = $gameID AND u.userID = p.userID AND p.approved = 0 ORDER BY u.username ASC");
		if ($waitingPlayers->rowCount()) {
?>
				<h2 id="waitingChars" class="headerbar hbDark hb_hasList">Players awaiting approval</h2>
				<ul id="waitingPlayers" class="hbAttachedList hbdMargined">
<?			foreach ($waitingPlayers as $playerInfo) { ?>
					<li id="userID_<?=$playerInfo['userID']?>" class="playerInfo clearfix">
						<div class="player"><a href="<?='/user/'.$playerInfo['userID']?>" class="username"><?=$playerInfo['username']?></a></div>
						<div class="actionLinks">
							<a href="<?='/games/'.$gameID.'/approvePlayer/'.$playerInfo['userID']?>/" class="approvePlayer">Approve Player</a>
							<a href="<?='/games/'.$gameID.'/rejectPlayer/'.$playerInfo['userID']?>/" class="rejectPlayer">Reject Player</a>
						</div>
					</li>
<?			} ?>
				</ul>
<?
		}
	}
?>
			</div>
		</div>

		<div id="gameFeatures" class="clearfix">
			<div id="maps" class="floatLeft">
<?		if ($isGM) { ?>
				<div class="clearfix hbdTopper"><a id="newMap" href="/games/<?=$gameID?>/maps/new" class="fancyButton smallButton">New Map</a></div>
<?		} ?>
				<h2 class="headerbar hbDark<?=$isGM?' hb_hasButton':''?> hb_hasList">Maps</h2>
				<div class="hbdMargined">
<?
		$mapList = $mysql->query('SELECT mapID, name, rows, cols, visible FROM maps WHERE gameID = '.$gameID);
		
		if ($mapList->rowCount()) {
?>
					<div class="tr clearfix headers">
						<div class="mapVisible"></div>
						<div class="mapLink">Name</div>
						<div class="mapSize">Size</div>
						<div class="mapActions">Actions</div>
					</div>
<?			foreach ($mapList as $mapInfo) { ?>
					<div class="tr clearfix">
						<div class="mapVisible<?=$mapInfo['visible']?'':' invisible'?>"></div>
						<div class="mapLink"><a href="/games/<?=$gameID?>/maps/<?=$mapInfo['mapID']?>"><?=$mapInfo['name']?></a></div>
						<div class="mapSize"><?=$mapInfo['rows']?> x <?=$mapInfo['cols']?></div>
<?				if ($isGM) { ?>
			 			<div class="mapActions">
			 				<a href="/games/<?=$gameID?>/maps/<?=$mapInfo['mapID']?>/edit/" class="iconLink edit">Edit</a>
			 				<a href="/games/<?=$gameID?>/maps/<?=$mapInfo['mapID']?>/delete/" class="iconLink delete">Delete</a>
			 			</div>
<?				} else { ?>
<?				} ?>
					</div>
<?
			}
		} else 
			echo "					<p class=\"notice\">There are no maps available at this time</p>\n";
?>
				</div>
			</div>
			<div id="decks" class="floatRight">
<?		if ($isGM) { ?>
				<div class="clearfix hbdTopper"><a id="newDeck" href="/games/<?=$gameID?>/decks/new" class="fancyButton smallButton">New Deck</a></div>
<?		} ?>
				<h2 class="headerbar hbDark<?=$isGM?' hb_hasButton':''?> hb_hasList">Decks</h2>
				<div class="hbdMargined">
<?
		$decks = $mysql->query('SELECT deckID, label, type, deck, position FROM decks WHERE gameID = '.$gameID);
		$decks = $decks->fetchAll();
		$temp = array();
		foreach ($decks as $key => $value) $temp[$value['deckID']] = $value;
		$decks = $temp;
		
		$deckTypes = array();
		foreach ($mysql->query('SELECT short, name FROM deckTypes') as $deckType) $deckTypes[$deckType['short']] = $deckType['name'];

		if (sizeof($decks)) {
?>
					<div class="tr clearfix headers">
						<div class="deckLabel">Label</div>
						<div class="deckRemaining">Cards Remaining</div>
						<div class="deckActions">Actions</div>
					</div>
<?
			foreach ($decks as $deckInfo) {
				$cardsRemaining = sizeof(explode('~', $deckInfo['deck'])) - $deckInfo['position'] + 1;
?>
					<div class="tr clearfix">
						<div class="deckLabel">
							<?=$deckInfo['label']?>
							<div class="deckType"><?=$deckTypes[$deckInfo['type']]?></div>
						</div>
						<div class="deckRemaining"><?=$cardsRemaining?></div>
<?				if ($isGM) { ?>
						<div class="deckActions">
							<a href="/games/<?=$gameID?>/decks/<?=$deckInfo['deckID']?>/edit/" title="Edit Deck" class="iconLink edit">Edit Deck</a>
							<a href="/games/<?=$gameID?>/decks/<?=$deckInfo['deckID']?>/shuffle/" title="Shuffle Deck" class="iconLink shuffle">Shuffle Deck</a>
							<a href="/games/<?=$gameID?>/decks/<?=$deckInfo['deckID']?>/delete/" title="Delete Deck" class="iconLink delete">Delete Deck</a>
						</div>
<?				} ?>
					</div>
<?
			}
			echo "				</div>\n";
		} else echo "					<p class=\"notice\">There are no decks available at this time</p>\n";
?>
				</div>
			</div>
		</div>
<?
	} else echo "		".'<div id="loggedOutNotice">Interested in this game? <a href="/login" class="loginLink">Login</a> or <a href="/register" class="last">Register</a> to join!</div>'."\n";

?>
<?	require_once(FILEROOT.'/footer.php'); ?>