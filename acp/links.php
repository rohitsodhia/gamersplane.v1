<?
	$currentUser->checkACP('links');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Links</h1>
			<links-edit data="newLink" new></links-edit>
			<div id="submitRow" class="pRow"><button type="submit" name="action" value="add" class="fancyButton">Add Link</button></div>
			<ul class="hbMargined">
				<li ng-repeat="link in links">
					<links-edit></links-edit>
				</li>
			</ul>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>