<?
	$currentUser->checkACP('links');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Links</h1>
			<div class="hbMargined"><links-edit data="newLink" new></links-edit></div>
			<ul class="hbMargined">
				<li ng-repeat="link in links">
					<links-edit data="link"></links-edit>
				</li>
			</ul>
			<paginate class="tr"></paginate>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>