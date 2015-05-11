				<div id="basicInfo" class="tr">
					<label class="textLabel">Name:</label>
					<input type="text" name="name" maxlength="50" value="<?=$this->getName()?>" class="medText">
					<label class="textLabel">Story Points:</label>
					<input type="text" name="storyPoints" maxlength="2" value="<?=$this->getStoryPoints()?>">
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
								<div><input type="text" name="attributes[<?=$attribute?>][starting]" value="<?=$this->getAttributes($attribute, 'starting')?>"></div>
								<div><input type="text" name="attributes[<?=$attribute?>][current]" value="<?=$this->getAttributes($attribute, 'current')?>"></div>
							</div>
<?	} ?>
						</div>
					</div>
					<div id="skills" class="triCol itemizedList" data-type="skill">
						<h2 class="headerbar hbDark">Skills</h2>
						<div id="skillsList" class="hbdMargined">
<?	$this->showSkillsEdit(); ?>
						</div>
					</div>
					<div id="traits" class="triCol itemizedList" data-type="trait">
						<h2 class="headerbar hbDark">Traits</h2>
						<div class="hbdMargined">
<?	foreach (array('good', 'bad', 'special') as $type) { ?>
							<div id="traits_<?=$type?>" class="itemList">
								<h3><?=ucwords($type)?> <a href="" class="addItem" data-type="<?=$type?>">[ Add Trait ]</a></h3>
<?	$this->showTraitsEdit($type); ?>
							</div>
<?	} ?>
						</div>
					</div>
				</div>

				<div class="clearfix">
					<div class="twoCol first">
						<h2 class="headerbar hbDark">Posessions</h2>
						<textarea name="equipment" class="hbdMargined"><?=$this->getEquipment()?></textarea>
					</div>
					<div class="twoCol">
						<h2 class="headerbar hbDark">Notes</h2>
						<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
					</div>
				</div>
