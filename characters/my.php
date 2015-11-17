<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">My Characters</h1>
		
<?	if (isset($_GET['invalidType']) || isset($_GET['invalidLabel'])) { ?>
		<div class="alertBox_error"><ul>
<?
	if (isset($_GET['invalidLabel'])) echo "\t\t\t<li>You must enter a unique and valid label. No profanity!</li>\n";
	if (isset($_GET['invalidType'])) echo "\t\t\t<li>You have to select a system if you want to make a new char!</li>\n";
?>
		</ul></div>
<?	} elseif (isset($_GET['delete']) || isset($_GET['label'])) { ?>
		<div class="alertBox_success"><ul>
<?
	if (isset($_GET['delete'])) echo "\t\t\t<li>Your character was successfully deleted.</li>\n";
	if (isset($_GET['label'])) echo "\t\t\t<li>Label successfully edited.</li>\n";
?>
		</ul></div>
<?	} ?>
		<div id="characterList">
			<h2 class="headerbar hbDark hb_hasButton hb_hasList">Characters</h2>
			<ul ng-if="characters.length" id="userChars" class="hbMargined hbAttachedList" hb-margined>
				<li ng-repeat="character in characters | orderBy: ['system.short', 'label']" class="clearfix character" ng-class="{ 'editing': character.characterID == editing.characterID }">
					<div class="label"><a href="/characters/{{character.system.short}}/{{character.characterID}}/" ng-bind-html="character.label | trustHTML" ng-show="editing.characterID != character.characterID"></a><input type="text" ng-model="character.label" ng-show="editing.characterID == character.characterID"></div
					><div class="charType"><span ng-show="editing.characterID != character.characterID">{{character.charType}}</span><combobox ng-show="editing.characterID == character.characterID" data="charTypes" value="editing.cCharType" select></combobox></div
					><div class="systemType" ng-bind-html="character.system.name | trustHTML"></div
					><div class="links">
						<span ng-hide="editing.characterID == character.characterID || deleting == character.characterID">
							<a class="sprite editWheel" title="Edit Label/Type" alt="Edit Label/Type" ng-click="editBasic(character)" ng-show="editing.characterID != character.characterID"></a>
							<a href="/characters/{{character.system.short}}/{{character.characterID}}/edit/" class="editChar sprite pencil" title="Edit Character" alt="Edit Character"></a>
							<a class="sprite book" ng-class="{ 'off': !character.inLibrary }" title="{{character.inLibrary?'Remove from':'Add to'}} Library" alt="{{character.inLibrary?'Remove from':'Add to'}} Library" ng-click="toggleLibrary(character)"></a>
							<a class="sprite cross" title="Delete Character" alt="Delete Character" ng-click="deleteChar(character)" ng-show="deleting != character.characterID"></a>
						</span>
						<span class="confirm" ng-show="editing.characterID == character.characterID">
							<a class="sprite check green" title="Save" alt="Save" ng-click="saveEdit(character)"></a>
							<a class="sprite cross" title="Cancel" alt="Cancel" ng-click="cancelEditing(character)"></a>
						</span>
						<span class="confirm" ng-show="deleting == character.characterID">
							<a class="sprite check" title="Delete" alt="Delete" ng-click="confirmDelete(character)"></a>
							<a class="sprite cross" title="Cancel" alt="Cancel" ng-click="cancelDeleting(character)"></a>
						</span>
					</div>
				</li>
			</ul>
			<div class="noItems" ng-if="characters.length == 0">It seems you don't have any characters yet. You might wanna get started!</div>
		</div>

		<div id="libraryFavorites">
			<div class="clearfix hbdTopper"><a href="/characters/library/" class="fancyButton">Character Library</a></div>
			<h2 class="headerbar hbDark hb_hasButton hb_hasList">Library Favorites</h2>
			<ul ng-if="library.length" id="libraryChars" class="hbMargined hbAttachedList" hb-margined>
				<li ng-repeat="character in library| orderBy: ['system.short', 'user.username']" id="character_{{character.characterID}}" class="clearfix character">
					<a class="sprite tassel" title="Unfavorite Character" alt="Unfavorite Character" ng-click="unfavorite(character)"></a
					><a href="/characters/{{character.system.short}}/{{character.characterID}}" class="label" ng-bind-html="character.label | trustHTML"></a
					><div class="charType">{{character.charType}}</div
					><div class="systemType" ng-bind-html="character.system.name | trustHTML"></div
					><div class="owner"><a href="/ucp/{{character.user.userID}}" class="username" ng-bind-html="character.user.username | trustHTML"></a></div>
				</li>
			</ul>
			<div ng-if="library.length == 0" class="noItems">You don't have anything from the library favorited. Check out what you're missing!</div>
		</div>

		<form id="newChar" method="post" ng-submit="createChar()">
			<h2 class="headerbar hbDark">New Character</h1>
			<div class="tr">
				<label class="textLabel">Label</label>
				<input type="text" ng-model="newChar.label" maxlength="50">
			</div>
			<div class="tr">
				<label class="textLabel">System</label>
				<combobox data="systems" value="newChar.system" select></combobox>
			</div>
			<div class="tr">
				<label class="textLabel">Type</label>
				<combobox data="charTypes" value="newChar.charType" select></combobox>
			</div>
			<div class="tr buttonPanel"><button type="submit" name="create" class="fancyButton">Create</button></div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>