<?
	require_once(FILEROOT.'/includes/tools/Music_consts.class.php');

	$currentUser->checkACP('music');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');

	$addJSFiles[] = 'tools/music.js'
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Music</h1>
			<a href="" ng-click="addSong()" class="fancyButton smallButton">Add Song</a>
			<music-form data="newSong" ng-show="showEdit == 'new'"></music-form>
			<ul class="prettyList">
				<li ng-repeat="song in music | orderBy: ['+approved', '+title']" ng-class="{ 'unapproved': !song.approved }">
					<div class="songDetails" ng-show="editing != song.id">
						<div class="clearfix">
							<a href="{{song.url}}" target="_blank" class="song">{{song.title}}<img src="/images/tools/quote.png" ng-if="song.lyrics" title="Has Lyrics" alt="Has Lyrics"><img src="/images/tools/battlebards_mini.png" ng-if="song.battlebards" title="Battlebards" alt="Battlebards"></a>
							<div class="manageSong">
								<a ng-click="toggleApproval(song)" href="" class="toggleApproval">{{song.approved?'Una':'A'}}pprove</a>
								<a href="" ng-click="editSong(song.id)">Edit</a>
								<a href="" class="delete">Delete</a>
								<span class="confirmDelete">(
									<a href="" class="confirm">Confirm</a>
									<a href="" class="deny">Deny</a>
								)</span>
							</div>
						</div>
						<div class="submittedBy"><a href="/user/{{song.user.userID}}" class="username">{{song.user.username}}</a></div>
						<div class="genres">{{song.genres.join(', ')}}</div>
						<div ng-if="song.notes.length" class="notes">{{song.notes}}</div>
					</div>
					<music-form data="song" ng-show="showEdit == song.id"></music-form>
				</li>
			</ul>
			<paginate class="tr"></paginate>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>