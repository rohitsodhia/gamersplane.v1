<?	$responsivePage=true;
	require_once(FILEROOT.'/header.php'); ?>
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
					<select ng-model="game.system" class="notPretty">
						<option ng-repeat="(key, value) in allSystems" value="{{key}}" ng-bind-html="value"></option>
					</select>
				</div>
				<div class="tr" ng-if="allSystems[game.system]=='Custom'">
					<label>Custom Type</label>
					<input id="customSystem" type="text" ng-model="game.customSystem">
				</div>
				<div class="tr">
					<label>Allowed Character Sheets</label>
					<div>
						<combobox data="systemsWCharSheets" change="setCharSheet(value)" select></combobox> <a href="" ng-click="addCharSheet()">[ + ]</a>
					</div>
				</div>
				<div class="error" ng-show="errors.indexOf('noCharSheets') >= 0 || game.allowedCharSheets.length==0">You must allow at least one character sheet.<br/>Click the [+] to add the selected character sheet to the game.</div>
				<div class="tr" ng-show="game.allowedCharSheets.length">
					<div class="shiftRight">
						<div ng-repeat="system in game.allowedCharSheets | orderBy: 'toString()'" class="allowedClass"><span ng-bind-html="allSystems[system]"></span> <a href="" ng-click="removeCharSheet(system)">[ - ]</a></div>
					</div>
				</div>
				<div class="tr">
					<label>Post Frequency</label>
					<input id="timesPer" type="number" ng-model="game.postFrequency.timesPer" maxlength="2" min="1"> time(s) per <select class="notPretty" ng-model="game.postFrequency.perPeriod"><option value="d">day</option><option value="w">week</option></select>
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
					<strong><span class="mob-hide">https://<?=getenv('APP_URL')?></span>/forums/thread/<input id="recruitmentThreadId" type="number" ng-model="game.recruitmentThreadId"></strong>
				</div>
				<blockquote class="spoiler closed"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] Advanced rules definitions</div><div class="hidden">
				<section class="tr textareaRow">
					<p>See the <a href="/forums/thread/22053/" target="guidesForum">guides forum</a> for help configuring these rules</p>
					<div id="ardHelpers">
					<hr/>
						<strong>Background image:</strong> <input type="text" id="adrBackground"/><br/>
						<br/>
						<strong>Choose from community dice rules</strong>
						<ul id="diceRules"></ul>
						<br/>
						<strong>Add community supplied gm sheets</strong>
						<ul id="customSheets"></ul>
						<div class="checkedRow"><label>GM - exclude NPC sheets:</label> <input type="checkbox" class="notPretty" id="gmExcludeNpcs"/></div>
						<div class="checkedRow"><label>GM - exclude PC sheets:</label> <input type="checkbox" class="notPretty" id="gmExcludePcs"/></div>
						<div class="checkedRow"><label>Reroll aces by default:</label> <input type="checkbox" class="notPretty" id="rerollAcesDefault"/></div>
						<hr/>
					</div>
					<textarea id="gameOptions" ng-model="game.gameOptions"></textarea>
					<p id="gameOptionsError" class="alertBox_error" style="display:none;">This is not valid JSON and will not be saved.</p>
				</section>
				</div></blockquote>

				<div id="submitDiv"><button type="submit" class="fancyButton">{{state == 'new'?'Create':'Save'}}</button></div>
			</form>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
