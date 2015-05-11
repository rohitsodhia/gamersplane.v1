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
			<div id="traits" class="triCol itemizedList" data-type="trait">
				<h2 class="headerbar hbDark">Traits</h2>
				<div class="hbdMargined">
<?	foreach (array('good', 'bad', 'special') as $type) { ?>
					<div id="traits_<?=$type?>" class="itemList">
						<h3><?=ucwords($type)?></h3>
<?	$this->displayTraits($type); ?>
					</div>
<?	} ?>
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
