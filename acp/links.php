<?
	$currentUser->checkACP('links');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Links</h1>
			<form id="editLinksMaster" method="post" action="/acp/process/manageLinks/" class=".attachForm">
				<input type="hidden" name="action" value="edit">
				<input id="mongoID" type="hidden" name="mongoID" value="">
				<div class="pRow">
					<label for="url">URL:</label>
					<input id="url" type="text" name="url">
				</div>
				<div id="noURL" class="alert hideDiv">URL is required.</div>
				<div id="dupURL" class="alert hideDiv">This song is already in our system.</div>
				<div id="invalidURL" class="alert hideDiv">This URL isn't one we currently accept.</div>
				<div class="pRow">
					<label for="title">Title:</label>
					<input id="title" type="text" name="title">
				</div>
				<div id="noTitle" class="alert hideDiv">Title is required.</div>
				<div class="pRow">
					<label>Has lyrics?</label>
					<input id="hasLyrics" type="radio" name="lyrics" value="yes"> <label for="hasLyrics" class="radioLabel">Yes</label>
					<input id="noLyrics" type="radio" name="lyrics" value="no"> <label for="noLyrics" class="radioLabel">No</label>
				</div>
				<div id="noTitle" class="alert hideDiv">Title is required.</div>
				<div class="pRow">
					<label>Genres:</label>
					<div id="noGenres" class="alert hideDiv">At least one genre must be selected.</div>
				</div>
				<div id="notesRow" class="pRow">
					<label for="notes">Notes:</label>
					<textarea id="notes" name="notes"></textarea>
				</div>
				<input type="hidden" name="modal" value="1">
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