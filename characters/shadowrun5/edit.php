				<div class="tr labelTR">
					<label for="name" class="medText lrBuffer shiftRight">Name</label>
					<label for="metatype" class="medText lrBuffer shiftRight">Metatype</label>
				</div>
				<div class="tr">
					<input id="name" type="text" maxlength="50" ng-model="character.name" class="medText lrBuffer">
					<input id="metatype" type="text" maxlength="50" ng-model="character.metatype" class="medText lrBuffer">
				</div>
				<div class="tr">
					<label ng-repeat="(key, rep) in character.reputation" class="leftLabel lrBuffer">
						<div class="labelText">{{labels.rep[key]}}</div>
						<input type="text" ng-model="rep">
					</label>
				</div>
				<div class="tr">
					<label class="leftLabel lrBuffer">
						<div class="labelText">Spent Karma</div>
						<input type="text" ng-model="character.karma.spent">
					</label>
					<label class="leftLabel lrBuffer">
						<div class="labelText">Total Karma</div>
						<input type="text" ng-model="character.karma.total">
					</label>
				</div>

				<div class="clearfix">
					<div id="stats" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Stats</h2>
						<div hb-margined>
							<ul ng-repeat="column in [0, 1]" ng-class="{ 'first': $first }">
								<li ng-repeat="label in labels.stats | limitTo: (labels.stats.length / 2):(column * labels.stats.length / 2)" class="tr">
									<label class="leftLabel">
										<div class="labelText">{{label.value}}</div>
										<input type="number" ng-model="character.stats[label.key]" min="0" ng-change="character.stats[label.key] = character.stats[label.key] >= 0?character.stats[label.key]:0">
									</label>
								</li>
							</ul>
						</div>
					</div>
					<div id="limits" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Limits</h2>
						<div hb-margined>
							<div ng-repeat="(label, limit) in character.limits" class="tr">
								<label class="leftLabel">
									<div class="labelText">{{label.capitalizeFirstLetter()}}</div>
									<input type="number" ng-model="limit">
								</label>
							</div>
						</div>
					</div>
					<div id="damage" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Damage Tracks</h2>
						<div hb-margined>
							<div class="clearfix">
								<div ng-repeat="(track, stat) in { 'physical': 'body', 'stun': 'willpower' }" id="{{track}}Track" class="damageType floatLeft">
									<h3>{{track.capitalizeFirstLetter()}}</h3>
									<div class="modify">+/- boxes: <input type="number" ng-model="character.damage[track].modify"></div>
									<a href="" class="clear" ng-click="character.damage[track].current = 0">[ Clear ]</a>
									<div class="track clearfix">
										<div ng-repeat="box in range(1, 8 + (character.stats[stat] / 2 | ceil) + character.damage[track].modify)" class="damageCell" ng-class="{ 'first': $first, 'filled': character.damage[track].current > $index }" ng-click="character.damage[track].current = $index + 1"></div>
									</div>
								</div>
							</div>
							<label id="overflow" class="leftLabel">
								<div class="labelText">Overflow</div>
								<input type="number" ng-model="character.damage.physical.overflow">
							</label>
						</div>
					</div>
				</div>

				<div class="clearfix">
					<div id="skills" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Skills <a href="" ng-click="addItem('skills')">[ Add Skill ]</a></h2>
						<div hb-margined>
							<div ng-repeat="skill in character.skills" class="skill tr">
								<div ng-click="skill.type = skill.type == 'a'?'k':'a'" class="type">{{skill.type.toUpperCase()}}</div>
								<combobox value="skill.name" autocomplete="searchSkills" placeholder="Skill" class="name lrBuffer"></combobox>
								<input type="text" ng-model="skill.rating" class="rating">
								<a href="" class="remove sprite cross" ng-click="character.skills.splice($index, 1)"></a>
							</div>
						</div>
					</div>
					<div class="floatRight">
						<div id="qualities">
							<h2 class="headerbar hbDark" skew-element>Qualities <a href="" ng-click="addItem('qualities')">[ Add Quality ]</a></h2>
							<div hb-margined>
								<div ng-repeat="quality in character.qualities" class="quality tr">
									<div ng-click="quality.type = quality.type == 'p'?'n':'p'" class="type">{{quality.type.toUpperCase()}}</div>
									<input type="text" ng-model="quality.name" placeholder="Quality" class="name lrBuffer">
									<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
									<a href="" class="remove sprite cross" ng-click="character.qualities.splice($index, 1)"></a>
									<textarea ng-model="quality.notes"></textarea>
								</div>
							</div>
						</div>
						<div id="contacts">
							<h2 class="headerbar hbDark" skew-element>Contacts <a href="" ng-click="addItem('contacts')">[ Add Contact ]</a></h2>
							<div hb-margined>
								<div class="labelTR">
									<label class="name"></label>
									<label class="shortNum lrBuffer">Loyalty</label>
									<label class="connection shortNum lrBuffer">Connection</label>
								</div>
								<div ng-repeat="contact in character.contacts" class="contact tr">
									<input type="text" ng-model="contact.name" placeholder="Contact" class="name">
									<input type="number" ng-model="contact.loyalty" class="loyalty lrBuffer">
									<input type="number" ng-model="contact.connection" class="connection lrBuffer">
									<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
									<a href="" class="remove sprite cross" ng-click="character.contacts.splice($index, 1)"></a>
									<textarea ng-model="contact.notes"></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<div id="rangedWeapons" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Ranged Weapons <a href="" ng-click="addItem('weapons.ranged')">[ Add Weapon ]</a></h2>
						<div hb-margined>
							<div ng-repeat="weapon in character.weapons.ranged" class="weapon" ng-class="{ 'first': $first }">
								<div class="labelTR row1">
									<label class="name medText shiftRight lrBuffer">Weapon</label>
									<label class="damage medNum lrBuffer">Damage</label>
								</div>
								<div class="tr row1">
									<input type="text" ng-model="weapon.name" class="name medText lrBuffer">
									<input type="text" ng-model="weapon.damage" class="damage medNum lrBuffer">
								</div>
								<div class="labelTR row2">
									<label class="accuracy shortNum lrBuffer">Accuracy</label>
									<label class="ap shortNum lrBuffer">AP</label>
									<label class="mode shortText shiftRight lrBuffer">Mode</label>
									<label class="rc shortNum lrBuffer">RC</label>
								</div>
								<div class="tr row2">
									<input type="text" ng-model="weapon.accuracy" class="accuracy lrBuffer">
									<input type="text" ng-model="weapon.ap" class="ap lrBuffer">
									<input type="text" ng-model="weapon.mode" class="mode shortText lrBuffer">
									<input type="text" ng-model="weapon.rc" class="rc lrBuffer">
								</div>
								<div class="labelTR lrBuffer">
									<label class="shiftRight">Notes</label>
								</div>
								<div class="tr lrBuffer">
									<textarea ng-model="weapon.notes"></textarea>
								</div>
								<div class="tr lrBuffer alignRight"><a href="" ng-click="character.weapons.ranged.splice($index, 1)" class="remove">[ Remove ]</a></div>
							</div>
						</div>
					</div>
					<div id="meleeWeapons" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Melee Weapons <a href="" ng-click="addItem('weapons.melee')">[ Add Weapon ]</a></h2>
						<div hb-margined>
							<div ng-repeat="weapon in character.weapons.melee" class="weapon" ng-class="{ 'first': $first }">
								<div class="labelTR row1">
									<label class="name medText shiftRight lrBuffer">Weapon</label>
								</div>
								<div class="tr row1 lrBuffer">
									<input type="text" ng-model="weapon.name" class="name">
								</div>
								<div class="labelTR row2">
									<label class="damage medNum lrBuffer">Damage</label>
									<label class="reach shortNum lrBuffer">Reach</label>
									<label class="accuracy shortNum lrBuffer">Accuracy</label>
									<label class="ap shortNum lrBuffer">AP</label>
								</div>
								<div class="tr row2">
									<input type="text" ng-model="weapon.damage" class="damage medNum lrBuffer">
									<input type="text" ng-model="weapon.reach" class="reach lrBuffer">
									<input type="text" ng-model="weapon.accuracy" class="accuracy lrBuffer">
									<input type="text" ng-model="weapon.ap" class="ap lrBuffer">
								</div>
								<div class="labelTR lrBuffer">
									<label class="shiftRight">Notes</label>
								</div>
								<div class="tr lrBuffer">
									<textarea ng-model="weapon.notes"></textarea>
								</div>
								<div class="tr lrBuffer alignRight"><a href="" ng-click="character.weapons.melee.splice($index, 1)" class="remove">[ Remove ]</a></div>
							</div>
						</div>
					</div>
					<div id="armor" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Armor <a href="" ng-click="addItem('Armor')">[ Add Armor ]</a></h2>
						<div hb-margined>
							<div ng-repeat="armor in character.armor" class="armor" ng-class="{ 'first': $first }">
								<div class="labelTR">
									<label class="name medText shiftRight lrBuffer">Armor</label>
									<label class="shortNum alignCenter lrBuffer">Rating</label>
								</div>
								<div class="tr">
									<input type="text" ng-model="armor.name" class="name medText lrBuffer">
									<input type="text" ng-model="armor.rating" class="rating lrBuffer">
								</div>
								<div class="labelTR">
									<label class="shiftRight lrBuffer">Notes</label>
								</div>
								<div class="tr">
									<textarea ng-model="armor.notes" class="lrBuffer"></textarea>
								</div>
								<div class="tr alignRight"><a href="" ng-click="character.armor.splice($index, 1)" class="remove">[ Remove ]</a></div>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<div id="cyberdeck" class="floatLeft">
						<h2 class="headerbar hbDark" skewElement>Cyberdeck</h2>
						<div hb-margined>
							<div class="labelTR">
								<label class="medText shiftRight lrBuffer">Model</label>
								<label class="shortNum alignCenter lrBuffer">Rating</label>
							</div>
							<div class="tr">
								<input type="text" ng-model="character.cyberdeck.model" class="medText lrBuffer">
								<input type="text" ng-model="character.cyberdeck.rating" class="lrBuffer">
							</div>
							<div class="labelTR row2 lrBuffer">
								<label class="shortNum alignCenter lrBuffer">Attack</label>
								<label class="shortNum alignCenter lrBuffer">Sleaze</label>
								<label class="shortNum alignCenter lrBuffer">Data</label>
								<label class="shortNum alignCenter lrBuffer">Firewall</label>
								<label class="condition shortNum alignCenter lrBuffer last">Condition</label>
							</div>
							<div class="tr row2 lrBuffer">
								<input type="text" ng-model="character.cyberdeck.attack" class="lrBuffer">
								<input type="text" ng-model="character.cyberdeck.sleaze" class="lrBuffer">
								<input type="text" ng-model="character.cyberdeck.data" class="lrBuffer">
								<input type="text" ng-model="character.cyberdeck.firewall" class="lrBuffer">
								<input type="text" ng-model="character.cyberdeck.condition" class="condition lrBuffer last">
							</div>
							<div class="labelTR lrBuffer">
								<label class="shiftRight">Programs</label>
								<a id="addProgram" href="" ng-click="addItem('cyberdeck.programs')">[ Add ]</a>
							</div>
							<div ng-repeat="program in character.cyberdeck.programs" class="tr lrBuffer program">
								<a href="" class="remove sprite cross" ng-click="character.cyberdeck.programs.splice($index, 1)"></a>
								<input type="text" ng-model="program.name" placeholder="Program" class="name">
								<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
								<textarea ng-model="program.notes"></textarea>
							</div>
							<div class="labelTR">
								<label class="shiftRight lrBuffer">Cyberdeck Notes</label>
							</div>
							<div class="tr lrBuffer">
								<textarea id="cyberdeckNote" ng-model="character.cyberdeck.notes"></textarea>
							</div>
						</div>
					</div>
					<div id="augments" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Augmentations <a href="" ng-click="addItem('augmentations')">[ Add Augment ]</a></h2>
						<div hb-margined>
							<div class="labelTR">
								<label class="medText"></label>
								<label class="shortNum alignCenter lrBuffer">Rating</label>
								<label class="shortNum lrBuffer">Essence</label>
							</div>
							<div ng-repeat="augment in character.augmentations" class="augment tr">
								<input type="text" ng-model="augment.name" placeholder="Augmentation" class="name medText">
								<input type="number" ng-model="augment.rating" class="rating lrBuffer">
								<input type="number" ng-model="augment.essence" class="essence lrBuffer">
								<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
								<a href="" class="remove sprite cross" ng-click="character.augments.splice($index, 1)"></a>
								<textarea ng-model="augment.notes"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<div id="sprcf" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Spells / Preparations / Rituals / Complex Forms <a href="" ng-click="addItem('sprcf')">[ Add SPRCF ]</a></h2>
						<div hb-margined>
							<div ng-repeat="sprcf in character.sprcf" class="sprcf tr">
								<div class="labelTR">
									<label class="name lrBuffer"></label>
									<label class="tt shorterText alignCenter lrBuffer">Type/Target</label>
								</div>
								<div class="tr">
									<input type="text" ng-model="sprcf.name" placeholder="SPRCF" class="name lrBuffer">
									<input type="text" ng-model="sprcf.tt" class="tt shorterText lrBuffer">
								</div>
								<div class="labelTR row2">
									<label class="shorterText alignCenter lrBuffer">Range</label>
									<label class="duration shortNum lrBuffer">Duration</label>
									<label class="shorterText lrBuffer">Drain</label>
								</div>
								<div class="tr row2">
									<input type="text" ng-model="sprcf.range" class="shorterText lrBuffer">
									<input type="text" ng-model="sprcf.duration" class="shortNum lrBuffer">
									<input type="text" ng-model="sprcf.drain" class="shorterText lrBuffer">
									<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
									<a href="" class="remove sprite cross" ng-click="character.sprcfs.splice($index, 1)"></a>
									<textarea ng-model="sprcf.notes" class="lrBuffer"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div id="powers" class="floatLeft">
						<h2 class="headerbar hbDark" skew-element>Powers/Abilities <a href="" ng-click="addItem('powers')">[ Add Power/Ability ]</a></h2>
						<div hb-margined>
							<div class="labelTR">
								<label class="name medText lrBuffer"></label>
								<label class="shortNum alignCenter lrBuffer">Rating</label>
							</div>
							<div ng-repeat="power in character.powers" class="power tr">
								<input type="text" ng-model="power.name" placeholder="Power" class="name medText lrBuffer">
								<input type="text" ng-model="power.rating" class="rating">
								<a href="" class="remove sprite cross" ng-click="character.powers.splice($index, 1)"></a>
								<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
								<textarea ng-model="power.notes" class="lrBuffer"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div id="gear">
					<h2 class="headerbar hbDark" skew-element>Gear <a href="" ng-click="addItem('gear')">[ Add Gear ]</a></h2>
					<div class="clearfix" hb-margined>
						<div class="labelTR">
							<label class="name"></label>
							<label class="rating shortNum alignCenter lrBuffer">Rating</label>
						</div>
						<div class="labelTR">
							<label class="name"></label>
							<label class="rating shortNum alignCenter lrBuffer">Rating</label>
						</div>
						<div ng-repeat="gear in character.gear" class="gear tr">
							<input type="text" ng-model="gear.name" class="name">
							<input type="number" ng-model="gear.rating" class="rating lrBuffer">
							<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
							<a href="" class="remove sprite cross" ng-click="character.gear.splice($index, 1)"></a>
							<textarea ng-model="gear.notes"></textarea>
						</div>
					</div>
				</div>
				<div id="notes">
					<h2 class="headerbar hbDark">Background/Notes</h2>
					<textarea id="notes" ng-model="character.notes" class="hbdMargined"></textarea>
				</div>
