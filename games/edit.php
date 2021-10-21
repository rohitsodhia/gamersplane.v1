<?	require_once(FILEROOT.'/header.php'); ?>
		<div ng-if="state == 'new'" class="sideWidget">
			<h2>LFGs</h2>
			<div class="widgetBody">
				<p>Players want to play (top 10)...</p>
				<ul ng-if="lfg.length">
					<li ng-repeat="system in lfg | orderBy: ['-count', '+name']"><span ng-bind-html="system.name"></span> - {{system.count}}</li>
				</ul>
			</div>
		</div>

		<div class="mainColumn" ng-class="{ 'fullWidth': state == 'edit' }">
			<h1 class="headerbar">{{state.capitalizeFirstLetter()}} Game</h1>

			<form ng-submit="save()">
				<div class="tr">
					<label>Title</label>
					<input id="title" type="text" ng-model="game.title" maxlength="100" ng-change="validateTitle()" ng-blur="validateTitle()" ng-class="{ 'error': errors.indexOf('invalidTitle') >= 0 || errors.indexOf('repeatTitle') >= 0 }">
				</div>
				<div class="error" ng-show="errors.indexOf('invalidTitle') >= 0">Invalid title</div>
				<div class="error" ng-show="errors.indexOf('repeatTitle') >= 0">Someone else already has a game by this title</div>
				<div class="tr">
					<label>System</label>
					<combobox ng-if="state == 'new'" data="allSystems" change="setSystem(value)" select></combobox>
					<div ng-if="state != 'new'" ng-bind-html="allSystems[game.system]"></div>
				</div>
				<div class="tr" ng-if="allSystems[game.system]=='Custom'">
					<label>Custom Type</label>
					<input id="customType" type="text" ng-model="game.customType">
				</div>
				<div class="tr">
					<label>Allowed Character Sheets</label>
					<div>
						<combobox data="systemsWCharSheets" change="setCharSheet(value)" select></combobox> <a href="" ng-click="addCharSheet()">[ + ]</a>
					</div>
				</div>
				<div class="error" ng-show="errors.indexOf('noCharSheets') >= 0">You must allow at least one character sheet</div>
				<div class="tr" ng-show="game.allowedCharSheets.length">
					<div class="shiftRight">
						<div ng-repeat="system in game.allowedCharSheets | orderBy: 'toString()'" class="allowedClass"><span ng-bind-html="allSystems[system]"></span> <a href="" ng-click="removeCharSheet(system)">[ - ]</a></div>
					</div>
				</div>
				<div class="tr">
					<label>Post Frequency</label>
					<input id="timesPer" type="number" ng-model="game.postFrequency.timesPer" maxlength="2" min="1"> time(s) per
					<combobox inputID="perPeriod" data="combobox.periods" change="setPeriod(value)" select></combobox>
				</div>
				<div class="tr">
					<label>Number of Players</label>
					<input id="numPlayers" type="number" ng-model="game.numPlayers" maxlength="2" min="1">
				</div>
				<div class="tr">
					<label>Number of Characters per Player</label>
					<input id="charsPerPlayer" type="number" ng-model="game.charsPerPlayer" maxlength="1" min="1">
				</div>
				<div class="tr textareaRow">
					<label>Description</label>
					<textarea ng-model="game.description" id="gameDescription" class="markItUp"></textarea>
				</div>
				<div class="tr textareaRow">
					<label>Character Generation Info</label>
					<textarea ng-model="game.charGenInfo"  id="gameCharGenInfo" class="markItUp"></textarea>
				</div>
				<div class="tr"><p>If you've created a recruitment thread in the <a href="/forums/10/" target="_blank">Games Tavern</a> link it to the game here.</p></div>
				<div class="tr textareaRow">
					<strong>https://gamersplane.com/forums/thread/<input id="recruitmentThreadId" type="number" ng-model="game.recruitmentThreadId"></strong>
				</div>
				<blockquote class="spoiler closed"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] Advanced rules definitions</div><div class="hidden">
				<div class="tr textareaRow">
					<p>See the release notes for configuring these rules</p>
					<textarea id="gameOptions" ng-model="game.gameOptions"></textarea>
					<p id="gameOptionsError" class="alertBox_error" style="display:none;">This is not valid JSON and will not be saved.</p>
				</div>
				</div></blockquote>

				<div id="submitDiv"><button type="submit" class="fancyButton">{{state == 'new'?'Create':'Save'}}</button></div>
			</form>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
