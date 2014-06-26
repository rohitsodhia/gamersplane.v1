			<div class="tr labelTR tr-noPadding">
				<label id="label_name" class="medText">Name</label>
				<label id="label_species" class="medText">Species</label>
			</div>
			<div class="tr dataTR">
				<div class="medText"><?=$this->getName()?></div>
				<div class="medText"><?=$this->getSpecies()?></div>
			</div>
			
			<div class="tr labelTR">
				<label id="label_career" class="medText">Career</label>
				<label id="label_specialization" class="medText">Specialization</label>
				<label id="label_totalXP" class="shortText">Total XP</label>
				<label id="label_spentXP" class="shortText">Spent XP</label>
			</div>
			<div class="tr dataTR">
				<div class="medText"><?=$this->getCareer()?></div>
				<div class="medText"><?=$this->getSpecialization()?></div>
				<div class="shortText"><?=$this->getXP('total')?></div>
				<div class="shortText"><?=$this->getXP('spent')?></div>
			</div>
			
			<div class="clearfix">
				<div id="stats">
					<div class="col">
<?
	$stats = sweote_consts::getStatNames();
	$count = 0;
	foreach ($stats as $short => $stat) {
		if ($count == 3) {
?>
					</div>
					<div class="col">
<?
		}
?>
						<div class="tr">
							<label id="label_<?=$short?>" class="shortText leftLabel"><?=$stat?></label>
							<div class="stat"><?=$this->getStat($short)?></div>
						</div>
<?
		$count++;
	}
?>
					</div>
				</div>
				
				<div id="defense">
					<div class="col">
						<div class="tr">
							<label class="leftLabel lrBuffer">Defense (Melee)</label>
							<div class="shortNum lrBuffer"><?=$this->getDefense('melee')?></div>
						</div>
						<div class="tr">
							<label class="leftLabel lrBuffer">Defense (Ranged)</label>
							<div class="shortNum lrBuffer"><?=$this->getDefense('ranged')?></div>
						</div>
						<div class="tr">
							<label class="leftLabel lrBuffer">Soak</label>
							<div class="shortNum lrBuffer"><?=$this->getDefense('soak')?></div>
						</div>
					</div>
					<div class="col">
						<div class="tr">
							<label class="leftLabel lrBuffer">Strain (Max)</label>
							<div class="shortNum lrBuffer"><?=$this->getHP('maxStrain')?></div>
						</div>
						<div class="tr">
							<label class="leftLabel lrBuffer">Strain (Current)</label>
							<div class="shortNum lrBuffer"><?=$this->getHP('currentStrain')?></div>
						</div>
						<div class="tr">
							<label class="leftLabel lrBuffer">Wounds (Max)</label>
							<div class="shortNum lrBuffer"><?=$this->getHP('maxWounds')?></div>
						</div>
						<div class="tr">
							<label class="leftLabel lrBuffer">Wounds (Current)</label>
							<div class="shortNum lrBuffer"><?=$this->getHP('currentWounds')?></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="skills" class="floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined">
						<div class="tr labelTR">
							<label class="medText">Skill</label>
							<label class="skill_stat alignCenter lrBuffer">Stat</label>
							<label class="shortNum alignCenter lrBuffer">Rank</label>
							<label class="shortNum alignCenter lrBuffer">Career</label>
						</div>
<?	$this->displaySkills(); ?>
					</div>
				</div>
				<div id="talents" class="floatRight">
					<h2 class="headerbar hbDark">Talents</h2>
					<div class="hbdMargined">
<?	$this->displayTalents(); ?>
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
				<div id="items" class="floatRight">
					<h2 class="headerbar hbDark">Items</h2>
					<div class="hbdMargined"><?=$this->getItems()?></div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="motivations" class="floatLeft">
					<h2 class="headerbar hbDark">Motivations</h2>
					<div class="hbdMargined"><?=$this->getMotivations()?></div>
				</div>
				
				<div id="obligations" class="floatRight">
					<h2 class="headerbar hbDark">Obligations</h2>
					<div class="hbdMargined"><?=$this->getObligations()?></div>
				</div>
			</div>

			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=$this->getNotes()?></div>
			</div>
