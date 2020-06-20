			<div class="tr">
				<label id="label_name" class="leftLabel lrBuffer borderBox">Name</label>
				<div><?=$this->getName()?></div>
			</div>
			<div class="tr labelTR">
				<label class="medText lrBuffer borderBox">Descriptor</label>
				<label class="shortText lrBuffer borderBox">Type</label>
				<label class="medText lrBuffer borderBox">Focus</label>
			</div>
			<div class="tr">
				<div class="medText lrBuffer"><?=$this->getDescriptor()?></div>
				<div class="shortText lrBuffer"><?=$this->getType()?></div>
				<div class="medText lrBuffer"><?=$this->getFocus()?></div>
			</div>

			<div class="clearfix">
				<div id="stats" class="floatLeft">
					<div class="clearfix">
						<div id="statCol_left" class="floatLeft">
							<div class="tr">
								<label class="width2 leftLabel">Tier</label>
								<div class="shortNum"><?=$this->getTier()?></div>
							</div>
							<div class="tr">
								<label class="width2 leftLabel">Effort</label>
								<div class="shortNum"><?=$this->getEffort()?></div>
							</div>
							<div class="tr">
								<label class="width2 leftLabel">XP</label>
								<div class="shortNum"><?=$this->getXP()?></div>
							</div>
						</div>
						<div class="floatLeft">
							<div class="tr labelTR mainStat">
								<div class="shortText spacer"></div>
								<div class="pool">
									<label id="poolLabel">Pool</label>
									<label class="shortNum">Current</label> / <label class="shortNum">Max</label>
								</div>
								<label class="shortNum lrBuffer alignBottom">Edge</label>
							</div>
<?	foreach (array('might', 'speed', 'intellect') as $stat) { ?>
							<div class="tr mainStat">
								<div class="shortText"><?=ucwords($stat)?></div>
								<div class="pool">
									<div class="shortNum"><?=$this->getStats($stat, 'pool.current')?></div> / <div class="shortNum"><?=$this->getStats($stat, 'pool.max')?></div>
								</div>
								<div class="shortNum lrBuffer alignCenter"><?=$this->getStats($stat, 'edge')?></div>
							</div>
<?	} ?>
						</div>
					</div>
					<div class="clearfix">
						<div id="damage" class="floatLeft">
							<div class="alignCenter header">Damage</div>
							<div class="tr damage">
								<div><?=$this->getDamage('impaired')?'<div class="sprite check small"></div>':''?></div> <span>Impaired</span>
							</div>
							<div class="tr damage">
								<div><?=$this->getDamage('debilitated')?'<div class="sprite check small"></div>':''?></div> <span>Debilitated</span>
							</div>
							<div id="armor" class="tr">
								<label class="leftLabel width3">Armor</label>
								<div class="width1"><?=$this->getArmor()?></div>
							</div>
						</div>
						<div id="recovery" class="floatLeft clearfix">
							<div class="alignCenter header">Recovery</div>
							<div class="tr floatLeft">
								<label class="leftLabel width1">1d6+</label>
								<div class="width1"><?=$this->getRecovery()?></div>
							</div>
							<div id="recoveryTimes" class="floatLeft">
<?	foreach (array('action' => 'Action', 'ten_min' => '10 Minutes', 'hour' => 'Hour', 'ten_hours' => '10 Hours') as $slug => $time) { ?>
								<div class="tr">
									<div><?=$this->getRecoveryTimes($slug)?'<div class="sprite check small"></div>':''?></div> <?=$time?>
								</div>
<?	} ?>
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
<?	$this->displayAttacks(); ?>
							</div>
						</div>
					</div>
				</div>
				<div id="skills" class="floatLeft nonDefault">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined">
						<div class="tr labelTR">
							<label class="name width5">Skill</label>
							<label class="shortNum alignCenter lrBuffer">Prof?</label>
						</div>
<?	$this->displaySkills(); ?>
					</div>
				</div>
				<div id="specialAbilities" class="floatLeft">
					<h2 class="headerbar hbDark">Special Abilities</h2>
					<div class="hbdMargined">
<?	$this->displaySpecialAbilities(); ?>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div id="cyphers" class="floatLeft">
					<h2 class="headerbar hbDark">Cyphers</h2>
					<div class="hbdMargined">
<?	$this->displayCyphers(); ?>
					</div>
				</div>
				<div id="posessions" class="floatRight">
					<h2 class="headerbar hbDark">Possessions</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getPossessions()))?></div>
				</div>
			</div>

			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=printReady(BBCode2Html($this->getNotes()))?></div>
			</div>
