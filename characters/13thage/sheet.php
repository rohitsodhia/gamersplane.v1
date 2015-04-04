			<div class="tr labelTR tr-noPadding">
				<label id="label_name" class="medText">Name</label>
				<label id="label_race" class="medText">Race</label>
				<label id="label_classes" class="longText">Class(es)</label>
			</div>
			<div class="tr dataTR">
				<div class="medText"><?=$this->getName()?></div>
				<div class="medText"><?=$this->getRace()?></div>
				<div class="longText"><? $this->displayClasses(); ?></div>
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
		$statRow .= "<div>{$this->getStat($short)}</div>\n";
		$modRow .= "<div class=\"statBonus_{$short}\">".$this->getStatMod($short)."</div>\n";
		$modPLRow .= "<div class=\"statBonus_{$short} addHL\">".showSign($this->getStatMod($short) + $this->getLevel())."</div>\n";
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
						<label><?=strtoupper($save)?></label><div class="total"><?=$this->getSave($save, 'total')?></div>
						<span>=</span>
						<div><?=$this->getSave($save, 'base')?></div>
						<span>+</span>
						<div id="<?=$save?>Stat" class="saveStat"><?=$this->getStatMod($this->getSaveStat($save), true)?></div>
						<span>+</span>
						<div><?=$this->getSave($save, 'misc')?></div>
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
						<div><?=$this->getHP('current')?></div>
						<div><?=$this->getHP('maximum')?></div>
					</div>
				</div>
				<div id="recoveries">
					<div class="title" class="tr labelTR">Recoveries</div>
					<div>
						<label for="recoveries_current">Current</label>
						<label for="recoveries_maximum">Max</label>
						<label for="recoveries_roll">Roll</label>
					</div>
					<div class="tr">
						<div><?=$this->getRecoveries('current')?></div>
						<div><?=$this->getRecoveries('maximum')?></div>
						<div><?=$this->getRecoveryRoll()?></div>
					</div>
				</div>
			</div>

				<div id="skills">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined">
						<div class="tr labelTR">
							<label class="shortNum alignCenter lfBuffer">Prof?</label>
							<label class="medText">Skill</label>
							<label class="skill_stat alignCenter">Stat</label>
						</div>
<?	$this->displaySkills(); ?>
					</div>
				</div>
				<div id="feats">
					<h2 class="headerbar hbDark">Feats/Abilities</h2>
					<div class="hbdMargined">
<?	$this->displayFeats(); ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2 class="headerbar hbDark">Weapons</h2>
					<div class="hbdMargined">
<?	$this->displayWeapons(); ?>
					</div>
				</div>
				<div id="spells" class="floatRight">
					<h2 class="headerbar hbDark">Spells</h2>
					<div class="hbdMargined">
					<div class="spell tr labelTR clearfix">
						<label class="spell_name"><?=$spell['name']?></label>
						<label class="spell_ab shortNum">AB</label>
						<label class="spell_save shortNum">Save</label>
					</div>
<?	$this->displaySpells(); ?>
					</div>
				</div>
			</div>
			
			<div id="items" class="clearfix">
				<h2 class="headerbar hbDark">Items</h2>
				<div class="hbdMargined"><?=printReady($this->getItems())?></div>
			</div>

			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=printReady($this->getNotes())?></div>
			</div>
