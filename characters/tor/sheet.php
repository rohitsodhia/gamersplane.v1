			<div class="clearFix">
				<div id="charInfo" class="floatLeft">
					<div class="tr labelTR">
						<label class="medText lrBuffer shiftRight font-kelt">Name</label>
					</div>
					<div class="tr">
						<span id="name" class="medText lrBuffer">{{character.name}}</span>
						<label id="sol" class="leftLabel lrBuffer">
							<div class="labelText font-kelt">Standard of Living</div>
							<span ng-bind-html="character.sol"></span>
						</label>
					</div>
					<div class="tr labelTR">
						<label for="metatype" class="medText lrBuffer shiftRight font-kelt">Culture</label>
						<label for="calling" class="medText lrBuffer shiftRight font-kelt">Calling</label>
						<label for="shadow_weakness" class="medText lrBuffer shiftRight font-kelt">Shadow Weakness</label>
					</div>
					<div class="tr">
						<span ng-bind-html="character.culture.name" class="medText lrBuffer"></span>
						<span ng-bind-html="character.calling" class="medText lrBuffer"></span>
						<span ng-bind-html="character.shadow_weakness" class="medText lrBuffer"></span>
					</div>
					<div class="clearfix">
						<div class="column">
							<div class="tr labelTR">
								<label for="cultural_blessing" class="medText lrBuffer shiftRight font-kelt">Cultural Blessing</label>
							</div>
							<div class="tr">
								<div id="cultural_blessing" ng-bind-html="character.culture.blessing" class="medText lrBuffer"></div>
							</div>
						</div>
						<div class="column">
							<div class="tr labelTR">
								<label for="specialties" class="medText lrBuffer shiftRight font-kelt">Specialties</label>
							</div>
							<div class="tr">
								<div id="specialties" ng-bind-html="character.specialties" class="medText lrBuffer"></div>
							</div>
						</div>
						<div class="column">
							<div class="tr labelTR">
								<label for="features" class="medText lrBuffer shiftRight font-kelt">Distinctive Features</label>
							</div>
							<div class="tr">
								<div id="features" ng-bind-html="character.features" class="medText lrBuffer"></div>
							</div>
						</div>
					</div>
				</div>
				<div id="topStats" class="floatLeft">
					<div>
						<div id="experience" class="alignCenter">
							<h2>Experience</h2>
							<div class="cSprite statBT">
								<span ng-bind-html="character.experience.spent" class="central"></span>
								<span>Total</span>
								<span ng-bind-html="character.experience.total" class="bubble"></span>
							</div>
						</div>
						<div>
							<div id="valor" class="alignCenter">
								<h2>Valor</h2>
								<div class="cSprite stat">
									<span ng-bind-html="character.valor"></span>
								</div>
							</div>
							<div id="wisdom" class="alignCenter">
								<h2>Wisdom</h2>
								<div class="cSprite stat">
									<span ng-bind-html="character.wisdom"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div id="attributes" class="column">
					<h2>- Attributes -</h2>
					<div ng-repeat="(aLabel, attribute) in character.attributes" class="attribute" ng-class="{ 'first': $first }">
						<label class="font-kelt">{{aLabel.capitalizeFirstLetter()}}</label>
						<div class="cSprite attribute">
							<span ng-bind-html="attribute.standard" class="standard"></span>
							<span ng-bind-html="attribute.favoured" class="favoured"></span>
						</div>
						<span class="font-kelt favouredLabel">Favoured</span>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<div id="commonSkills" class="floatLeft">
					<h2>- Common Skills -</h2>
					<ul ng-repeat="set in skills" class="skillSet" ng-class="{ 'first': $first }">
						<li ng-repeat="skill in set" class="skill">
							<span class="name font-kelt">{{skill}}</span>
							<span ng-repeat="rank in [1, 2, 3, 4, 5]" class="cSprite skillRank" ng-class="{ 'selected': character.skills[skill] >= rank }"></span>
						</li>
					</ul>
				</div>
				<div id="skillGroups" class="floatLeft">
					<h2>- Skill Groups -</h2>
					<ul class="skillSet">
						<li ng-repeat="skill in skillGroups" class="skill">
							<span class="name font-kelt">{{skill}}</span>
							<span ng-repeat="rank in [1, 2, 3]" class="cSprite skillGroupRank" ng-class="{ 'selected': character.skillGroups[skill] >= rank }"></span>
						</li>
					</ul>
				</div>
			</div>
			<div class="clearfix">
				<div id="combatStats">
					<div class="alignCenter">
						<h2>Damage</h2>
						<div class="cSprite statBB">
							<span ng-bind-html="character.combat.damage" class="central"></span>
							<span>Ranged</span>
							<span ng-bind-html="character.combat.ranged" class="bubble"></span>
						</div>
					</div>
					<div class="alignCenter">
						<h2>Parry</h2>
						<div class="cSprite statBB">
							<span ng-bind-html="character.combat.parry" class="central"></span>
							<span>Shield</span>
							<span ng-bind-html="character.combat.shield" class="bubble"></span>
						</div>
					</div>
					<div class="alignCenter">
						<h2>Armor</h2>
						<div class="cSprite statBB">
							<span ng-bind-html="character.combat.armor" class="central"></span>
							<span>Head</span>
							<span ng-bind-html="character.combat.head" class="bubble"></span>
						</div>
					</div>
				</div>
				<div id="weaponSkills">
					<h2>- Weapon Skills -</h2>
					<ul class="skillSet">
						<li ng-repeat="weapon in character.weaponSkills" class="skill">
							<span ng-bind-html="weapon.name" class="medText" placeholder="Weapon Type"></span>
							<span ng-repeat="rank in [1, 2, 3, 4, 5]" class="cSprite skillRank" ng-class="{ 'selected': weapon.rank >= rank }"></span>
						</li>
					</ul>
				</div>
				<div id="weapons">
					<h2>- Weapons -</h2>
					<ul>
						<li ng-repeat="weapon in character.weapons">
							<span ng-bind-html="weapon.name" class="medText" placeholder="Weapon"></span>
							<div ng-repeat="stat in ['damage', 'edge', 'injury', 'enc']">
								<span>{{stat}}</span>
								<span ng-bind-html="weapon[stat]"></span>
							</div>
						</li>
					</ul>
				</div>
				<div id="gear">
					<h2>- Gear -</h2>
					<ul class="hasNotesLinks">
						<li>
							<div class="medText">&nbsp;</div>
							<span class="font-kelt">enc</span>
						</li>
						<li ng-repeat="gear in character.gear">
							<span ng-bind-html="gear.name" class="medText" placeholder="Gear"></span>
							<span ng-bind-html="gear.enc"></span>
							<a href="" class="notesLink">[ Notes ]</a>
							<div ng-bind-html="gear.notes" class="notes"></div>
						</li>
					</ul>
				</div>
				<div id="hp">
					<div id="endurance" class="alignCenter">
						<h2>Endurance</h2>
						<div class="cSprite statTB">
							<span ng-bind-html="character.hp.endurance.current" class="central"></span>
							<span class="top">Rating</span>
							<span ng-bind-html="character.hp.endurance.rating" class="top"></span>
							<span class="bottom">Fatigue</span>
							<span ng-bind-html="character.hp.endurance.fatigue" class="bottom"></span>
						</div>
					</div>
					<div id="hope" class="alignCenter">
						<h2>Hope</h2>
						<div class="cSprite statTB">
							<span ng-bind-html="character.hp.hope.current" class="central"></span>
							<span class="top">Rating</span>
							<span ng-bind-html="character.hp.hope.rating" class="top"></span>
							<span class="bottom">Shadow</span>
							<span ng-bind-html="character.hp.hope.shadow" class="bottom"></span>
						</div>
					</div>
					<div id="status">
						<div ng-repeat="status in ['weary', 'miserable', 'wounded']">
							<div class="cSprite oval" ng-class="{ 'selected': character.status[status] }"></div>
							<span class="font-kelt">{{status.capitalizeFirstLetter()}}</span>
						</div>
					</div>
				</div>
				<div id="rewards">
					<h2>- Rewards -</h2>
					<div ng-bind-html="character.rewards"></div>
				</div>
				<div id="virtues">
					<h2>- Virtues -</h2>
					<div ng-bind-html="character.virtues"></div>
				</div>
			</div>
			<div id="bigStats">
				<span ng-repeat="stat in ['fellowship', 'advancement', 'treasure', 'standing']">
					<h2>{{stat.capitalizeFirstLetter()}}</h2>
					<div id="{{stat}}" class="cSprite fellowship">
						<span ng-bind-html="character[stat]"></span>
					</div>
				</span>
			</div>
			<div id="notes">
				<h2>- Notes -</h2>
				<div id="notes" ng-bind-html="character.notes" class="hbdMargined"></div>
			</div>
