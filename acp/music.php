<?
	$acpPermissions = $mysql->query("SELECT permission FROM acpPermissions WHERE userID = {$currentUser->userID}");
	$acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);
	if (sizeof($acpPermissions) == 0) { header('Location: /'); exit; }
	elseif (!in_array('music', $acpPermissions) && !in_array('all', $acpPermissions)) { header('Location: /acp/'); exit; }

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Music</h1>
			<form id="editMusicMaster" method="post" action="/acp/process/manageMusic/">
				<div class="pRow"><button type="submit" name="add" class="fancyButton">Add FAQ</button></div>
			</form>
			<ul class="prettyList">
<?
	$result = $mongo->music->find()->sort(array('approved' => 1, 'genres' => 1, 'title' => 1));
	foreach ($result as $song) {
?>
				<li<?=!$song['approved']?' class="unapproved"':''?> data-id="<?=$song['_id']?>">
					<div class="songDetails">
						<div class="clearfix">
							<a href="<?=$song['url']?>" target="_blank" class="song"><?=$song['title']?><?=$song['lyrics']?'<img src="/images/tools/quote.png" title="Has Lyrics" alt="Has Lyrics">':''?></a>
							<div class="manageSong">
								<a href="" class="toggleApproval"><?=$song['approved']?'Unapprove':'Approve'?></a>
								<a href="" class="delete">Delete</a>
								<span class="confirmDelete">(
									<a href="" class="confirm">Confirm</a>
									<a href="" class="deny">Deny</a>
								)</span>
								<a href="" class="edit">Edit</a>
							</div>
						</div>
						<div class="genres"><?=implode(', ', $song['genres'])?></div>
<?		if (strlen($song['notes'])) { ?>
						<div class="notes"><?=printReady($song['notes'])?></div>
<?		} ?>
					</div>
				</li>
<?	} ?>
			</ul>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>