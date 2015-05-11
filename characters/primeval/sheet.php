		<div id="basicInfo" class="tr">
			<label class="textLabel">Name:</label>
			<div><?=$this->getName()?></div>
			<label class="textLabel">Story Points:</label>
			<div><?=$this->getStoryPoints()?></div>
		</div>
		
		<div class="clearfix">
			<div id="attributes" class="triCol first">
				<h2 class="headerbar hbDark">Attributes</h2>
				<div class="hbdMargined">
					<div class="tr labelTR">
						<div class="spacer">&nbsp;</div>
						<div>Starting</div>
						<div>Current</div>
					</div>
<?	foreach ($this->getAttributeNames() as $attribute) { ?>
					<div class="tr">
						<label class="leftLabel"><?=ucwords($attribute)?></label>
						<div><?=$this->getAttributes($attribute, 'starting')?></div>
						<div><?=$this->getAttributes($attribute, 'current')?></div>
					</div>
<?	} ?>
				</div>
			</div>
			<div id="skills" class="triCol itemizedList" data-type="skill">
				<h2 class="headerbar hbDark">Skills</h2>
				<div id="skillsList" class="hbdMargined">
<?	$this->displaySkills(); ?>
				</div>
			</div>
			<div id="talents" class="triCol itemizedList" data-type="talent">
				<h2 class="headerbar hbDark">Talents</h2>
				<div id="talentsList" class="hbdMargined">
<?	$this->displayTalents(); ?>
				</div>
			</div>
		</div>

		<div class="clearfix">
			<div class="twoCol first">
				<h2 class="headerbar hbDark">Posessions</h2>
				<div class="hbdMargined"><?=$this->getEquipment(true)?></div>
			</div>
			<div class="twoCol">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=$this->getNotes(true)?></div>
			</div>
		</div>
