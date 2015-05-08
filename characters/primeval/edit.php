				<div id="basicInfo" class="tr">
					<label class="textLabel">Name:</label>
					<input type="text" name="name" maxlength="50" value="<?=$this->getName()?>" class="medText">
					<label class="textLabel">Story Points:</label>
					<input type="text" name="storyPoints" maxlength="2" value="<?=$this->getStoryPoints()?>">
				</div>
				
				<div class="clearfix">
					<div id="attributes">
						<h2 class="headerbar hbDark">Attributes</h2>
						<div class="hbdMargined">
						</div>
					</div>
				</div>

				<div class="clearfix">
					<div class="twoCol">
						<h2 class="headerbar hbDark">Posessions</h2>
						<textarea name="equipment" class="hbdMargined"><?=$this->getEquipment()?></textarea>
					</div>
					<div class="twoCol">
						<h2 class="headerbar hbDark">Notes</h2>
						<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
					</div>
				</div>
