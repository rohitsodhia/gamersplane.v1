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
							<input type="text" name="level[]" value="<?=$level?>" class="shortNum levelInput lrBuffer">
						</div>
<?
	}
	if (!$hasClasses) {
?>
						<div class="classSet">
							<input type="text" name="class[]" class="medText lrBuffer">
							<input type="text" name="level[]" class="shortNum levelInput lrBuffer">
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
		$modRow .= "<div class=\"statBonus_{$short}\">".$this->getStatMod($short)."</div>\n";
		$modPLRow .= "<div class=\"statBonus_{$short} addHL\">".showSign($this->getStatMod($short) + $this->getLevel())."</div>\n";
	}
	echo "<div class=\"tr\">$statLabels</div>";
	echo "<div class=\"tr\">$statRow</div>";
	echo "<div id=\"statMods\" class=\"tr\">$modRow</div>";
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
						<div id="<?=$save?>Row" class="saveSet tr">
							<label><?=strtoupper($save)?></label><div class="total"><?=$this->getSave($save, 'total')?></div>
							<span>=</span>
							<input type="text" name="saves[<?=$save?>][base]" value="<?=$this->getSave($save, 'base')?>">
							<span>+</span>
							<div id="<?=$save?>Stat" class="saveStat"><?=$this->getStatMod($this->getSaveStat($save), true)?></div>
							<span>+</span>
							<input type="text" name="saves[<?=$save?>][misc]" value="<?=$this->getSave($save, 'misc')?>">
						</div>
<?	} ?>
					</div>
					<div id="hp">
						<div class="title" class="tr labelTR">HP</div>
						<div>
							<label for="hp_current">Current</label>
							<label for="hp_maximum">Max</label>
						</div>
						<div class="tr">
							<input id="hp_current" type="text" name="hp[current]" value="<?=$this->getHP('current')?>">
							<input id="hp_maximum" type="text" name="hp[maximum]" value="<?=$this->getHP('maximum')?>">
						</div>
					</div>
					<div id="recoveries">
						<div class="title" class="tr labelTR">Recoveries</div>
						<div>
							<label for="recoveries_current">Current</label>
							<label for="recoveries_maximum">Max</label>
							<label for="recoveries_roll" class="recovery">Roll</label>
						</div>
						<div class="tr">
							<input id="recoveries_current" type="text" name="recoveries[current]" value="<?=$this->getRecoveries('current')?>">
							<input id="recoveries_maximum" type="text" name="recoveries[maximum]" value="<?=$this->getRecoveries('maximum')?>">
							<input id="recoveries_roll" type="text" name="recoveryRoll" value="<?=$this->getRecoveryRoll()?>" class="recovery medNum">
						</div>
					</div>
				</div>

				<div class="clearfix">
					<div id="uniqueThing" class="floatLeft">
						<h2 class="headerbar hbDark">One Unique Thing</h2>
						<textarea name="uniqueThing" class="hbdMargined"><?=$this->getUniqueThing()?></textarea>
					</div>
					<div id="iconRelationships" class="floatRight">
						<h2 class="headerbar hbDark">Icon Relationships</h2>
						<textarea name="iconRelationships" class="hbdMargined"><?=$this->getIconRelationships()?></textarea>
					</div>
				</div>
				<div class="clearfix">
					<div class="column first">
						<div id="backgrounds" class="itemizedList" data-type="background">
							<h2 class="headerbar hbDark">Backgrounds/Racial <a id="addBackground" href="" class="addItem">[ Add Background/Racial ]</a></h2>
							<div id="backgroundList" class="hbdMargined">
<?	$this->showBackgroundsEdit(); ?>
							</div>
						</div>
						<div id="feats" class="itemizedList" data-type="feat">
							<h2 class="headerbar hbDark">Feats <a id="addFeat" href="" class="addItem">[ Add Feat ]</a></h2>
							<div id="featList" class="hbdMargined">
<?	$this->showFeatsEdit(); ?>
							</div>
						</div>
					</div>
					<div class="column">
						<div id="classAbilities" class="itemizedList" data-type="classAbility">
							<h2 class="headerbar hbDark">Abilities/Talents <a id="addAbilityTalent" href="" class="addItem">[ Add Ability/Talent ]</a></h2>
							<div id="classAbilitiesList" class="hbdMargined">
<?	$this->showClassAbilitiesEdit(); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<div class="column first">
						<div id="powers" class="itemizedList" data-type="power">
							<h2 class="headerbar hbDark">Powers/Spells <a id="addPower" href="" class="addItem">[ Add Power/Spells ]</a></h2>
							<div id="powerList" class="hbdMargined">
<?	$this->showPowersEdit(); ?>
							</div>
						</div>
					</div>
					<div class="column">
						<div id="attacks" class="itemizedList" data-type="attack">
							<h2 class="headerbar hbDark">Attacks <a id="addAttack" href="" class="addItem">[ Add Attack ]</a></h2>
							<div id="basicAttacks" class="hbdMargined">
<?	foreach (array('melee', 'ranged') as $attack) { ?>
								<div id="ba_<?=$attack?>" class="tr" data-type="<?=$attack?>">
									<span class="label"><?=ucwords($attack)?></span>
									<span class="total addStat_<?=$this->getBasicAttacks($attack, 'stat')?>"><?=showSign($this->getLevel() + $this->getBasicAttacks($attack, 'misc'))?></span>
									<span> = </span>
									<span class="stat">
										<select name="basicAttacks[<?=$attack?>][stat]">
<?		foreach ($stats as $short => $stat) { ?>
											<option value="<?=$short?>"<?=$this->getBasicAttacks($attack, 'stat') == $short?' selected="selected"':''?>><?=ucwords($short)?></option>
<?		} ?>
										</select>
									</span>
									<span> + Lvl +</span>
									<input type="text" name="basicAttacks[<?=$attack?>][misc]" value="<?=$this->getBasicAttacks($attack, 'misc')?>">
								</div>
								<div id="baDmg_<?=$attack?>" class="tr baDmg">
									<span class="hit">Hit: <input type="text" name="basicAttacks[<?=$attack?>][hit]"  value="<?=$this->getBasicAttacks($attack, 'hit')?>" class="medNum"></span>
									<span class="miss">Miss: <input type="text" name="basicAttacks[<?=$attack?>][miss]"  value="<?=$this->getBasicAttacks($attack, 'miss')?>" class="medNum"></span>
								</div>
<?	} ?>
							</div>
							<div id="attackList" class="hbdMargined">
<?	$this->showAttacksEdit(); ?>
							</div>
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
