<?
	require_once(FILEROOT.'/includes/tools/Music_consts.class.php');

	$currentUser->checkACP('music');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');

	$addJSFiles[] = 'tools/music.js'
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Music</h1>
			<a id="addMusic" href="/tools/music/add/" class="fancyButton smallButton">Add Music</a>
			<music-form data="newSong" save="testFunc"></music-form>
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