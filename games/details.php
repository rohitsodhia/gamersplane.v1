<?
	$gameID = intval($pathOptions[0]);
	
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
				<div>{{details.status?'Open':'Closed'}}  <a ng-if="isPrimaryGM" href="" ng-click="toggleGameStatus()">[ {{details.status ? 'Close' : 'Open'}} Game ]</a></div>
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
				<div ng-bind-html="details.description | trustHTML"></div>
			</div>
			<div class="tr textareaRow clearfix">
				<label>Character Generation Info</label>
				<div ng-bind-html="details.charGenInfo | trustHTML"></div>
			</div>
			<div class="tr clearfix">
				<label>Game Forums are</label>
				<div>{{details.readPermissions ? 'Public' : 'Private'}} <a ng-if="isPrimaryGM" href="" ng-click="toggleForum()">[ Make game {{!details.readPermissions ? 'Public' : 'Private'}} ]</a></div>
			</div>
		</div>

		<div id="playerDetails" class="clearfix">
			<div ng-if="!details.status && !pendingInvite && !inGame" class="rightCol">
				<h2 skew-element class="headerbar hbDark">Game Closed</h2>
				<p class="notice">This game is closed</p>
			</div>
			<div ng-if="details.status && !loggedIn" class="rightCol">
				<h2 skew-element class="headerbar hbDark">Join Game</h2>
				<p class="alignCenter">Interested in this game?</p>
				<p class="alignCenter"><a href="/login/" class="loginLink" colorbox>Login</a> or <a href="/register/" class="last">Register</a> to join!</p>
			</div>
			<div ng-if="details.status && loggedIn && !pendingInvite && !inGame && details.numPlayers <= details.playersInGame" class="rightCol">
				<h2 class="headerbar hbDark" skew-element>Game Full</h2>
				<p class="hbdMargined notice">This game is currently full</p>
			</div>
			<div ng-if="details.status && loggedIn && !pendingInvite && !inGame && details.numPlayers > details.playersInGame" class="rightCol">
				<h2 class="headerbar hbDark" skew-element>Join Game</h2>
				<form ng-submit="applyToGame()" class="alignCenter">
					<input type="hidden" name="gameID" value="<?=$gameID?>">
					<button type="submit" name="apply" class="fancyButton" skew-element>Apply to Game</button>
				</form>
			</div>
			<div ng-if="loggedIn && !pendingInvite && inGame && !approved" class="rightCol">
				<h2 skew-element class="headerbar hbDark">Join Game</h2>
				<p class="hbMargined notice">Your request to join this game is awaiting approval</p>
				<p class="hbMargined">If you're tired of waiting, you can <a id="withdrawFromGame" href="/games/{{gameID}}/leaveGame/{{currentUser.userID}}/" colorbox>withdraw</a> from the game.</p>
			</div>
			<div ng-if="loggedIn && pendingInvite" class="rightCol">
				<h2 skew-element class="headerbar hbDark">Invite Pending</h2>
				<p hb-margined>You've been invited to join this game!</p>
				<div class="alignCenter">
					<button skew-element type="submit" name="acceptInvite" class="fancyButton" ng-click="acceptInvite()">Join</button>
					<button skew-element type="submit" name="declineInvite" class="fancyButton" ng-click="rejectInvite()">Decline</button>
				</div>
			</div>
			<div ng-if="loggedIn && inGame && approved" class="rightCol">
				<h2 class="headerbar hbDark" skew-element>Submit a Character</h2>
				<form ng-if="characters.length && (curPlayer.characters.length < details.charsPerPlayer || isGM)" id="submitChar" method="post" ng-submit="submitCharacter()" hb-margined>
					<input type="hidden" name="gameID" value="{{gameID}}">
					<combobox data="combobox.characters" value="submitChar.character" search="combobox.search.characters" placeholder="Character" strict></combobox>
					<div><button skew-element type="submit" name="submitCharacter" class="fancyButton">Submit</button></div>
				</form>
				<p ng-if="curPlayer.characters.length >= details.charsPerPlayer && !isGM" class="hbMargined notice">You cannot submit any more characters to this game</p>
				<p ng-if="characters.length == 0 && curPlayer.characters.length < details.charsPerPlayer" class="notice" hb-margined>You don't have any characters to submit</p>
			</div>
			
			<div class="leftCol">
				<h2 class="headerbar hbDark hb_hasList">Players in Game</h2>
				<ul id="playersInGame" class="hbAttachedList hbMargined">
					<li ng-repeat="player in players | filter: { approved: true }" id="userID_{{player.userID}}" ng-class="{ 'hasChars': player.characters > 0 }">
						<div class="playerInfo clearfix" ng-class="{ 'hasChars': player.characters.length }">
							<div class="player"><a href="/user/{{player.userID}}/" class="username">{{player.username}}</a> <img ng-if="player.isGM" src="/images/gm_icon.png"></div>
							<div class="actionLinks">
								<a ng-if="isGM && !player.primaryGM" href="/games/{{gameID}}/removePlayer/{{player.userID}}/" colorbox>Remove player</a>
								<a ng-if="isGM && !player.primaryGM" href="/games/{{gameID}}/toggleGM/{{player.userID}}/" colorbox>{{player.isGM?'Remove as':'Make'}} GM</a>
								<a ng-if="player.userID == currentUser.userID && !player.primaryGM" href="/games/{{gameID}}/leaveGame/{{player.userID}}/" colorbox>Leave Game</a>
							</div>
						</div>
						<ul ng-if="player.characters.length" class="characters">
							<li ng-repeat="character in player.characters" class="clearfix">
								<div class="charLabel">
									<a ng-if="isGM || player.userID == currentUser.userID" href="/characters/{{details.system['_id']}}/{{character.characterID}}/sheet/">{{character.label}}</a>
									<div ng-if="!isGM && player.userID != currentUser.userID">{{character.label}}</div>
								</div>
								<div class="actionLinks">
									<a ng-if="isGM && !character.approved" href="" ng-click="approveCharacter(character)">Approve Character</a>
									<a ng-if="isGM && player.userID != currentUser.userID" href="" ng-click="removeCharacter(character)">{{!character.approved?'Reject':'Remove'}} Character</a>
									<a ng-if="player.userID == currentUser.userID" href="" ng-click="removeCharacter(character)">Withdraw Character</a>
								</div>
							</li>
						</ul>
					</li>
				</ul>

				<div ng-if="isGM && playersAwaitingApproval">
					<h2 class="headerbar hbDark hb_hasList" skew-element>Players Pending Approval</h2>
					<ul id="playersInGame" class="hbAttachedList hbMargined">
						<li ng-repeat="player in players | filter: { approved: false }" id="userID_{{player.userID}}">
							<div class="playerInfo clearfix">
								<div class="player"><a href="/user/{{player.userID}}/" class="username">{{player.username}}</a> <img ng-if="player.isGM" src="/images/gm_icon.png"></div>
								<div class="actionLinks">
									<a href="/games/{{gameID}}/approvePlayer/{{player.userID}}/" colorbox>Approve</a>
									<a href="/games/{{gameID}}/rejectPlayer/{{player.userID}}/" colorbox>Reject</a>
								</div>
							</div>
						</li>
					</ul>
				</div>

				<div ng-if="isGM">
					<h2 skew-element class="headerbar hbDark hb_hasList">Invited</h2>
					<ul class="hbAttachedList" hb-margined>
						<li ng-repeat="invite in invites.waiting | orderBy: 'username'" class="playerInfo clearfix">
							<div class="player"><a href="/user/{{invite.userID}}/?>" class="username">{{invite.username}}</a></div>
							<div class="actionLinks">
								<a href="" ng-click="withdrawInvite(invite)">Withdraw Invite</a>
							</div>
						</li>
					</ul>
					<form id="invites" hb-margined ng-submit="inviteUser()">
						<label>Invite player to game:</label>
						<input type="text" name="user" ng-model="invites.user">
						<button skew-element type="submit" name="invite" class="fancyButton">Invite</button>
						<div class="error" ng-show="invites.errorMsg">{{invites.errorMsg}}</div>
					</form>
				</div>
			</div>
		</div>

		<div id="gameFeatures" class="clearfix">
<? /*			<div id="maps" class="floatLeft">
				<div ng-if="isGM" class="clearfix hbdTopper"><a id="newMap" href="/games/{{gameID}}/maps/new" class="fancyButton smallButton">New Map</a></div>
				<h2 class="headerbar hbDark hb_hasList" ng-class="{ 'hb_hasButton': isGM }">Maps</h2>
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
			</div> */ ?>
			<div id="decks"<? // class="floatRight" ?>>
				<div ng-if="isGM" class="clearfix" hb-topper><a id="newDeck" href="/games/{{gameID}}/decks/new/" colorbox class="fancyButton smallButton" skew-element>New Deck</a></div>
				<h2 class="headerbar hbDark hb_hasList" ng-class="{ 'hb_hasButton': isGM }">Decks</h2>
				<div class="hbdMargined">
					<div ng-if="decks.length" class="tr clearfix headers">
						<div class="deckLabel">Label</div>
						<div class="deckRemaining">Cards Remaining</div>
						<div class="deckActions">Actions</div>
					</div>
					<div ng-repeat="deck in decks" class="tr clearfix">
						<div class="deckLabel">
							{{deck.label}}
							<div class="deckType">{{deck.type.name}}</div>
						</div>
						<div class="deckRemaining">{{deck.cardsRemaining}}</div>
						<div ng-if="isGM" class="deckActions">
							<a href="/games/{{gameID}}/decks/{{deck.deckID}}/edit/" title="Edit Deck" class="sprite editWheel" colorbox></a>
							<a href="/games/{{gameID}}/decks/{{deck.deckID}}/shuffle/" title="Shuffle Deck" class="sprite shuffle" colorbox></a>
							<a href="/games/{{gameID}}/decks/{{deck.deckID}}/delete/" title="Delete Deck" class="sprite cross" colorbox></a>
						</div>
					</div>
					<p ng-if="decks.length == 0" class="notice">There are no decks available at this time</p>
				</div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
