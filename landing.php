		<div id="landing_top">
			<div id="landing_top_content" class="contentContainer">
				<header>
					<h1>Scratch that RPG itch</h1>
					<h2>Talk and play <strong>RPGs</strong> with <strong>hundreds of players</strong>!</h2>
				</header>
				<div id="landing_top_whiteBox">
					<div id="landing_latestGames">
						<h1 class="headerbar">Latest Games</h1>
						<div hb-margined>
							<div id="landing_systemSearch">
								<div class="sprite magnifyingglass"></div>
								<combobox data="systems" change="setSystem(value)" select></combobox>
							</div>

							<div ng-repeat="game in games" class="game" ng-class="{ 'first': $first }">
								<div class="title">
									<a ng-href="/games/{{game.gameID}}/" ng-bind-html="game.title | trustHTML"></a> ({{game.playerCount}} / {{game.numPlayers}})
								</div>
								<div class="info">
									<span class="system" ng-bind-html="game.customType?game.customType:game.system"></span> run by <user-link user="game.gm"></user-link>
								</div>
							</div>
						</div>
					</div>
					<div id="landing_signup">
						<p><a href="/register/" class="register fancyButton">Sign up!</a></p>
						<p>or if you're already a member...</p>
						<p><a href="/login/" class="login fancyButton" colorbox>Log in</a></p>
<!--						<div id="landing_signup_username">
							<span ng-show="signup.username.length == 0 && formFocus != 'username'" ng-click="setFormFocus('username')">Username</span>
							<input type="text" ng-focus="setFormFocus('username')" ng-blur="setFormFocus('')" ng-model="signup.username">
						</div>
						<div id="landing_signup_password">
							<span ng-show="signup.password.length == 0 && formFocus != 'password'" ng-click="setFormFocus('password')">Password</span>
							<input type="password" ng-focus="setFormFocus('password')" ng-blur="setFormFocus('')" ng-model="signup.password">
						</div>
						<div class="alignCenter">
							<a href="" class="fancyButton">Sign Up</a> or <a href="" class="fancyButton">Log In</a>
						</div>-->
					</div>
				</div>
			</div>
		</div>
		<div id="landing_whatIs" class="contentContainer flexWrapper">
			<div id="landing_whatIs_logos">
				<img ng-repeat="system in whatIsLogos" ng-attr-id="landing_whatIs_{{system}}" ng-src="/images/logos/{{system}}.png">
			</div>
			<div id="landing_whatIs_text">
				<h2>What is Play-by-Post?</h2>
				<p>Play-By-Post is a different way to experience tabletop RPGs. Rather than dedicating a few hours at a time to sit together around a table, you can play at your own convenience. Log in and respond to other players and the GM whenever you have a few minutes to spare.</p>

				<p>Gamers' Plane offers you a PbP experience you won’t get anywhere else, focused around a community of gamers, with tools to make the experience as smooth as possible. You can play with old friends, or make new ones around the world!</p>
			</div>
		</div>
		<div id="landing_features">
			<div class="contentContainer">
				<div id="landing_features_tools">
					<div class="icon"><i class="ra ra-three-keys"></i></div>
					<h3>Any RPG</h3>
					<p>Support for <em>all</em> table top RPGs - mainstream favorites, old classics, indie, small press and home-brew games.</p>
				</div>
				<div id="landing_features_library">
					<div class="icon"><i class="ra ra-perspective-dice-six"></i></div>
					<h3>Integrated tools</h3>
					<p>Dedicated game forums, post as your character, integrated character sheets, dice rollers and playing cards.</p>
				</div>
				<div id="landing_features_sheets">
					<div class="icon"><i class="ra ra-double-team"></i></div>
					<h3>Community</h3>
					<p>A diverse and friendly community that welcomes RPG veterans and newcomers alike to the wonderful world of playing RPGs by Post.</p>
				</div>
			</div>
		</div>