				<div class="tr labelTR">
					<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
					<label id="label_species" class="medText lrBuffer borderBox shiftRight">Species</label>
				</div>
				<div class="tr">
					<input type="text" name="name" value="<?=$this->getName()?>" class="medText lrBuffer">
					<input type="text" name="species" value="<?=$this->getSpecies()?>" class="medText lrBuffer">
				</div>
				<div class="tr labelTR">
					<label id="label_career" class="medText lrBuffer borderBox shiftRight">Career</label>
					<label id="label_specialization" class="medText lrBuffer borderBox shiftRight">Specialization</label>
					<label id="label_totalXP" class="shortText lrBuffer borderBox shiftRight">Total XP</label>
					<label id="label_spentXP" class="shortText lrBuffer borderBox shiftRight">Spent XP</label>
				</div>
				<div class="tr">
					<input type="text" name="career" value="<?=$this->getCareer()?>" class="medText lrBuffer">
					<input type="text" name="specialization" value="<?=$this->getSpecialization()?>" class="medText lrBuffer">
					<input type="text" name="xp[total]" value="<?=$this->getXP('total')?>" class="shortText lrBuffer">
					<input type="text" name="xp[spent]" value="<?=$this->getXP('spent')?>" class="shortText lrBuffer">
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
								<label id="label_<?=$short?>" class="textLabel shortText lrBuffer leftLabel"><?=$stat?></label>
								<input type="text" id="<?=$short?>" name="stats[<?=$short?>]" value="<?=$this->getStat($short)?>" maxlength="2" class="stat lrBuffer">
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
								<label class="textLabel leftLabel lrBuffer">Defense (Melee)</label>
								<input type="text" name="defenses[melee]" value="<?=$this->getDefense('melee')?>" maxlength="2" class="lrBuffer">
							</div>
							<div class="tr">
								<label class="textLabel leftLabel lrBuffer">Defense (Ranged)</label>
								<input type="text" name="defenses[ranged]" value="<?=$this->getDefense('ranged')?>" maxlength="2" class="lrBuffer">
							</div>
							<div class="tr">
								<label class="textLabel leftLabel lrBuffer">Soak</label>
								<input type="text" name="defenses[soak]" value="<?=$this->getDefense('soak')?>" maxlength="2" class="lrBuffer">
							</div>
						</div>
						<div class="col">
							<div class="tr">
								<label class="textLabel leftLabel lrBuffer">Strain (Max)</label>
								<input type="text" name="hp[maxStrain]" value="<?=$this->getHP('maxStrain')?>" maxlength="2" class="lrBuffer">
							</div>
							<div class="tr">
								<label class="textLabel leftLabel lrBuffer">Strain (Current)</label>
								<input type="text" name="hp[currentStrain]" value="<?=$this->getHP('currentStrain')?>" maxlength="2" class="lrBuffer">
							</div>
							<div class="tr">
								<label class="textLabel leftLabel lrBuffer">Wounds (Max)</label>
								<input type="text" name="hp[maxWounds]" value="<?=$this->getHP('maxWounds')?>" maxlength="2" class="lrBuffer">
							</div>
							<div class="tr">
								<label class="textLabel leftLabel lrBuffer">Wounds (Current)</label>
								<input type="text" name="hp[currentWounds]" value="<?=$this->getHP('currentWounds')?>" maxlength="2" class="lrBuffer">
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<div id="skills" class="floatLeft">
						<h2 class="headerbar hbDark">Skills</h2>
						<div class="hbdMargined">
							<div id="addSkillWrapper">
								<input id="skillName" type="text" name="newSkill[name]" class="medText placeholder" autocomplete="off" data-placeholder="Skill Name">
								<select id="skillStat" name="newSkill[stat]">
<?	foreach ($stats as $short => $stat) { ?>
									<option value="<?=$short?>"><?=$stat?></option>
<?	} ?>
								</select>
								<button id="addSkill" type="submit" name="newSkill_add" class="fancyButton">Add</button>
							</div>
							<div class="tr labelTR">
								<label class="medText">Skill</label>
								<label class="skill_stat alignCenter lrBuffer">Stat</label>
								<label class="shortNum alignCenter lrBuffer">Rank</label>
								<label class="shortNum alignCenter lrBuffer">Career</label>
							</div>
<?	$this->showSkillsEdit(); ?>
						</div>
					</div>
					<div id="talents" class="floatRight">
						<h2 class="headerbar hbDark">Talents</h2>
						<div class="hbdMargined">
							<div id="addTalentWrapper">
								<input id="talentName" type="text" name="newTalent_name" class="medText placeholder" autocomplete="off" data-placeholder="Talent Name">
								<button id="addTalent" type="submit" name="newTalent_add" class="fancyButton">Add</button>
							</div>
<?	$this->showTalentsEdit(); ?>
						</div>
					</div>
				</div>
				
				<div class="clearfix">
					<div id="weapons" class="floatLeft">
						<h2 class="headerbar hbDark">Weapons <a id="addWeapon" href="">[ Add Weapon ]</a></h2>
						<div class="hbMargined">
<?	$this->showWeaponsEdit(2); ?>
						</div>
					</div>
				
					<div id="items" class="floatRight">
						<h2 class="headerbar hbDark">Items</h2>
						<textarea name="items" class="hbdMargined"><?=$this->getItems()?></textarea>
					</div>
				</div>

				<div class="clearfix">
					<div id="motivations" class="floatLeft">
						<h2 class="headerbar hbDark">Motivations</h2>
						<textarea name="motivations" class="hbdMargined"><?=$this->getMotivations()?></textarea>
					</div>
					<div id="obligations" class="floatRight">
						<h2 class="headerbar hbDark">Obligations</h2>
						<textarea name="obligations" class="hbdMargined"><?=$this->getObligations()?></textarea>
					</div>
				</div>

				<div id="notes">
					<h2 class="headerbar hbDark">Notes</h2>
					<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
				</div>
