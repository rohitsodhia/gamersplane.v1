				<div class="tr labelTR">
					<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
					<label id="label_codename" class="medText lrBuffer borderBox shiftRight">Codename</label>
				</div>
				<div class="tr">
					<input type="text" name="name" value="<?=$this->getName()?>" class="medText lrBuffer">
					<input type="text" name="codename" value="<?=$this->getCodename()?>" class="medText lrBuffer">
				</div>
				
				<div class="tr labelTR">
					<label id="label_classes" class="medText lrBuffer borderBox shiftRight">Class(es)</label>
					<label id="label_levels" class="shortNum lrBuffer borderBox">Level(s)</label>
					<label id="label_department" class="medText lrBuffer borderBox shiftRight">Department</label>
				</div>
				<div class="tr">
					<div id="classWrapper">
						<a href="">[ Add Class ]</a>
<?
	$hasClasses = false;
	foreach ($this->getClasses() as $class => $level) {
			$hasClasses = true;
?>
						<div class="classSet">
							<input type="text" name="class[]" value="<?=$class?>" class="medText lrBuffer">
							<input type="text" name="level[]" value="<?=$level?>" class="shortNum lrBuffer">
						</div>
<?
	}
	if (!$hasClasses) {
?>
						<div class="classSet">
							<input type="text" name="class[]" class="medText lrBuffer">
							<input type="text" name="level[]" class="shortNum lrBuffer">
						</div>
<?	} ?>
					</div>
					<input id="department" type="text" name="department" value="<?=$this->getDepartment()?>" class="medText lrBuffer alignLeft">
				</div>
				
				<div class="clearfix">
					<div id="stats">
<?
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
?>
						<div class="tr">
							<label id="label_<?=$short?>" class="textLabel shortText lrBuffer leftLabel"><?=$stat?></label>
							<input type="text" id="<?=$short?>" name="stats[<?=$short?>]" value="<?=$this->getStat($short)?>" maxlength="2" class="stat lrBuffer">
							<span id="<?=$short?>Modifier"><?=$this->getStatMod($short)?></span>
						</div>
<?	} ?>
					</div>
					
					<div id="savingThrows">
						<div class="tr labelTR">
							<div class="fillerBlock cell">&nbsp;</div>
							<label class="shortNum lrBuffer">Total</label>
							<label class="shortNum lrBuffer">Base</label>
							<label class="statSelect lrBuffer">Ability</label>
							<label class="shortNum lrBuffer">Misc</label>
						</div>
<?	foreach (d20Character_consts::getSaveNames() as $save => $saveFull) { ?>
						<div id="<?=$save?>Row" class="tr sumRow">
							<label class="leftLabel"><?=$saveFull?></label>
							<span id="<?=$save?>Total" class="shortNum lrBuffer total addStat_<?=$this->getSave($save, 'stat')?>"><?=showSign($this->getSave($save, 'total'))?></span>
							<input type="text" name="saves[<?=$save?>][base]" value="<?=$this->getSave($save, 'base')?>" class="lrBuffer" data-save-type="<?=$save?>">
							<span class="statSelect lrBuffer">
								<select name="saves[<?=$save?>][stat]" class="abilitySelect">
<? 	foreach ($stats as $short => $stat) { ?>
									<option value="<?=$short?>"<?=$this->getSave($save, 'stat') == $short?' selected="selected"':''?>><?=ucwords($short)?></option>
<?	} ?>
								</select>
								<span class="shortNum abilitySelectMod statBonus_<?=$this->getSave($save, 'stat')?>" data-stat-hold="<?=$this->getSave($save, 'stat')?>" data-total-ele="<?=$save?>Total"><?=$this->getStatMod($this->getSave($save, 'stat'))?></span>
							</span>
							<input type="text" name="saves[<?=$save?>][misc]"  value="<?=$this->getSave($save, 'misc')?>" class="lrBuffer" data-save-type="<?=$save?>">
						</div>
<?	} ?>
					</div>
					
					<div id="hp">
						<div class="tr">
							<label class="leftLabel textLabel">Vitality</label>
							<input type="text" name="hp[vitality]" value="<?=$this->getHP('vitality')?>" class="medNum">
						</div>
						<div class="tr">
							<label class="leftLabel textLabel">Wounds</label>
							<input type="text" name="hp[wounds]" value="<?=$this->getHP('wounds')?>" class="medNum">
						</div>
						<div class="tr">
							<label class="leftLabel textLabel">Base Speed</label>
							<input type="text" name="speed" value="<?=$this->getSpeed()?>" class="shortNum">
						</div>
					</div>
					
					<div id="ac">
						<div class="tr labelTR">
							<label class="medText">Total Def</label>
							<div class="fillerBlock cell medNum">&nbsp;</div>
							<label class="lrBuffer">Class/ Armor</label>
							<label class="lrBuffer">Dex</label>
							<label class="lrBuffer">Size</label>
							<label class="lrBuffer">Misc</label>
						</div>
						<div class="tr sumRow">
							<span id="ac_total" class="total noSign addInt_10"><?=$this->getAC('total')?></span>
							<span> = 10 + </span>
							<input type="text" name="ac[armor]" value="<?=$this->getAC('armor')?>" class="acComponents lrBuffer">
							<input type="text" name="ac[dex]" value="<?=$this->getAC('dex')?>" class="acComponents lrBuffer">
							<input type="text" name="ac[size]" value="<?=$this->getAC('size')?>" class="acComponents lrBuffer">
							<input type="text" name="ac[misc]" value="<?=$this->getAC('misc')?>" class="acComponents lrBuffer">
						</div>
					</div>
				</div>
				
				<div class="clearfix">
					<div class="col">
						<div id="combatBonuses">
							<div class="tr labelTR">
								<div class="fillerBlock cell shortText">&nbsp;</div>
								<label class="shortNum lrBuffer first">Total</label>
								<label class="shortNum lrBuffer">Base</label>
								<label class="statSelect lrBuffer">Ability</label>
								<label class="shortNum lrBuffer">Misc</label>
							</div>
							<div id="init" class="tr sumRow">
								<label class="leftLabel shortText">Initiative</label>
								<span id="initTotal" class="shortNum lrBuffer total addStat_<?=$this->getInitiative('stat')?>"><?=showSign($this->getInitiative('total'))?></span>
								<span class="lrBuffer shortNum">&nbsp;</span>
								<span class="statSelect lrBuffer">
									<select name="initiative[stat]" class="abilitySelect">
<? 	foreach ($stats as $short => $stat) { ?>
										<option value="<?=$short?>"<?=$this->getInitiative('stat') == $short?' selected="selected"':''?>><?=ucwords($short)?></option>
<?	} ?>
									</select>
									<span class="shortNum abilitySelectMod statBonus_<?=$this->getInitiative('stat')?>" data-stat-hold="<?=$this->getInitiative('stat')?>" data-total-ele="initTotal"><?=$this->getStatMod($this->getInitiative('stat'))?></span>
								</span>
								<input type="text" name="initiative[misc]" value="<?=$this->getInitiative('misc')?>" class="lrBuffer">
							</div>
							<div id="melee" class="tr sumRow">
								<label class="leftLabel shortText">Melee</label>
								<span id="meleeTotal" class="shortNum lrBuffer total addStat_<?=$this->getAttackBonus('stat', 'melee')?>"><?=showSign($this->getAttackBonus('total', 'melee'))?></span>
								<input id="bab" type="text" name="attackBonus[base]" value="<?=$this->getAttackBonus('base')?>" class="lrBuffer">
								<span class="statSelect lrBuffer">
									<select name="attackBonus[stat][melee]" class="abilitySelect">
<? 	foreach ($stats as $short => $stat) { ?>
										<option value="<?=$short?>"<?=$this->getAttackBonus('stat', 'melee') == $short?' selected="selected"':''?>><?=ucwords($short)?></option>
<?	} ?>
									</select>
									<span class="shortNum abilitySelectMod statBonus_<?=$this->getAttackBonus('stat', 'melee')?>" data-stat-hold="<?=$this->getAttackBonus('stat', 'melee')?>" data-total-ele="meleeTotal"><?=$this->getStatMod($this->getAttackBonus('stat', 'melee'))?></span>
								</span>
								<input id="melee_misc" type="text" name="attackBonus[misc][melee]" value="<?=$this->getAttackBonus('misc', 'melee')?>" class="lrBuffer">
							</div>
							<div id="ranged" class="tr sumRow">
								<label class="leftLabel shortText">Ranged</label>
								<span id="rangedTotal" class="shortNum lrBuffer total addBAB addStat_<?=$this->getAttackBonus('stat', 'ranged')?> addSize"><?=showSign($this->getAttackBonus('total', 'ranged'))?></span>
								<span class="shortNum lrBuffer bab"><?=showSign($this->getAttackBonus('base'))?></span>
								<span class="statSelect lrBuffer">
									<select name="attackBonus[stat][ranged]" class="abilitySelect">
<? 	foreach ($stats as $short => $stat) { ?>
										<option value="<?=$short?>"<?=$this->getAttackBonus('stat', 'ranged') == $short?' selected="selected"':''?>><?=ucwords($short)?></option>
<?	} ?>
									</select>
									<span class="shortNum abilitySelectMod statBonus_<?=$this->getAttackBonus('stat', 'ranged')?>" data-stat-hold="<?=$this->getAttackBonus('stat', 'ranged')?>" data-total-ele="rangedTotal"><?=$this->getStatMod($this->getAttackBonus('stat', 'ranged'))?></span>
								</span>
								<input id="ranged_misc" type="text" name="attackBonus[misc][ranged]" value="<?=$this->getAttackBonus('misc', 'ranged')?>" class="lrBuffer">
							</div>
						</div>

						<div id="actionDie">
							<div class="tr labelTR">
								<div class="fillerBlock cell shortText">&nbsp;</div>
								<label class="shortNum lrBuffer first">Number</label>
								<label class="medNum lrBuffer">Dice Type</label>
							</div>
							<div class="tr">
								<label class="leftLabel shortText alignRight">Action Die</label>
								<input type="text" name="actionDie[number]" value="<?=$this->getActionDie('number')?>" class="lrBuffer">
								<input type="text" name="actionDie[type]" value="<?=$this->getActionDie('type')?>" class="medNum lrBuffer">
							</div>
						</div>
						
						<div id="extraStats">
							<div class="tr labelTR">
								<div class="fillerBlock cell shortText">&nbsp;</div>
								<label class="shortNum lrBuffer first">Total</label>
								<label class="shortNum lrBuffer">Stat</label>
								<label class="shortNum lrBuffer">Misc</label>
							</div>
							<div class="tr sumRow">
								<label class="leftLabel shortText alignRight">Inspiration</label>
								<span id="inspiration_total" class="shortNum lrBuffer total addStat_wis"><?=showSign($this->getExtraStats('inspiration') + $this->getStatMod('wis', false))?></span>
								<span class="shortNum lrBuffer statBonus_wis"><?=$this->getStatMod('wis')?></span>
								<input id="inspiration_misc" type="text" name="extraStats[inspiration]" value="<?=$this->getExtraStats('inspiration')?>" class="lrBuffer">
							</div>
							<div class="tr sumRow">
								<label class="leftLabel shortText alignRight">Education</label>
								<span id="education_total" class="shortNum lrBuffer total addStat_int"><?=showSign($this->getExtraStats('education') + $this->getStatMod('int', false))?></span>
								<span class="shortNum lrBuffer statBonus_int"><?=$this->getStatMod('int')?></span>
								<input id="education_misc" type="text" name="extraStats[education]" value="<?=$this->getExtraStats('education')?>" class="lrBuffer">
							</div>
						</div>
					</div>

					<div id="feats" class="floatRight">
						<h2 class="headerbar hbDark">Feats/Abilities</h2>
						<div id="featList" class="hbdMargined">
<?	$this->showFeatsEdit(); ?>
						</div>
					</div>
				</div>
				
				<div id="skills" class="floatLeft" data-type="skills">
					<h2 class="headerbar hbDark">Skills <a id="addSkill" href="" class="addItem">[ Add Skill ]</a></h2>
					<div class="hbdMargined">
						<div class="tr labelTR">
							<label class="medText skill_name">Skill</label>
							<label class="shortNum alignCenter lrBuffer">Total</label>
							<label class="skill_stat alignCenter">Stat</label>
							<label class="shortNum alignCenter lrBuffer">Ranks</label>
							<label class="shortNum alignCenter lrBuffer">Misc</label>
							<label class="medNum alignCenter lrBuffer">Error</label>
							<label class="medNum alignCenter lrBuffer">Threat</label>
						</div>
						<div id="skillList">
<?	$this->showSkillsEdit(); ?>
						</div>
					</div>
				</div>
				
				<div class="clearfix">
					<div id="weapons" class="floatLeft">
						<h2 class="headerbar hbDark">Weapons <a id="addWeapon" href="">[ Add Weapon ]</a></h2>
						<div>
<?	$this->showWeaponsEdit(2); ?>
						</div>
					</div>
					<div id="armor" class="floatRight">
						<h2 class="headerbar hbDark">Armor <a id="addArmor" href="">[ Add Armor ]</a></h2>
						<div>
<?	$this->showArmorEdit(1); ?>
						</div>
					</div>
				</div>
				
				<div id="items">
					<h2 class="headerbar hbDark">Items</h2>
					<textarea name="items" class="hbdMargined"><?=$this->getItems()?></textarea>
				</div>
				
				<div id="notes">
					<h2 class="headerbar hbDark">Notes</h2>
					<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
				</div>
