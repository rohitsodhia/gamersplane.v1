				<div class="clearfix">
					<div class="column">
						<div id="basic" hb-margined="dark">
							<div class="tr">
								<label class="textLabel leftLabel">Name:</label>
								<input type="text" ng-model="character.name" maxlength="50">
							</div>
							<div class="tr">
								<label class="textLabel leftLabel">Concept:</label>
								<input type="text" ng-model="character.concept">
							</div>
							<div class="tr">
								<label class="textLabel leftLabel">Nation:</label>
								<input type="text" ng-model="character.nation">
							</div>
							<div class="tr">
								<label class="textLabel leftLabel">Religion:</label>
								<input type="text" ng-model="character.religion">
							</div>
							<div class="tr clearfix">
								<label class="textLabel leftLabel">Reputations:</label>
								<a href="" ng-click="addReputation()">[ Add Reputation ]</a>
								<input ng-repeat="reputation in character.reputations" type="text" ng-model="character.concept">
							</div>
							<div class="tr">
								<label class="textLabel leftLabel">Wealth:</label>
								<input type="text" ng-model="character.wealth">
							</div>
						</div>
						<div id="backgrounds">
							<h2 class="headerbar hbDark" skew-element>Backgrounds</h2>
							<textarea ng-model="character.backgrounds" hb-margined="dark"></textarea>
						</div>
						<div id="advantages">
							<h2 class="headerbar hbDark" skew-element>Advantages</h2>
							<textarea ng-model="character.advantages" hb-margined="dark"></textarea>
						</div>
					</div>
					<div class="column">
						<div id="traits">
							<h2 class="headerbar hbDark" skew-element>Traits</h2>
							<div hb-margined="dark">
								<div ng-repeat="count in range(1, 5)" class="rankLabel">{{count}}</div>
								<div ng-repeat="(trait, rank) in character.traits" class="traits">
									<label class="leftLabel">{{trait.capitalizeFirstLetter()}}</label>
									<div class="ranks">
										<span ng-repeat="count in range(1, 5)" class="rankWrapper"><pretty-radio radio="character.traits[trait]" r-value="count"></pretty-radio></span>
									</div>
								</div>
							</div>
						</div>
						<div id="skills">
							<h2 class="headerbar hbDark" skew-element>Skills</h2>
							<div hb-margined="dark">
								<div ng-repeat="count in range(0, 5)" class="rankLabel">{{count}}</div>
								<div ng-repeat="(skill, rank) in character.skills" class="skills">
									<label class="leftLabel">{{skill.capitalizeFirstLetter()}}</label>
									<div class="ranks">
										<span ng-repeat="count in range(0, 5)" class="rankWrapper"><pretty-radio radio="character.skills[skill]" r-value="count"></pretty-radio></span>
									</div>
								</div>
							</div>
						</div>
						<div id="deathSpiral">
							<h2 class="headerbar hbDark" skew-element>Death Spiral</h2>
							<div hb-margined="dark">
								<img src="/images/characters/7thsea_2e/deathspiral.jpg">
							</div>
						</div>
					</div>
				</div>
				<div id="notes">
					<h2 class="headerbar hbDark" skew-element>Background/Notes</h2>
					<textarea id="notes" ng-model="character.notes" hb-margined="dark"></textarea>
				</div>
