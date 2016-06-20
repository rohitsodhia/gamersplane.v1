				<div class="clearfix">
					<div class="column floatLeft">
						<div id="basic" hb-margined="dark">
							<div class="tr">
								<label class="textLabel leftLabel">Name:</label>
								<input type="text" ng-model="character.name">
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
							<div id="reputations" class="tr clearfix">
								<div id="repLabelWrapper">
									<label class="textLabel leftLabel">Reputations:</label>
									<a ng-click="addReputation()" href="">[ Add Reputation ]</a>
								</div>
								<div id="repInputWrapper">
									<div ng-repeat="reputation in character.reputations track by $index"><input type="text" ng-model="character.reputations[$index]"></div>
								</div>
							</div>
							<div class="tr">
								<label class="textLabel leftLabel">Wealth:</label>
								<input type="text" ng-model="character.wealth">
							</div>
						</div>
						<div id="arcana">
							<h2 class="headerbar hbDark" skew-element>Arcana</h2>
							<div hb-margined="dark"><textarea ng-model="character.arcana"></textarea></div>
						</div>
						<div id="backgrounds">
							<h2 class="headerbar hbDark" skew-element>Backgrounds</h2>
							<div hb-margined="dark"><textarea ng-model="character.backgrounds"></textarea></div>
						</div>
						<div id="advantages">
							<h2 class="headerbar hbDark" skew-element>Advantages</h2>
							<div hb-margined="dark"><textarea ng-model="character.advantages"></textarea></div>
						</div>
						<div id="stories">
							<h2 class="headerbar hbDark" skew-element>Stories <a href="" ng-click="addItem('stories')">[ Add Story ]</a></h2>
							<div hb-margined="dark">
								<div ng-repeat="story in character.stories" class="story" ng-class="{ 'first': $first }">
									<div class="tr">
										<label class="leftLabel">
											<span>Name</span>
											<input type="text" ng-model="story.name">
										</label>
									</div>
									<div class="tr">
										<label class="leftLabel">
											<span>Goal</span>
											<input type="text" ng-model="story.goal">
										</label>
									</div>
									<div class="tr">
										<label class="leftLabel">
											<span>Reward</span>
											<input type="text" ng-model="story.reward">
										</label>
									</div>
									<div class="tr steps">
										<label class="leftLabel">
											<span>Steps</span>
											<textarea ng-model="story.steps"></textarea>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="column floatRight">
						<div id="traits">
							<h2 class="headerbar hbDark" skew-element>Traits</h2>
							<div hb-margined="dark">
								<div class="tr rankLabels">
									<label class="leftLabel"></label>
									<span ng-repeat="count in range(1, 5)" class="rankLabel">{{count}}</span>
								</div>
								<div ng-repeat="(trait, rank) in character.traits" class="tr traits">
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
								<div class="tr rankLabels">
									<label class="leftLabel"></label>
									<span ng-repeat="count in range(0, 5)" class="rankLabel">{{count}}</span>
								</div>
								<div ng-repeat="(skill, rank) in character.skills" class="tr skills">
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
								<ol>
									<li ng-class="{ inactive: character.deathSpiral < 5 }">+1 Bonus Die to all Risks</li>
									<li ng-class="{ inactive: character.deathSpiral < 10 }">Villains gain +2 Bonus Dice</li>
									<li ng-class="{ inactive: character.deathSpiral < 15 }">Your 10s explode (+1 die)</li>
									<li ng-class="{ inactive: character.deathSpiral < 20 }">You become Helpless</li>
								</ol>
								<div><a id="resetDeathSpiral" href="" ng-click="setDeathSpiral(0)">[ Reset Death Spiral ]</a></div>
								<div id="deathSpiralImg">
									<img src="/images/characters/7thsea_2e/deathspiral.jpg">
									<a ng-repeat="count in range(1, 20)" ng-attr-id="{{'bubble_' + count}}" href="" ng-click="setDeathSpiral(count)" class="bubble"></a>
									<img ng-if="character.deathSpiral != 0" ng-repeat="count in range(1, character.deathSpiral)" ng-attr-id="{{'cross_' + count}}" src="/images/characters/7thsea_2e/cross.png" class="cross">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="notes">
					<h2 class="headerbar hbDark" skew-element>Notes</h2>
					<div hb-margined="dark"><textarea ng-model="character.notes"></textarea></div>
				</div>
