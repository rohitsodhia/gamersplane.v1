				<div id="nameDiv" class="tr">
					<label class="textLabel">Name:</label>
					<input type="text" name="name" maxlength="50" value="<?=$this->getName()?>">
				</div>

				<div class="flexWrapper">
					<div class="sidebar left">
						<h2 class="headerbar hbDark">Traits &amp; Skills</h2>
						<div id="primaryTraits">
<?
	foreach (savageworlds_consts::getTraits() as $abbrev => $label) {
		$dice = $this->getTraits($abbrev);
?>
							<div class="hbdMargined traitDiv" data-trait="<?=$abbrev?>">
								<div class="trait clearfix">
									<div class="traitName"><?=$label?></div>
									<div class="diceSelect"><span>d</span> <select name="traits[<?=$abbrev?>]" class="diceType">
<?		foreach ([4, 6, 8, 10, 12] as $dCount) { ?>
										<option<?=$dice == $dCount?' selected="selected"':''?>><?=$dCount?></option>
<?		} ?>
									</select></div>
								</div>
								<div class="skillHeader"><?=$label?> Skills <a href="" class="addSkill">+</a></div>
								<div class="skills">
<?		$this->showSkillsEdit($abbrev); ?>
								</div>
							</div>
<?	} ?>
						</div>

						<div id="derivedTraits">
<?	foreach (['Pace', 'Charisma', 'Parry', 'Toughness'] as $derivedTrait) { ?>
							<div class="tr<?=$derivedTrait == 'Parry' || $derivedTrait == 'Toughness'?' longer':''?>">
								<label class="traitName"><?=$derivedTrait?></label>
								<input type="text" name="derivedTraits[<?=strtolower($derivedTrait)?>]" value="<?=$this->getDerivedTraits(strtolower($derivedTrait))?>">
							</div>
<?	} ?>
						</div>
					</div>
					<div class="right">
						<div class="clearfix">
							<div class="twoCol">
								<h2 class="headerbar hbDark">Edges &amp; Hindrances</h2>
								<div class="hbdMargined"><textarea id="edge_hind" name="edge_hind"><?=$this->getEdgesHindrances()?></textarea></div>
							</div>
							<div class="twoCol lastTwoCol">
								<h2 class="headerbar hbDark">Injuries</h2>
								<div id="injNums" class="hbdMargined">
									<div>
										<div>Wounds</div>
										<input type="text" name="wounds" maxlength="2" value="<?=$this->getWounds()?>">
									</div>
									<div>
										<div>Fatigue</div>
										<input type="text" name="fatigue" maxlength="2" value="<?=$this->getFatigue()?>">
									</div>
								</div>
								<div class="hbdMargined"><textarea id="injuries" name="injuries"><?=$this->getInjuries()?></textarea></div>
							</div>
						</div>

						<div class="clearfix">
							<div class="twoCol">
								<h2 class="headerbar hbDark">Weapons</h2>
								<div class="hbdMargined"><textarea id="weapons" name="weapons"><?=$this->getWeapons()?></textarea></div>
							</div>
							<div class="twoCol lastTwoCol">
								<h2 class="headerbar hbDark">Equipment</h2>
								<div class="hbdMargined"><textarea id="equipment" name="equipment"><?=$this->getEquipment()?></textarea></div>
							</div>
						</div>

						<h2 class="headerbar hbDark">Background/Notes</h2>
						<div class="hbdMargined"><textarea id="notes" name="notes"><?=$this->getNotes()?></textarea></div>
					</div>
				</div>
