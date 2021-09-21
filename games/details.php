<?php
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');

	$gameID = intval($pathOptions[0]);

	$gameInfo = $mongo->games->findOne(
		['gameID' => $gameID],
		['projection' => ['title' => true, 'description' => true, 'charGenInfo' => true]]
	);
	if (!$gameInfo) { header('Location: /games/list/'); exit; }

	$dispatchInfo['title'] = $gameInfo['title'];
	$dispatchInfo['description'] = "A {$systems->getFullName($gameInfo['system'])} game for {$gameInfo['numPlayers']} players. " . ($gameInfo['description'] ? $gameInfo['description'] : 'No description provided.');
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar"><?=$gameInfo['title'] ?> <a ng-if="isGM" href="/games/{{gameID}}/edit/">[ EDIT ]</a></h1>

<?php	if ($_GET['submitted'] || $_GET['wrongSystem'] || $_GET['approveError']) { ?>
		<div class="alertBox_error"><ul>
<?php
		if ($_GET['submitted']) {
			echo "\t\t\t<li>You already submitted that character to a game.</li>\n";
		}
		if ($_GET['wrongSystem']) {
			echo "\t\t\t<li>That character isn't made for this game.</li>\n";
		}
		if ($_GET['approveError']) {
			echo "\t\t\t<li>There was an issue approving the character.</li>\n";
		}
?>
		</ul></div>
<?php	} if ($_GET['removed'] || $_GET['gmAdded'] || $_GET['gmRemoved']) { ?>
		<div class="alertBox_success"><ul>
<?php
		if ($_GET['gmAdded']) {
			echo "\t\t\t<li>GM successfully added.</li>\n";
		}
		if ($_GET['gmRemoved']) {
			echo "\t\t\t<li>GM successfully removed.</li>\n";
		}
		if ($_GET['removed']) {
			echo "\t\t\t<li>Character successfully removed from game.</li>\n";
		}
?>
		</ul></div>
<?php	} ?>
		<div class="relativeWrapper">
			<div ng-if="details.retired" id="gameRetired">
				This game has been retired! That means it's no longer being run.
			</div>
			<div id="details">
				<div class="tr clearfix descriptionRow">
					<?=printReady(BBCode2Html($gameInfo['description'])) ?>
				</div>
				<div class="tr clearfix">
					<hr/>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>Game Status</label></div>
					<div class="infoCol">{{details.status == 'open' ? 'Open for game applications' : 'Closed for game applications'}}  <a ng-if="isGM" href="" ng-click="toggleGameStatus()">[ {{details.status == 'open' ? 'Close for applications' : 'Open to applications'}} ]</a></div>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>Game Title</label></div>
					<div class="infoCol">{{details.title}}</div>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>System</label></div>
					<div ng-bind-html="details.customType?details.customType:systems[details.system] | trustHTML"></div>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>Allowed Character Sheets</label></div>
					<div><span ng-repeat="system in details.allowedCharSheets"><span ng-bind-html="systems[system] | trustHTML"></span><span ng-if="!$last">, </span></div>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>Game Master</label></div>
					<div class="infoCol"><a href="/user/{{details.gm.userID}}/" class="username">{{details.gm.username}}</a><span ng-bind-html="details.gm.inactive | trustHTML" class="inactive"></span></div>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>Created</label></div>
					<div class="infoCol">{{details.created}}</div>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>Post Frequency</label></div>
					<div class="infoCol">{{details.postFrequency.timesPer}} post<span ng-if="details.postFrequency.timesPer > 1">s</span> per {{details.postFrequency.perPeriod == 'd' ? 'day' : 'week'}}</div>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>Number of Players</label></div>
					<div class="infoCol">{{details.approvedPlayers}} / {{details.numPlayers}}</div>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>Number of Characters per Player</label></div>
					<div class="infoCol">{{details.charsPerPlayer}}</div>
				</div>
				<div class="tr textareaRow clearfix">
					<div class="labelCol"><label>Character Generation Info</label></div>
					<div class="infoCol"><?=printReady(BBCode2Html($gameInfo['charGenInfo'])) ?></div>
				</div>
				<div class="tr clearfix">
					<div class="labelCol"><label>Game Forums are</label></div>
					<div class="infoCol">{{details.readPermissions ? 'Public' : 'Private'}} <a ng-if="isGM" href="" ng-click="toggleForum()">[ Make game {{!details.readPermissions ? 'Public' : 'Private'}} ]</a></div>
				</div>
				<div ng-if="isPrimaryGM" id="deleteGame" class="tr clearfix">
					<div class="labelCol"><label>Retire Game</label></div>
					<div class="infoCol"><a href="" ng-click="toggleRetireConfirm()">I want to close and retire this game!</a></div>
				</div>
				<div ng-if="isPrimaryGM" class="slideToggle" ng-show="displayRetireConfirm">
					<div class="infoCol shiftRight">
						<div>Are you sure you want to retire this game? All characters in the game will be removed and it will be moved to your list of retired games. Gamers' Plane admins may choose to make the game forums public, at their discretion.</div>
						<p>
							<button type="submit" ng-click="confirmRetire()" class="fancyButton smallButton" skew-element>Retire</button>
							<button type="submit" ng-click="toggleRetireConfirm()" class="fancyButton smallButton" skew-element>Cancel</button>
					</div>
				</div>
			</div>

			<div id="playerDetails" class="clearfix">
				<div ng-if="!details.retired && details.status!='open' && !pendingInvite && !inGame" class="rightCol">
					<h2 skew-element class="headerbar hbDark">Game Closed</h2>
					<p class="notice">This game is closed for applications</p>
				</div>
				<div ng-if="!details.retired && details.status=='open' && !loggedIn" class="rightCol">
					<h2 skew-element class="headerbar hbDark">Join Game</h2>
					<p class="alignCenter">Interested in this game?</p>
					<p class="alignCenter"><a href="/login/" class="loginLink" colorbox>Login</a> or <a href="/register/" class="last">Register</a> to join!</p>
				</div>
				<div ng-if="!details.retired && details.status=='open' && loggedIn && !pendingInvite && !inGame && details.numPlayers <= details.approvedPlayers" class="rightCol">
					<h2 class="headerbar hbDark" skew-element>Game Full</h2>
					<p class="hbdMargined notice">This game is currently full</p>
				</div>
				<div ng-if="!details.retired && details.status=='open' && loggedIn && !pendingInvite && !inGame && details.numPlayers > details.approvedPlayers" class="rightCol">
					<h2 class="headerbar hbDark" skew-element>Join Game</h2>
					<div ng-if="details.recruitmentThreadId">
						<form action="{{'/forums/thread/'+details.recruitmentThreadId}}"  method="post">
						<p class="hbMargined"><button type="submit" name="navigateToTavern" class="fancyButton" skew-element>Apply in Games Tavern</button></p>
						</form>

						<form action="{{'/pms/send/?userID='+details.gm.userID}}" method="post">
							<p class="hbMargined"><button type="submit" name="pmTheGm" class="fancyButton" skew-element>Message the GM</button></p>
						</form>
					</div>
					
					<form ng-submit="applyToGame()" class="alignCenter">
						<input type="hidden" name="gameID" value="<?=$gameID?>">
						<button type="submit" name="apply" class="fancyButton" ng-if="!details.recruitmentThreadId" skew-element>Apply to Game</button>
						<div class="alignRight" ng-if="details.recruitmentThreadId">
							<hr/>
							<a href="" onclick="this.closest('form').submit();return false;">Apply to game</a>
						</div>
					</form>
				</div>
				<div ng-if="!details.retired && loggedIn && !pendingInvite && inGame && !approved" class="rightCol">
					<h2 skew-element class="headerbar hbDark">Join Game</h2>
					<p class="hbMargined notice">Your request to join this game is awaiting approval</p>
					<p class="hbMargined">If you're tired of waiting, you can <a id="withdrawFromGame" href="/games/{{gameID}}/leaveGame/{{CurrentUser.userID}}/" colorbox>withdraw</a> from the game.</p>
				</div>
				<div ng-if="!details.retired && loggedIn && pendingInvite" class="rightCol">
					<h2 skew-element class="headerbar hbDark">Invite Pending</h2>
					<p hb-margined>You've been invited to join this game!</p>
					<div class="alignCenter">
						<button skew-element type="submit" name="acceptInvite" class="fancyButton" ng-click="acceptInvite()">Join</button>
						<button skew-element type="submit" name="declineInvite" class="fancyButton" ng-click="declineInvite()">Decline</button>
					</div>
				</div>
				<div ng-if="!details.retired && loggedIn && inGame && approved" class="rightCol">
					<h2 class="headerbar hbDark" skew-element>Submit a Character</h2>
					<form ng-if="characters.length && (curPlayer.characters.length < details.charsPerPlayer || isGM)" id="submitChar" method="post" ng-submit="submitCharacter()" hb-margined>
						<input type="hidden" name="gameID" value="{{gameID}}">
						<combobox data="availChars" search="submitChar.character" change="selectCharacter(value)" select></combobox>
						<div><button skew-element type="submit" name="submitCharacter" class="fancyButton">Submit</button></div>
					</form>
					<p ng-if="curPlayer.characters.length >= details.charsPerPlayer && !isGM" class="hbMargined notice">You cannot submit any more characters to this game</p>
					<p ng-if="characters.length == 0 && curPlayer.characters.length < details.charsPerPlayer" class="notice" hb-margined>You don't have any characters to submit</p>
				</div>

				<div class="leftCol">
					<h2 class="headerbar hbDark hb_hasList">Players in Game</h2>
					<ul id="playersInGame" class="hbAttachedList hbMargined">
						<li ng-repeat="player in players | filter: { approved: true }" id="userID_{{player.user.userID}}" ng-class="{ 'hasChars': player.characters > 0 }">
							<div class="playerInfo clearfix" ng-class="{ 'hasChars': player.characters.length }">
								<div class="player"><a href="/user/{{player.user.userID}}/" class="username">{{player.user.username}}</a> <img ng-if="player.isGM" src="/images/gm_icon.png"></div>
								<div class="actionLinks">
									<a ng-if="player.user.userID != CurrentUser.userID && isGM && !player.primaryGM" href="/games/{{gameID}}/removePlayer/{{player.user.userID}}/" colorbox>Remove player</a>
									<a ng-if="isPrimaryGM && !player.primaryGM" href="/games/{{gameID}}/toggleGM/{{player.user.userID}}/" colorbox>{{player.isGM?'Remove as':'Make'}} GM</a>
									<a ng-if="player.user.userID == CurrentUser.userID && !player.primaryGM" href="/games/{{gameID}}/leaveGame/{{player.user.userID}}/" colorbox>Leave Game</a>
								</div>
							</div>
							<ul ng-if="player.characters.length" class="characters">
								<li ng-repeat="character in player.characters" class="clearfix">
									<div class="charLabel">
										<a ng-if="isGM || player.user.userID == CurrentUser.userID" href="/characters/{{character.system}}/{{character.characterID}}/sheet/">{{character.label}}</a>
										<div ng-if="!isGM && player.user.userID != CurrentUser.userID">{{character.label}}</div>
									</div>
									<div class="actionLinks">
										<a ng-if="isGM && !character.approved" href="" ng-click="approveCharacter(character, player.user.userID)">Approve Character</a>
										<a ng-if="isGM && player.user.userID != CurrentUser.userID" href="" ng-click="removeCharacter(character, player.user.userID)">{{!character.approved?'Reject':'Remove'}} Character</a>
										<a ng-if="player.user.userID == CurrentUser.userID" href="" ng-click="removeCharacter(character, player.user.userID)">Withdraw Character</a>
									</div>
								</li>
							</ul>
						</li>
					</ul>

					<div ng-if="!details.retired && isGM && playersAwaitingApproval">
						<h2 class="headerbar hbDark hb_hasList" skew-element>Players Pending Approval</h2>
						<ul id="playersInGame" class="hbAttachedList hbMargined">
							<li ng-repeat="player in players | filter: { approved: false }" id="userID_{{player.user.userID}}">
								<div class="playerInfo clearfix">
									<div class="player"><a href="/user/{{player.user.userID}}/" class="username">{{player.user.username}}</a> <img ng-if="player.isGM" src="/images/gm_icon.png"></div>
									<div class="actionLinks">
										<a href="/games/{{gameID}}/approvePlayer/{{player.user.userID}}/" colorbox>Approve</a>
										<a href="/games/{{gameID}}/rejectPlayer/{{player.user.userID}}/" colorbox>Reject</a>
									</div>
								</div>
							</li>
						</ul>
					</div>

					<div ng-if="!details.retired">
						<h2 skew-element class="headerbar hbDark hb_hasList">Invited</h2>
						<ul class="hbAttachedList" hb-margined>
							<li ng-repeat="invite in invites.pending | orderBy: 'username'" class="playerInfo clearfix">
								<div class="player"><a href="/user/{{invite.userID}}/" class="username">{{invite.username}}</a></div>
								<div class="actionLinks" ng-show="isGM">
									<a href="" ng-click="withdrawInvite(invite)">Withdraw Invite</a>
								</div>
							</li>
						</ul>
						<form id="invites" hb-margined ng-submit="inviteUser()" ng-show="isGM">
							<label>Invite player:</label>
							<combobox data="invites.users" change="searchForUsers(search, value)" placeholder="User" select></combobox>
							<button skew-element type="submit" name="invite" class="fancyButton">Invite</button>
							<div class="error" ng-show="invites.errorMsg">{{invites.errorMsg}}</div>
						</form>
					</div>
				</div>
			</div>

			<div id="gameFeatures" class="clearfix">
				<div id="decks">
					<div ng-if="!details.retired && isGM" class="clearfix" hb-topper><a id="newDeck" href="/games/{{gameID}}/decks/new/" colorbox class="fancyButton smallButton" skew-element>New Deck</a></div>
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
								<div class="deckType">{{deckTypes[deck.type].name}}</div>
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
		</div>
<?php	require_once(FILEROOT . '/footer.php'); ?>
