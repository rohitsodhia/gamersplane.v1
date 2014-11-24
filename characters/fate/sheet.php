			<div id="nameDiv" class="tr">
				<label for="name" class="textLabel">Name:</label>
				<div><?=$this->getName()?></div>
			</div>
			<div id="fpStats" class="tr">
				<label for="fatePoints" class="textLabel">Fate Points:</label>
				<div><?=$this->getFatePoints()?></div>
				<label for="refresh" class="textLabel">Refresh:</label>
				<div><?=$this->getRefresh()?></div>
			</div>

			<div class="clearfix">
				<div class="mainColumn">
					<div class="clearfix">
						<div id="aspects" class="itemizedList" data-type="aspect">
							<h2 class="headerbar hbDark">Aspects</h2>
							<div id="aspectList" class="hbdMargined">
								<div class="aspect withLabel tr clearfix">
									<div><label class="shiftRight">High Aspect</label></div>
									<div><?=$this->getHighConcept()?></div>
								</div>
								<div class="aspect withLabel tr clearfix">
									<div><label class="shiftRight">Trouble</label></div>
									<div><?=$this->getTrouble()?></div>
								</div>
<?	$this->displayAspects(); ?>
							</div>
						</div>
						<div id="skills" class="nonDefault" data-type="skill">
							<h2 class="headerbar hbDark">Skills</h2>
							<div id="skillList" class="hbdMargined">
<?	$this->displaySkills(); ?>
							</div>
						</div>
					</div>
					<div class="clearfix">
						<div id="extras">
							<h2 class="headerbar hbDark">Extras</h2>
							<div><?=$this->getExtras()?></div>
						</div>
						<div id="stunts">
							<h2 class="headerbar hbDark">Stunts</h2>
							<div><?=$this->getStunts()?></div>
						</div>
					</div>
				</div>
				<div class="sidebar">
					<div id="stress">
						<h2 class="headerbar hbDark">Stress</h2>
						<div id="physicalStress" class="hbdMargined">
							<h3><span>Physical Stress</span></h3>
							<div class="track">
<?	for ($count = 1; $count <= $this->getStress('physical', 'total'); $count++) { ?>
								<div class="stressBox">
									<div class="prettyCheckbox<? if ($this->getStress('physical', 'current') == $count) echo ' checked'?>"></div> <span><?=$count?></span>
								</div>
<?	} ?>
							</div>
						</div>
						<div id="mentalStress" class="hbdMargined">
							<h3><span>Mental Stress</span></h3>
							<div class="track">
<?	for ($count = 1; $count <= $this->getStress('mental', 'total'); $count++) { ?>
								<div class="stressBox">
									<div class="prettyCheckbox<? if ($this->getStress('mental', 'current') == $count) echo ' checked'?>"></div> <span><?=$count?></span>
								</div>
<?	} ?>
							</div>
						</div>
					</div>
					<div id="consequences">
						<h2 class="headerbar hbDark">Consequences</h2>
						<div><?=$this->getConsequences()?></div>
					</div>
				</div>
			</div>
			
			<h2 class="headerbar hbDark">Background/Notes</h2>
			<div><?=$this->getNotes()?></div>
