				<div class="tr labelTR">
					<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
					<label id="label_race" class="medText lrBuffer borderBox shiftRight">Race</label>
					<label id="label_classes" class="medText lrBuffer borderBox shiftRight">Class(es)</label>
					<label id="label_levels" class="shortNum lrBuffer borderBox">Level(s)</label>
				</div>
				<div class="tr">
					<input type="text" name="name" value="<?=$this->getName()?>" class="medText lrBuffer">
					<input type="text" name="race" value="<?=$this->getRace()?>" class="medText lrBuffer">
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
				</div>
				
				<div class="clearfix">
					<div id="stats">
<?
	$statLabels = '';
	$statRow = '';
	$modRow = '';
	$modPLRow = '';
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
		$statLabels .= "<label>".ucwords($short)."</label>\n";
		$statRow .= "<input type=\"text\" id=\"{$short}\" name=\"stats[{$short}]\" value=\"{$this->getStat($short)}\" maxlength=\"2\" class=\"stat\">\n";
		$modRow .= "<div>".$this->getStatMod($short)."</div>\n";
		$modPLRow .= "<div>".($this->getStatMod($short) + $this->getLevel())."</div>\n";
	}
	echo "<div class=\"tr\">$statLabels</div>";
	echo "<div class=\"tr\">$statRow</div>";
	echo "<div class=\"tr\">$modRow</div>";
	echo "<div class=\"tr\">$modPLRow</div>";
?>
					</div>

					<div id="saves">
						<div class="labelTR tr">
							<div class="cell">&nbsp;</div>
							<label>Base</label>
							<span>&nbsp;</span>
							<label>Stat</label>
							<span>&nbsp;</span>
							<label>Misc</label>
						</div>
<?	foreach (array('ac', 'pd', 'md') as $save) { ?>
						<div class="saveSet tr">
							<label for="<?=$save?>"><?=strtoupper($save)?></label><div class="total"><?=$this->getSave($save, 'total')?></div>
							<span>=</span>
							<input type="text" name="save[<?=$def?>][base]" value="<?=$this->getSave($save, 'base')?>">
							<span>+</span>
							<div class="stat"></div>
							<span>+</span>
							<input type="text" name="save[<?=$def?>][misc]" value="<?=$this->getSave($save, 'misc')?>">
						</div>
<?	} ?>
					</div>
				</div>

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
					<div id="skills">
						<h2 class="headerbar hbDark">Skills <a id="addSkill" href="">[ Add Skill ]</a></h2>
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
					<div id="feats">
						<h2 class="headerbar hbDark">Feats/Abilities <a id="addFeat" href="">[ Add Feat/Ability ]</a></h2>
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
