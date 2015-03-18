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
					<label for="publisherName">Publisher</label>
					<input id="publisherName" type="text" ng-model="edit.publisher.name">
				</div>
				<div class="tr">
					<label for="publisherSite">Publisher Site</label>
					<input id="publisherSite" type="text" ng-model="edit.publisher.site">
				</div>
				<div id="buttonDiv">
					<button type="submit" class="fancyButton" ng-click="setEditBtn('save')">Save</button>
					<button type="cancel" class="fancyButton" ng-click="setEditBtn('cancel')">Cancel</button>
				</div>
			</form>

			<form ng-submit="loadSystem()">
				<combobox data="combobox.systems" results="systemSearch"></combobox>
				<button type="submit" class="fancyButton">Load</button>
			</form>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>