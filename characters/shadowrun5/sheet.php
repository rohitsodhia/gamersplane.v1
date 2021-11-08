			<div class="tr labelTR">
				<label class="medText">Name</label>
				<label class="medText">Metatype</label>
			</div>
			<div class="tr">
				<span class="medText">{{character.name | trustHTML}}</span>
				<span class="medText">{{character.metatype | trustHTML}}</span>
			</div>
			<div class="tr">
				<span ng-repeat="(key, rep) in character.reputation" class="rep">
					<span>{{labels.rep[key]}}</span>
					<span class="shortNum displayBorder">{{rep}}</span>
				</span>
			</div>
			<div class="tr">
				<span class="karma">
					<span>Spent Karma</span>
					<span class="shortNum displayBorder">{{character.karma.spent}}</span>
				</span>
				<span class="karma">
					<span>Total Karma</span>
					<span class="shortNum displayBorder">{{character.karma.total}}</span>
				</span>
			</div>

			<div class="clearfix">
				<div id="stats" class="floatLeft">
					<h2 class="headerbar hbDark">Stats</h2>
					<div hb-margined>
						<ul ng-repeat="column in [0, 1]" ng-class="{ 'first': $first }">
							<li ng-repeat="label in labels.stats | limitTo: (labels.stats.length / 2):(column * labels.stats.length / 2)" class="tr">
								<label class="leftLabel">{{label.value}}</label>
								<span class="shortNum displayBorder">{{character.stats[label.key]}}</span>
							</li>
						</ul>
					</div>
				</div>
				<div id="limits" class="floatLeft">
					<h2 class="headerbar hbDark">Limits</h2>
					<div hb-margined>
						<div ng-repeat="(label, limit) in character.limits" class="tr">
							<label class="leftLabel">{{label.capitalizeFirstLetter()}}</label>
							<span class="shortNum displayBorder">{{limit}}</span>
						</div>
					</div>
				</div>
				<div id="damage" class="floatLeft">
					<h2 class="headerbar hbDark">Damage Tracks</h2>
					<div hb-margined>
						<div class="clearfix">
							<div ng-repeat="(track, stat) in { 'physical': 'body', 'stun': 'willpower' }" id="{{track}}Track" class="damageType floatLeft">
								<h3>{{track.capitalizeFirstLetter()}}</h3>
								<div class="track clearfix">
									<div ng-repeat="box in range(1, 8 + (character.stats[stat] / 2 | ceil) + character.damage[track].modify)" class="damageCell" ng-class="{ 'first': $first, 'filled': character.damage[track].current > $index }"></div>
								</div>
							</div>
						</div>
						<div id="overflow">
							<label class="leftLabel">Overflow</label>
							<span class="shortNum displayBorder">{{character.damage.physical.overflow}}</span>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div id="skills" class="floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<div hb-margined>
						<div ng-repeat="skill in character.skills" class="skill tr">
							<div class="type">{{skill.type.toUpperCase()}}</div>
							<div class="name lrBuffer">{{skill.name}}</div>
							<div class="rating shortNum displayBorder">{{skill.rating}}</div>
						</div>
					</div>
				</div>
				<div class="floatRight">
					<div id="qualities">
						<h2 class="headerbar hbDark">Qualities</h2>
						<div hb-margined>
							<div ng-repeat="quality in character.qualities" class="quality tr">
								<div class="type">{{quality.type.toUpperCase()}}</div>
								<div class="name lrBuffer">{{quality.name}}</div>
								<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
								<div ng-bind-html="quality.notes | trustHTML" class="notes"></div>
							</div>
						</div>
					</div>
					<div id="contacts">
						<h2 class="headerbar hbDark">Contacts</h2>
						<div hb-margined>
							<div class="labelTR">
								<label class="name"></label>
								<label class="shortNum lrBuffer">Loyalty</label>
								<label class="connection shortNum lrBuffer">Connection</label>
							</div>
							<div ng-repeat="contact in character.contacts" class="contact tr">
								<div class="name">{{contact.name}}</div>
								<div class="loyalty shortNum lrBuffer">{{contact.loyalty}}</div>
								<div class="connection shortNum lrBuffer">{{contact.connection}}</div>
								<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
								<div ng-bind-html="contact.notes | trustHTML" class="notes"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<div id="rangedWeapons" class="floatLeft">
					<h2 class="headerbar hbDark">Ranged Weapons</h2>
					<div hb-margined>
						<div ng-repeat="weapon in character.weapons.ranged" class="weapon" ng-class="{ 'first': $first }">
							<div class="labelTR row1">
								<label class="name medText lrBuffer">Weapon</label>
								<label class="damage medNum lrBuffer">Damage</label>
							</div>
							<div class="tr row1">
								<div class="name medText lrBuffer">{{weapon.name}}</div>
								<div class="damage medNum lrBuffer">{{weapon.damage}}</div>
							</div>
							<div class="labelTR row2">
								<label class="accuracy shortNum lrBuffer">Accuracy</label>
								<label class="ap shortNum lrBuffer">AP</label>
								<label class="mode shortText lrBuffer">Mode</label>
								<label class="rc shortNum lrBuffer">RC</label>
							</div>
							<div class="tr row2">
								<div class="accuracy shortNum lrBuffer">{{weapon.accuracy}}</div>
								<div class="ap shortNum lrBuffer">{{weapon.ap}}</div>
								<div class="mode shortText lrBuffer">{{weapon.mode}}</div>
								<div class="rc shortNum lrBuffer">{{weapon.rc}}</div>
							</div>
							<div class="labelTR lrBuffer">
								<label>Notes</label>
							</div>
							<div class="tr lrBuffer">
								<div ng-bind-html="weapon.notes | trustHTML" class="notes avNotes"></div>
							</div>
						</div>
					</div>
				</div>
				<div id="meleeWeapons" class="floatLeft">
					<h2 class="headerbar hbDark">Melee Weapons</h2>
					<div hb-margined>
						<div ng-repeat="weapon in character.weapons.melee" class="weapon" ng-class="{ 'first': $first }">
							<div class="labelTR row1">
								<label class="name medText lrBuffer">Weapon</label>
							</div>
							<div class="tr row1 lrBuffer">
								<div class="name">{{weapon.name}}</div>
							</div>
							<div class="labelTR row2">
								<label class="damage medNum lrBuffer">Damage</label>
								<label class="reach shortNum lrBuffer">Reach</label>
								<label class="accuracy shortNum lrBuffer">Accuracy</label>
								<label class="ap shortNum lrBuffer">AP</label>
							</div>
							<div class="tr row2">
								<div class="damage medNum lrBuffer">{{weapon.damage}}</div>
								<div class="reach shortNum lrBuffer">{{weapon.reach}}</div>
								<div class="accuracy shortNum lrBuffer">{{weapon.accuracy}}</div>
								<div class="ap shortNum lrBuffer">{{weapon.ap}}</div>
							</div>
							<div class="labelTR lrBuffer">
								<label>Notes</label>
							</div>
							<div class="tr lrBuffer">
								<div ng-bind-html="weapon.notes | trustHTML" class="notes avNotes"></div>
							</div>
						</div>
					</div>
				</div>
				<div id="armor" class="floatLeft">
					<h2 class="headerbar hbDark">Armor</h2>
					<div hb-margined>
						<div ng-repeat="armor in character.armor" class="armor" ng-class="{ 'first': $first }">
							<div class="labelTR">
								<label class="name medText lrBuffer">Armor</label>
								<label class="shortNum alignCenter lrBuffer">Rating</label>
							</div>
							<div class="tr">
								<div class="name medText lrBuffer">{{armor.name}}</div>
								<div class="rating shortNum lrBuffer">{{armor.rating}}</div>
							</div>
							<div class="labelTR">
								<label class="lrBuffer">Notes</label>
							</div>
							<div class="tr">
								<div ng-bind-html="armor.notes | trustHTML" class="notes avNotes lrBuffer"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<div id="cyberdeck" class="floatLeft">
					<h2 class="headerbar hbDark" skewElement>Cyberdeck</h2>
					<div hb-margined>
						<div class="labelTR">
							<label class="medText lrBuffer">Model</label>
							<label class="shortNum alignCenter lrBuffer">Rating</label>
						</div>
						<div class="tr">
							<div class="medText lrBuffer">{{character.cyberdeck.model}}</div>
							<div class="shortNum lrBuffer">{{character.cyberdeck.rating}}</div>
						</div>
						<div class="labelTR row2 lrBuffer">
							<label class="shortNum alignCenter lrBuffer">Attack</label>
							<label class="shortNum alignCenter lrBuffer">Sleaze</label>
							<label class="shortNum alignCenter lrBuffer">Data</label>
							<label class="shortNum alignCenter lrBuffer">Firewall</label>
							<label class="condition shortNum alignCenter lrBuffer last">Condition</label>
						</div>
						<div class="tr row2 lrBuffer">
							<div class="shortNum lrBuffer">{{character.cyberdeck.attack}}</div>
							<div class="shortNum lrBuffer">{{character.cyberdeck.sleaze}}</div>
							<div class="shortNum lrBuffer">{{character.cyberdeck.data}}</div>
							<div class="shortNum lrBuffer">{{character.cyberdeck.firewall}}</div>
							<div class="condition shortNum lrBuffer last">{{character.cyberdeck.condition}}</div>
						</div>
						<div class="labelTR lrBuffer">
							<label>Programs</label>
						</div>
						<div ng-repeat="program in character.cyberdeck.programs" class="tr lrBuffer program">
							<div class="name">{{program.name}}</div>
							<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
							<div ng-bind-html="program.notes | trustHTML" class="notes"></div>
						</div>
						<div class="labelTR">
							<label class="lrBuffer">Cyberdeck Notes</label>
						</div>
						<div class="tr lrBuffer">
							<div ng-bind-html="character.cyberdeck.notes | trustHTML" class="notes avNotes"></div>
						</div>
					</div>
				</div>
				<div id="augments" class="floatLeft">
					<h2 class="headerbar hbDark">Augmentations</h2>
					<div hb-margined>
						<div class="labelTR">
							<label class="name medText"></label>
							<label class="shortNum alignCenter lrBuffer">Rating</label>
							<label class="shortNum lrBuffer">Essence</label>
						</div>
						<div ng-repeat="augment in character.augmentations" class="augment tr">
							<div class="name medText">{{augment.name}}</div>
							<div class="rating shortNum lrBuffer">{{augment.rating}}</div>
							<div class="essence shortNum lrBuffer">{{augment.essence}}</div>
							<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
							<div ng-bind-html="augment.notes | trustHTML" class="notes"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<div id="sprcf" class="floatLeft">
					<h2 class="headerbar hbDark">Spells / Preparations / Rituals / Complex Forms</h2>
					<div hb-margined>
						<div ng-repeat="sprcf in character.sprcf" class="sprcf tr">
							<div class="labelTR">
								<label class="name lrBuffer"></label>
								<label class="tt shorterText alignCenter lrBuffer">Type/Target</label>
							</div>
							<div class="tr">
								<div class="name lrBuffer">{{sprcf.name}}</div>
								<div class="tt shorterText lrBuffer">{{sprcf.tt}}</div>
							</div>
							<div class="labelTR row2">
								<label class="shorterText alignCenter lrBuffer">Range</label>
								<label class="duration shortNum lrBuffer">Duration</label>
								<label class="shorterText lrBuffer">Drain</label>
							</div>
							<div class="tr row2">
								<div class="shorterText lrBuffer">{{sprcf.range}}</div>
								<div class="shortNum lrBuffer">{{sprcf.duration}}</div>
								<div class="shorterText lrBuffer">{{sprcf.drain}}</div>
								<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
								<div ng-bind-html="sprcf.notes | trustHTML" class="notes lrBuffer"></div>
							</div>
						</div>
					</div>
				</div>
				<div id="powers" class="floatLeft">
					<h2 class="headerbar hbDark">Powers/Abilities</h2>
					<div hb-margined>
						<div class="labelTR">
							<label class="name medText lrBuffer">&nbsp;</label>
							<label class="shortNum alignCenter lrBuffer">Rating</label>
						</div>
						<div ng-repeat="power in character.powers" class="power tr">
							<div class="name medText lrBuffer">{{power.name}}</div>
							<div class="rating shortNum lrBuffer">{{power.rating}}</div>
							<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
							<div ng-bind-html="power.notes | trustHTML" class="notes lrBuffer"></div>
						</div>
					</div>
				</div>
			</div>
			<div id="gear">
				<h2 class="headerbar hbDark">Gear</h2>
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
						<div class="name">{{gear.name}}</div>
						<div class="rating shortNum lrBuffer">{{gear.rating}}</div>
						<a href="" ng-click="toggleNotes($event)" class="notesLink">[ Notes ]</a>
						<div ng-bind-html="gear.notes | trustHTML" class="notes"></div>
					</div>
				</div>
			</div>
			<div id="notes">
				<h2 class="headerbar hbDark">Background/Notes</h2>
				<div class="hbdMargined"><?=printReady(BBCode2Html($this->getNotes()))?></div>
			</div>
