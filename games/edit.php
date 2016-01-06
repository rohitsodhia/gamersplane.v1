<?
	if ($pathOptions[0] == 'new') 
		$display = 'new';
	else 
		$display = 'edit';

	if ($display == 'edit') {
		$gameID = intval($pathOptions[0]);
		$gameDetails = $mysql->query("SELECT g.gameID, g.title, g.system, g.gmID, g.postFrequency, g.numPlayers, g.charsPerPlayer, g.description, g.charGenInfo, g.status FROM games g INNER JOIN players gms ON g.gameID = gms.gameID AND gms.isGM = 1 WHERE g.gameID = {$gameID} AND gms.userID = {$currentUser->userID} AND retired IS NULL");
		if ($gameDetails->rowCount() == 0) { header('Location: /403'); exit; }
		else {
			$gameDetails = $gameDetails->fetch();
		}
	}

	if (isset($gameDetails['postFrequency'])) {
		$postFrequency = array('timesPer' => 0, 'perPeriod' => 0);
		list($postFrequency['timesPer'], $postFrequency['perPeriod']) = explode('/', $gameDetails['postFrequency']);
		$gameDetails['postFrequency'] = $postFrequency;
	}

	require_once(FILEROOT.'/header.php');
?>
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
				<div ng-if="state == 'new'" class="tr">
					<label>System</label>
					<combobox data="systems" value="game.system" select></combobox>
				</div>
				<div class="tr">
					<label>Post Frequency</label>
					<input id="timesPer" type="number" ng-model="game.timesPer" maxlength="2" min="1"> time(s) per 
					<select ng-model="game.perPeriod">
						<option value="d" ng-selected="game.perPeriod == 'd'">Day</option>
						<option value="w" ng-selected="game.perPeriod == 'w'">Week</option>
					</select>
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
					<textarea ng-model="game.description"></textarea>
				</div>
				<div class="tr textareaRow">
					<label>Character Generation Info</label>
					<textarea ng-model="game.charGenInfo"></textarea>
				</div>
				
				<div id="submitDiv"><button type="submit" class="fancyButton">{{state == 'new'?'Create':'Save'}}</button></div>
			</form>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>