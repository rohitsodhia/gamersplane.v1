<?
	$currentUser->checkACP('systems');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<form id="loadSystem" ng-submit="loadSystem()" class="borderBottom">
				<combobox data="selectSystem.data" value="selectSystem.value" placeholder="System"></combobox>
				<button type="submit" class="fancyButton" skew-element>Load</button>
				<a href="" ng-click="setNewSystem()" class="fancyButton" skew-element>New</a>
			</form>
			<form ng-submit="saveSystem()">
				<div class="tr">
					<label for="shortName">Short name</label>
					<input id="shortName" type="text" ng-model="edit.shortName" ng-disabled="!newSystem">
				</div>
				<div class="tr">
					<label for="fullName">Full name</label>
					<input id="fullName" type="text" ng-model="edit.fullName">
				</div>
				<div class="tr vAlignMiddle">
					<label for="hasCharSheet">Has Char Sheet</label>
					<pretty-checkbox eleID="hasCharSheet" checkbox="edit.hasCharSheet"></pretty-checkbox>
				</div>
				<div class="tr">
					<label for="genres">Genres</label>
					<combobox data="newGenre.data" value="newGenre.value"></combobox> <a ng-click="addGenre()">[ + ]</a>
					<div id="genres" ng-show="edit.genres.length">
						<a ng-repeat="genre in edit.genres | orderBy:genre" ng-click="removeGenre(genre)">{{genre}}{{$last?'':', '}}</a>
					</div>
				</div>
				<div class="tr">
					<label for="publisherName">Publisher</label>
					<input id="publisherName" type="text" ng-model="edit.publisher.name">
				</div>
				<div class="tr">
					<label for="publisherSite">Publisher Site</label>
					<input id="publisherSite" type="text" ng-model="edit.publisher.site">
				</div>
				<div class="tr">
					<label for="buyTheBasics">Buy the Basics</label>
					<input id="buyTheBasics" type="text" placeholder="Basic Label" ng-model="edit.newBasic.text"> <a ng-click="addBasic()">[ + ]</a>
					<input id="buyTheBasics_site" type="text" placeholder="Basic URL" ng-model="edit.newBasic.site">
					<div id="basics" ng-show="edit.basics.length">
						<a ng-repeat="basic in edit.basics" ng-click="removeBasic(basic)">{{basic.text}} / {{basic.site}}</a>
					</div>
				</div>
				<div id="systemSave" class="alertBox_success" ng-show="saveSuccess">
					System saved!
				</div>
				<div id="buttonDiv">
					<button type="submit" ng-click="setEditBtn('save')" class="fancyButton" skew-element>Save</button>
					<button type="cancel" ng-click="setEditBtn('cancel')" class="fancyButton" skew-element>Cancel</button>
				</div>
			</form>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>