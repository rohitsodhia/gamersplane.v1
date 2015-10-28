<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Notifications</h1>
<script type="text/ng-template" id="characterCreated">
<span>You created a new <span class="system" ng-bind-html="history.character.system.label | trustHTML"></span> character: <span ng-bind-html="history.language.characterLink | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="basicEdited">
<span>You edited the basic info for your <span class="system" ng-bind-html="history.character.system.label | trustHTML"></span> character: <span ng-bind-html="history.language.characterLink | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="characterEdited">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> edited <span ng-bind-html="history.language.targetUser | trustHTML"></span> <span class="system" ng-bind-html="history.character.system.label | trustHTML"></span> character: <span ng-bind-html="history.language.characterLink | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="characterDeleted">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> deleted <span ng-bind-html="history.language.targetUser | trustHTML"></span> <span class="system" ng-bind-html="history.character.system.label | trustHTML"></span> character: <span ng-bind-html="history.language.characterLink | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="addToLibrary">
<span>You added <span ng-bind-html="history.language.characterLink | trustHTML"></span> (<span class="system" ng-bind-html="history.character.system.label | trustHTML"></span>) to the character library</span>
</script>
<script type="text/ng-template" id="removeFromLibrary">
<span>You removed <span ng-bind-html="history.language.characterLink | trustHTML"></span> (<span class="system" ng-bind-html="history.character.system.label | trustHTML"></span>) from the character library</span>
</script>
<script type="text/ng-template" id="characterFavorited">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> favorited <span ng-bind-html="history.language.targetUser | trustHTML"></span> character: <span ng-bind-html="history.language.characterLink | trustHTML"></span> (<span class="system" ng-bind-html="history.character.system.label | trustHTML"></span>)</span>
</script>
<script type="text/ng-template" id="characterUnfavorited">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> unfavorited <span ng-bind-html="history.language.targetUser | trustHTML"></span> character: <span ng-bind-html="history.language.characterLink | trustHTML"></span> (<span class="system" ng-bind-html="history.character.system.label | trustHTML"></span>)</span>
</script>
<script type="text/ng-template" id="characterApplied">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> applied <span ng-bind-html="history.language.characterLink | trustHTML"></span> (<span class="system" ng-bind-html="history.character.system.label | trustHTML"></span>) to <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="characterApproved">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> approved <span ng-bind-html="history.language.targetUser | trustHTML"></span> character <span ng-bind-html="history.language.characterLink | trustHTML"></span> (<span class="system" ng-bind-html="history.character.system.label | trustHTML"></span>) to <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="characterRejected">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> rejected <span ng-bind-html="history.language.targetUser | trustHTML"></span> character <span ng-bind-html="history.language.characterLink | trustHTML"></span> (<span class="system" ng-bind-html="history.character.system.label | trustHTML"></span>) from <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="characterRemoved">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> removed <span ng-bind-html="history.language.targetUser | trustHTML"></span> character <span ng-bind-html="history.language.characterLink | trustHTML"></span> (<span class="system" ng-bind-html="history.character.system.label | trustHTML"></span>) from <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="gameCreated">
<span>You created a new <span class="system">{{history.game.system.label}}</span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="editedGame">
<span>You edited your <span class="system">{{history.game.system.label}}</span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="playerApplied">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> applied to <span class="system">{{history.game.system.label}}</span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="playerInvited">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> invited <span ng-bind-html="history.language.targetUser | trustHTML"></span> to <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="inviteAccepted">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> accepted an invite to <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="inviteWithdrawn">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> withdrew <span ng-bind-html="history.language.targetUser | trustHTML"></span> invite to <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="inviteDeclined">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> declined their invite to <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="playerApproved">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> approved <span ng-bind-html="history.language.targetUser | trustHTML"></span> to <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="playerRejected">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> rejected <span ng-bind-html="history.language.targetUser | trustHTML"></span> from <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="playerRemoved">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> removed <span ng-bind-html="history.language.targetUser | trustHTML"></span> from <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="playerLeft">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> left <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="gmAdded">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> added <span ng-bind-html="history.language.targetUser | trustHTML"></span> as a GM to <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
<script type="text/ng-template" id="gmRemoved">
<span><span ng-bind-html="history.language.actor | trustHTML"></span> removed <span ng-bind-html="history.language.targetUser | trustHTML"></span> as a GM from <span ng-bind-html="history.language.targetGM | trustHTML"></span> game: <span ng-bind-html="LanguageService.gameLink(history.game.gameID, history.game.title) | trustHTML"></span></span>
</script>
		<div ng-repeat="(datestamp, dateHistories) in histories">
			<h2 class="headerbar hbDark" skew-element>{{datestamp | amParse: 'YYYY-MM-DD' | amDateFormat: 'MMMM D, YYYY'}}</h2>
			<div class="hbdMargined" hb-margined>
				<div ng-repeat="history in dateHistories" class="notification tr">
					<div class="timestamp">{{history.timestamp | amLocal | amDateFormat: 'h:mm A'}}</div>
					<div class="dash">-</div>
					<div class="text" ng-include="history.action"></div>
				</div>
			</div>
		</div>
		<paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current" change-func="loadHistories" class="tr"></paginate>
<? require_once(FILEROOT.'/footer.php'); ?>