<?
	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">Music and Clips</h1>

		<div id="info" class="hbMargined">
			<p>Taking a tabletop game to another level is as simple as setting the mood. Eerie winds blowing through a forest, upbeat tempos when the group catches the boss, light techno as the runners make way way through the tunnels. Add that extra depth that puts players at ease or off tilt.</p>
			<p>If you're looking for that little edge to bring your game over the top, find it here! If you know a song or audio clip that works perfectly in a game, share it with everyone else. We're currently only accepting YouTube and SoundCloud links. If you have another service you think we should be using, <a href="/contact/">get in touch</a>.</p>
		</div>

		<div class="flexWrapper">
			<div class="sideWidget left">
				<h2>Filter</h2>
				<form ng-submit="loadMusic()">
					<h3>Genre</h3>
					<ul class="clearfix">
						<li ng-repeat="genre in genres"><label>
							<pretty-checkbox checkbox="filter.genres" value="genre"></pretty-checkbox>
							<div class="labelText">{{genre}}</div>
						</label></li>
					</ul>
					<h3>Lyrics?</h3>
					<ul class="clearfix">
						<li><label>
							<pretty-checkbox checkbox="filter.lyrics" value="'hasLyrics'"></pretty-checkbox> <div class="labelText">Has Lyrics</div>
						</label></li>
						<li><label>
							<pretty-checkbox checkbox="filter.lyrics" value="'noLyrics'"></pretty-checkbox> <div class="labelText">No Lyrics</div>
						</label></li>
					</ul>
					<div class="alignCenter"><button class="fancyButton">Filter</button></div>
				</form>
			</div>
			<div class="mainColumn right">
				<a ng-if="loggedIn" class="fancyButton smallButton" ng-click="toggleAddSong()">Add Music</a>
				<music-form ng-if="addSong" data="newSong"></music-form>
				<div ng-show="songSubmitted" class="alertBox_success">Song submitted!</div>
				<div class="relativeWrapper">
					<ul class="hbAttachedList">
						<li ng-repeat="song in music | orderBy: 'title'">
							<div class="clearfix" equalize-columns>
								<a href="{{song.url}}" target="_blank" class="song">{{song.title}}<img ng-if="song.lyrics" src="/images/tools/quote.png" title="Has Lyrics" alt="Has Lyrics"><img ng-if="song.battlebards" src="/images/tools/battlebards_mini.png" title="Battlebards Clip" alt="Battlebards Clip"></a
								><div class="genres">{{song.genres.join(', ')}}</div>
							</div>
							<div ng-if="song.notes" class="notes">{{song.notes}}</div>
						</li>
					</ul>
				</div>
				<div class="tr"><paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current" change-func="loadMusic"></paginate></div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>