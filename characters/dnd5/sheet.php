			<div class="tr labelTR tr-noPadding">
				<label id="label_name" class="medText">Name</label>
				<label id="label_race" class="medText">Race</label>
				<label id="label_background" class="medText">Background</label>
			</div>
			<div class="tr dataTR">
				<div class="medText"><?=$this->getName()?></div>
				<div class="medText"><?=$this->getRace()?></div>
				<div class="medText"><?=$this->getBackground()?></div>
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="longText">Class(es)</label>
				<label id="label_alignment" class="medText">Alignment</label>
			</div>
			<div class="tr dataTR">
				<div class="longText"><? $this->displayClasses(); ?></div>
				<div class="longText"><?=$this->getAlignment()?></div>
			</div>
			
			<div class="clearfix">
				<div id="stats">
					<div class="tr">
						<label class="longerStatLabel leftLabel">Inspiration</label>
						<div><?=$this->getInspiration()?></div>
					</div>
					<div class="tr">
						<label class="longerStatLabel leftLabel">Proficiency Bonus</label>
						<div><?=$this->getProfBonus()?></div>
					</div>

					<div id="abilityScoreLabels" class="labelTR">
						<label class="saveProficient">Save</label>
					</div>
<?
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
?>
					<div class="tr abilityScore">
						<label id="label_<?=$short?>" class="shortText leftLabel"><?=$stat?></label>
						<div class="stat"><?=$this->getStat($short)?></div>
						<span id="<?=$short?>Modifier" class="stat_mod"><?=$this->getStatMod($short)?></span>
						<span class="saveProficient"><?=showSign($this->getStatMod($short, false) + ($this->getSaveProf($short)?$this->getProfBonus():0))?></span>
					</div>
<?	} ?>

					<div class="tr">
						<label class="shortText leftLabel">Total HP</label>
						<div><?=$this->getHP('total')?></div>
					</div>
					<div class="tr">
						<label class="shortText leftLabel">Temp HP</label>
						<div><?=$this->getHP('temp')?></div>
					</div>

					<div class="tr">
						<label class="shortText leftLabel">AC</label>
						<div><?=$this->getAC()?></div>
					</div>
					<div class="tr">
						<label class="shortText leftLabel">Initiative</label>
						<div><?=$this->getInitiative()?></div>
					</div>
					<div class="tr">
						<label class="shortText leftLabel">Speed</label>
						<div><?=$this->getSpeed()?></div>
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
