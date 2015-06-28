<?
	require_once(FILEROOT.'/includes/tools/Music_consts.class.php');

	$currentUser->checkACP('music');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Music</h1>
			<a id="addMusic" href="/tools/music/add/" class="fancyButton smallButton">Add Music</a>
			<form method="post" action="/acp/process/manageMusic/" class="attachForm">
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
				<div id="genres" class="pRow">
<?
	foreach (Music_consts::getGenres() as $type) {
		$cleanType = preg_replace('/[^A-za-z]/', '', $type);
?>
					<div>
						<input id="<?=$cleanType?>" type="checkbox" name="genre[<?=$type?>]">
						<label for="<?=$cleanType?>"><?=$type?></label>
					</div>
<?	} ?>
				</div>
				<div id="notesRow" class="pRow">
					<label for="notes">Notes:</label>
					<textarea id="notes" name="notes"></textarea>
				</div>
				<input type="hidden" name="modal" value="1">
				<div class="pRow"><button type="submit" name="add" class="fancyButton">Add FAQ</button></div>
			</form>
			<ul class="prettyList">
				<li ng-repeat="song in music" ng-class="{ 'unapproved': !song.approved }">
					<div class="songDetails">
						<div class="clearfix">
							<a href="{{song.url}}" target="_blank" class="song">{{song.title}}<img src="/images/tools/quote.png" ng-if="song.lyrics" title="Has Lyrics" alt="Has Lyrics"></a>
							<div class="manageSong">
								<a ng-click="toggleApproval(song)" href="" class="toggleApproval">{{song.approved?'Una':'A'}}pprove</a>
								<a href="" class="delete">Delete</a>
								<span class="confirmDelete">(
									<a href="" class="confirm">Confirm</a>
									<a href="" class="deny">Deny</a>
								)</span>
								<a href="" class="edit">Edit</a>
							</div>
						</div>
						<div class="genres">{{song.genres.join(', ')}}</div>
						<div ng-if="song.notes.length" class="notes">{{song.notes}}</div>
					</div>
				</li>
			</ul>
			<paginate class="tr"></paginate>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>