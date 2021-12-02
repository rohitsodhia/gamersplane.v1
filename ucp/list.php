<?	$responsivePage=true;
	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><i class="ra ra-gamers-plane"></i> GP's Gamers <input type="text" ng-model="filter.search" placeholder="Search..." class="headerSearch"/></h1>

		<p class="hbMargined gamerListOptions" hb-margined>
			<label>
				<span class="labelText mob-hide">Show inactive users</span>
				<span class="labelText non-mob-hide">Inactive</span>
				<pretty-checkbox checkbox="showInactive"></pretty-checkbox>
			</label>
			<label>
				<span class="labelText mob-hide">Looking for a game</span>
				<span class="labelText non-mob-hide">LFG</span>
				<pretty-checkbox checkbox="lookingForAGame"></pretty-checkbox>
			</label>
			<label>
				<span class="labelText mob-hide">Sort by</span>
				<span class="labelText non-mob-hide">Sort</span>
				<select class="notPretty" ng-model="ordering"><option value="0">Name</option><option value="2">Newest</option><option value="1">Oldest</option></select>
			</label>
			<paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current"></paginate>
		</p>
		<ul id="gamersList" hb-margined>
			<li ng-repeat="user in users| filter:filterItems| orderBy:sortOrder | paginateItems: 25:(pagination.current - 1) * 25" ng-class="{ 'last': $index % 5 == 4 }">
				<div class="onlineIndicator" ng-class="{ 'online': user.online, 'offline': !user.online }"></div>
				<div class="lfgIndicator" ng-if="user.lfgStatus"><i class="ra ra-health"></i></div>
				<a href="/user/{{user.userID}}/" class="avatar">
					<img src="{{user.avatar}}" on-error-src="/ucp/avatars/avatar.png"/>
				</a>
				<p><a href="/user/{{user.userID}}/">{{user.username}}</a><span ng-bind-html="user.inactive | trustHTML"></span></p>
			</li>
		</ul>
		<paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current"></paginate>
<?	require_once(FILEROOT.'/footer.php'); ?>