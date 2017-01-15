				<div id="nameDiv" class="tr">
					<label class="textLabel">Name:</label>
					<input type="text" name="name" maxlength="50" value="<?=$this->getName()?>">
				</div>

				<div class="clearfix">
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
					<div class="mainColumn right">
						<div class="clearfix">
							<div class="twoCol">
								<h2 class="headerbar hbDark">Edges &amp; Hindrances</h2>
								<textarea id="edge_hind" name="edge_hind" class="hbdMargined"><?=$this->getEdgesHindrances()?></textarea>
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
								<textarea id="injuries" name="injuries" class="hbdMargined"><?=$this->getInjuries()?></textarea>
							</div>
						</div>

						<div class="clearfix">
							<div class="twoCol">
								<h2 class="headerbar hbDark">Weapons</h2>
								<textarea id="weapons" name="weapons" class="hbdMargined"><?=$this->getWeapons()?></textarea>
							</div>
							<div class="twoCol lastTwoCol">
								<h2 class="headerbar hbDark">Equipment</h2>
								<textarea id="equipment" name="equipment" class="hbdMargined"><?=$this->getEquipment()?></textarea>
							</div>
						</div>

						<h2 class="headerbar hbDark">Background/Notes</h2>
						<textarea id="notes" name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
					</div>
				</div>
