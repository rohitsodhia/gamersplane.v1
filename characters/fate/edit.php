				<div id="nameDiv" class="tr">
					<label for="name" class="textLabel">Name</label>
					<input id="name" type="text" name="name" maxlength="50" value="<?=$this->getName()?>" class="width5">
				</div>
				<div id="fpStats" class="tr">
					<label for="fatePoints" class="textLabel">Fate Points</label>
					<input id="fatePoints" type="text" name="fatePoints[current]" maxlength="2" value="<?=$this->getFatePoints('current')?>">
					<label for="refresh" class="textLabel">Refresh</label>
					<input id="refresh" type="text" name="fatePoints[refresh]" maxlength="2" value="<?=$this->getFatePoints('refresh')?>">
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
					<div class="mainColumn">
						<div class="clearfix">
							<div id="aspects" class="itemizedList" data-type="aspect">
								<h2 class="headerbar hbDark">Aspects <a id="addAspect" href="" class="addItem">[ Add Aspect ]</a></h2>
								<div id="aspectList" class="hbdMargined">
<?	$this->showAspectsEdit(); ?>
								</div>
							</div>
							<div id="skills" class="nonDefault" data-type="skill">
								<h2 class="headerbar hbDark">Skills <a id="addSkill" href="" class="addItem">[ Add Skill ]</a></h2>
								<div id="skillList" class="hbdMargined">
<?	$this->showSkillsEdit(); ?>
								</div>
							</div>
						</div>
						<div class="clearfix">
							<div id="extras">
								<h2 class="headerbar hbDark">Extras</h2>
								<textarea name="extras" class="hbdMargined"><?=$this->getExtras()?></textarea>
							</div>
							<div id="stunts" data-type="stunt">
								<h2 class="headerbar hbDark">Stunts <a href="" class="addItem">[ Add Stunt ]</a></h2>
								<div id="stuntsList" class="hbdMargined">
<?	$this->showStuntsEdit(); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="sidebar">
						<div id="stress">
							<h2 class="headerbar hbDark">Stress</h2>
<?
	$stresses = $this->getStress();
	foreach ($stresses as $stressType => $stress) {
?>
							<div id="<?=$stressType?>Stress" class="hbdMargined">
								<h3><span><?=ucwords($stressType)?> Stress</span> <a href="" class="add">[ + ]</a><a href="" class="remove">[ - ]</a></h3>
								<div class="track">
									<input type="hidden" name="stress[<?=$stressType?>][total]" value="<?=$stress['total']?>">
<?		for ($count = 0; $count <= $stress['total']; $count++) { ?>
									<div class="stressBox">
										<input type="radio" name="stress[<?=$stressType?>][current]" value="<?=$count?>"<? if ($stress['current'] == $count) echo ' checked="checked"'?>> <span><?=$count?></span>
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
