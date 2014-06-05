				<div class="tr labelTR">
					<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
					<label id="label_professions" class="medText lrBuffer borderBox shiftRight">Profession(s)</label>
					<label id="label_levels" class="shortNum lrBuffer borderBox">Level(s)</label>
				</div>
				<div class="tr">
					<input type="text" name="name" value="<?=$this->getName()?>" class="medText alignTop lrBuffer">
					<div id="classWrapper">
						<a href="">[ Add Profession ]</a>
<?
	$hasProfessions = FALSE;
	foreach ($this->getClasses() as $class => $level) {
		$hasProfessions = TRUE;
?>
						<div class="classSet">
							<input type="text" name="class[]" value="<?=$class?>" class="medText lrBuffer">
							<input type="text" name="level[]" value="<?=$level?>" class="shortNum lrBuffer">
						</div>
<?
	}
	if (!$hasProfessions) {
?>
						<div class="classSet">
							<input type="text" name="class[]" class="medText lrBuffer">
							<input type="text" name="level[]" class="shortNum lrBuffer">
						</div>
<?	} ?>
					</div>
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
							<label class="shortNum lrBuffer">Magic</label>
							<label class="shortNum lrBuffer">Misc</label>
						</div>
<?	foreach (d20Character_consts::getSaveNames() as $save => $saveFull) { ?>
						<div id="<?=$save?>Row" class="tr">
							<label class="leftLabel"><?=$saveFull?></label>
							<span id="<?=$save?>Total" class="shortNum lrBuffer addStat_<?=d20Character_consts::getSaveStats($save)?>"><?=showSign($this->getSave($save, 'total'))?></span>
							<input type="text" name="saves[<?=$save?>][base]" value="<?=$this->getSave($save, 'base')?>" class="lrBuffer" data-save-type="<?=$save?>">
							<span class="statSelect lrBuffer">
								<select name="saves[<?=$save?>][stat]" class="abilitySelect">
<? 	foreach ($stats as $short => $stat) { ?>
									<option value="<?=$short?>"<?=$this->getSave($save, 'stat') == $short?' selected="selected"':''?>><?=ucwords($short)?></option>
<?	} ?>
								</select>
								<span class="shortNum abilitySelectMod statBonus_<?=$this->getSave($save, 'stat')?>" data-stat-hold="<?=$this->getSave($save, 'stat')?>" data-total-ele="<?=$save?>Total"><?=$this->getStatMod($this->getSave($save, 'stat'))?></span>
							</span>
							<input type="text" name="saves[<?=$save?>][magic]"  value="<?=$this->getSave($save, 'magic')?>" class="lrBuffer" data-save-type="<?=$save?>">
							<input type="text" name="saves[<?=$save?>][misc]"  value="<?=$this->getSave($save, 'misc')?>" class="lrBuffer" data-save-type="<?=$save?>">
						</div>
<?	} ?>
					</div>
					
					<div id="hp">
						<div class="tr">
							<label class="leftLabel textLabel">Total HP</label>
							<input type="text" name="hp[total]" value="<?=$this->getHP('total')?>" class="medNum">
							<label class="leftLabel textLabel">Subdual HP</label>
							<input type="text" name="hp[subdual]" value="<?=$this->getHP('subdual')?>" class="medNum">
						</div>
						<div class="tr">
							<label class="leftLabel textLabel">Max Sanity</label>
							<input type="text" name="sanity[max]" value="<?=$this->getSanity('max')?>" class="medNum">
							<label class="leftLabel textLabel">Current Sanity</label>
							<input type="text" name="sanity[current]" value="<?=$this->getSanity('current')?>" class="medNum">
						</div>
					</div>
				</div>
				
				<div class="clearfix">
					<div id="ac">
						<div class="tr labelTR">
							<label class="lrBuffer">Total AC</label>
							<div class="fillerBlock cell">&nbsp;</div>
							<label>Armor</label>
							<label>Dex</label>
							<label>Misc</label>
						</div>
						<div class="tr">
							<div id="ac_total" class="lrBuffer"><?=$this->getAC('total')?></div>
							<div> = 10 + </div>
							<input type="text" name="ac[armor]" value="<?=$this->getAC('armor')?>" class="acComponents lrBuffer">
							<input type="text" name="ac[dex]" value="<?=$this->getAC('dex')?>" class="acComponents lrBuffer">
							<input type="text" name="ac[misc]" value="<?=$this->getAC('misc')?>" class="acComponents lrBuffer">
						</div>
					</div>
					<div id="speed">
						<label class="leftLabel textLabel">Speed</label>
						<input type="text" name="speed" value="<?=$this->getSpeed()?>" class="lrBuffer">
					</div>
				</div>
				
				<div id="combatBonuses" class="clearFix">
					<div class="tr labelTR">
						<div class="fillerBlock cell shortText">&nbsp;</div>
						<label class="shortNum lrBuffer">Total</label>
						<label class="shortNum lrBuffer">Base</label>
						<label class="statSelect lrBuffer">Ability</label>
						<label class="shortNum lrBuffer">Misc</label>
					</div>
					<div id="init" class="tr">
						<label class="leftLabel shortText">Initiative</label>
						<span id="initTotal" class="shortNum lrBuffer addStat_dex"><?=showSign($this->getInitiative('total'))?></span>
						<span class="shortNum lrBuffer">&nbsp;</span>
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
					<div id="melee" class="tr">
						<label class="leftLabel shortText">Melee</label>
						<span id="meleeTotal" class="shortNum lrBuffer addStat_str"><?=showSign($this->getAttackBonus('total', 'melee') + $this->getStatMod('str'))?></span>
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
					<div id="ranged" class="tr">
						<label class="leftLabel shortText">Ranged</label>
						<span id="rangedTotal" class="shortNum lrBuffer addStat_dex"><?=showSign($this->getAttackBonus('total', 'ranged') + $this->getStatMod('dex'))?></span>
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
				
				<div class="clearfix">
					<div id="skills" class="floatLeft">
						<h2 class="headerbar hbDark">Skills</h2>
						<div class="hbdMargined">
							<div id="addSkillWrapper">
								<input id="skillName" type="text" name="newSkill[name]" class="medText placeholder" autocomplete="off" data-placeholder="Skill Name">
								<select id="skillStat" name="newSkill[stat]">
									<option value="">N/A</option>
<?
	foreach ($stats as $short => $stat) echo "								<option value=\"$short\">".ucfirst($short)."</option>\n";
?>
								</select>
								<button id="addSkill" type="submit" name="newSkill_add" class="fancyButton">Add</button>
							</div>
							<div class="tr labelTR">
								<label class="medText">Skill</label>
								<label class="shortNum alignCenter lrBuffer">Total</label>
								<label class="shortNum alignCenter lrBuffer">Stat</label>
								<label class="shortNum alignCenter lrBuffer">Ranks</label>
								<label class="shortNum alignCenter lrBuffer">Misc</label>
							</div>
<?	$this->showSkillsEdit(); ?>
						</div>
					</div>
					<div id="feats" class="floatRight">
						<h2 class="headerbar hbDark">Feats/Abilities</h2>
						<div class="hbdMargined">
							<div id="addFeatWrapper">
								<input id="featName" type="text" name="newFeat_name" class="medText placeholder" autocomplete="off" data-placeholder="Feat Name">
								<button id="addFeat" type="submit" name="newFeat_add" class="fancyButton">Add</button>
							</div>
<?	$this->showFeatsEdit(); ?>
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
				</div>
				
				<div class="clearfix">
					<div id="items">
						<h2 class="headerbar hbDark">Items</h2>
						<textarea name="items" class="hbdMargined"><?=$this->getItems()?></textarea>
					</div>
					
					<div id="spells">
						<h2 class="headerbar hbDark">Spells</h2>
						<textarea name="spells" class="hbdMargined"><?=$this->getSpells()?></textarea>
					</div>
				</div>

				<div id="notes">
					<h2 class="headerbar hbDark">Notes</h2>
					<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
				</div>
