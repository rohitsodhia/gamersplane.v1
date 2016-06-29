			<div class="clearfix">
				<div class="column floatLeft">
					<div id="basic" hb-margined="dark">
						<div class="tr">
							<label class="textLabel leftLabel">Name:</label>
							{{character.name}}
						</div>
						<div class="tr">
							<label class="textLabel leftLabel">Concept:</label>
							{{character.concept}}
						</div>
						<div class="tr">
							<label class="textLabel leftLabel">Nation:</label>
							{{character.nation}}
						</div>
						<div class="tr">
							<label class="textLabel leftLabel">Religion:</label>
							{{character.religion}}
						</div>
						<div id="reputations" class="tr clearfix">
							<div id="repLabelWrapper">Reputations:</div>
							<div id="repInputWrapper">
								<div ng-repeat="reputation in character.reputations track by $index">{{character.reputations[$index]}}</div>
							</div>
						</div>
						<div class="tr">
							<label class="textLabel leftLabel">Wealth:</label>
							{{character.wealth}}
						</div>
					</div>
					<div id="arcana">
						<h2 class="headerbar hbDark" skew-element>Arcana</h2>
						<div hb-margined="dark">
							<div ng-repeat="(type, arcana) in character.arcana" class="arcana" ng-class="{ 'first': $first }">
								<h3 ng-hide="type == 'hubris' && character.arcana.virtue.arcana == character.arcana.hubris.arcana" ng-bind-html="arcana.arcana | trustHTML"></h3>
								<div class="tr">{{type.capitalizeFirstLetter()}} <span ng-bind-html="arcana.label | trustHTML"></span></div>
								<div class="tr" ng-bind-html="arcana.description | trustHTML"></div>
							</div>
						</div>
					</div>
					<div id="backgrounds">
						<h2 class="headerbar hbDark" skew-element>Backgrounds</h2>
						<div hb-margined="dark">
							<div ng-repeat="background in character.backgrounds track by $index" class="background" ng-class="{ 'first': $first }">
								<div class="tr" ng-bind-html="background.name | trustHTML"></div>
								<div class="tr" ng-bind-html="background.quirk | trustHTML"></div>
							</div>
						</div>
					</div>
					<div id="advantages">
						<h2 class="headerbar hbDark" skew-element>Advantages</h2>
						<div hb-margined="dark">
							<div ng-repeat="advantage in character.advantages track by $index" class="advantage" ng-class="{ 'first': $first }">
								<div class="tr" ng-bind-html="advantage.name | trustHTML"></div>
								<div class="tr" ng-bind-html="advantage.description | trustHTML"></div>
							</div>
						</div>
					</div>
					<div id="stories">
						<h2 class="headerbar hbDark" skew-element>Stories</h2>
						<div hb-margined="dark">
							<div ng-repeat="story in character.stories" class="story" ng-class="{ 'first': $first }">
								<div class="tr">
									<span class="leftLabel">Name</span>
									<div>{{story.name}}</div>
								</div>
								<div class="tr">
									<span class="leftLabel">Goal</span>
									<div>{{story.goal}}</div>
								</div>
								<div class="tr">
									<span class="leftLabel">Reward</span>
									<div>{{story.reward}}</div>
								</div>
								<div class="tr steps">
									<span class="leftLabel">Steps</span>
									<div ng-bind-html="story.steps | trustHTML"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="column floatRight">
					<div id="traits">
						<h2 class="headerbar hbDark" skew-element>Traits</h2>
						<div hb-margined="dark">
							<div ng-repeat="(trait, rank) in character.traits" class="tr traits">
								<label class="leftLabel">{{trait.capitalizeFirstLetter()}}</label>
								<div class="ranks">
									<span ng-repeat="count in range(1, 5)" class="rankWrapper" ng-class="{ 'selected': count <= rank }"></span>
								</div>
							</div>
						</div>
					</div>
					<div id="skills">
						<h2 class="headerbar hbDark" skew-element>Skills</h2>
						<div hb-margined="dark">
							<div ng-repeat="(skill, rank) in character.skills" class="tr skills">
								<label class="leftLabel">{{skill.capitalizeFirstLetter()}}</label>
								<div class="ranks">
									<span ng-repeat="count in range(1, 5)" class="rankWrapper" ng-class="{ 'selected': count <= rank }"></span>
								</div>
							</div>
						</div>
					</div>
					<div id="deathSpiral">
						<h2 class="headerbar hbDark" skew-element>Death Spiral</h2>
						<div hb-margined="dark">
							<ol>
								<li ng-class="{ inactive: !character.dramaticWounds[1] }">+1 Bonus Die to all Risks</li>
								<li ng-class="{ inactive: !character.dramaticWounds[2] }">Villains gain +2 Bonus Dice</li>
								<li ng-class="{ inactive: !character.dramaticWounds[3] }">Your 10s explode (+1 die)</li>
								<li ng-class="{ inactive: !character.dramaticWounds[4] }">You become Helpless</li>
							</ol>
							<div id="deathSpiralImg">
								<img src="/images/characters/7thsea_2e/deathspiral.jpg">
								<img ng-if="character.deathSpiral != 0" ng-repeat="count in range(1, character.deathSpiral)" ng-attr-id="{{'cross_' + count}}" src="/images/characters/7thsea_2e/cross.png" class="cross">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="notes">
				<h2 class="headerbar hbDark" skew-element>Notes</h2>
				<div hb-margined="dark"><span ng-bind-html="character.notes | trustHTML"></span></div>
			</div>
