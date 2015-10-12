<?
	$currentUser->checkACP('links');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Links</h1>
			<div class="hbMargined"><links-edit data="newLink" new></links-edit></div>
			<div id="search" hb-margined>Search: <input type="text" ng-model="search"></div>
			<ul hb-margined>
				<li ng-repeat="link in links | filter: { title: search } | limitTo: pagination.itemsPerPage : (pagination.current - 1) * pagination.itemsPerPage">
					<links-edit data="link"></links-edit>
				</li>
			</ul>
			<paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current" class="tr"></paginate>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>