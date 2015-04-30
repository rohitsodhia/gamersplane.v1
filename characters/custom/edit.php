				<div class="tr">
					<label id="name" class="leftCol">Character Name</label>
					<div class="rightCol"><input id="name" type="text" name="name" value="<?=$this->getName()?>" class="midText alignLeft"></div>
				</div>
				<div class="tr">
					<div class="leftCol">Character Sheet</div>
					<div class="rightCol"><textarea name="charSheet"><?=printReady($this->getNotes())?></textarea></div>
				</div>
