				<div class="tr labelTR">
					<label for="name" class="medText lrBuffer shiftRight">Name</label>
					<label for="occupation" class="medText lrBuffer shiftRight">Occupation</label>
				</div>
				<div class="tr">
					<input id="name" type="text" maxlength="50" ng-model="character.name" class="medText lrBuffer">
					<input id="occupation" type="text" maxlength="50" ng-model="character.occupation" class="medText lrBuffer">
				</div>

				<div class="clearfix">
					<div id="characteristics" class="floatLeft">
						<h2 class="headerbar hbDark">Characteristics</h2>
						<ul hb-margined>
							<li ng-repeat="(label, characteristic) in character.characteristics" class="tr" ng-class="{ 'third': $index % 3 == 2 }">
								<label>
									<div class="labelText">{{label.toUpperCase()}}</div>
									<div>
										<input type="number" ng-model="character.characteristics[label]" min="0" ng-class="{ 'move': label == 'move' }"
										><div ng-if="label != 'move'" class="textVals">
											<div class="half">{{getHalfValue(character.characteristics[label])}}</div>
											<div class="fifth">{{getFifthValue(character.characteristics[label])}}</div>
										</div>
									</div>
								</label>
							</li>
						</ul>
					</div>
					<div id="stats" class="floatLeft">
						<h2 class="headerbar hbDark">Stats</h2>
						<div hb-margined>
							<div ng-repeat="stat in labels.stats" ng-id="stat.key" class="tr stat">
								<h3>{{stat.value}}</h3>
								<label class="leftLabel first">Current <input type="number" ng-model="character[stat.key].current" min="0"></label>
								<label class="leftLabel">Max <input type="number" ng-model="character[stat.key].max" min="0"></label>
								<div ng-if="stat.key == 'hp' || stat.key == 'sanity'" class="tr">
									<label ng-if="stat.key == 'hp'">
										<pretty-checkbox checkbox="character.hp.major"></pretty-checkbox> Major Wound
									</label>
									<label ng-if="stat.key == 'sanity'">
										<pretty-checkbox checkbox="character.sanity.temp"></pretty-checkbox> Temp Insane
									</label>
									<label ng-if="stat.key == 'sanity'">
										<pretty-checkbox checkbox="character.sanity.indef"></pretty-checkbox> Indef Insane
									</label>
								</div>
							</div>
							<div ng-id="stat.key" class="tr stat">
								<h3><label for="luck" class="leftLabel">Luck</label></h3>
								<input id="luck" type="number" ng-model="character.luck" min="0"></label>
							</div>
						</div>
					</div>
				</div>
				<div id="skills" class="clearfix">
					<h2 class="headerbar hbDark">Skills <a href="" ng-click="addSkill()">[ Add Skill ]</a></h2>
					<div hb-margined>
						<ul ng-repeat="column in range(0, numCols - 1)" ng-class="{ 'first': $first }">
							<li ng-repeat="skill in character.skills" ng-if="$index % 3 == column" class="tr skill">
								<combobox data="skill.search" search="skill.name" change="changeSkillName(skill, value)" placeholder="Skill" class="name lrBuffer"></combobox>
								<input type="number" min="0" ng-model="skill.value" class="value">
								<a href="" class="remove sprite cross" ng-click="removeSkill(skill)"></a>
							</li>
						</ul>
					</div>
				</div>
				<div class="clearfix">
					<div id="weapons" class="floatLeft">
						<h2 class="headerbar hbDark">Weapons <a href="" ng-click="addItem('weapons')">[ Add Weapon ]</a></h2>
						<div hb-margined>
							<div ng-repeat="weapon in character.weapons" class="weapon" ng-class="{ 'first': $first }">
								<div class="labelTR">
									<label class="medText name lrBuffer">Weapon</label>
									<label class="shortNum lrBuffer">Regular</label>
									<label class="shortNum lrBuffer">Hard</label>
									<label class="shortNum lrBuffer">Extreme</label>
									<label class="shortText lrBuffer">Damage</label>
									<label class="shortNum lrBuffer">Range</label>
									<label class="shortNum lrBuffer">Attacks</label>
									<label class="shortNum lrBuffer">Ammo</label>
									<label class="shortNum lrBuffer">Malf</label>
								</div>
								<div class="tr">
									<input type="text" ng-model="weapon.name" class="medText name lrBuffer">
									<input type="text" ng-model="weapon.regular" class="shortNum lrBuffer">
									<input type="text" ng-model="weapon.hard" class="shortNum lrBuffer">
									<input type="text" ng-model="weapon.extreme" class="shortNum lrBuffer">
									<input type="text" ng-model="weapon.damage" class="shortText lrBuffer">
									<input type="text" ng-model="weapon.range" class="shortNum lrBuffer">
									<input type="text" ng-model="weapon.attacks" class="shortNum lrBuffer">
									<input type="text" ng-model="weapon.ammo" class="shortNum lrBuffer">
									<input type="text" ng-model="weapon.malf" class="shortNum lrBuffer">
								</div>
								<div class="labelTR lrBuffer">
									<label class="shiftRight">Notes</label>
								</div>
								<div class="tr lrBuffer">
									<textarea ng-model="weapon.notes"></textarea>
								</div>
								<div class="tr lrBuffer alignRight"><a href="" ng-click="character.weapons.splice($index, 1)" class="remove">[ Remove ]</a></div>
							</div>
						</div>
					</div>
					<div id="combat" class="floatRight">
						<h2 class="headerbar hbDark">Combat</h2>
						<div hb-margined>
							<div class="tr">
								<h3>Damage Bonus</h3>
								<div>{{computeDamage_Build(character.characteristics.str + character.characteristics.siz)[0]}}</div>
							</div>
							<div class="tr">
								<h3>Build</h3>
								<div>{{computeDamage_Build(character.characteristics.str + character.characteristics.siz)[1]}}</div>
							</div>
							<div id="dodge" class="tr">
								<h3>Dodge</h3>
								<div>{{character.dodge}}</div>
							</div>
						</div>
					</div>
				</div>
				<div id="items">
					<h2 class="headerbar hbDark">Items <a href="" ng-click="addItem('items')">[ Add Item ]</a></h2>
					<div class="clearfix" hb-margined>
						<div ng-repeat="items in character.items" class="item tr">
							<input type="text" ng-model="items.name" class="name">
							<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
							<a href="" class="remove sprite cross" ng-click="character.items.splice($index, 1)"></a>
							<textarea ng-model="items.notes"></textarea>
						</div>
					</div>
				</div>
				<div id="notes">
					<h2 class="headerbar hbDark">Background/Notes</h2>
					<textarea id="notes" ng-model="character.notes" class="hbdMargined"></textarea>
				</div>
