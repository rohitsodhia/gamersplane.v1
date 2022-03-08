<div class="flex-row">
	<label id="name">Character Name</label>
	<div class="col-1"><input id="name" type="text" ng-model="character.name" class="midText alignLeft"></div>
	<label style="align-self:end;">Character Sheet</label> <span id="loadTemplate" style="display:none;margin-left:auto;"><select id="templateList" class="notPretty"><option>--load template--</option></select></span>
	<div class="col-1">
		<ul class="customTabs"><li class="tabSel" data-showsel="#tabEdit">Edit</li><li data-showsel="#tabPreview">Preview</li></ul>
		<div class="editTab" id="tabEdit"><textarea ng-model="character.notes" class="markItUp"></textarea></div>
		<div class="editTab customChar ignoreSave" id="tabPreview" style="display:none"></div>
	</div>
</div>
