			<div class="tr">
				<label id="charLabel" class="leftCol">Character Name</label>
				<div class="rightCol">{{character.name}}</div>
			</div>
			
			<div class="tr">
				<div id="charSheetLabel" class="leftCol">Character Sheet</div>
				<div class="rightCol" ng-bind-html="character.notes | trustHTML"></div>
			</div>
