<?
	require_once(FILEROOT.'/includes/tools/Music_consts.class.php');

	$currentUser->checkACP('systems');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<form ng-submit="saveSystem()" class="borderBottom">
				<div class="tr">
					<label for="shortName">Short name</label>
					<input type="text" ng-model="edit.shortName" disabled="disabled">
				</div>
				<div class="tr">
					<label for="fullName">Full name</label>
					<input id="fullName" type="text" ng-model="edit.fullName">
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
					<button type="submit" class="fancyButton" ng-click="setEditBtn('save')">Save</button>
					<button type="cancel" class="fancyButton" ng-click="setEditBtn('cancel')">Cancel</button>
				</div>
			</form>

			<form id="loadSystem" ng-submit="loadSystem()">
				<combobox data="selectSystem.data" value="selectSystem.value" placeholder="System"></combobox>
				<button type="submit" class="fancyButton">Load</button>
			</form>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>