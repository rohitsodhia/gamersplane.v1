<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar" skew-element>{{user.username}}</h1>
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
					<div ng-if="user.gender != 'n'" class="tr">
						<div class="title">Gender</div>
						<div>{{user.gender == 'm'?'Male':'Female'}}</div>
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

			<div id="charStats" class="userInfoBox">
				<h2 class="headerbar hbDark" skew-element>Characters Stats</h2>
				<div class="details clearfix" ng-class="{ 'noInfo': !characters.length }">
					<p ng-if="characters.length">{{user.username}} has made {{charCount}} character<span ng-if="charCount > 1">s</span> so far.</p>
					<div ng-repeat="system in characters" class="game" ng-class="{ 'third': $index % 3 == 2 }">
						<div class="gameLogo"><img ng-src="/images/logos/{{system.system._id}}.png"></div>
						<div class="gameInfo">
							<p>{{system.system.name}}</p>
							<p>{{system.numChars}} Char<span ng-if="system.numChars > 1">s</span> - {{system.percentage}}%</p>
						</div>
					</div>
					<div ng-if="characters.length == 0">{{user.username}} has not yet made any characters.</div>
				</div>
			</div>

			<div id="gameStats" class="userInfoBox">
				<h2 class="headerbar hbDark" skew-element>GM Stats</h2>
				<div class="details clearfix" ng-class="{ 'noInfo': !games.length }">
					<p ng-if="games.length">{{user.username}} has run {{gameCount}} game<span ng-if="gameCount > 1">s</span> so far.</p>
					<div ng-repeat="system in games" class="game" ng-class="{ 'third': $index % 3 == 2 }">
						<div class="gameLogo"><img ng-src="/images/logos/{{system.system._id}}.png"></div>
						<div class="gameInfo">
							<p>{{system.system.name}}</p>
							<p>{{system.numGames}} Char<span ng-if="system.numChars > 1">s</span> - {{system.percentage}}%</p>
						</div>
					</div>
					<div ng-if="games.length == 0">{{user.username}} has not yet run any games.</div>
				</div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>