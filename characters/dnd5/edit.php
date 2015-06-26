				<div class="tr labelTR">
					<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
					<label id="label_race" class="medText lrBuffer borderBox shiftRight">Race</label>
					<label id="label_background" class="medText lrBuffer borderBox shiftRight">Background</label>
				</div>
				<div class="tr">
					<input type="text" name="name" value="<?=$this->getName()?>" class="medText lrBuffer">
					<input type="text" name="race" value="<?=$this->getRace()?>" class="medText lrBuffer">
					<input type="text" name="background" value="<?=$this->getBackground()?>" class="medText lrBuffer">
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
					<select id="alignment" name="alignment" class="lrBuffer">
<?	foreach (dnd5_consts::getAlignments() as $alignShort => $alignment) { ?>
						<option value="<?=$alignShort?>"<?=$this->getAlignment() == $alignment?' selected="selected"':''?>><?=$alignment?></option>
<?	} ?>
					</select>
				</div>
				
				<div class="clearfix">
					<div id="stats">
						<div class="tr">
							<label class="longerStatLabel leftLabel">Inspiration</label>
							<input type="text" name="inspiration" value="<?=$this->getInspiration()?>">
						</div>
						<div class="tr">
							<label class="longerStatLabel leftLabel">Proficiency Bonus</label>
							<input type="text" name="profBonus" value="<?=$this->getProfBonus()?>">
						</div>

						<div id="abilityScoreLabels" class="labelTR">
							<label class="saveProficient">Save Prof?</label>
						</div>
<?
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
?>
						<div class="tr abilityScore">
							<label id="label_<?=$short?>" class="textLabel shortText leftLabel"><?=$stat?></label>
							<input type="text" id="<?=$short?>" name="stats[<?=$short?>]" value="<?=$this->getStat($short)?>" maxlength="2" class="stat">
							<span id="<?=$short?>Modifier"><?=$this->getStatMod($short)?></span>
							<span class="saveProficient"><input type="checkbox" name="statProf[<?=$short?>]"<?=$this->getSaveProf($short)?' checked="checked"':''?>></span>
						</div>
<?	} ?>
						
						<div class="tr">
							<label class="shortText leftLabel textLabel">Total HP</label>
							<input type="text" name="hp[total]" value="<?=$this->getHP('total')?>" class="medNum">
						</div>
						<div class="tr">
							<label class="shortText leftLabel textLabel">Temp HP</label>
							<input type="text" name="hp[temp]" value="<?=$this->getHP('temp')?>" class="medNum">
						</div>
					
						<div class="tr">
							<label class="shortText leftLabel">AC</label>
							<input type="text" name="ac" value="<?=$this->getAC()?>">
						</div>
						<div class="tr">
							<label class="shortText leftLabel">Initiative</label>
							<input type="text" name="initiative" value="<?=$this->getInitiative()?>">
						</div>
						<div class="tr">
							<label class="shortText leftLabel">Speed</label>
							<input type="text" name="speed" value="<?=$this->getSpeed()?>">
						</div>
					</div>
					<div id="skills" data-type="skill">
						<h2 class="headerbar hbDark">Skills <a id="addSkill" href="" class="addItem">[ Add Skill ]</a></h2>
						<div class="hbdMargined">
							<div class="tr labelTR">
								<label class="shortNum alignCenter lfBuffer">Prof?</label>
								<label class="medText">Skill</label>
								<label class="skill_stat alignCenter">Stat</label>
							</div>
							<div id="skillList">
<?	$this->showSkillsEdit(); ?>
							</div>
						</div>
					</div>
					<div id="feats" data-type="feat">
						<h2 class="headerbar hbDark">Feats/Abilities <a id="addFeat" href="" class="addItem">[ Add Feat/Ability ]</a></h2>
						<div id="featList" class="hbdMargined">
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
					<div id="spells" class="floatRight">
						<h2 class="headerbar hbDark">Spells <a id="addSpell" href="">[ Add Spell ]</a></h2>
						<div id="spellList" class="hbdMargined">
<?	$this->showSpellsEdit(); ?>
						</div>
					</div>
				</div>
				
				<div id="items" class="clearfix">
					<h2 class="headerbar hbDark">Items</h2>
					<textarea name="items" class="hbdMargined"><?=$this->getItems()?></textarea>
				</div>

				<div id="notes">
					<h2 class="headerbar hbDark">Notes</h2>
					<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
				</div>
