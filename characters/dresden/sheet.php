			<div id="basicInfo" class="tr">
				<label class="textLabel">Name</label>
				<div class="width5"><?=$this->getName()?></div>
				<label class="textLabel">Template</label>
				<div class="width5"><?=$this->getTemplate()?></div>
			</div>
			<div id="fpStats" class="tr">
				<label class="textLabel">Power Level</label>
				<div class="width4"><?=$this->getPowerLevel()?></div>
				<label class="textLabel">Fate Points</label>
				<div><?=$this->getFatePoints('current')?></div>
				<label class="textLabel">Refresh</label>
				<div><?=$this->getFatePoints('refresh')?></div>
				<label class="textLabel">Adjusted Refresh</label>
				<div><?=$this->getFatePoints('adjustedRefresh')?></div>
			</div>
			<div id="coreAspects_labels" class="tr labelTR">
				<label class="width5 borderBox">High Aspect</label>
				<label class="width5 borderBox">Trouble</label>
			</div>
			<div id="coreAspects" class="tr">
				<div class="width5"><?=$this->getHighConcept()?></div>
				<div class="width5"><?=$this->getTrouble()?></div>
			</div>

			<div class="clearfix">
				<div id="leftCol" class="floatLeft"><div id="phases">
					<h2 class="headerbar hbDark">Phases</h2>
<?
	$numWords = array(1 => 'One', 'Two', 'Three', 'Four', 'Five');
	$phases = array(
		1 => 'Background: Where did you come from?',
		'Rising Conflict: What shaped you?',
		'The Story: What was your first adventure?',
		'Guest Star: Whose path have you crossed?',
		"Guest Star Redux: Who else's path have you crossed?"
	);
	foreach ($phases as $phase => $phaseText) {
?>
					<div class="phase hbMargined">
						<h3>Phase <?=$numWords[$phase]?> - <?=$phaseText?></h3>
						<div class="aspect"><?=$this->getPhase($phase, 'aspect')?></div>
						<div class="events"><?=printReady(BBCode2Html($this->getPhase($phase, 'events')))?></div>
					</div>
<?	} ?>
				</div></div>
				<div id="rightCol" class="floatRight">
					<div id="aspects">
						<h2 class="headerbar hbDark">Aspects</h2>
						<div class="hbdMargined">
<?	$this->displayAspects(); ?>
						</div>
					</div>
					<div id="stunts">
						<h2 class="headerbar hbDark">Stunts</h2>
						<div class="hbdMargined">
<?	$this->displayStunts(); ?>
						</div>
					</div>
					<div id="skills">
						<h2 class="headerbar hbDark">Skills</h2>
						<div class="hbdMargined">
							<h4>Skill Points</h4>
							<div id="skillPoints">
								<label class="leftLabel">Cap</label>
								<div><?=$this->getSkillCap()?></div>
								<label class="leftLabel">Spent</label>
								<div><?=$this->getSkillPoints('spent')?></div>
								<label class="leftLabel">Available</label>
								<div><?=$this->getSkillPoints('available')?></div>
							</div>
<?	$this->displaySkills(); ?>
						</div>
					</div>
					<div id="stress">
						<h2 class="headerbar hbDark">Stress</h2>
						<div class="hbdMargined">
<?
	$stresses = $this->getStress();
	foreach ($stresses as $stressType => $stress) {
?>
							<div id="<?=$stressType?>Stress" class="stress hbdMargined">
								<div class="type"><?=ucwords($stressType)?> Stress</div>
								<div class="track">
									<div class="labels clearfix">
<?		foreach ($stress as $key => $value) { ?>
										<label><?=$key?></label>
<?		} ?>
									</div>
									<div class="checkboxes clearfix">
<?		foreach ($stress as $key => $value) { ?>
										<div><div class="prettyCheckbox<? if ($value == 1) echo ' checked'?>"></div></div>
<?		} ?>
									</div>
								</div>
							</div>
<?	} ?>
						</div>
					</div>
					<div id="consequences">
						<h2 class="headerbar hbDark">Consequences</h2>
						<div class="hbdMargined"><?=printReady(BBCode2Html($this->getConsequences()))?></div>
					</div>
				</div>
			</div>

			<h2 class="headerbar hbDark">Background/Notes</h2>
			<div class="hbdMargined"><?=printReady(BBCode2Html($this->getNotes()))?></div>
