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
						<div class="clearfix">
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
							<div id="stunts">
								<h2 class="headerbar hbDark">Stunts</h2>
								<textarea name="stunts" class="hbdMargined"><?=$this->getStunts()?></textarea>
							</div>
						</div>
					</div>
					<div class="sidebar">
						<div id="stress">
							<h2 class="headerbar hbDark">Stress</h2>
							<div id="physicalStress" class="hbdMargined">
								<h3><span>Physical Stress</span> <a href="" class="add">[ + ]</a><a href="" class="remove">[ - ]</a></h3>
								<div class="track">
									<input type="hidden" name="stress[physical][total]" value="<?=$this->getStress('physical', 'total')?>">
<?	for ($count = 0; $count <= $this->getStress('physical', 'total'); $count++) { ?>
									<div class="stressBox">
										<input type="radio" name="stress[physical][current]" value="<?=$count?>"<? if ($this->getStress('physical', 'current') == $count) echo ' checked="checked"'?>> <span><?=$count?></span>
									</div>
<?	} ?>
								</div>
							</div>
							<div id="mentalStress" class="hbdMargined">
								<h3><span>Mental Stress</span> <a href="" class="add">[ + ]</a><a href="" class="remove">[ - ]</a></h3>
								<div class="track">
									<input type="hidden" name="stress[mental][total]" value="<?=$this->getStress('mental', 'total')?>">
<?	for ($count = 0; $count <= $this->getStress('mental', 'total'); $count++) { ?>
									<div class="stressBox">
										<input type="radio" name="stress[mental][current]" value="<?=$count?>"<? if ($this->getStress('mental', 'current') == $count) echo ' checked="checked"'?>> <span><?=$count?></span>
									</div>
<?	} ?>
								</div>
							</div>
						</div>
						<div id="consequences">
							<h2 class="headerbar hbDark">Consequences</h2>
							<textarea name="consequences" class="hbdMargined"><?=$this->getConsequences()?></textarea>
						</div>
					</div>
				</div>
				
				<h2 class="headerbar hbDark">Background/Notes</h2>
				<textarea id="notes" name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
