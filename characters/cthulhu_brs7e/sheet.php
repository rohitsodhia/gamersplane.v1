			<div class="tr labelTR">
				<label class="medText">Name</label>
				<label class="medText">Occupation</label>
			</div>
			<div class="tr">
				<span class="medText">{{character.name | trustHTML}}</span>
				<span class="medText">{{character.occupation | trustHTML}}</span>
			</div>

			<div class="clearfix">
				<div id="characteristics" class="floatLeft">
					<h2 class="headerbar hbDark" skew-element>Characteristics</h2>
					<ul hb-margined>
						<li ng-repeat="(label, characteristic) in character.characteristics" class="tr" ng-class="{ 'third': $index % 3 == 2 }">
							<label class="leftLabel">{{label.toUpperCase()}}</label>
							<span class="shortNum displayBorder" ng-class="{ 'move': label == 'move' }">{{characteristic}}</span
							><div ng-if="label != 'move'" class="textVals">
								<div class="half">{{getHalfValue(character.characteristics[label])}}</div>
								<div class="fifth">{{getFifthValue(character.characteristics[label])}}</div>
							</div>
						</li>
					</ul>
				</div>
				<div id="stats" class="floatLeft">
					<h2 class="headerbar hbDark" skew-element>Stats</h2>
					<div hb-margined>
						<div ng-repeat="stat in labels.stats" ng-id="stat.key" class="tr stat">
							<h3>{{stat.value}}</h3>
							<label class="leftLabel first">Current <span class="displayBorder">{{character[stat.key].current}}</span></label>
							<label ng-if="stat.key != 'luck'" class="leftLabel">Max <span class="displayBorder">{{character[stat.key].max}}</span></label>
							<div ng-if="stat.key == 'hp' || stat.key == 'sanity'" class="tr">
								<label ng-if="stat.key == 'hp'">
									<div class="prettyCheckbox" ng-class="{ 'checked': character.hp.major }"></div> Major Wound
								</label>
								<label ng-if="stat.key == 'sanity'">
									<div class="prettyCheckbox" ng-class="{ 'checked': character.sanity.temp }"></div> Temp Insane
								</label>
								<label ng-if="stat.key == 'sanity'">
									<div class="prettyCheckbox" ng-class="{ 'checked': character.sanity.indef }"></div> Indef Insane
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="skills" class="clearfix">
				<h2 class="headerbar hbDark" skew-element>Skills</h2>
				<div hb-margined>
					<p class="note">Skills in grey italics are default values</p>
					<ul ng-repeat="skillCol in skillCols" ng-class="{ 'first': $first }">
						<li ng-repeat="skill in skillCol" class="tr skill" ng-class="{ 'default': skill.default }">
							<span class="name" ng-bind-html="skill.name"></span>
							<span class="value">{{skill.value}}%</span>
						</li>
					</ul>
				</div>
			</div>
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2 class="headerbar hbDark" skew-element>Weapons</h2>
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
								<span class="medText name lrBuffer" ng-bind-html="weapon.name"></span>
								<span class="shortNum lrBuffer">{{weapon.regular}}</span>
								<span class="shortNum lrBuffer">{{weapon.hard}}</span>
								<span class="shortNum lrBuffer">{{weapon.extreme}}</span>
								<span class="shortText lrBuffer">{{weapon.damage}}</span>
								<span class="shortNum lrBuffer">{{weapon.range}}</span>
								<span class="shortNum lrBuffer">{{weapon.attacks}}</span>
								<span class="shortNum lrBuffer">{{weapon.ammo}}</span>
								<span class="shortNum lrBuffer">{{weapon.malf}}</span>
							</div>
							<div class="labelTR lrBuffer">
								<label class="shiftRight">Notes</label>
							</div>
							<div class="tr lrBuffer">
								<span ng-bind-html="weapon.notes"></span>
							</div>
						</div>
					</div>
				</div>
				<div id="combat" class="floatRight">
					<h2 class="headerbar hbDark" skew-element>Combat</h2>
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
				<h2 class="headerbar hbDark" skew-element>Items</h2>
				<div class="clearfix" hb-margined>
					<div ng-repeat="item in character.items" class="item tr">
						<div class="name">{{item.name}}</div>
						<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
						<div ng-bind-html="item.notes | trustHTML" class="notes"></div>
					</div>
				</div>
			</div>
			<div id="notes">
				<h2 class="headerbar hbDark">Background/Notes</h2>
				<div ng-bind-html="character.notes | trustHTML" hb-margined></div>
			</div>
