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
				<p>While tabletop RPGs are the most fun face to face, not everyone has the time for protracted sesions, or might not live near other players into the same games as them. Starting in the early days of computers, play-by-post (PbP) gaming is simply playing the RPGs you know and love over a forum. Rather than saying your actions aloud, you just type them up!</p>
				<p>Playing via forum means you don’t need to set side a dedicated few hours to game and can play at your convience. You can log in and respond to other players/the GM whenever you have a few minutes to spare, and the nature of PbP makes for a robust, RP friendliy environment. You can play with old friends, or make new friends around the world!</p>
				<p>Gamers’ Plane offers you a PbP experience you won’t get anywhere else, focused around a community of gamers, with tools to make the experience as close to tabletop as you can get!</p>
			</div>
		</div>
		<div id="landing_features">
			<div class="contentContainer">
				<div id="landing_features_tools">
					<div class="icon"><span></span></div>
					<h3>Tools</h3>
					<p>With dice rollers and card decks built straight into the forums, the random factor is easy to include!</p>
				</div>
				<div id="landing_features_library">
					<div class="icon"><span></span></div>
					<h3>Character Library</h3>
					<p>Need an idea for a new character? Trying to build a big baddie? See what other people share in the library!</p>
				</div>
				<div id="landing_features_sheets">
					<div class="icon"><span></span></div>
					<h3>Character Sheets</h3>
					<p>Every character sheet is a digital replica of its real life version, so what you see online is the same as off line!</p>
				</div>
			</div>
		</div>