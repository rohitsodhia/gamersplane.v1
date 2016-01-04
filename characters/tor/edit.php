				<div class="clearFix">
					<div id="charInfo" class="floatLeft">
						<div class="tr labelTR">
							<label for="name" class="medText lrBuffer shiftRight font-kelt">Name</label>
						</div>
						<div class="tr">
							<input id="name" type="text" maxlength="50" ng-model="character.name" class="medText lrBuffer">
							<label id="sol" class="leftLabel lrBuffer">
								<div class="labelText font-kelt">Standard of Living</div>
								<input type="text" ng-model="character.sol">
							</label>
						</div>
						<div class="tr labelTR">
							<label for="metatype" class="medText lrBuffer shiftRight font-kelt">Culture</label>
							<label for="calling" class="medText lrBuffer shiftRight font-kelt">Calling</label>
							<label for="shadow_weakness" class="medText lrBuffer shiftRight font-kelt">Shadow Weakness</label>
						</div>
						<div class="tr">
							<input id="metatype" type="text" maxlength="50" ng-model="character.culture.name" class="medText lrBuffer">
							<input id="calling" type="text" maxlength="50" ng-model="character.calling" class="medText lrBuffer">
							<input id="shadow_weakness" type="text" maxlength="50" ng-model="character.shadow_weakness" class="medText lrBuffer">
						</div>
						<div class="clearfix">
							<div class="column">
								<div class="tr labelTR">
									<label for="cultural_blessing" class="medText lrBuffer shiftRight font-kelt">Cultural Blessing</label>
								</div>
								<div class="tr">
									<textarea id="cultural_blessing" ng-model="character.culture.blessing" class="medText lrBuffer"></textarea>
								</div>
							</div>
							<div class="column">
								<div class="tr labelTR">
									<label for="specialties" class="medText lrBuffer shiftRight font-kelt">Specialties</label>
								</div>
								<div class="tr">
									<textarea id="specialties" ng-model="character.specialties" class="medText lrBuffer"></textarea>
								</div>
							</div>
							<div class="column">
								<div class="tr labelTR">
									<label for="features" class="medText lrBuffer shiftRight font-kelt">Distinctive Features</label>
								</div>
								<div class="tr">
									<textarea id="features" ng-model="character.features" class="medText lrBuffer"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div id="topStats" class="floatLeft">
						<div>
							<div id="experience" class="alignCenter">
								<h2>Experience</h2>
								<div class="cSprite statBT">
									<input id="exp_spent" type="number" ng-model="character.experience.spent" min="0" class="central">
									<span>Total</span>
									<input id="exp_total" type="number" ng-model="character.experience.total" min="0" class="bubble">
								</div>
							</div>
							<div>
								<div id="valor" class="alignCenter">
									<h2>Valor</h2>
									<div class="cSprite stat">
										<input type="number" ng-model="character.valor" min="0">
									</div>
								</div>
								<div id="wisdom" class="alignCenter">
									<h2>Wisdom</h2>
									<div class="cSprite stat">
										<input type="number" ng-model="character.wisdom" min="0">
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
								<input type="number" ng-model="attribute.standard" min="0" class="standard">
								<input type="number" ng-model="attribute.favoured" min="0" class="favoured">
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
								<a href="" ng-click="setSkill(skill, 0)" class="unset"><span ng-class="{ 'hide': character.skills[skill] == 0 }"></span></a>
								<span ng-repeat="rank in [1, 2, 3, 4, 5]" ng-click="setSkill(skill, rank)" class="cSprite skillRank" ng-class="{ 'selected': character.skills[skill] >= rank }"></span>
							</li>
						</ul>
					</div>
					<div id="skillGroups" class="floatLeft">
						<h2>- Skill Groups -</h2>
						<ul class="skillSet">
							<li ng-repeat="skill in skillGroups" class="skill">
								<span class="name font-kelt">{{skill}}</span>
								<a href="" ng-click="setSkillGroup(skill, 0)" class="unset"><span ng-class="{ 'hide': character.skillGroups[skill] == 0 }"></span></a>
								<span ng-repeat="rank in [1, 2, 3]" ng-click="setSkillGroup(skill, rank)" class="cSprite skillGroupRank" ng-class="{ 'selected': character.skillGroups[skill] >= rank }"></span>
							</li>
						</ul>
					</div>
				</div>
				<div class="clearfix">
					<div id="combatStats">
						<div class="alignCenter">
							<h2>Damage</h2>
							<div class="cSprite statBB">
								<input id="damage" type="number" ng-model="character.combat.damage" min="0" class="central">
								<span>Ranged</span>
								<input id="ranged" type="number" ng-model="character.combat.ranged" min="0" class="bubble">
							</div>
						</div>
						<div class="alignCenter">
							<h2>Parry</h2>
							<div class="cSprite statBB">
								<input id="parry" type="number" ng-model="character.combat.parry" min="0" class="central">
								<span>Shield</span>
								<input id="shield" type="number" ng-model="character.combat.shield" min="0" class="bubble">
							</div>
						</div>
						<div class="alignCenter">
							<h2>Armor</h2>
							<div class="cSprite statBB">
								<input id="armor" type="number" ng-model="character.combat.armor" min="0" class="central">
								<span>Head</span>
								<input id="head" type="number" ng-model="character.combat.head" min="0" class="bubble">
							</div>
						</div>
					</div>
					<div id="weaponSkills">
						<h2>- Weapon Skills - <a href="" ng-click="addItem('weaponSkills')">[ Add Weapon Skill ]</a></h2>
						<ul class="skillSet">
							<li ng-repeat="weapon in character.weaponSkills" class="skill">
								<input type="text" ng-model="weapon.name" class="medText" placeholder="Weapon Type">
								<a href="" ng-click="setWeaponSkill(weapon, 0)" class="unset"><span ng-class="{ 'hide': weapon.rank == 0 }"></span></a>
								<span ng-repeat="rank in [1, 2, 3, 4, 5]" ng-click="setWeaponSkill(weapon, rank)" class="cSprite skillRank" ng-class="{ 'selected': weapon.rank >= rank }"></span>
							</li>
						</ul>
					</div>
					<div id="weapons">
						<h2>- Weapons - <a href="" ng-click="addItem('weapons')">[ Add Weapon ]</a></h2>
						<ul>
							<li ng-repeat="weapon in character.weapons">
								<input type="text" ng-model="weapon.name" class="medText" placeholder="Weapon">
								<div ng-repeat="stat in ['damage', 'edge', 'injury', 'enc']">
									<span>{{stat}}</span>
									<input type="text" ng-model="weapon[stat]">
								</div>
							</li>
						</ul>
					</div>
					<div id="gear">
						<h2>- Gear - <a href="" ng-click="addItem('gear')">[ Add Gear ]</a></h2>
						<ul class="hasNotesLinks">
							<li>
								<div class="medText">&nbsp;</div>
								<span class="font-kelt">enc</span>
							</li>
							<li>
								<input type="text" ng-model="character.mainGear.armour.name" class="medText" placeholder="Armour">
								<input type="text" ng-model="character.mainGear.armour.enc">
								<a href="" class="notesLink">[ Notes ]</a>
								<textarea ng-model="character.mainGear.armour.notes" class="notes"></textarea>
							</li>
							<li>
								<input type="text" ng-model="character.mainGear.headgear.name" class="medText" placeholder="Headgear">
								<input type="text" ng-model="character.mainGear.headgear.enc">
								<a href="" class="notesLink">[ Notes ]</a>
								<textarea ng-model="character.mainGear.headgear.notes" class="notes"></textarea>
							</li>
							<li>
								<input type="text" ng-model="character.mainGear.shield.name" class="medText" placeholder="Shield">
								<input type="text" ng-model="character.mainGear.shield.enc">
								<a href="" class="notesLink">[ Notes ]</a>
								<textarea ng-model="character.mainGear.shield.notes" class="notes"></textarea>
							</li>
							<li ng-repeat="gear in character.gear">
								<input type="text" ng-model="gear.name" class="medText" placeholder="Gear">
								<input type="text" ng-model="gear.enc">
								<a href="" class="notesLink">[ Notes ]</a>
								<textarea ng-model="gear.notes" class="notes"></textarea>
							</li>
						</ul>
					</div>
					<div id="hp">
						<div id="endurance" class="alignCenter">
							<h2>Endurance</h2>
							<div class="cSprite statTB">
								<input type="number" ng-model="character.hp.endurance.current" min="0" class="central">
								<span class="top">Rating</span>
								<input type="number" ng-model="character.hp.endurance.rating" min="0" class="top">
								<span class="bottom">Fatigue</span>
								<input type="number" ng-model="character.hp.endurance.fatigue" min="0" class="bottom">
							</div>
						</div>
						<div id="hope" class="alignCenter">
							<h2>Hope</h2>
							<div class="cSprite statTB">
								<input type="number" ng-model="character.hp.hope.current" min="0" class="central">
								<span class="top">Rating</span>
								<input type="number" ng-model="character.hp.hope.rating" min="0" class="top">
								<span class="bottom">Shadow</span>
								<input type="number" ng-model="character.hp.hope.shadow" min="0" class="bottom">
							</div>
						</div>
						<div id="status">
							<div ng-repeat="status in ['weary', 'miserable', 'wounded']">
								<div ng-click="toggleStatus(status)" class="cSprite oval" ng-class="{ 'selected': character.status[status] }"></div>
								<span class="font-kelt">{{status.capitalizeFirstLetter()}}</span>
							</div>
						</div>
					</div>
					<div id="rewards">
						<h2>- Rewards -</h2>
						<textarea ng-model="character.rewards"></textarea>
					</div>
					<div id="virtues">
						<h2>- Virtues -</h2>
						<textarea ng-model="character.virtues"></textarea>
					</div>
				</div>
				<div id="bigStats">
					<span ng-repeat="stat in ['fellowship', 'advancement', 'treasure', 'standing']">
						<h2>{{stat.capitalizeFirstLetter()}}</h2>
						<div id="{{stat}}" class="cSprite fellowship">
							<input type="number" ng-model="character[stat]" min="0">
						</div>
					</span>
				</div>
				<div id="notes">
					<h2>- Notes -</h2>
					<textarea id="notes" ng-model="character.notes" class="hbdMargined"></textarea>
				</div>
