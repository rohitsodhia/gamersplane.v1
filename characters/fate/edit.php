				<div id="nameDiv" class="tr">
					<label for="name" class="textLabel">Name:</label>
					<input id="name" type="text" name="name" maxlength="50" value="<?=$this->getName()?>" class="width5">
				</div>
				<div id="fpStats" class="tr">
					<label for="fatePoints" class="textLabel">Fate Points:</label>
					<input id="fatePoints" type="text" name="fatePoints" maxlength="2" value="<?=$this->getFatePoints()?>">
					<label for="refresh" class="textLabel">Refresh:</label>
					<input id="refresh" type="text" name="refresh" maxlength="2" value="<?=$this->getRefresh()?>">
				</div>

				<div class="clearfix">
					<div class="mainColumn">
						<div id="aspects">
							<h2 class="headerbar hbDark">Aspects <a id="addAspect" href="">[ Add Aspect ]</a></h2>
							<div class="hbdMargined">
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
						<div id="skills">
							<h2 class="headerbar hbDark">Skills <a id="addSkill" href="">[ Add Skill ]</a></h2>
							<div id="skillList" class="hbdMargined">
<?	$this->showSkillsEdit(); ?>
							</div>
						</div>
					</div>
					<div class="sidebar">
						<div id="stress">
							<h2 class="headerbar hbDark">Stress</div>
						</div>
					</div>
				</div>
				
				<h2 class="headerbar hbDark">Background/Notes</h2>
				<textarea id="notes" name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
