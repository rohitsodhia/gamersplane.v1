<?
	$currentUser->checkACP('links');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Links</h1>
			<form id="newLink" method="post" action="/acp/process/manageLink/" class="hbMargined">
<?	linkFormat(); ?>
				<div id="submitRow" class="pRow"><button type="submit" name="action" value="add" class="fancyButton">Add Link</button></div>
			</form>
			<ul class="hbMargined">
<?
	$result = $mongo->links->find();
	foreach ($result as $link) linkFormat($link);
?>
			</ul>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>