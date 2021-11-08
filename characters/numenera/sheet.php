			<div id="name_class" class="tr">
				<span class="input">
					<div>{{character.name | trustHTML}}</div>
					<label>Name</label>
				</span>
				<span class="text"> is a </span>
				<span class="input">
					<div>{{character.descriptor | trustHTML}}</div>
					<label>Descriptor</label>
				</span>
				<span id="class_type" class="input">
					<div>{{character.type | trustHTML}}</div>
					<label>Type</label>
				</span>
				<span class="text"> who </span>
				<span id="class_focus" class="input">
					<div>{{character.focus | trustHTML}}</div>
					<label>Focus</label>
				</span>
			</div>

			<div class="clearfix">
				<div id="stats" class="floatLeft">
					<div class="clearfix">
						<div id="statCol" class="floatLeft">
							<div ng-repeat="(key, display) in labels.stats" class="tr">
								<label class="leftLabel">{{display}}</label>
								<div class="shortNum">{{character[key]}}</div>
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
									<div class="shortNum">{{character.attributes[attr].pool.current}}</div> / <div class="shortNum">{{character.attributes[attr].pool.max}}</div>
								</div>
								<div class="shortNum lrBuffer alignCenter">{{character.attributes[attr].edge}}</div>
							</div>
						</div>
					</div>
					<div class="clearfix">
						<div id="damage" class="floatLeft">
							<div class="alignCenter header">Damage</div>
							<div class="tr damage">
								<div><div ng-if="character.damage.impaired" class="sprite check small"></div></div> <span>Impaired</span>
							</div>
							<div class="tr damage">
								<div><div ng-if="character.damage.debilitated" class="sprite check small"></div></div> <span>Debilitated</span>
							</div>
							<div id="armor" class="tr">
								<label class="leftLabel width3">Armor</label>
								<div class="width1">{{character.armor}}</div>
							</div>
						</div>
						<div id="recovery" class="floatLeft clearfix">
							<div class="alignCenter header">Recovery</div>
							<div class="tr floatLeft">
								<label class="leftLabel width1">1d6+</label>
								<div class="width1">{{character.recovery}}</div>
							</div>
							<div id="recoveryTimes" class="floatLeft">
								<div ng-repeat="(recovery, label) in labels.recoveries" class="tr">
									<div><div ng-if="character.recoveryTimes[recovery]" class="sprite check small"></div></div> {{label}}
								</div>
							</div>
						</div>
					</div>
					<div id="attacks">
						<h2 class="headerbar hbDark">Attacks</h2>
						<div class="hbdMargined">
							<div class="tr labelTR">
								<label class="name medText">Attack</label>
								<label class="mod shortNum alignCenter lrBuffer">Mod</label>
								<label class="dmg shortNum alignCenter">Dmg</label>
							</div>
							<div id="attackList">
								<div ng-repeat="attack in character.attacks" class="attack tr">
									<div class="name medText">{{attack.name | trustHTML}}</div>
									<div class="mod shortNum lrBuffer alignCenter">{{attack.mod}}</div>
									<div class="dmg shortNum alignCenter">{{attack.dmg}}</div>
								</div>
								<p id="noAttacks" ng-if="character.attacks.length == 0">This character currently has no attacks.</p>
							</div>
						</div>
					</div>
				</div>
				<div id="skills" class="floatLeft nonDefault">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined">
						<div class="tr labelTR">
							<label class="name width5">Skill</label>
							<label class="shortNum alignCenter lrBuffer">Attr</label>
							<label class="shortNum alignCenter lrBuffer">Prof?</label>
						</div>
						<div ng-repeat="skill in character.skills" class="skill tr clearfix">
							<div class="name width5">{{skill.name | trustHTML}}</div>
							<div class="attr alignCenter shortNum lrBuffer"><div>{{skill.attr}}</div></div>
							<div class="prof alignCenter shortNum lrBuffer"><div>{{skill.prof}}</div></div>
						</div>
						<p id="noSkills" ng-if="character.skills.length == 0">This character currently has no skills.</p>
					</div>
				</div>
				<div id="specialAbilities" class="floatLeft">
					<h2 class="headerbar hbDark">Special Abilities</h2>
					<div class="hbdMargined">
						<div ng-repeat="specialAbility in character.specialAbilities" class="specialAbility tr clearfix">
							<span class="name">{{specialAbility.name | trustHTML}}</span>
							<a ng-if="specialAbility.notes.length" ng-click="toggleNotes($event)" href="" class="notesLink">Notes</a>
							<div ng-if="specialAbility.notes.length" class="notes" ng-bind-html="specialAbility.notes"></div>
						</div>
						<p id="noSpecialAbilities" ng-if="character.specialAbilities.length == 0">This character currently has no special abilities.</p>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div id="cyphers" class="floatLeft">
					<h2 class="headerbar hbDark">Cyphers</h2>
					<div class="hbdMargined">
						<div ng-repeat="cypher in character.cyphers" class="cypher tr clearfix">
							<span class="name">{{cypher.name | trustHTML}}</span>
							<a ng-if="cypher.notes.length" ng-click="toggleNotes($event)" href="" class="notesLink">Notes</a>
							<div ng-if="cypher.notes.length" class="notes" ng-bind-html="cypher.notes | trustHTML"></div>
						</div>
						<p ng-if="character.cyphers.length == 0" id="noCyphers">This character currently has no cyphers.</p>
					</div>
				</div>
				<div id="posessions" class="floatRight">
					<h2 class="headerbar hbDark">Possessions</h2>
					<div class="hbdMargined" ng-bind-html="character.possessions | trustHTML"></div>
				</div>
			</div>

			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=printReady(BBCode2Html($this->getNotes()))?></div>
			</div>
