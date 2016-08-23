				<div id="name_class" class="tr">
					<span class="input">
						<input type="text" ng-model="character.name">
						<label>Name</label>
					</span>
					<span class="text"> is a </span>
					<span class="input">
						<input type="text" ng-model="character.descriptor">
						<label>Descriptor</label>
					</span>
					<span id="class_type" class="input">
						<input type="text" ng-model="character.type">
						<label>Type</label>
					</span>
					<span class="text"> who </span>
					<span id="class_focus" class="input">
						<input type="text" ng-model="character.focus">
						<label>Focus</label>
					</span>
				</div>

				<div class="clearfix">
					<div id="stats" class="floatLeft">
						<div class="clearfix">
							<div id="statCol" class="floatLeft">
								<div ng-repeat="(key, display) in labels.stats" class="tr">
									<label class="leftLabel">{{display}}</label>
									<input type="number" ng-model="character[key]" min="0" step="1" class="shortNum">
								</div>
							</div>
							<div class="floatLeft">
								<div class="tr labelTR attribute">
									<div class="shortText spacer"></div>
									<div class="pool">
										<label id="poolLabel">Pool</label>
										<label class="shortNum">Current</label> / <label class="shortNum">Max</label>
									</div>
									<label class="shortNum lrBuffer alignBottom">Edge</label>
								</div>
								<div ng-repeat="attr in labels.attributes" class="tr attribute">
									<div class="shortText">{{attr.capitalizeFirstLetter()}}</div>
									<div class="pool">
										<input type="number" ng-model="character.attributes[attr].pool.current" min="0" step="1" class="shortNum"> / <input type="number" ng-model="character.attributes[attr].pool.max" min="0" step="1" class="shortNum">
									</div>
									<input type="number" ng-model="character.attributes[attr].edge" min="0" step="1" class="shortNum lrBuffer">
								</div>
							</div>
						</div>
						<div class="clearfix">
							<div id="damage" class="floatLeft">
								<div class="alignCenter header">Damage</div>
								<div class="tr damage">
									<pretty-checkbox checkbox="character.damage.impaired" eleID="damage_impaired"></pretty-checkbox>
									<label for="damage_impaired" class="leftLabel">Impaired</label>
								</div>
								<div class="tr damage">
									<pretty-checkbox checkbox="character.damage.debilitated" eleID="damage_debilitated"></pretty-checkbox>
									<label for="damage_debilitated" class="leftLabel">Debilitated</label>
								</div>
								<div id="armor" class="tr">
									<label class="leftLabel width3">Armor</label>
									<input type="number" ng-model="character.armor" min="0" step="1" class="width1">
								</div>
							</div>
							<div id="recovery" class="floatLeft clearfix">
								<div class="alignCenter header">Recovery</div>
								<div class="tr floatLeft">
									<label class="leftLabel width1">1d6+</label>
									<input type="number" ng-model="character.recovery" min="0" step="1">
								</div>
								<div id="recoveryTimes" class="floatLeft">
									<div ng-repeat="(recovery, label) in labels.recoveries" class="tr">
										<pretty-checkbox checkbox="character.recoveryTimes[recovery]" eleID="recovery_{{recovery}}"></pretty-checkbox> <label for="recovery_{{recovery}}" class="leftLabel">{{label}}</label>
									</div>
								</div>
							</div>
						</div>
						<div id="attacks">
							<h2 class="headerbar hbDark">Attacks <a ng-click="addItem('attacks')" href="">[ Add Attack ]</a></h2>
							<div class="hbdMargined">
								<div class="tr labelTR">
									<label class="name shiftRight borderBox">Attack</label>
									<label class="mod shortNum alignCenter lrBuffer">Mod</label>
									<label class="dmg shortNum alignCenter">Dmg</label>
								</div>
								<div id="attackList">
									<div ng-repeat="attack in character.attacks" class="attack tr">
										<input type="text" ng-model="attack.name" class="name medText">
										<input type="number" ng-model="attack.mod" step="1" class="mod shortNum lrBuffer">
										<input type="number" ng-model="attack.dmg" step="1" class="dmg shortNum">
										<a ng-click="character.attacks.splice($index, 1)" href="" class="remove sprite cross lrBuffer"></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="skills" class="floatLeft nonDefault">
						<h2 class="headerbar hbDark">Skills <a ng-click="addItem('skills')" href="">[ Add Skill ]</a></h2>
						<div class="hbdMargined">
							<div class="tr labelTR">
								<label class="name shiftRight borderBox">Skill</label>
								<label class="shortNum alignCenter">Attr</label>
								<label class="shortNum alignCenter">Prof?</label>
							</div>
							<div id="skillList">
								<div ng-repeat="skill in character.skills" class="skill clearfix">
									<input type="text" ng-model="skill.name" class="name alignLeft placeholder" ng-placeholder="Skill Name">
									<div class="attr alignCenter shortNum"><div ng-click="cycleValues('attr', skill, 'attrs')">{{skill.attr == ''?'&nbsp;':skill.attr}}</div></div>
									<div class="prof alignCenter shortNum"><div ng-click="cycleValues('prof', skill, 'profs')">{{skill.prof == ''?'&nbsp;':skill.prof}}</div></div>
									<a ng-click="character.skills.splice($index, 1)" href="" class="remove sprite cross lrBuffer"></a>
								</div>
							</div>
						</div>
					</div>
					<div id="specialAbilities" class="floatLeft nonDefault">
						<h2 class="headerbar hbDark">Special Abilities <a id="addSpecialAbility" ng-click="addItem('specialAbilities')" href="">[ Add Special Ability ]</a></h2>
						<div class="hbdMargined">
							<div id="specialAbilityList">
								<div ng-repeat="specialAbility in character.specialAbilities" class="specialAbility clearfix">
									<input type="text" ng-model="specialAbility.name" class="name placeholder" ng-placeholder="Ability Name">
									<a ng-click="toggleNotes($event)" href="" class="notesLink">Notes</a>
									<a ng-click="character.specialAbilities.splice($index, 1)" href="" class="remove sprite cross"></a>
									<textarea ng-model="specialAbility.notes"></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="clearfix">
					<div id="cyphers" class="floatLeft">
						<h2 class="headerbar hbDark">Cyphers <a ng-click="addItem('cyphers')" href="">[ Add Cypher ]</a></h2>
						<div class="hbdMargined">
							<div id="cypherList">
								<div ng-repeat="cypher in character.cyphers" class="cypher clearfix">
									<input type="text" ng-model="cypher.name" class="name placeholder" ng-placeholder="Cypher Name">
									<a ng-click="toggleNotes($event)" href="" class="notesLink">Notes</a>
									<a ng-click="character.cyphers.splice($index, 1)" href="" class="remove sprite cross"></a>
									<textarea ng-model="cypher.notes"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div id="posessions" class="floatRight">
						<h2 class="headerbar hbDark">Possessions</h2>
						<textarea ng-model="character.possessions" class="hbdMargined"></textarea>
					</div>
				</div>

				<div id="notes">
					<h2 class="headerbar hbDark">Notes</h2>
					<textarea ng-model="character.notes" class="hbdMargined"></textarea>
				</div>
