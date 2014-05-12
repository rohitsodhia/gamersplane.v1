				<div class="tr">
					<label id="charLabel" class="leftCol">Character Label</label>
					<div class="rightCol"><?=$this->getLabel()?></div>
				</div>
				
				<div class="tr">
					<div class="leftCol">Character Sheet</div>
					<div class="rightCol"><textarea name="charSheet"><?=printReady($this->getNotes())?></textarea></div>
				</div>
