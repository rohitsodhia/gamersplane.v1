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
					<combobox data="combobox.genres" value="newGenre" search="combobox.search.systems" placeholder="New Genre"></combobox> <a ng-click="addGenre()">[ + ]</a>
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
				<div id="systemSave" class="alertBox_success" ng-show="saveSuccess">
					System saved!
				</div>
				<div id="buttonDiv">
					<button type="submit" class="fancyButton" ng-click="setEditBtn('save')">Save</button>
					<button type="cancel" class="fancyButton" ng-click="setEditBtn('cancel')">Cancel</button>
				</div>
			</form>

			<form id="loadSystem" ng-submit="loadSystem()">
				<combobox data="combobox.systems" value="systemSearch" search="combobox.search.genres" placeholder="System"></combobox>
				<button type="submit" class="fancyButton">Load</button>
			</form>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>