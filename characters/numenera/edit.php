				<div class="tr">
					<label id="label_name" class="leftLabel lrBuffer borderBox">Name</label>
					<input type="text" name="name" value="<?=$this->getName()?>" class="medText lrBuffer">
				</div>
				<div class="tr labelTR">
					<label class="medText lrBuffer borderBox shiftRight">Descriptor</label>
					<label class="shortText lrBuffer borderBox shiftRight">Type</label>
					<label class="medText lrBuffer borderBox shiftRight">Focus</label>
				</div>
				<div class="tr">
					<input type="text" name="descriptor" value="<?=$this->getDescriptor()?>" class="medText lrBuffer">
					<input type="text" name="type" value="<?=$this->getType()?>" class="shortText lrBuffer">
					<input type="text" name="focus" value="<?=$this->getFocus()?>" class="medText lrBuffer">
				</div>

				<div class="clearfix">
					<div id="stats" class="floatLeft">
						<div class="clearfix">
							<div id="statCol_left" class="floatLeft">
								<div class="tr">
									<label class="width2 leftLabel">Tier</label>
									<input type="text" name="tier" value="<?=$this->getTier()?>" class="shortNum">
								</div>
								<div class="tr">
									<label class="width2 leftLabel">Effort</label>
									<input type="text" name="effort" value="<?=$this->getEffort()?>" class="shortNum">
								</div>
								<div class="tr">
									<label class="width2 leftLabel">XP</label>
									<input type="text" name="xp" value="<?=$this->getXP()?>" class="shortNum">
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
										<input type="text" name="stats[<?=$stat?>][pool][current]" value="<?=$this->getStats($stat, 'pool.current')?>" class="shortNum"> / <input type="text" name="stats[<?=$stat?>][pool][max]" value="<?=$this->getStats($stat, 'pool.max')?>" class="shortNum">
									</div>
									<input type="text" name="stats[<?=$stat?>][edge]" value="<?=$this->getStats($stat, 'edge')?>" class="shortNum lrBuffer">
								</div>
<?	} ?>
							</div>
						</div>
						<div class="clearfix">
							<div id="damage" class="floatLeft">
								<div class="alignCenter header">Damage</div>
								<div class="tr damage">
									<input type="checkbox" name="damage[impaired]"<?=$this->getDamage('impaired')?' checked="checked"':''?>>
									<label class="leftLabel">Impaired</label>
								</div>
								<div class="tr damage">
									<input type="checkbox" name="damage[debilitated]"<?=$this->getDamage('debilitated')?' checked="checked':''?>>
									<label class="leftLabel">Debilitated</label>
								</div>
								<div id="armor" class="tr">
									<label class="leftLabel width3">Armor</label>
									<input type="text" name="armor" class="width1" value="<?=$this->getArmor()?>">
								</div>
							</div>
							<div id="recovery" class="floatLeft clearfix">
								<div class="alignCenter header">Recovery</div>
								<div class="tr floatLeft">
									<label class="leftLabel width1">1d6+</label>
									<input type="text" name="recovery" class="width1" value="<?=$this->getRecovery()?>">
								</div>
								<div id="recoveryTimes" class="floatLeft">
<?	foreach (array('action' => 'Action', 'ten_min' => '10 Minutes', 'hour' => 'Hour', 'ten_hours' => '10 Hours') as $slug => $time) { ?>
									<div class="tr">
										<input type="checkbox" name="recoveryTimes[<?=$slug?>]"<?=$this->getRecoveryTimes($slug)?' checked="checked"':''?>> <label class="leftLabel"><?=$time?></label>
									</div>
<?	} ?>
								</div>
							</div>
						</div>
						<div id="attacks">
							<h2 class="headerbar hbDark">Attacks <a id="addAttack" href="">[ Add Attack ]</a></h2>
							<div class="hbdMargined">
								<div class="tr labelTR">
									<label class="name shiftRight borderBox">Attack</label>
									<label class="mod shortNum alignCenter lrBuffer">Mod</label>
									<label class="dmg shortNum alignCenter">Dmg</label>
								</div>
								<div id="attackList">
<?	$this->showAttacksEdit(3); ?>
								</div>
							</div>
						</div>
					</div>
					<div id="skills" class="floatLeft nonDefault">
						<h2 class="headerbar hbDark">Skills <a id="addSkill" href="">[ Add Skill ]</a></h2>
						<div class="hbdMargined">
							<div class="tr labelTR">
								<label class="width5 shiftRight borderBox">Skill</label>
								<label class="shortNum alignCenter lrBuffer">Prof?</label>
							</div>
							<div id="skillList">
<?	$this->showSkillsEdit(); ?>
							</div>
						</div>
					</div>
					<div id="specialAbilities" class="floatLeft nonDefault">
						<h2 class="headerbar hbDark">Special Abilities <a id="addSpecialAbility" href="">[ Add Special Ability ]</a></h2>
						<div class="hbdMargined">
							<div id="specialAbilityList">
<?	$this->showSpecialAbilitiesEdit(); ?>
							</div>
						</div>
					</div>
				</div>

				<div class="clearfix">
					<div id="cyphers" class="floatLeft">
						<h2 class="headerbar hbDark">Cyphers <a id="addCypher" href="">[ Add Cypher ]</a></h2>
						<div class="hbdMargined">
							<div id="cypherList">
<?	$this->showCyphersEdit(); ?>
							</div>
						</div>
					</div>
					<div id="posessions" class="floatRight">
						<h2 class="headerbar hbDark">Possessions</h2>
						<textarea name="possessions" class="hbdMargined"><?=$this->getPossessions()?></textarea>
					</div>
				</div>

				<div id="notes">
					<h2 class="headerbar hbDark">Notes</h2>
					<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
				</div>
