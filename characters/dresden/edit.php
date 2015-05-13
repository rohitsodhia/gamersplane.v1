				<div id="basicInfo" class="tr">
					<label for="name" class="textLabel">Name</label>
					<input id="name" type="text" name="name" maxlength="50" value="<?=$this->getName()?>" class="width5">
					<label for="template" class="textLabel">Template</label>
					<input id="template" type="text" name="template" maxlength="50" value="<?=$this->getTemplate()?>" class="width5">
				</div>
				<div id="fpStats" class="tr">
					<label for="powerLevel" class="textLabel">Power Level</label>
					<input id="powerLevel" type="text" name="powerLevel" value="<?=$this->getPowerLevel()?>" class="width4 alignLeft">
					<label for="fatePoints" class="textLabel">Fate Points</label>
					<input id="fatePoints" type="text" name="fatePoints[current]" maxlength="2" value="<?=$this->getFatePoints('current')?>">
					<label for="refresh" class="textLabel">Refresh</label>
					<input id="refresh" type="text" name="fatePoints[refresh]" maxlength="2" value="<?=$this->getFatePoints('refresh')?>">
					<label for="adjustedRefresh" class="textLabel">Adjusted Refresh</label>
					<input id="adjustedRefresh" type="text" name="fatePoints[adjustedRefresh]" maxlength="2" value="<?=$this->getFatePoints('adjustedRefresh')?>">
				</div>
				<div id="coreAspects_labels" class="tr labelTR">
					<label for="highConcept" class="shiftRight width5 borderBox">High Aspect</label>
					<label for="trouble" class="shiftRight width5 borderBox">Trouble</label>
				</div>
				<div id="coreAspects" class="tr">
					<input id="highConcept" type="text" name="highConcept" value="<?=$this->getHighConcept()?>" class="width5">
					<input id="trouble" type="text" name="trouble" value="<?=$this->getTrouble()?>" class="width5">
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
							<div><input type="text" name="phases[<?=$phase?>][aspect]" value="<?=$this->getPhase($phase, 'aspect')?>" data-placeholder="Phase Aspect" class="placeholder" autocomplete="off"></div>
							<textarea name="phases[<?=$phase?>][events]"><?=$this->getPhase($phase, 'events')?></textarea>
						</div>
<?	} ?>
					</div></div>
					<div id="rightCol" class="floatRight">
						<div id="aspects" class="itemizedList" data-type="aspect">
							<h2 class="headerbar hbDark">Other Aspects <a id="addAspect" href="" class="addItem">[ Add Aspect ]</a></h2>
							<div id="aspectList" class="hbdMargined">
<?	$this->showAspectsEdit(); ?>
							</div>
						</div>
						<div id="stunts" data-type="stunt">
							<h2 class="headerbar hbDark">Stunts <a href="" class="addItem">[ Add Stunt ]</a></h2>
							<div id="stuntsList" class="hbdMargined">
<?	$this->showStuntsEdit(); ?>
							</div>
						</div>
						<div id="skills" class="nonDefault" data-type="skill">
							<h2 class="headerbar hbDark">Skills <a id="addSkill" href="" class="addItem">[ Add Skill ]</a></h2>
							<div class="hbdMargined">
								<h4>Skill Points</h4>
								<div id="skillPoints">
									<label for="skillCap" class="leftLabel">Cap</label>
									<input id="sskillCap" type="text" name="skillCap" value="<?=$this->getSkillCap()?>">
									<label for="skillPoints_spent" class="leftLabel">Spent</label>
									<input id="skillPoints_spent" type="text" name="skillPoints[spent]" value="<?=$this->getSkillPoints('spent')?>">
									<label for="skillPoints_available" class="leftLabel">Available</label>
									<input id="skillPoints_available" type="text" name="skillPoints[available]" value="<?=$this->getSkillPoints('available')?>">
								</div>
								<div id="skillList">
<?	$this->showSkillsEdit(); ?>
								</div>
							</div>
						</div>
						<div id="stress">
							<h2 class="headerbar hbDark">Stress</h2>
<?
	$stresses = $this->getStress();
	foreach ($stresses as $stressType => $stress) {
?>
							<div id="<?=$stressType?>Stress" class="stress hbdMargined" data-type="<?=$stressType?>">
								<div class="type"><span><?=ucwords($stressType)?> Stress</span> <a href="" class="add">[+]</a><a href="" class="remove">[-]</a></div>
								<input type="hidden" name="stressCap[<?=$stressType?>]" value="<?=sizeof($stress)?>">
								<div class="track">
									<div class="labels clearfix">
<?		for ($count = 1; $count <= sizeof($stress); $count++) { ?>
										<label for="stress_<?=$stressType?>_<?=$count?>"><?=$count?></label>
<?		} ?>
									</div>
									<div class="checkboxes clearfix">
<?		for ($count = 1; $count <= sizeof($stress); $count++) { ?>
										<div><input id="stress_<?=$stressType?>_<?=$count?>" type="checkbox" name="stresses[<?=$stressType?>][<?=$count?>]"<?=$stress[$count]?' checked="checked"':''?>></div>
<?		} ?>
									</div>
								</div>
							</div>
<?	} ?>
						</div>
						<div id="consequences">
							<h2 class="headerbar hbDark">Consequences</h2>
							<textarea name="consequences" class="hbdMargined"><?=$this->getConsequences()?></textarea>
						</div>
					</div>
				</div>
				
				<h2 class="headerbar hbDark">Background/Notes</h2>
				<textarea id="notes" name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
