				<div class="tr labelTR">
					<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
					<label id="label_race" class="medText lrBuffer borderBox shiftRight">Race</label>
					<label id="label_size" class="medText lrBuffer borderBox">Size Modifier</label>
				</div>
				<div class="tr">
					<input type="text" name="name" value="<?=$this->getName()?>" class="medText lrBuffer">
					<input type="text" name="race" value="<?=$this->getRace()?>" class="medText lrBuffer">
					<input id="size" type="text" name="size" value="<?=$this->getSize()?>" class="lrBuffer">
				</div>
				
				<div class="tr labelTR">
					<label id="label_classes" class="medText lrBuffer borderBox shiftRight">Class(es)</label>
					<label id="label_levels" class="shortNum lrBuffer borderBox">Level(s)</label>
					<label id="label_alignment" class="medText lrBuffer borderBox shiftRight">Alignment</label>
				</div>
				<div class="tr">
					<div id="classWrapper">
						<a href="">[ Add Class ]</a>
<?
	$hasClasses = FALSE;
	foreach ($this->getClasses() as $class => $level) {
			$hasClasses = TRUE;
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
					<select id="alignment" name="alignment" class="lrBuffer">
<?	foreach (dnd3_consts::getAlignments() as $alignShort => $alignment) { ?>
						<option value="<?=$alignShort?>"<?=$this->getAlignment() == $alignShort?' selected="selected"':''?>><?=$alignment?></option>
<?	} ?>
					</select>
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
<?
		$statBonus[$short] = $bonus;
	}
?>
					</div>
					
					<div id="savingThrows">
						<div class="tr labelTR">
							<div class="fillerBlock cell">&nbsp;</div>
							<label class="statCol shortNum lrBuffer">Total</label>
							<label class="statCol shortNum lrBuffer">Base</label>
							<label class="statCol shortNum lrBuffer">Ability</label>
							<label class="statCol shortNum lrBuffer">Magic</label>
							<label class="statCol shortNum lrBuffer">Race</label>
							<label class="statCol shortNum lrBuffer">Misc</label>
						</div>
<?	foreach (d20Character_consts::getSaveNames() as $save => $saveFull) { ?>
						<div id="<?=$save?>Row" class="tr">
							<label class="leftLabel"><?=$saveFull?></label>
							<span id="<?=$save?>Total" class="shortNum lrBuffer addStat_<?=d20Character_consts::getSaveStats($save)?>"><?=showSign($this->getSave('fort', 'total'))?></span>
							<input type="text" name="saves[<?=$save?>][base]" value="<?=$this->getSave($save, 'base')?>" class="lrBuffer">
							<span class="shortNum lrBuffer statBonus_<?=d20Character_consts::getSaveStats($save)?>"><?=$this->getStatMod(d20Character_consts::getSaveStats($save))?></span>
							<input type="text" name="saves[<?=$save?>][magic]"  value="<?=$this->getSave($save, 'magic')?>" class="lrBuffer">
							<input type="text" name="saves[<?=$save?>][race]"  value="<?=$this->getSave($save, 'race')?>" class="lrBuffer">
							<input type="text" name="saves[<?=$save?>][misc]"  value="<?=$this->getSave($save, 'misc')?>" class="lrBuffer">
						</div>
<?	} ?>
					</div>
					
					<div id="hp">
						<label class="leftLabel textLabel">Total HP</label>
						<input type="text" name="hp[total]" value="<?=$this->getHP('total')?>" class="medNum">
						<label class="leftLabel textLabel">Damage Reduction</label>
						<input id="damageReduction" type="text" name="damageReduction" value="<?=$this->getDamageReduction()?>" class="medText">
					</div>
				</div>
				
				<div id="ac">
					<div class="tr labelTR">
						<label class="lrBuffer">Total AC</label>
						<div class="fillerBlock cell">&nbsp;</div>
						<label>Armor</label>
						<label>Shield</label>
						<label>Dex</label>
						<label>Class</label>
						<label>Size</label>
						<label>Natural</label>
						<label>Deflection</label>
						<label>Misc</label>
					</div>
					<div class="tr">
						<div id="ac_total" class="lrBuffer addSize"><?=$this->getAC('total')?></div>
						<div> = 10 + </div>
						<input type="text" name="ac[armor]" value="<?=$this->getAC('armor')?>" class="acComponents lrBuffer">
						<input type="text" name="ac[shield]" value="<?=$this->getAC('shield')?>" class="acComponents lrBuffer">
						<input type="text" name="ac[dex]" value="<?=$this->getAC('dex')?>" class="acComponents lrBuffer">
						<input type="text" name="ac[class]" value="<?=$this->getAC('class')?>" class="acComponents lrBuffer">
						<div class="sizeVal lrBuffer"><?=showSign($this->getSize())?></div>
						<input type="text" name="ac[natural]" value="<?=$this->getAC('natural')?>" class="acComponents lrBuffer">
						<input type="text" name="ac[deflection]" value="<?=$this->getAC('deflection')?>" class="acComponents lrBuffer">
						<input type="text" name="ac[misc]" value="<?=$this->getAC('misc')?>" class="acComponents lrBuffer">
					</div>
				</div>
				
				<div id="combatBonuses" class="clearFix">
					<div class="tr labelTR">
						<div class="fillerBlock cell shortText">&nbsp;</div>
						<label class="statCol shortNum lrBuffer">Total</label>
						<label class="statCol shortNum lrBuffer">Base</label>
						<label class="statCol shortNum lrBuffer">Ability</label>
						<label class="statCol shortNum lrBuffer">Size</label>
						<label class="statCol shortNum lrBuffer">Misc</label>
					</div>
					<div id="init" class="tr">
						<label class="leftLabel shortText">Initiative</label>
						<span id="initTotal" class="shortNum lrBuffer addStat_dex"><?=showSign($this->getInitiative('total'))?></span>
						<span class="lrBuffer">&nbsp;</span>
						<span class="shortNum lrBuffer statBonus_dex"><?=$this->getStatMod('dex')?></span>
						<span class="lrBuffer">&nbsp;</span>
						<input type="text" name="initiative[misc]" value="<?=$this->getInitiative('misc')?>" class="lrBuffer">
					</div>
					<div id="melee" class="tr">
						<label class="leftLabel shortText">Melee</label>
						<span id="meleeTotal" class="shortNum lrBuffer addStat_str addSize"><?=showSign($this->getAttackBonus('total', 'melee') + $this->getStatMod('str'))?></span>
						<input id="bab" type="text" name="attackBonus[base]" value="<?=$this->getAttackBonus('base')?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_str"><?=$this->getStatMod('str')?></span>
						<span class="shortNum lrBuffer sizeVal"><?=showSign($this->getSize())?></span>
						<input id="melee_misc" type="text" name="attackBonus[misc][melee]" value="<?=$this->getAttackBonus('misc', 'melee')?>" class="lrBuffer">
					</div>
					<div id="ranged" class="tr">
						<label class="leftLabel shortText">Ranged</label>
						<span id="rangedTotal" class="shortNum lrBuffer addStat_dex addSize"><?=showSign($this->getAttackBonus('total', 'ranged') + $this->getStatMod('dex'))?></span>
						<span class="shortNum lrBuffer bab"><?=showSign($this->getAttackBonus('base'))?></span>
						<span class="shortNum lrBuffer statBonus_dex"><?=$this->getStatMod('dex')?></span>
						<span class="shortNum lrBuffer sizeVal"><?=showSign($this->getSize())?></span>
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
					<div id="armor" class="floatRight">
						<h2 class="headerbar hbDark">Armor <a id="addArmor" href="">[ Add Armor ]</a></h2>
						<div>
<?	$this->showArmorEdit(1); ?>
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
