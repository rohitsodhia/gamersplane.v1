				<div id="nameDiv" class="tr">
					<label for="name" class="textLabel">Name</label>
					<input id="name" type="text" name="name" maxlength="50" value="<?=$this->getName()?>" class="width5">
				</div>
				<div id="fpStats" class="tr">
					<label for="powerLevel" class="textLabel">Power Level</label>
					<input id="powerLevel" type="text" name="powerLevel" maxlength="2" value="<?=$this->getPowerLevel()?>">
					<label for="fatePoints" class="textLabel">Fate Points</label>
					<input id="fatePoints" type="text" name="fatePoints[current]" maxlength="2" value="<?=$this->getFatePoints('current')?>">
					<label for="refresh" class="textLabel">Refresh</label>
					<input id="refresh" type="text" name="fatePoints[refresh]" maxlength="2" value="<?=$this->getFatePoints('refresh')?>">
					<label for="adjustedRefresh" class="textLabel">Adjusted Refresh:</label>
					<input id="adjustedRefresh" type="text" name="fatePoints[adjustedRefresh]" maxlength="2" value="<?=$this->getFatePoints('adjustedRefresh')?>">
				</div>

				<div class="clearfix">
					<div id="leftCol" class="floatLeft">
						<div id="aspects" class="itemizedList" data-type="aspect">
							<h2 class="headerbar hbDark">Aspects <a id="addAspect" href="" class="addItem">[ Add Aspect ]</a></h2>
							<div id="aspectList" class="hbdMargined">
								<div class="aspect withLabel tr clearfix">
									<div><label for="highConcept" class="shiftRight">High Aspect</label></div>
									<input id="highConcept" type="text" name="highConcept" value="<?=$this->getHighConcept()?>" class="width5">
								</div>
								<div class="aspect withLabel tr clearfix">
									<div><label for="trouble" class="shiftRight">Trouble</label></div>
									<input id="trouble" type="text" name="trouble" value="<?=$this->getTrouble()?>" class="width5">
								</div>
<?	$this->showAspectsEdit(); ?>
							</div>
						</div>
						<div id="stunts" data-type="stunt">
							<h2 class="headerbar hbDark">Stunts <a href="" class="addItem">[ Add Stunt ]</a></h2>
							<div id="stuntsList" class="hbdMargined">
<?	$this->showStuntsEdit(); ?>
							</div>
						</div>
					</div>
					<div id="rightCol" class="floatRight">
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
							<div id="<?=$stressType?>Stress" class="hbdMargined stress">
								<h3><?=ucwords($stressType)?> Stress</h3>
								<div class="track">
									<input type="hidden" name="stress[<?=$stressType?>][total]" value="<?=$stress['total']?>">
<?		for ($count = 0; $count <= 8; $count++) { ?>
									<div class="stressBox">
										<input type="radio" name="stress[<?=$stressType?>][current]" value="<?=$count?>"<? if ($stress == $count) echo ' checked="checked"'?>> <span><?=$count?></span>
									</div>
<?		} ?>
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
