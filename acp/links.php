<?
	$currentUser->checkACP('links');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Links</h1>
			<form id="editLinksMaster" method="post" action="/acp/process/manageLinks/" class="attachForm">
				<input type="hidden" name="action" value="edit">
				<input id="mongoID" type="hidden" name="mongoID" value="">
				<div class="pRow">
					<label for="title">Title:</label>
					<input id="title" type="text" name="title">
				</div>
				<div class="pRow">
					<label for="url">URL:</label>
					<input id="url" type="text" name="url">
				</div>
				<div class="pRow">
					<label for="title">Title:</label>
					<input id="title" type="text" name="title">
				</div>
				<div class="pRow">
					<label>Category:</label>
					<select name="category">
						<option>Resource</option>
						<option>Podcast</option>
						<option>Blog</option>
					</select>
				</div>
				<div class="pRow">
					<label>Type:</label>
					<select name="type">
						<option>Link</option>
						<option>Affiliate</option>
					</select>
				</div>
				<div class="pRow">
					<label for="active">Active:</label>
					<input type="checkbox" name="active"> Active
				</div>
				<input type="hidden" name="modal" value="1">
				<div class="pRow"><button type="submit" name="add" class="fancyButton">Add FAQ</button></div>
			</form>
			<ul class="prettyList">
<?
	$result = $mongo->links->find();
	foreach ($result as $link) {
?>
				<li<?=!$link['active']?' class="inactive"':''?> data-id="<?=$link['_id']?>">
				</li>
<?	} ?>
			</ul>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>