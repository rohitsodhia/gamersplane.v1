<?
	$gameID = intval($pathOptions[0]);
	
/*	$gameInfo = $mysql->query("SELECT g.gameID, g.status, g.title, g.system, g.created, g.postFrequency, g.numPlayers, g.charsPerPlayer, g.description, g.charGenInfo, g.forumID, g.gmID, u.username, gms.isGM IS NOT NULL isGM, gms.primaryGM IS NOT NULL primaryGM FROM games g INNER JOIN users u ON g.gmID = u.userID LEFT JOIN (SELECT gameID, isGM, primaryGM FROM players WHERE isGM = 1 AND userID = {$currentUser->userID}) gms ON g.gameID = gms.gameID WHERE g.gameID = $gameID");
	if ($gameInfo->rowCount() == 0) { header('Location: /games/list'); exit; }
	$gameInfo = $gameInfo->fetch();

	$gameInfo['postFrequency'] = explode('/', $gameInfo['postFrequency']);
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
	$playerApprovedChars = $playerApprovedChars->fetchColumn();*/

	$gameInfo = $mysql->query("SELECT g.title, g.description FROM games g WHERE g.gameID = $gameID");
	if ($gameInfo->rowCount() == 0) { header('Location: /games/list'); exit; }
	$gameInfo = $gameInfo->fetch();

	$dispatchInfo['title'] = $gameInfo['title'];
	$dispatchInfo['description'] = "A {$systems->getFullName($gameInfo['system'])} game for {$gameInfo['numPlayers']} players. ".($gameInfo['description']?$gameInfo['description']:'No description provided.');
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Game Details <a ng-if="isGM" href="/games/{{gameID}}/edit/">[ EDIT ]</a></h1>

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
		
		<div id="details">
			<div class="tr clearfix">
				<label>Game Status</label>
				<div>{{details.status}}</div>
			</div>
			<div class="tr clearfix">
				<label>Game Title</label>
				<div>{{details.title}}</div>
			</div>
			<div class="tr clearfix">
				<label>System</label>
				<div ng-bind-html="details.system.name | trustHTML"></div>
			</div>
			<div class="tr clearfix">
				<label>Game Master</label>
				<div><a href="/user/{{details.gm.userID}}" class="username">{{details.gm.username}}</a></div>
			</div>
			<div class="tr clearfix">
				<label>Created</label>
				<div>{{details.created}}</div>
			</div>
			<div class="tr clearfix">
				<label>Post Frequency</label>
				<div>{{details.postFrequency[0]}} post<span ng-if="details.postFrequency[0] > 1">s</span> per {{details.postFrequency[1]}}</div>
			</div>
			<div class="tr clearfix">
				<label>Number of Players</label>
				<div>{{details.playersInGame}} / {{details.numPlayers}}</div>
			</div>
			<div class="tr clearfix">
				<label>Number of Characters per Player</label>
				<div>{{details.charsPerPlayer}}</div>
			</div>
			<div class="tr textareaRow clearfix">
				<label>Description</label>
				<div>{{details.description}}</div>
			</div>
			<div class="tr textareaRow clearfix">
				<label>Character Generation Info</label>
				<div>{{details.charGenInfo}}</div>
			</div>
			<div class="tr clearfix">
				<label>Game Forums are:</label>
				<div>{{details.readPermissions ? 'Public' : 'Private'}} <a ng-if="isPrimaryGM" href="">[ Make game {{!details.readPermissions ? 'Public' : 'Private'}} ]</a></div>
			</div>
		</div>

		<div id="playerDetails" class="clearfix">
			<div ng-if="loggedIn && !inGame && details.numPlayers <= details.playersInGame" class="rightCol">
				<h2 class="headerbar hbDark" skew-element>Game Full</h2>
				<p class="hbdMargined notice">This game is currently full</p>
			</div>
			<div ng-if="loggedIn && !inGame && details.numPlayers > details.playersInGame" class="rightCol">
				<h2 class="headerbar hbDark" skew-element>Join Game</h2>
				<form ng-submit="applyToGame()" class="alignCenter">
					<input type="hidden" name="gameID" value="<?=$gameID?>">
					<button type="submit" name="apply" class="fancyButton" skew-element>Apply to Game</button>
				</form>
			</div>
			<div ng-if="loggedIn && inGame && !approved" class="rightCol">
				<h2 skew-element class="headerbar hbDark">Join Game</h2>
				<p class="hbMargined notice">Your request to join this game is awaiting approval</p>
				<p class="hbMargined">If you're tired of waiting, you can <a id="withdrawFromGame" ng-click="withdrawEarly = !withdrawEarly">withdraw</a> from the game.</p>
				<form id="withdrawEarly" ng-submit="leaveGame()" ng-show="withdrawEarly" class="hbMargined toggleSlide">
					<p>Are you sure you want to withdraw your application?</p>
					<div class="alignCenter"><button skew-element type="text" name="withdraw" class="fancyButton">Withdraw</button></div>
				</form>
			</div>
			<div ng-if="loggedIn && inGame && approved" class="rightCol">
				<h2 class="headerbar hbDark" skew-element>Submit a Character</h2>
				<form ng-if="characters.length" id="submitChar" method="post" action="/games/process/addCharacter/" hb-margined>
					<input type="hidden" name="gameID" value="{{gameID}}">
					<combobox data="combobox.characters" value="subChar" search="combobox.search.characters" placeholder="Character" strict></combobox>
					<div><button skew-element type="submit" name="submitCharacter" class="fancyButton">Submit</button></div>
				</form>
				<p ng-if="curPlayer.characters.length >= details.charsPerPlayer && !isGM" class="hbMargined notice">You cannot submit any more characters to this game</p>
				<p ng-if="characters.length == 0 && curPlayer.characters.length < details.charsPerPlayer && !isGM" class="hbMargined notice">You cannot submit any more characters to this game</p>
			</div>
			
			<div ng-class="{ 'leftCol': loggedIn }">
				<h2 class="headerbar hbDark hb_hasList">Players in Game</h2>
				<ul id="playersInGame" class="hbAttachedList hbMargined">
					<li ng-repeat="player in players | filter: { approved: true }" id="userID_{{player.userID}}" ng-class="{ 'hasChars': player.characters > 0 }">
						<div class="playerInfo clearfix">
							<div class="player"><a href="/user/{{player.userID}}/" class="username">{{player.username}}</a> <img ng-if="player.isGM" src="/images/gm_icon.png"></div>
							<div class="actionLinks">
								<a ng-if="isGM && !player.primaryGM" href="/games/{{gameID}}/removePlayer/{{player.userID}}/" class="removePlayer">Remove player from Game</a>
								<a ng-if="isGM && !player.primaryGM" href="/games/{{gameID}}/toggleGM/{{player.userID}}/" class="toggleGM">{{player.isGM?'Remove as GM':'Make'}} GM</a>
								<a ng-if="player.userID == currentUser.userID && !player.primaryGM" href="/games/{{gameID}}/leaveGame/{{player.userID}}/" class="leaveGame">Leave Game</a>
							</div>
						</div>
						<ul ng-if="player.characters.length" class="characters">
							<li ng-repeat="character in player.characters" class="clearfix">
								<div class="charLabel">
									<a ng-if="isGM || player.userID == currentUser.userID" href="/characters/{{details.system['_id']}}/{{character.characterID}}/sheet/">{{character.label}}</a>
									<div ng-if="!isGM && player.userID != currentUser.userID">{{character.label}}</div>
								</div>
								<div class="actionLinks">
									<a ng-if="isGM && !character.approved" href="/games/{{gameID}}/approveChar/{{character.characterID}}/" class="approveChar">Approve Character</a>
									<a ng-if="isGM" href="/games/{{gameID}}/removeChar/{{character.characterID}}/" class="removeChar">{{!character.approved?'Reject':'Remove'}} Character</a>
									<a ng-if="!isGM && player.userID == currentUser.userID" href="/games/{{gameID}}/removeChar/{{character.characterID}}/" class="removeChar">Withdraw Character</a>
								</div>
							</li>
						</ul>
					</li>
				</ul>

				<div ng-if="isGM" id="invites" hb-margined>
					<form id="invite" method="post" action="<?=API_HOST?>/games/invite/">
						<label>Invite player to game:</label>
						<input type="hidden" name="gameID" value="<?=$gameID?>">
						<input type="text" name="user">
						<button  skew-element type="submit" name="invite" class="fancyButton">Invite</button>
					</form>
				</div>

				<div ng-if="">
					<h2 id="waitingApproval" class="headerbar hbDark hb_hasList">Players awaiting approval</h2>
					<ul id="waitingPlayers" class="hbAttachedList hbdMargined">
						<li id="userID_<?=$playerInfo['userID']?>" class="playerInfo clearfix">
							<div class="player"><a href="<?='/user/'.$playerInfo['userID']?>" class="username"><?=$playerInfo['username']?></a></div>
							<div class="actionLinks">
								<a href="<?='/games/'.$gameID.'/approvePlayer/'.$playerInfo['userID']?>/" class="approvePlayer">Approve Player</a>
								<a href="<?='/games/'.$gameID.'/rejectPlayer/'.$playerInfo['userID']?>/" class="rejectPlayer">Reject Player</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
<? /*
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
		$mapList = $mysql->query("SELECT mapID, name, rows, cols, visible FROM maps WHERE gameID = {$gameID} AND deleted = 0");
		
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
			 				<a href="/games/<?=$gameID?>/maps/<?=$mapInfo['mapID']?>/edit/" Title="Edit" alt="Edit" class="sprite editWheel"></a>
			 				<a href="/games/<?=$gameID?>/maps/<?=$mapInfo['mapID']?>/delete/" title="Delete" alt="Delete" class="sprite cross"></a>
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
							<a href="/games/<?=$gameID?>/decks/<?=$deckInfo['deckID']?>/edit/" title="Edit Deck" class="sprite editWheel"></a>
							<a href="/games/<?=$gameID?>/decks/<?=$deckInfo['deckID']?>/shuffle/" title="Shuffle Deck" class="sprite shuffle"></a>
							<a href="/games/<?=$gameID?>/decks/<?=$deckInfo['deckID']?>/delete/" title="Delete Deck" class="sprite cross"></a>
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
*/
?>
<?	require_once(FILEROOT.'/footer.php'); ?>
