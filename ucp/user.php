<?	$responsivePage=true;
	require_once(FILEROOT.'/header.php'); ?>
	<h1 class="headerbar">{{user.username}}</h1>
	<div class="flex-row">
		<div id="leftCol">
			<img ng-src="{{user.avatar.url}}" class="avatar">
			<div id="actions">
				<a href="/pms/send/?userID={{user.userID}}">Send Private Message</a>
			</div>
		</div>
		<div id="rightCol">
			<div id="userInfo" class="userInfoBox">
				<h2 class="headerbar hbDark">User Information</h2>
				<div class="details">
					<div class="tr">
						<div class="title">Member Since</div>
						<div>{{user.joinDate | amUtc | amLocal | amDateFormat:'MMMM D, YYYY h:mm a'}}</div>
					</div>
					<div ng-if="user.lastActivity" class="tr">
						<div class="title">Inactive</div>
						<div ng-bind-html="user.lastActivity | trustHTML"></div>
					</div>
					<div ng-if="user.pronoun" class="tr">
						<div class="title">Pronouns</div>
						<div>{{user.pronoun}}</div>
					</div>
					<div ng-if="user.birthday.showAge" class="tr">
						<div class="title">Age</div>
						<div>{{user.birthday.age}}</div>
					</div>
					<div ng-repeat="(field, label) in profileFields" ng-if="user[field].length" class="tr">
						<div class="title">{{label}}</div>
						<div>{{user[field]}}</div>
					</div>
				</div>
			</div>

			<div id="forumStats" class="userInfoBox">
				<h2 class="headerbar hbDark">Forum Stats</h2>
				<div class="details clearfix" hb-margined>
					<div class="tr">
						<div class="title">Total Posts:</div>
						<div>{{posts.postCount}}</div>
					</div>
				</div>
			</div>

			<div id="charStats" class="userInfoBox">
				<h2 class="headerbar hbDark">Characters Stats</h2>
				<p ng-if="charCount > 0">{{user.username}} has made {{charCount}} character<span ng-if="charCount > 1">s</span> so far.</p>
				<div class="details clearfix" ng-class="{ 'noInfo': charCount == 0 }" hb-margined>
					<div ng-repeat="system in characters | orderBy: ['-numChars', 'system.name']" class="game" ng-class="{ 'third': $index % 3 == 2 }">
						<div class="gameLogo"><img ng-src="/images/logos/{{system.system.slug}}.png"></div>
						<div class="gameInfo">
							<p ng-bind-html="system.system.name"></p>
							<p>{{system.numChars}} char<span ng-if="system.numChars > 1">s</span> - {{system.percentage}}%</p>
						</div>
					</div>
					<div ng-if="characters.length == 0">{{user.username}} has not yet made any characters.</div>
				</div>
			</div>

			<div id="gameStats" class="userInfoBox">
				<h2 class="headerbar hbDark">GM Stats</h2>
				<p ng-if="games.length">{{user.username}} has run {{gameCount}} game<span ng-if="gameCount > 1">s</span> so far.</p>
				<div class="details clearfix" ng-class="{ 'noInfo': !games.length }">
					<div ng-repeat="system in games | orderBy: ['-numGames', 'system.name']" class="game" ng-class="{ 'third': $index % 3 == 2 }">
						<div class="gameLogo"><img ng-src="/images/logos/{{system.system.slug}}.png"></div>
						<div class="gameInfo">
							<p ng-bind-html="system.system.name"></p>
							<p>{{system.numGames}} games<span ng-if="system.numGames > 1">s</span> - {{system.percentage}}%</p>
						</div>
					</div>
					<div ng-if="games.length == 0">{{user.username}} has not yet run any games.</div>
				</div>
			</div>
		</div>
	</div>
<?	require_once(FILEROOT.'/footer.php'); ?>